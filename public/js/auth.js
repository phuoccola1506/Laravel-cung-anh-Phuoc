// ============================================================================
// AUTH.JS - AUTHENTICATION SYSTEM
// ============================================================================
// Xử lý đăng nhập, đăng ký, đăng xuất với AJAX
// ============================================================================

// ============================================================================
// GLOBAL VARIABLES
// ============================================================================
let isProcessing = false; // Prevent double submission

// ============================================================================
// CSRF TOKEN SETUP
// ============================================================================
function setupCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.csrfToken = token.getAttribute('content');
        console.log('✅ CSRF token loaded');
    } else {
        console.error('❌ CSRF token not found');
    }
}

// ============================================================================
// LOGIN FUNCTION
// ============================================================================
async function login(event) {
    if (event) event.preventDefault();
    
    if (isProcessing) {
        showNotification('Đang xử lý, vui lòng đợi...', 'warning');
        return;
    }
    
    console.log('🔐 Starting login...');
    
    // Get form elements
    const emailInput = document.getElementById('email') || document.querySelector('input[name="email"]');
    const passwordInput = document.getElementById('password') || document.querySelector('input[name="password"]');
    const rememberCheckbox = document.getElementById('remember') || document.querySelector('input[name="remember"]');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    if (!emailInput || !passwordInput) {
        console.error('❌ Email or password input not found');
        showNotification('Lỗi: Không tìm thấy form đăng nhập!', 'error');
        return;
    }
    
    // Get values
    const email = emailInput.value.trim();
    const password = passwordInput.value;
    const remember = rememberCheckbox ? rememberCheckbox.checked : false;
    
    // Validate
    if (!email) {
        showNotification('Vui lòng nhập email!', 'error');
        emailInput.focus();
        return;
    }
    
    if (!validateEmail(email)) {
        showNotification('Email không đúng định dạng!', 'error');
        emailInput.focus();
        return;
    }
    
    if (!password) {
        showNotification('Vui lòng nhập mật khẩu!', 'error');
        passwordInput.focus();
        return;
    }
    
    if (password.length < 6) {
        showNotification('Mật khẩu phải có ít nhất 6 ký tự!', 'error');
        passwordInput.focus();
        return;
    }
    
    try {
        isProcessing = true;
        
        // Disable button and show loading
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng nhập...';
        }
        
        // Clear previous errors
        clearErrors();
        
        console.log('📤 Sending login request...');
        
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                password: password,
                remember: remember
            })
        });
        
        const data = await response.json();
        console.log('📥 Login response:', data);
        
        if (data.success) {
            showNotification(data.message || 'Đăng nhập thành công!', 'success');
            
            // Redirect after 500ms
            setTimeout(() => {
                window.location.href = data.redirect || '/';
            }, 500);
        } else {
            showNotification(data.message || 'Đăng nhập thất bại!', 'error');
            
            // Show field errors if any
            if (data.errors) {
                showFieldErrors(data.errors);
            }
            
            // Reset button
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Đăng nhập';
            }
            
            isProcessing = false;
        }
        
    } catch (error) {
        console.error('❌ Login error:', error);
        showNotification('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
        
        // Reset button
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Đăng nhập';
        }
        
        isProcessing = false;
    }
}

// ============================================================================
// REGISTER FUNCTION
// ============================================================================
async function register(event) {
    if (event) event.preventDefault();
    
    if (isProcessing) {
        showNotification('Đang xử lý, vui lòng đợi...', 'warning');
        return;
    }
    
    console.log('📝 Starting registration...');
    
    // Get form elements
    const nameInput = document.getElementById('name') || document.querySelector('input[name="name"]');
    const emailInput = document.getElementById('email') || document.querySelector('input[name="email"]');
    const passwordInput = document.getElementById('password') || document.querySelector('input[name="password"]');
    const passwordConfirmInput = document.getElementById('password_confirmation') || document.querySelector('input[name="password_confirmation"]');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    if (!nameInput || !emailInput || !passwordInput || !passwordConfirmInput) {
        console.error('❌ Form inputs not found');
        showNotification('Lỗi: Không tìm thấy form đăng ký!', 'error');
        return;
    }
    
    // Get values
    const name = nameInput.value.trim();
    const email = emailInput.value.trim();
    const password = passwordInput.value;
    const passwordConfirm = passwordConfirmInput.value;
    
    // Validate
    if (!name) {
        showNotification('Vui lòng nhập họ tên!', 'error');
        nameInput.focus();
        return;
    }
    
    if (name.length < 2) {
        showNotification('Họ tên phải có ít nhất 2 ký tự!', 'error');
        nameInput.focus();
        return;
    }
    
    if (!email) {
        showNotification('Vui lòng nhập email!', 'error');
        emailInput.focus();
        return;
    }
    
    if (!validateEmail(email)) {
        showNotification('Email không đúng định dạng!', 'error');
        emailInput.focus();
        return;
    }
    
    if (!password) {
        showNotification('Vui lòng nhập mật khẩu!', 'error');
        passwordInput.focus();
        return;
    }
    
    if (password.length < 6) {
        showNotification('Mật khẩu phải có ít nhất 6 ký tự!', 'error');
        passwordInput.focus();
        return;
    }
    
    if (!passwordConfirm) {
        showNotification('Vui lòng xác nhận mật khẩu!', 'error');
        passwordConfirmInput.focus();
        return;
    }
    
    if (password !== passwordConfirm) {
        showNotification('Xác nhận mật khẩu không khớp!', 'error');
        passwordConfirmInput.focus();
        return;
    }
    
    try {
        isProcessing = true;
        
        // Disable button and show loading
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng ký...';
        }
        
        // Clear previous errors
        clearErrors();
        
        console.log('📤 Sending registration request...');
        
        const response = await fetch('/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: name,
                email: email,
                password: password,
                password_confirmation: passwordConfirm
            })
        });
        
        const data = await response.json();
        console.log('📥 Register response:', data);
        
        if (data.success) {
            showNotification(data.message || 'Đăng ký thành công!', 'success');
            
            // Redirect after 500ms
            setTimeout(() => {
                window.location.href = data.redirect || '/';
            }, 500);
        } else {
            showNotification(data.message || 'Đăng ký thất bại!', 'error');
            
            // Show field errors if any
            if (data.errors) {
                showFieldErrors(data.errors);
            }
            
            // Reset button
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Đăng ký';
            }
            
            isProcessing = false;
        }
        
    } catch (error) {
        console.error('❌ Registration error:', error);
        showNotification('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
        
        // Reset button
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Đăng ký';
        }
        
        isProcessing = false;
    }
}

// ============================================================================
// LOGOUT FUNCTION
// ============================================================================
async function logout() {
    if (isProcessing) return;
    
    if (!confirm('Bạn có chắc muốn đăng xuất?')) {
        return;
    }
    
    console.log('🚪 Logging out...');
    
    try {
        isProcessing = true;
        
        const response = await fetch('/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message || 'Đăng xuất thành công!', 'success');
            
            setTimeout(() => {
                window.location.href = data.redirect || '/';
            }, 500);
        } else {
            showNotification('Có lỗi xảy ra khi đăng xuất!', 'error');
            isProcessing = false;
        }
        
    } catch (error) {
        console.error('❌ Logout error:', error);
        showNotification('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
        isProcessing = false;
    }
}

// ============================================================================
// VALIDATION HELPERS
// ============================================================================
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function clearErrors() {
    // Remove error messages
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    
    // Remove error classes
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function showFieldErrors(errors) {
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.querySelector(`input[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');
            
            // Create error message element
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-danger small mt-1';
            errorDiv.textContent = Array.isArray(messages) ? messages[0] : messages;
            
            // Insert after input
            input.parentElement.appendChild(errorDiv);
        }
    }
}

// ============================================================================
// NOTIFICATION SYSTEM
// ============================================================================
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    let icon = 'info-circle';
    if (type === 'success') icon = 'check-circle';
    if (type === 'error') icon = 'exclamation-circle';
    if (type === 'warning') icon = 'exclamation-triangle';
    
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// ============================================================================
// TOGGLE PASSWORD VISIBILITY
// ============================================================================
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = event.target;
    
    if (!input) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// ============================================================================
// INITIALIZATION
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔐 Auth.js loaded');
    
    // Setup CSRF token
    setupCSRFToken();
    
    // Setup form submission
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (loginForm) {
        console.log('✅ Login form found');
        loginForm.addEventListener('submit', login);
    }
    
    if (registerForm) {
        console.log('✅ Register form found');
        registerForm.addEventListener('submit', register);
    }
    
    // Setup logout buttons
    document.querySelectorAll('.logout-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            logout();
        });
    });
    
    console.log('✅ Auth system initialized');
});
