<div class="logs-container">
    <div class="logs-header">
        <h2>
            <span class="material-symbols-outlined">receipt_long</span>
            Nhật ký hệ thống
        </h2>
    </div>
    
    <div class="logs-filter">
        <a href="#" hx-push-url="/logs/10" hx-get="/api/logs/10" <?= $lines==10?'class="active"':'' ?> hx-target="#adminmain" role="button">
            <span class="material-symbols-outlined">filter_list</span> 10 dòng
        </a>
        <a href="#" hx-push-url="/logs/50" hx-get="/api/logs/50" <?= $lines==50?'class="active"':'' ?> hx-target="#adminmain" role="button">
            <span class="material-symbols-outlined">filter_list</span> 50 dòng
        </a>
        <a href="#" hx-push-url="/logs/100" hx-get="/api/logs/100" <?= $lines==100?'class="active"':'' ?> hx-target="#adminmain" role="button">
            <span class="material-symbols-outlined">filter_list</span> 100 dòng
        </a>
        <a href="#" hx-push-url="/logs/200" hx-get="/api/logs/200" <?= $lines==200?'class="active"':'' ?> hx-target="#adminmain" role="button">
            <span class="material-symbols-outlined">filter_list</span> 200 dòng
        </a>
        <a href="#" hx-push-url="/logs/500" hx-get="/api/logs/500" <?= $lines==500?'class="active"':'' ?> hx-target="#adminmain" role="button">
            <span class="material-symbols-outlined">filter_list</span> 500 dòng
        </a>
    </div>

    <div class="log-section">
        <h3>
            <span class="material-symbols-outlined">mail</span>
            Nhật ký máy chủ mail
        </h3>
        <div class="log-content">
            <pre><code class="language-log"><?= file_exists($mailserverlogfile)?tailShell($mailserverlogfile, $lines):'- Không tìm thấy file nhật ký máy chủ mail -' ?></code></pre>
        </div>
    </div>

    <div class="log-section">
        <h3>
            <span class="material-symbols-outlined">error</span>
            Nhật ký lỗi máy chủ web
        </h3>
        <div class="log-content">
            <pre><code class="language-log"><?= file_exists($webservererrorlogfile)?tailShell($webservererrorlogfile, $lines):'- Không tìm thấy file nhật ký lỗi máy chủ web -' ?></code></pre>
        </div>
    </div>

    <div class="log-section">
        <h3>
            <span class="material-symbols-outlined">login</span>
            Nhật ký truy cập máy chủ web
        </h3>
        <div class="log-content">
            <pre><code class="language-log"><?= file_exists($webserveraccesslogfile)?tailShell($webserveraccesslogfile, $lines):'- Không tìm thấy file nhật ký truy cập máy chủ web -' ?></code></pre>
        </div>
    </div>

    <div class="log-section">
        <h3>
            <span class="material-symbols-outlined">settings</span>
            Cấu hình hiện tại
        </h3>
        <div class="log-content">
            <pre><code class="language-ini"><?= file_exists($configfile)?file_get_contents($configfile):'- Không tìm thấy file cấu hình -' ?></code></pre>
        </div>
    </div>
</div>

<script src="/js/prism.js"></script>