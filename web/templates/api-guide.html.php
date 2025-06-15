<div class="api-guide-container">
    <div class="api-guide-header">
        <h1>
            <span class="material-symbols-outlined">api</span>
            Hướng dẫn API
        </h1>
        <p>Trang này cung cấp tổng quan về các API endpoint có sẵn cho OpenTrashMail, bao gồm ví dụ sử dụng và giải thích.</p>
    </div>

    <div class="api-section">
        <h2>
            <span class="material-symbols-outlined">settings</span>
            Các Endpoint chung
        </h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Endpoint</th>
                        <th>Phương thức</th>
                        <th>Giải thích</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>/rss/[email-address]</code></td>
                        <td>GET</td>
                        <td>Trả về XML RSS cho các client RSS để hiển thị email.</td>
                    </tr>
                    <tr>
                        <td><code>/api/raw/[email-address]/[id]</code></td>
                        <td>GET</td>
                        <td>Trả về email gốc của địa chỉ. Kết quả có thể lớn đối với email có tệp đính kèm.</td>
                    </tr>
                    <tr>
                        <td><code>/api/attachment/[email-address]/[attachment-id]</code></td>
                        <td>GET</td>
                        <td>Trả về tệp đính kèm với mime type chính xác trong header.</td>
                    </tr>
                    <tr>
                        <td><code>/api/delete/[email-address]/[id]</code></td>
                        <td>DELETE</td>
                        <td>Xóa một email cụ thể và các tệp đính kèm của nó.</td>
                    </tr>
                    <tr>
                        <td><code>/api/deleteaccount/[email-address]</code></td>
                        <td>DELETE</td>
                        <td>Xóa tất cả tin nhắn và tệp đính kèm của tài khoản email này.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="api-section">
        <h2>
            <span class="material-symbols-outlined">data_object</span>
            JSON API
        </h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Endpoint</th>
                        <th>Phương thức</th>
                        <th>Giải thích</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>/json/[email-address]</code></td>
                        <td>GET</td>
                        <td>Trả về một mảng các email đã nhận với liên kết đến tệp đính kèm và nội dung dạng văn bản của email. Nếu nhập <code>ADMIN</code>, trả về tất cả email của tất cả tài khoản.</td>
                    </tr>
                    <tr>
                        <td><code>/json/[email-address]/[id]</code></td>
                        <td>GET</td>
                        <td>Trả về tất cả dữ liệu của email đã nhận, bao gồm nội dung gốc và HTML. Có thể lớn nếu có tệp đính kèm.</td>
                    </tr>
                    <tr>
                        <td><code>/json/listaccounts</code></td>
                        <td>GET</td>
                        <td>Nếu <code>SHOW_ACCOUNT_LIST</code> được bật, trả về một mảng tất cả địa chỉ email đã nhận ít nhất một email.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="api-section">
        <h2>
            <span class="material-symbols-outlined">code</span>
            Ví dụ: Lấy Email cho một Địa chỉ
        </h2>
        <div class="code-block">
            <pre><code class="language-bash">curl http://yourdomain.com/json/test@example.com</code></pre>
        </div>
    </div>

    <div class="api-section">
        <h2>
            <span class="material-symbols-outlined">code</span>
            Ví dụ: Lấy Email Gốc
        </h2>
        <div class="code-block">
            <pre><code class="language-bash">curl http://yourdomain.com/api/raw/test@example.com/1234567890</code></pre>
        </div>
    </div>

    <div class="api-section">
        <h2>
            <span class="material-symbols-outlined">code</span>
            Ví dụ: Xóa một Email
        </h2>
        <p>Để xóa một email, sử dụng phương thức <code>DELETE</code>:</p>
        <div class="code-block">
            <pre><code class="language-bash">curl -X DELETE http://yourdomain.com/api/delete/test@example.com/1234567890</code></pre>
        </div>
    </div>

    <div class="api-section">
        <h2>
            <span class="material-symbols-outlined">lock</span>
            Xác thực (nếu được bật)
        </h2>
        <p>Nếu mật khẩu được đặt trong cấu hình, bạn phải cung cấp nó qua biểu mẫu, biến POST/GET <code>password</code> hoặc header HTTP <code>PWD</code>:</p>
        <div class="code-block">
            <pre><code class="language-bash">curl -H "PWD: yourpassword" http://yourdomain.com/json/test@example.com</code></pre>
        </div>
    </div>

    <div class="api-section">
        <h2>
            <span class="material-symbols-outlined">webhook</span>
            Webhook
        </h2>
        <p>Nếu <code>WEBHOOK_URL</code> được đặt trong cấu hình, OpenTrashMail sẽ gửi một yêu cầu POST đến URL này với dữ liệu JSON của email làm nội dung. Điều này có thể được sử dụng để tích hợp với các dự án của riêng bạn.</p>
    </div>

    <div class="api-section">
        <p>Để biết thêm chi tiết, xem <a href="https://github.com/HaschekSolutions/opentrashmail#readme" target="_blank" class="github-link">
            <span class="material-symbols-outlined">description</span> README
        </a>.</p>
    </div>
</div>

<script src="/js/prism.js"></script>
<script>
// Lấy URL gốc
var apiBase = window.location.origin;
// Lấy email hiện tại nếu có trên trang (từ input email hoặc breadcrumb)
var email = '';
var emailInput = document.getElementById('email');
if(emailInput && emailInput.value) email = emailInput.value;
else {
  var bc = document.querySelector('nav[aria-label="breadcrumb"] li');
  if(bc) email = bc.textContent.trim();
}
if(!email) email = 'test@' + window.location.hostname;
// Thay thế các ví dụ
Array.from(document.querySelectorAll('code.language-bash')).forEach(function(el){
  el.innerHTML = el.innerHTML.replace(/yourdomain.com/g, apiBase.replace(/^https?:\/\//,''))
    .replace(/test@example.com/g, email);
});
</script>