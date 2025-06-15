
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenTrashMail - Đăng nhập</title>
    <link rel="stylesheet" href="/css/pico.min.css">
    <link rel="stylesheet" href="/css/opentrashmail.css">
    <link rel="stylesheet" href="/css/opentrashmail-dark.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="/imgs/logo-200.png" alt="OpenTrashMail Logo" class="login-logo">
                <h1>OpenTrashMail</h1>
            </div>
            
            <div class="login-form">
                <h2>
                    <span class="material-symbols-outlined">lock</span>
                    Đăng nhập
                </h2>
                
                <?php if($error): ?>
                <div class="alert alert-danger">
                    <span class="material-symbols-outlined">error</span>
                    <?= $error ?>
                </div>
                <?php endif; ?>
                
                <form action="/" method="POST">
                    <div class="form-group">
                        <label for="password">
                            <span class="material-symbols-outlined">password</span>
                            Mật khẩu:
                        </label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="login-btn">
                        <span class="material-symbols-outlined">login</span>
                        Đăng nhập
                    </button>
                </form>
            </div>
            
            <div class="login-footer">
                <a href="https://github.com/HaschekSolutions/opentrashmail" target="_blank">
                    <span class="material-symbols-outlined">code</span>
                    GitHub
                </a>
            </div>
        </div>
    </div>
</body>
</html>
