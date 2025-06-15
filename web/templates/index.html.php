<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="/css/pico.min.css">
  <link rel="stylesheet" href="/css/prism.css">
  <link rel="stylesheet" href="/css/opentrashmail-dark.css">
  <title>Private Trash Mail</title>
</head>

<body class="dark-theme">
  <!-- Header với glass morphism effect cải tiến -->
  <header class="app-header">
    <div class="header-left">
      <button id="sidebar-toggle" class="icon-button" aria-label="Toggle Sidebar">
        <span class="material-symbols-outlined">menu</span>
      </button>
      <a href="/" class="logo-container">
        <img src="/imgs/logo-50.png" width="40px" alt="Private Trash Mail Logo" />
        <span class="app-title">Private Trash Mail</span>
        <small class="version"><?=getVersion()?></small>
      </a>
    </div>
    
    <div class="header-center">
      <div class="search-container">
        <span class="material-symbols-outlined search-icon">search</span>
        <input type="text" id="search-mail" placeholder="Tìm kiếm email..." aria-label="Search emails">
      </div>
    </div>
    
    <div class="header-right">
      <button id="refresh-btn" class="icon-button" aria-label="Refresh">
        <span class="material-symbols-outlined">refresh</span>
      </button>
      <div class="email-input-container">
        <input id="email_name" name="email_name" type="text" placeholder="email name" aria-label="email name">
        <span>@</span>
        <select id="email_domain" name="email_domain">
          <?php foreach($domains as $dom): ?>
            <option value="<?=trim($dom)?>"><?=trim($dom)?></option>
          <?php endforeach; ?>
        </select>
        <button id="getmail" class="primary">Get mail</button>
      </div>
    </div>
  </header>

  <!-- Main container with sidebar and content -->
  <div class="app-container">
    <!-- Sidebar với hiệu ứng glass morphism -->
    <aside class="app-sidebar" id="app-sidebar">
      <div class="sidebar-content">
        <div class="sidebar-section">
          <button id="compose-btn" class="compose-btn">
            <span class="material-symbols-outlined">add</span>
            <span>Tạo email ngẫu nhiên</span>
          </button>
        </div>
        
        <nav class="sidebar-nav">
          <ul>
            <li>
              <a href="/" class="sidebar-item active" hx-get="/api/" hx-target="#main-content">
                <span class="material-symbols-outlined">inbox</span>
                <span>Inbox</span>
                <span class="badge unread-count">0</span>
              </a>
            </li>
            <li>
              <a href="#" class="sidebar-item" hx-get="/api/sent" hx-target="#main-content">
                <span class="material-symbols-outlined">send</span>
                <span>Sent</span>
              </a>
            </li>
            <li>
              <a href="#" class="sidebar-item" hx-get="/api/trash" hx-target="#main-content">
                <span class="material-symbols-outlined">delete</span>
                <span>Trash</span>
              </a>
            </li>
            <li>
              <a href="#" class="sidebar-item" hx-get="/api/settings" hx-target="#main-content">
                <span class="material-symbols-outlined">settings</span>
                <span>Settings</span>
              </a>
            </li>
          </ul>
        </nav>
        
        <div class="sidebar-section sidebar-footer">
          <?php if($this->settings['ADMIN_ENABLED']==true):?>
          <a href="/admin" class="sidebar-item" hx-get="/api/admin" hx-target="#main-content" hx-push-url="/admin">
            <span class="material-symbols-outlined">admin_panel_settings</span>
            <span>Admin</span>
          </a>
          <?php endif; ?>
          <a href="/api-guide" class="sidebar-item" hx-get="/api/api-guide" hx-target="#main-content" hx-push-url="/api-guide">
            <span class="material-symbols-outlined">menu_book</span>
            <span>API Guide</span>
          </a>
        </div>
      </div>
    </aside>

    <!-- Main content với hiệu ứng loading -->
    <main id="main-content" class="main-content" hx-get="/api/<?= $url ?>" hx-trigger="load">
      <!-- Content will be loaded here -->
    </main>
  </div>

  <!-- Footer cải tiến -->
  <footer class="app-footer">
    <div class="footer-content">
      <div class="footer-links">
        <a href="https://github.com/x1-2023/PrivateTrashMail" target="_blank">
          <span class="material-symbols-outlined">code</span>
          <span>GitHub</span>
        </a>
      </div>
      <div class="footer-copyright">
        &copy; <?= date('Y') ?> Private Trash Mail
      </div>
    </div>
  </footer>

  <!-- Loading indicator cải tiến -->
  <div class="htmx-indicator" aria-busy="true">
    <div class="loading-spinner"></div>
    <span>Loading...</span>
  </div>

  <!-- Scripts -->
  <script src="/js/htmx.min.js"></script>
  <script src="/js/moment-with-locales.min.js"></script>
  <script src="/js/opentrashmail.js"></script>
</body>
</html>