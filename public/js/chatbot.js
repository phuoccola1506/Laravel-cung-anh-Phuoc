/**
 * AI Chatbot for Product Search
 * Phân tích ngôn ngữ tự nhiên và tìm kiếm sản phẩm
 */

class ProductChatbot {
    constructor() {
        this.chatWindow = document.getElementById('chatbot-window');
        this.chatToggle = document.getElementById('chatbot-toggle');
        this.chatClose = document.getElementById('chatbot-close');
        this.chatForm = document.getElementById('chatbot-form');
        this.chatInput = document.getElementById('chatbot-input');
        this.messagesContainer = document.getElementById('chatbot-messages');
        
        this.isOpen = false;
        this.isProcessing = false;
        
        this.init();
    }
    
    init() {
        // Event listeners
        this.chatToggle.addEventListener('click', () => this.toggleChat());
        this.chatClose.addEventListener('click', () => this.closeChat());
        this.chatForm.addEventListener('submit', (e) => this.handleSubmit(e));
        
        // Quick suggestion clicks
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('suggestion-chip')) {
                const query = e.target.getAttribute('data-query');
                this.chatInput.value = query;
                this.handleSubmit(e);
            }
        });
    }
    
    toggleChat() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
            this.chatWindow.classList.add('open');
            this.chatInput.focus();
        } else {
            this.chatWindow.classList.remove('open');
        }
    }
    
    closeChat() {
        this.isOpen = false;
        this.chatWindow.classList.remove('open');
    }
    
    async handleSubmit(e) {
        e.preventDefault();
        
        if (this.isProcessing) return;
        
        const userMessage = this.chatInput.value.trim();
        if (!userMessage) return;
        
        // Add user message
        this.addMessage(userMessage, 'user');
        this.chatInput.value = '';
        
        // Show typing indicator
        this.showTyping();
        this.isProcessing = true;
        
        try {
            // Parse query
            const parsedQuery = this.parseQuery(userMessage);
            
            // Search products
            const results = await this.searchProducts(parsedQuery);
            
            // Remove typing indicator
            this.removeTyping();
            
            // Display results
            if (results.success && results.products.length > 0) {
                this.displayProducts(results.products, parsedQuery);
            } else {
                this.addMessage(
                    'Xin lỗi, tôi không tìm thấy sản phẩm nào phù hợp với yêu cầu của bạn. 😔\n\n' +
                    'Bạn có thể thử:\n' +
                    '• Mô tả chi tiết hơn (ví dụ: "điện thoại Samsung màn hình lớn")\n' +
                    '• Điều chỉnh khoảng giá\n' +
                    '• Tìm theo danh mục khác',
                    'bot'
                );
            }
        } catch (error) {
            this.removeTyping();
            this.addMessage(
                'Oops! Có lỗi xảy ra. Vui lòng thử lại sau. 😓',
                'bot'
            );
            console.error('Chatbot error:', error);
        } finally {
            this.isProcessing = false;
        }
    }
    
    /**
     * Phân tích câu truy vấn bằng regex và keyword matching
     */
    parseQuery(query) {
        query = query.toLowerCase().trim();
        
        const parsed = {
            original: query,
            category: null,
            brand: null,
            price_min: null,
            price_max: null,
            keywords: []
        };
        
        // Danh mục sản phẩm (categories)
        const categoryMap = {
            'điện thoại': ['điện thoại', 'smartphone', 'phone', 'dt', 'đtdđ'],
            'laptop': ['laptop', 'máy tính xách tay', 'máy tính', 'pc'],
            'tai nghe': ['tai nghe', 'headphone', 'earphone', 'airpods'],
            'chuột': ['chuột', 'mouse', 'chuột máy tính'],
            'bàn phím': ['bàn phím', 'keyboard', 'phím']
        };
        
        for (const [category, keywords] of Object.entries(categoryMap)) {
            if (keywords.some(kw => query.includes(kw))) {
                parsed.category = category;
                break;
            }
        }
        
        // Thương hiệu (brands) - Case insensitive, partial matching
        const brandMappings = {
            'apple': ['apple', 'iphone', 'macbook', 'airpod', 'ipad'],
            'samsung': ['samsung', 'galaxy'],
            'xiaomi': ['xiaomi', 'redmi', 'poco'],
            'oppo': ['oppo', 'reno', 'find'],
            'vivo': ['vivo'],
            'realme': ['realme'],
            'dell': ['dell', 'alienware', 'xps'],
            'hp': ['hp', 'pavilion', 'envy', 'omen'],
            'asus': ['asus', 'rog', 'tuf', 'zenbook', 'vivobook'],
            'lenovo': ['lenovo', 'thinkpad', 'ideapad', 'legion'],
            'acer': ['acer', 'aspire', 'predator', 'nitro'],
            'msi': ['msi'],
            'sony': ['sony'],
            'jbl': ['jbl'],
            'logitech': ['logitech'],
            'razer': ['razer'],
            'corsair': ['corsair'],
            'steelseries': ['steelseries']
        };
        
        // Check brands (case insensitive, partial match)
        for (const [brandName, keywords] of Object.entries(brandMappings)) {
            if (keywords.some(keyword => query.includes(keyword))) {
                // Capitalize first letter for consistent output
                parsed.brand = brandName.charAt(0).toUpperCase() + brandName.slice(1);
                break;
            }
        }
        
        // Giá (price ranges)
        // Pattern: "giá dưới 10 triệu", "từ 15 đến 25 triệu", "giá < 20 triệu"
        
        // QUAN TRỌNG: Check khoảng giá TRƯỚC (để tránh match nhầm "từ" trong range)
        const rangePricePattern = /từ\s*(\d+)\s*(?:triệu|tr|m)\b\s*(?:đến|-)\s*(\d+)\s*(?:triệu|tr|m)\b/i;
        const rangeMatch = query.match(rangePricePattern);
        if (rangeMatch) {
            parsed.price_min = parseInt(rangeMatch[1]) * 1000000;
            parsed.price_max = parseInt(rangeMatch[2]) * 1000000;
        } else {
            // Nếu không phải range, mới check min/max riêng lẻ
            
            // Giá tối đa: "dưới X triệu", "từ X triệu trở xuống", "giá X triệu"
            const maxPricePatterns = [
                /(?:dưới|dư[oô]i|<|nhỏ hơn|max)\s*(\d+)\s*(?:triệu|tr|m)\b/i,
                /(?:giá|price)\s*(?:dưới|<|nhỏ hơn)\s*(\d+)\s*(?:triệu|tr|m)\b/i,
                /(?:từ|giá)\s*(\d+)\s*(?:triệu|tr|m)\b\s*(?:trở xuống|trở lại)/i,
                /(\d+)\s*(?:triệu|tr|m)\b\s*(?:trở xuống|trở lại)/i,
                /(?:giá|price)\s+(\d+)\s*(?:triệu|tr|m)\b/i  // "giá 20 triệu"
            ];
            
            for (const pattern of maxPricePatterns) {
                const match = query.match(pattern);
                if (match) {
                    parsed.price_max = parseInt(match[1]) * 1000000;
                    break;
                }
            }
            
            // Giá tối thiểu: "trên X triệu", "từ X triệu trở lên" (nhưng KHÔNG phải "từ X triệu trở xuống")
            const minPricePatterns = [
                /(?:trên|>|lớn hơn)\s*(\d+)\s*(?:triệu|tr|m)\b/i,
                /từ\s*(\d+)\s*(?:triệu|tr|m)\b\s*(?:trở lên)/i,
                /(\d+)\s*(?:triệu|tr|m)\b\s*(?:trở lên)/i
            ];
            
            for (const pattern of minPricePatterns) {
                const match = query.match(pattern);
                if (match) {
                    parsed.price_min = parseInt(match[1]) * 1000000;
                    break;
                }
            }
        }
        
        // Keywords đặc biệt
        if (query.includes('giá rẻ') || query.includes('rẻ nhất')) {
            parsed.price_max = 5000000; // < 5 triệu
        }
        if (query.includes('cao cấp') || query.includes('high-end')) {
            parsed.price_min = 20000000; // > 20 triệu
        }
        
        // Extract other keywords
        const stopWords = ['tìm', 'cho', 'tôi', 'mua', 'cần', 'muốn', 'giá', 'từ', 'đến', 'triệu', 'trở', 'lên', 'xuống'];
        parsed.keywords = query
            .split(/\s+/)
            .filter(word => 
                word.length > 2 && 
                !stopWords.includes(word) &&
                !/^\d+$/.test(word)
            );
        
        return parsed;
    }
    
    /**
     * Gọi API tìm kiếm sản phẩm
     */
    async searchProducts(parsedQuery) {
        const response = await fetch('/chatbot/search', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(parsedQuery)
        });
        
        if (!response.ok) {
            // Log chi tiết lỗi
            const errorText = await response.text();
            console.error('Server error:', response.status, errorText);
            throw new Error(`Server error: ${response.status}`);
        }
        
        return await response.json();
    }
    
    /**
     * Hiển thị sản phẩm tìm được
     */
    displayProducts(products, query) {
        let message = `Tôi đã tìm thấy ${products.length} sản phẩm phù hợp`;
        
        if (query.category) {
            message += ` cho "${query.category}"`;
        }
        if (query.brand) {
            message += ` của ${query.brand}`;
        }
        if (query.price_max) {
            message += ` giá dưới ${this.formatPrice(query.price_max)}`;
        }
        
        message += ':';
        
        const messageDiv = this.addMessage(message, 'bot');
        
        // Add products HTML
        const productsHTML = `
            <div class="product-results">
                ${products.map(product => `
                    <a href="/product/${product.id}" class="product-item" target="_blank">
                        <img src="${product.image || '/images/no-image.png'}" 
                             alt="${product.name}" 
                             class="product-image-1"
                             onerror="this.src='/images/no-image.png'">
                        <div class="product-info">
                            <div class="product-name">${product.name}</div>
                            <div>
                                <span class="product-price">${product.price}</span>
                            </div>
                        </div>
                    </a>
                `).join('')}
            </div>
        `;
        
        messageDiv.querySelector('.message-content').insertAdjacentHTML('beforeend', productsHTML);
        
        // Add follow-up suggestions
        const followUp = `
            <div class="quick-suggestions" style="margin-top: 12px;">
                <p class="suggestions-label">Bạn có thể hỏi:</p>
                <button class="suggestion-chip" data-query="Tìm ${query.category || 'sản phẩm'} khác">
                    Xem sản phẩm khác
                </button>
                <button class="suggestion-chip" data-query="Tìm ${query.category || 'sản phẩm'} giá rẻ hơn">
                    Giá rẻ hơn
                </button>
            </div>
        `;
        
        messageDiv.querySelector('.message-content').insertAdjacentHTML('beforeend', followUp);
    }
    
    /**
     * Add message to chat
     */
    addMessage(text, sender = 'bot') {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const avatar = sender === 'bot' 
            ? '<i class="fas fa-robot"></i>' 
            : '<i class="fas fa-user"></i>';
        
        messageDiv.innerHTML = `
            <div class="message-avatar">${avatar}</div>
            <div class="message-content">
                <p>${text.replace(/\n/g, '</p><p>')}</p>
            </div>
        `;
        
        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
        
        return messageDiv;
    }
    
    /**
     * Show typing indicator
     */
    showTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot-message typing-message';
        typingDiv.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
                <div class="typing-indicator">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        `;
        
        this.messagesContainer.appendChild(typingDiv);
        this.scrollToBottom();
    }
    
    /**
     * Remove typing indicator
     */
    removeTyping() {
        const typingMsg = this.messagesContainer.querySelector('.typing-message');
        if (typingMsg) {
            typingMsg.remove();
        }
    }
    
    /**
     * Scroll to bottom of messages
     */
    scrollToBottom() {
        setTimeout(() => {
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        }, 100);
    }
    
    /**
     * Format price to VND
     */
    formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    }
}

// Initialize chatbot when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new ProductChatbot();
});
