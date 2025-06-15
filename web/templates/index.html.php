<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
  <link rel="stylesheet" href="/css/pico.min.css">
  <link rel="stylesheet" href="/css/fontawesome.min.css">
  <link rel="stylesheet" href="/css/prism.css">
  <link rel="stylesheet" href="/css/opentrashmail.css">
  <title>Private Trash Mail</title>
</head>

<body class="dark-theme">
  <!-- Header với glass morphism effect -->
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
    <!-- Sidebar -->
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

    <!-- Main content -->
    <main id="main-content" class="main-content" hx-get="/api/<?= $url ?>" hx-trigger="load">
      <!-- Content will be loaded here -->
    </main>
  </div>

  <!-- Footer -->
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

  <!-- Loading indicator -->
  <div class="htmx-indicator" aria-busy="true">
    <div class="loading-spinner"></div>
    <span>Loading...</span>
  </div>

  <!-- Scripts -->
  <script src="/js/opentrashmail.js"></script>
  <script src="/js/htmx.min.js"></script>
  <script src="/js/moment-with-locales.min.js"></script>
  <script>
    // Biến môi trường cho domain
    const availableDomains = <?= json_encode($domains) ?>;
    
    // Tự động ghép email và gửi khi ấn nút Get mail
    document.getElementById('getmail').onclick = function(e) {
      e.preventDefault();
      var name = document.getElementById('email_name').value.trim();
      var domain = document.getElementById('email_domain').value.trim();
      if(!name) { alert('Please enter email name'); return; }
      var email = name + '@' + domain;
      // Gửi qua htmx như input cũ
      htmx.ajax('POST', '/api/address', {target:'#main-content', values:{email:email}});
    };
    
    // Enter trong input cũng trigger nút
    document.getElementById('email_name').addEventListener('keydown', function(e){
      if(e.key==='Enter') document.getElementById('getmail').click();
    });
    
    // Toggle sidebar trên mobile
    document.getElementById('sidebar-toggle').addEventListener('click', function() {
      document.getElementById('app-sidebar').classList.toggle('sidebar-open');
    });
    
    // Tạo email ngẫu nhiên
    document.getElementById('compose-btn').addEventListener('click', function() {
      htmx.ajax('GET', '/api/random', {target:'#main-content'});
    });
    
    // Refresh emails
    document.getElementById('refresh-btn').addEventListener('click', function() {
      const currentUrl = window.location.pathname;
      htmx.ajax('GET', '/api' + currentUrl, {target:'#main-content'});
    });
    
    // Debounce function for search
    function debounce(func, wait) {
      let timeout;
      return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
      };
    }
    
    // Search emails with debounce
    const searchInput = document.getElementById('search-mail');
    const performSearch = debounce(function(query) {
      // Implement search functionality
      console.log('Searching for:', query);
      // Add actual search implementation here
    }, 300);
    
    searchInput.addEventListener('input', function(e) {
      performSearch(e.target.value);
    });
    
    // Auto reload emails every 15 seconds
    let autoReloadInterval;
    function startAutoReload() {
      autoReloadInterval = setInterval(function() {
        const currentUrl = window.location.pathname;
        if (currentUrl.includes('/address/')) {
          htmx.ajax('GET', '/api' + currentUrl, {target:'#main-content'});
          console.log('Auto-reloaded emails');
        }
      }, 15000); // 15 seconds
    }
    
    // Start auto reload
    startAutoReload();
    
    // Update unread count (placeholder for actual implementation)
    function updateUnreadCount(count) {
      const unreadBadge = document.querySelector('.unread-count');
      if (unreadBadge) {
        unreadBadge.textContent = count;
        unreadBadge.style.display = count > 0 ? 'inline-flex' : 'none';
      }
    }
    
    // Example: Update with some dummy data
    setTimeout(() => updateUnreadCount(3), 1000);
  </script>
</body>
</html>