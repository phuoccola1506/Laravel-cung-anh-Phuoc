<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-main">
            <div class="footer-col">
                <h3>{{ setting('site_name', 'TechShop') }}</h3>
                <p>{{ setting('site_description', 'Hệ thống bán lẻ điện thoại, laptop, tablet, phụ kiện chính hãng mới nhất, giá tốt, dịch vụ khách hàng chuyên nghiệp.') }}</p>
                <div class="social-links">
                    @if(setting('social_facebook'))
                        <a class="text-decoration-none" href="{{ setting('social_facebook') }}" target="_blank"><i class="fab fa-facebook"></i></a>
                    @endif
                    @if(setting('social_youtube'))
                        <a class="text-decoration-none" href="{{ setting('social_youtube') }}" target="_blank"><i class="fab fa-youtube"></i></a>
                    @endif
                    @if(setting('social_instagram'))
                        <a class="text-decoration-none" href="{{ setting('social_instagram') }}" target="_blank"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if(setting('social_tiktok'))
                        <a class="text-decoration-none" href="{{ setting('social_tiktok') }}" target="_blank"><i class="fab fa-tiktok"></i></a>
                    @endif
                </div>
            </div>

            <div class="footer-col">
                <h4>Thông tin</h4>
                <ul>
                    <li><a class="text-decoration-none" href="#">Giới thiệu công ty</a></li>
                    <li><a class="text-decoration-none" href="#">Chính sách bảo mật</a></li>
                    <li><a class="text-decoration-none" href="#">Quy chế hoạt động</a></li>
                    <li><a class="text-decoration-none" href="#">Kiểm tra hóa đơn</a></li>
                    <li><a class="text-decoration-none" href="#">Tra cứu bảo hành</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Chăm sóc khách hàng</h4>
                <ul>
                    <li><a class="text-decoration-none" href="#">Hướng dẫn mua hàng</a></li>
                    <li><a class="text-decoration-none" href="#">Chính sách đổi trả</a></li>
                    <li><a class="text-decoration-none" href="#">Chính sách bảo hành</a></li>
                    <li><a class="text-decoration-none" href="#">Giao hàng & Thanh toán</a></li>
                    <li><a class="text-decoration-none" href="#">Câu hỏi thường gặp</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Liên hệ</h4>
                <ul class="contact-info">
                    <li><i class="fas fa-phone"></i> <strong>Hotline:</strong> {{ setting('contact_phone', '1800.1234') }}</li>
                    <li><i class="fas fa-envelope"></i> <strong>Email:</strong> {{ setting('contact_email', 'support@techshop.vn') }}</li>
                    <li><i class="fas fa-clock"></i> <strong>Giờ làm việc:</strong> {{ setting('contact_working_hours', '8:00 - 22:00') }}</li>
                    <li><i class="fas fa-map-marker-alt"></i> <strong>Địa chỉ:</strong> {{ setting('contact_address', '123 Nguyễn Văn Linh, Q.7, TP.HCM') }}</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ setting('site_name', 'TechShop') }}. All rights reserved. Designed with <i class="fas fa-heart"></i></p>
        </div>
    </div>
</footer>
