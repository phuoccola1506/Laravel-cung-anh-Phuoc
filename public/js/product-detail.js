// Product Detail Page Scripts
// ============================================================================
// GLOBAL VARIABLES
// ============================================================================

let productVariants = [];      // Toàn bộ variants từ backend
let productId = null;          // ID sản phẩm (lấy từ data-product-id)
let selectedColor = '';        // Màu đang chọn
let selectedStorage = '';      // Dung lượng đang chọn
let selectedAttributes = {};   // Tất cả attributes đang chọn
let currentVariant = null;     // Variant hiện tại đang active

// ============================================================================
// IMAGE GALLERY
// ============================================================================

/**
 * Thay đổi ảnh chính khi click thumbnail
 */
function changeImage(thumbnail) {
    const mainImage = document.getElementById('mainImage');
    if (!mainImage) {
        console.warn('Main image element not found');
        return;
    }
    
    const thumbnails = document.querySelectorAll('.thumbnail-images img');
    
    // Remove active class from all thumbnails
    thumbnails.forEach(img => img.classList.remove('active'));
    
    // Add active class to clicked thumbnail
    thumbnail.classList.add('active');
    
    // Change main image source
    const newSrc = thumbnail.src.replace('w=100&h=100', 'w=600&h=600');
    mainImage.src = newSrc;
}

// ============================================================================
// VARIANTS INITIALIZATION
// ============================================================================

/**
 * Khởi tạo dữ liệu variants từ DOM
 * Parse JSON và setup default values
 */
function initializeVariants() {
    console.log('🚀 Initializing variants...');
    
    const variantsDataEl = document.getElementById('variants-data');
    if (!variantsDataEl) {
        console.error('❌ #variants-data element not found in DOM');
        return false;
    }

    try {
        // Parse variants JSON
        const variantsJson = variantsDataEl.dataset.variants;
        if (!variantsJson) {
            console.error('❌ data-variants attribute is empty');
            return false;
        }
        
        productVariants = JSON.parse(variantsJson);
        console.log('✅ Parsed variants:', productVariants.length, 'items');
        
        // Lấy product ID
        productId = parseInt(variantsDataEl.dataset.productId);
        if (!productId || isNaN(productId)) {
            console.error('❌ data-product-id not found or invalid');
            return false;
        }
        console.log('✅ Product ID:', productId);
        
        // Lấy default values
        selectedColor = variantsDataEl.dataset.defaultColor || '';
        selectedStorage = variantsDataEl.dataset.defaultStorage || '';
        
        // Khởi tạo selectedAttributes từ variant đầu tiên
        if (productVariants.length > 0) {
            const firstVariant = productVariants[0];
            if (firstVariant.attributes) {
                selectedAttributes = { ...firstVariant.attributes };
                currentVariant = firstVariant;
                console.log('✅ Initial variant:', currentVariant.id);
                console.log('✅ Initial attributes:', selectedAttributes);
            } else {
                console.error('❌ First variant has no attributes');
                return false;
            }
        } else {
            console.error('❌ No variants available');
            return false;
        }
        
        console.log('🎉 Variants initialization complete!');
        return true;
        
    } catch (error) {
        console.error('❌ Error parsing variants data:', error);
        return false;
    }
}


// ============================================================================
// COLOR SELECTION
// ============================================================================

/**
 * Setup event listeners cho các nút chọn màu
 */
function setupColorOptions() {
    const colorOptions = document.querySelectorAll('.color-option');
    console.log('🎨 Color options found:', colorOptions.length);
    
    if (colorOptions.length === 0) {
        console.log('ℹ️ No color options in this product');
        return;
    }
    
    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            const newColor = this.dataset.color;
            if (!newColor) {
                console.error('❌ Color button missing data-color attribute');
                return;
            }
            
            selectedColor = newColor;
            console.log('🎨 Color selected:', selectedColor);
            
            // Update active state
            colorOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            // Update selected attributes with new color
            selectedAttributes.color = selectedColor;
            
            // Tìm variant mới dựa trên color và attributes hiện tại
            findAndUpdateVariant();
        });
    });
}

// ============================================================================
// STORAGE SELECTION
// ============================================================================

/**
 * Setup event listeners cho các nút chọn dung lượng
 */
function setupStorageOptions() {
    const storageOptions = document.querySelectorAll('.storage-option');
    console.log('💾 Storage options found:', storageOptions.length);
    
    if (storageOptions.length === 0) {
        console.log('ℹ️ No storage options in this product');
        return;
    }
    
    storageOptions.forEach(option => {
        option.addEventListener('click', function() {
            const newStorage = this.dataset.storage;
            if (!newStorage) {
                console.error('❌ Storage button missing data-storage attribute');
                return;
            }
            
            selectedStorage = newStorage;
            console.log('💾 Storage selected:', selectedStorage);
            
            // Update active state
            storageOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            // Update selected attributes
            selectedAttributes.storage = selectedStorage;
            
            // Tìm variant mới
            findAndUpdateVariant();
        });
    });
}

// ============================================================================
// OTHER VARIANT OPTIONS (RAM, CPU, DPI, etc.)
// ============================================================================

/**
 * Setup event listeners cho các variant options khác
 */
function setupVariantOptions() {
    const variantOptions = document.querySelectorAll('.variant-option');
    console.log('⚙️ Variant options found:', variantOptions.length);
    
    if (variantOptions.length === 0) {
        console.log('ℹ️ No other variant options in this product');
        return;
    }
    
    variantOptions.forEach(option => {
        option.addEventListener('click', function() {
            const attribute = this.dataset.attribute;
            const value = this.dataset.value;
            
            if (!attribute || !value) {
                console.error('❌ Variant button missing data-attribute or data-value');
                return;
            }
            
            console.log('⚙️ Variant option selected:', attribute, '=', value);
            
            // Update active state for this attribute group
            const siblings = this.parentElement.querySelectorAll('.variant-option');
            siblings.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            // Update selected attributes
            selectedAttributes[attribute] = value;
            
            // Tìm variant mới
            findAndUpdateVariant();
        });
    });
}

// ============================================================================
// VARIANT MATCHING & UPDATE
// ============================================================================

/**
 * Tìm variant khớp với các attributes đã chọn và update UI
 */
function findAndUpdateVariant() {
    if (!productVariants.length) {
        console.error('❌ No variants available');
        return;
    }

    console.log('🔍 Finding variant for:', selectedAttributes);

    // Tìm variant khớp với TẤT CẢ selected attributes
    const matchedVariant = productVariants.find(v => {
        if (!v.attributes) {
            console.warn('⚠️ Variant missing attributes:', v);
            return false;
        }
        
        // Check từng attribute trong selectedAttributes
        for (const [key, value] of Object.entries(selectedAttributes)) {
            // Bỏ qua color_code vì không dùng để match
            if (key === 'color_code') continue;
            
            // Nếu attribute không khớp, loại variant này
            if (v.attributes[key] !== value) {
                return false;
            }
        }
        
        return true;
    });

    if (matchedVariant) {
        console.log('✅ Variant found:', matchedVariant.id, matchedVariant.sku);
        currentVariant = matchedVariant;
        
        // Cập nhật TOÀN BỘ selectedAttributes từ variant tìm được
        selectedAttributes = { ...matchedVariant.attributes };
        
        // Update UI
        updatePrice(matchedVariant);
        updateStock(matchedVariant);
        updateSKU(matchedVariant);
        updateVariantImage(matchedVariant);
        updateOtherAttributesUI(matchedVariant.attributes);
    } else {
        console.warn('⚠️ No matching variant found for:', selectedAttributes);
        showNotification('Không tìm thấy biến thể phù hợp!', 'warning');
    }
}

/**
 * Update UI của các attribute buttons dựa trên variant hiện tại
 */
function updateOtherAttributesUI(attributes) {
    console.log('🔄 Updating attributes UI:', attributes);
    
    // Duyệt qua tất cả attributes
    for (const [key, value] of Object.entries(attributes)) {
        // Bỏ qua color_code và color (đã xử lý riêng)
        if (key === 'color_code') continue;
        
        // Tìm buttons có data-attribute khớp
        const buttons = document.querySelectorAll(`[data-attribute="${key}"]`);
        if (buttons.length === 0) continue;
        
        // Update active state
        buttons.forEach(btn => {
            if (btn.dataset.value === value) {
                // Remove active từ siblings
                const siblings = btn.parentElement.querySelectorAll('.variant-option');
                siblings.forEach(b => b.classList.remove('active'));
                // Add active
                btn.classList.add('active');
                console.log(`✅ Updated ${key} UI to: ${value}`);
            }
        });
    }
    
    // Update storage UI nếu thay đổi
    if (attributes.storage && attributes.storage !== selectedStorage) {
        selectedStorage = attributes.storage;
        const storageButtons = document.querySelectorAll('.storage-option');
        storageButtons.forEach(btn => {
            if (btn.dataset.storage === attributes.storage) {
                storageButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                console.log(`✅ Updated storage UI to: ${attributes.storage}`);
            }
        });
    }
}



// ============================================================================
// PRICE & STOCK UPDATE
// ============================================================================

/**
 * Cập nhật hiển thị giá dựa trên variant
 */
function updatePrice(variant) {
    const priceContainer = document.getElementById('product-price');
    if (!priceContainer) {
        console.error('❌ #product-price element not found');
        return;
    }

    // Parse price và discount từ variant hiện tại
    const price = parseInt(variant.price) || 0;
    const discount = parseFloat(variant.discount) || 0;
    
    // Lấy currency từ data attribute hoặc default VND
    const currency = priceContainer.dataset.currency || 'đ';
    
    console.log('💰 Updating price for variant:', { 
        sku: variant.sku,
        price: price.toLocaleString('vi-VN'), 
        discount: discount + '%',
        currency: currency
    });
    
    if (discount > 0) {
        // Tính giá sau giảm: price * (1 - discount/100)
        const discountedPrice = price * (1 - discount / 100);
        // Làm tròn đến hàng chục nghìn
        const finalPrice = Math.round(discountedPrice / 10000) * 10000;
        
        priceContainer.innerHTML = `
            <span class="price-old fs-2 text-muted text-decoration-line-through">
                ${price.toLocaleString('vi-VN')}${currency}
            </span>
            <span class="price-new fs-2 text-danger fw-bold">
                ${finalPrice.toLocaleString('vi-VN')}${currency}
            </span>
        `;
        console.log('✅ Price updated with discount:', {
            original: price.toLocaleString('vi-VN') + currency,
            discounted: finalPrice.toLocaleString('vi-VN') + currency,
            saved: (price - finalPrice).toLocaleString('vi-VN') + currency
        });
    } else {
        priceContainer.innerHTML = `
            <span class="price-new fs-2 text-danger fw-bold">
                ${price.toLocaleString('vi-VN')}${currency}
            </span>
        `;
        console.log('✅ Price updated (no discount):', price.toLocaleString('vi-VN') + currency);
    }
    
    // Cập nhật badge trên ảnh chính
    updateProductBadge(discount);
}

/**
 * Cập nhật badge giảm giá trên ảnh chính
 */
function updateProductBadge(discount) {
    const badge = document.getElementById('product-badge');
    if (!badge) {
        console.warn('⚠️ #product-badge element not found');
        return;
    }
    
    if (discount > 0) {
        badge.className = 'product-badge sale';
        badge.textContent = `-${discount.toFixed(0)}%`;
        console.log('✅ Badge updated: Sale -' + discount.toFixed(0) + '%');
    } else {
        badge.className = 'product-badge new';
        badge.textContent = 'Mới';
        console.log('✅ Badge updated: New');
    }
}

/**
 * Cập nhật hiển thị tồn kho
 */
function updateStock(variant) {
    const stockQuantityEl = document.getElementById('stock-quantity');
    const quantityInput = document.getElementById('quantity');
    
    if (!stockQuantityEl) {
        console.error('❌ #stock-quantity element not found');
    } else {
        stockQuantityEl.textContent = variant.stock;
        console.log('✅ Stock updated:', variant.stock);
    }
    
    if (!quantityInput) {
        console.error('❌ #quantity input not found');
    } else {
        quantityInput.max = variant.stock;
        // Reset quantity to 1 if current value exceeds new max
        if (parseInt(quantityInput.value) > variant.stock) {
            quantityInput.value = Math.min(1, variant.stock);
            console.log('⚠️ Quantity reset to', quantityInput.value);
        }
    }
}

/**
 * Cập nhật hiển thị SKU
 */
function updateSKU(variant) {
    const skuEl = document.getElementById('product-sku');
    
    if (!skuEl) {
        console.warn('⚠️ #product-sku element not found');
        return;
    }
    
    skuEl.textContent = variant.sku;
    console.log('✅ SKU updated:', variant.sku);
}

/**
 * Cập nhật ảnh khi chọn variant
 */
function updateVariantImage(variant) {
    const mainImage = document.getElementById('mainImage');
    
    if (!mainImage) {
        console.warn('⚠️ #mainImage element not found');
        return;
    }
    
    // Nếu variant có ảnh riêng, hiển thị ảnh variant
    if (variant.image) {
        const variantImageUrl = '/images/' + variant.image;
        mainImage.src = variantImageUrl;
        console.log('✅ Main image updated to variant image:', variantImageUrl);
        
        // Tìm thumbnail tương ứng và set active
        const thumbnails = document.querySelectorAll('.thumbnail-images img');
        thumbnails.forEach(thumb => {
            thumb.classList.remove('active');
            
            // Check nếu thumbnail này là ảnh của variant
            const thumbVariantId = thumb.dataset.variantId;
            if (thumbVariantId && parseInt(thumbVariantId) === variant.id) {
                thumb.classList.add('active');
                console.log('✅ Thumbnail activated for variant:', variant.id);
            }
        });
    } else {
        // Nếu variant không có ảnh, fallback về ảnh sản phẩm chính
        const thumbnails = document.querySelectorAll('.thumbnail-images img');
        if (thumbnails.length > 0) {
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            thumbnails[0].classList.add('active'); // Ảnh đầu tiên là ảnh sản phẩm chính
            mainImage.src = thumbnails[0].src;
            console.log('✅ Main image reset to product default image');
        }
    }
}

// ============================================================================
// QUANTITY CONTROLS
// ============================================================================

/**
 * Tăng số lượng
 */
function increaseQty() {
    const input = document.getElementById('quantity');
    if (!input) {
        console.error('❌ #quantity input not found');
        return;
    }
    
    const currentValue = parseInt(input.value);
    const max = parseInt(input.max);
    
    if (currentValue < max) {
        input.value = currentValue + 1;
        console.log('➕ Quantity increased to', input.value);
    } else {
        showNotification('Đã đạt số lượng tối đa!', 'warning');
    }
}

/**
 * Giảm số lượng
 */
function decreaseQty() {
    const input = document.getElementById('quantity');
    if (!input) {
        console.error('❌ #quantity input not found');
        return;
    }
    
    const currentValue = parseInt(input.value);
    const min = parseInt(input.min);
    
    if (currentValue > min) {
        input.value = currentValue - 1;
        console.log('➖ Quantity decreased to', input.value);
    }
}



// ============================================================================
// ADD TO CART
// ============================================================================

/**
 * Thêm sản phẩm vào giỏ hàng - AJAX
 */
async function addToCart() {
    console.log('🛒 Adding to cart...');
    
    // Validate quantity input
    const quantityInput = document.getElementById('quantity');
    if (!quantityInput) {
        console.error('❌ Quantity input not found');
        showNotification('Lỗi: Không tìm thấy ô nhập số lượng!', 'error');
        return;
    }
    
    const quantity = parseInt(quantityInput.value);
    if (isNaN(quantity) || quantity < 1) {
        showNotification('Vui lòng nhập số lượng hợp lệ!', 'error');
        return;
    }
    
    // Validate product ID
    if (!productId) {
        console.error('❌ Product ID not available');
        showNotification('Lỗi: Không xác định được sản phẩm!', 'error');
        return;
    }
    
    // Validate current variant
    if (!currentVariant) {
        console.error('❌ No variant selected');
        showNotification('Vui lòng chọn phiên bản sản phẩm!', 'error');
        return;
    }

    // Kiểm tra stock
    if (quantity > currentVariant.stock) {
        showNotification(`Chỉ còn ${currentVariant.stock} sản phẩm trong kho!`, 'error');
        return;
    }
    
    // Validate CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('❌ CSRF token not found');
        showNotification('Lỗi: Không tìm thấy CSRF token!', 'error');
        return;
    }

    console.log('📦 Cart data:', {
        product_id: productId,
        variant_id: currentVariant.id,
        quantity: quantity,
        sku: currentVariant.sku
    });

    try {
        // Show loading state
        const addToCartBtn = event && event.target;
        if (addToCartBtn) {
            addToCartBtn.disabled = true;
            addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
        }
        
        const response = await fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                variant_id: currentVariant.id,
                quantity: quantity
            })
        });

        const data = await response.json();
        
        console.log('📥 Response:', data);

        if (data.success) {
            showNotification(data.message || 'Đã thêm sản phẩm vào giỏ hàng!', 'success');
            
            // Update cart count real-time
            if (typeof updateCartCountInHeader === 'function') {
                updateCartCountInHeader(data.cart_count);
                console.log('✅ Cart count updated:', data.cart_count);
            } else {
                // Fallback: Update manual
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(el => {
                    el.textContent = data.cart_count;
                    // Add animation
                    el.classList.add('updated');
                    setTimeout(() => el.classList.remove('updated'), 500);
                });
                console.log('✅ Cart count updated (fallback):', data.cart_count);
            }
        } else {
            showNotification(data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng!', 'error');
        }
        
        // Restore button state
        if (addToCartBtn) {
            addToCartBtn.disabled = false;
            addToCartBtn.innerHTML = '<i class="fas fa-cart-plus"></i> Thêm vào giỏ';
        }
        
    } catch (error) {
        console.error('❌ Error adding to cart:', error);
        showNotification('Không thể thêm vào giỏ hàng. Vui lòng thử lại!', 'error');
        
        // Restore button state
        const addToCartBtn = event && event.target;
        if (addToCartBtn) {
            addToCartBtn.disabled = false;
            addToCartBtn.innerHTML = '<i class="fas fa-cart-plus"></i> Thêm vào giỏ';
        }
    }
}

// ============================================================================
// BUY NOW
// ============================================================================

/**
 * Mua ngay - Thêm vào giỏ và chuyển đến trang giỏ hàng
 */
async function buyNow() {
    console.log('⚡ Buy now...');
    
    // Validate quantity
    const quantityInput = document.getElementById('quantity');
    if (!quantityInput) {
        console.error('❌ Quantity input not found');
        showNotification('Lỗi: Không tìm thấy ô nhập số lượng!', 'error');
        return;
    }
    
    const quantity = parseInt(quantityInput.value);
    if (isNaN(quantity) || quantity < 1) {
        showNotification('Vui lòng nhập số lượng hợp lệ!', 'error');
        return;
    }
    
    // Validate product ID
    if (!productId) {
        console.error('❌ Product ID not available');
        showNotification('Lỗi: Không xác định được sản phẩm!', 'error');
        return;
    }
    
    // Validate current variant
    if (!currentVariant) {
        console.error('❌ No variant selected');
        showNotification('Vui lòng chọn phiên bản sản phẩm!', 'error');
        return;
    }

    // Kiểm tra stock
    if (quantity > currentVariant.stock) {
        showNotification(`Chỉ còn ${currentVariant.stock} sản phẩm trong kho!`, 'error');
        return;
    }
    
    // Validate CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('❌ CSRF token not found');
        showNotification('Lỗi: Không tìm thấy CSRF token!', 'error');
        return;
    }

    try {
        // Show loading state
        const buyNowBtn = event && event.target;
        if (buyNowBtn) {
            buyNowBtn.disabled = true;
            buyNowBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        }
        
        const response = await fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                variant_id: currentVariant.id,
                quantity: quantity
            })
        });

        const data = await response.json();
        
        console.log('📥 Response:', data);

        if (data.success) {
            // Redirect to cart page
            console.log('✅ Redirecting to cart...');
            window.location.href = '/cart';
        } else {
            showNotification(data.message || 'Có lỗi xảy ra!', 'error');
            
            // Restore button state
            if (buyNowBtn) {
                buyNowBtn.disabled = false;
                buyNowBtn.innerHTML = '<i class="fas fa-bolt"></i> Mua ngay';
            }
        }
    } catch (error) {
        console.error('❌ Error:', error);
        showNotification('Không thể thực hiện. Vui lòng thử lại!', 'error');
        
        // Restore button state
        const buyNowBtn = event && event.target;
        if (buyNowBtn) {
            buyNowBtn.disabled = false;
            buyNowBtn.innerHTML = '<i class="fas fa-bolt"></i> Mua ngay';
        }
    }
}



// ============================================================================
// WISHLIST
// ============================================================================

/**
 * Thêm vào danh sách yêu thích
 */
function addToWishlist() {
    // TODO: Implement wishlist functionality
    showNotification('Đã thêm vào danh sách yêu thích!', 'success');
    console.log('❤️ Added to wishlist');
}

// ============================================================================
// UTILITIES
// ============================================================================

/**
 * Format số tiền theo chuẩn VND
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

/**
 * Hiển thị thông báo toast
 */
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    // Icon theo loại thông báo
    let icon = 'info-circle';
    if (type === 'success') icon = 'check-circle';
    if (type === 'error') icon = 'exclamation-circle';
    if (type === 'warning') icon = 'exclamation-triangle';
    
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// ============================================================================
// PRODUCT TABS
// ============================================================================

/**
 * Setup tabs navigation (description, specifications, reviews)
 */
function setupTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    if (tabButtons.length === 0) {
        console.log('ℹ️ No tabs found on this page');
        return;
    }
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Remove active from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active to clicked button and target pane
            this.classList.add('active');
            const targetPane = document.getElementById(targetTab);
            if (targetPane) {
                targetPane.classList.add('active');
                console.log('📑 Tab switched to:', targetTab);
            }
        });
    });
}

// ============================================================================
// INITIALIZATION
// ============================================================================

/**
 * Main initialization function
 * Runs when DOM is fully loaded
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Product Detail JS Loaded');
    console.log('====================================');
    
    // 1. Initialize variants data
    const variantsInitialized = initializeVariants();
    if (!variantsInitialized) {
        console.error('❌ Failed to initialize variants. Check #variants-data element and data-variants attribute.');
        showNotification('Lỗi: Không thể tải dữ liệu sản phẩm!', 'error');
        return;
    }
    
    // 2. Setup variant selection controls
    setupColorOptions();
    setupStorageOptions();
    setupVariantOptions();
    
    // 3. Setup tabs
    setupTabs();
    
    // 4. Validate required DOM elements
    const requiredElements = [
        { id: 'product-price', name: 'Price container' },
        { id: 'quantity', name: 'Quantity input' },
        { id: 'stock-quantity', name: 'Stock display' }
    ];
    
    let allElementsPresent = true;
    requiredElements.forEach(element => {
        const el = document.getElementById(element.id);
        if (!el) {
            console.error(`❌ Required element #${element.id} (${element.name}) not found`);
            allElementsPresent = false;
        } else {
            console.log(`✅ ${element.name} found`);
        }
    });
    
    if (!allElementsPresent) {
        console.warn('⚠️ Some required elements are missing. Some features may not work.');
    }
    
    // 5. Log initialization complete
    console.log('====================================');
    console.log('✅ Product Detail Initialized Successfully');
    console.log('📦 Current variant:', currentVariant ? currentVariant.sku : 'None');
    console.log('🎨 Selected attributes:', selectedAttributes);
    console.log('====================================');
});

