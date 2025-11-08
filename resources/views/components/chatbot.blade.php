<!-- Chatbot Component -->
<div id="chatbot-container">
    <!-- Chat Button -->
    <button id="chatbot-toggle" class="chatbot-button" aria-label="Mở chat tư vấn">
        <i class="fas fa-comments"></i>
        <span class="chat-badge">AI</span>
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" class="chatbot-window">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-content">
                <div class="chatbot-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="chatbot-title">
                    <h4>Tư vấn mua hàng</h4>
                    <span class="chatbot-status">
                        <span class="status-dot"></span>
                        Trực tuyến
                    </span>
                </div>
            </div>
            <button id="chatbot-close" class="chatbot-close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Messages Area -->
        <div id="chatbot-messages" class="chatbot-messages">
            <!-- Welcome Message -->
            <div class="message bot-message">
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <p>Xin chào! 👋 Tôi là trợ lý mua sắm AI.</p>
                    <p>Hãy cho tôi biết bạn đang tìm sản phẩm gì nhé!</p>
                    <div class="quick-suggestions">
                        <p class="suggestions-label">Ví dụ:</p>
                        <button class="suggestion-chip" data-query="Tìm điện thoại Samsung giá dưới 10 triệu">
                            📱 Điện thoại Samsung < 10 triệu
                        </button>
                        <button class="suggestion-chip" data-query="Tìm laptop Dell giá từ 15 đến 25 triệu">
                            💻 Laptop Dell 15-25 triệu
                        </button>
                        <button class="suggestion-chip" data-query="Tìm tai nghe giá rẻ">
                            🎧 Tai nghe giá rẻ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="chatbot-input-area">
            <form id="chatbot-form">
                <div class="input-wrapper">
                    <input 
                        type="text" 
                        id="chatbot-input" 
                        class="chatbot-input" 
                        placeholder="Nhập câu hỏi của bạn..."
                        autocomplete="off"
                    >
                    <button type="submit" class="chatbot-send-btn" id="chatbot-send">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
            <div class="chatbot-footer-text">
                <small>Được hỗ trợ bởi AI 🤖</small>
            </div>
        </div>
    </div>
</div>
