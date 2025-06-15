<div class="admin-container">
    <div class="admin-header">
        <h1>
            <span class="material-symbols-outlined">admin_panel_settings</span>
            Quản trị
        </h1>
    </div>

    <?php
        if($_REQUEST['password'] && $_REQUEST['password'] == $settings['ADMIN_PASSWORD'])
            $_SESSION['admin'] = true;
        else if($_REQUEST['password'] && $_REQUEST['password'] != $settings['ADMIN_PASSWORD'])
            echo '<div class="alert alert-danger"><span class="material-symbols-outlined">error</span> Sai mật khẩu</div>';
    ?>

    <?php if($settings['ADMIN_PASSWORD'] != "" && !$_SESSION['admin']): ?>
        <div class="admin-login-form">
            <form method="post" hx-post="/api/admin" hx-target="#main-content">
                <div class="form-group">
                    <label for="admin-password">
                        <span class="material-symbols-outlined">password</span>
                        Mật khẩu quản trị
                    </label>
                    <input type="password" name="password" id="admin-password" placeholder="Nhập mật khẩu" required />
                </div>
                <button type="submit">
                    <span class="material-symbols-outlined">login</span>
                    Đăng nhập
                </button>
            </form>
        </div>
    <?php return; endif; ?>

    <div class="admin-nav">
        <nav>
            <ul>
                <?php if($settings['SHOW_ACCOUNT_LIST']): ?>
                <li>
                    <a href="/listaccounts" hx-get="/api/listaccounts" hx-target="#adminmain" hx-push-url="/listaccounts">
                        <span class="material-symbols-outlined">list</span>
                        Danh sách tài khoản
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if($settings['SHOW_LOGS']==true): ?>
                <li>
                    <a href="/logs" hx-get="/api/logs" hx-target="#adminmain" hx-push-url="/logs">
                        <span class="material-symbols-outlined">receipt_long</span>
                        Xem nhật ký
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <div id="adminmain" class="admin-content"></div>
</div>