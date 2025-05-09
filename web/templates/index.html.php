<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/pico.min.css">
  <link rel="stylesheet" href="/css/fontawesome.min.css">
  <link rel="stylesheet" href="/css/prism.css">
  <link rel="stylesheet" href="/css/opentrashmail.css">
  <title>Private Trash Mail</title>
</head>

<body>
  <div class="topnav" id="OTMTopnav">
    <a href="/"><img src="/imgs/logo-50.png" width="50px" /> Private Trash Mail <small class="version"><?=getVersion()?></small></a>
    <a>
      <input id="email_name" name="email_name" type="text" style="margin-bottom:0px;width:140px;" placeholder="email name" aria-label="email name">
      <span>@</span>
      <select id="email_domain" name="email_domain" style="margin-bottom:0px;">
        <?php foreach($domains as $dom): ?>
          <option value="<?=trim($dom)?>"><?=trim($dom)?></option>
        <?php endforeach; ?>
      </select>
      <button id="getmail" style="margin-left:5px;">Get mail</button>
    </a>
    <a href="/random" hx-get="/api/random" hx-target="#main"><i class="fas fa-random"></i> Generate random</a>
    <?php if($this->settings['ADMIN_ENABLED']==true):?><a href="/admin" hx-get="/api/admin" hx-target="#main" hx-push-url="/admin"><i class="fas fa-user-shield"></i> Admin</a><?php endif; ?>
    <a href="/api-guide" hx-get="/api/api-guide" hx-target="#main" hx-push-url="/api-guide"><i class="fas fa-book"></i> API Guide</a>
    <a href="javascript:void(0);" class="icon" onclick="navbarmanager()">
      <i class="fa fa-bars"></i>
    </a>
  </div>

  <button class="htmx-indicator" aria-busy="true">Loading…</button>

  <main id="main" class="container" hx-get="/api/<?= $url ?>" hx-trigger="load">

  </main>

  <script src="/js/opentrashmail.js"></script>
  <script src="/js/htmx.min.js"></script>
  <script src="/js/moment-with-locales.min.js"></script>
  <script>
    // Tự động ghép email và gửi khi ấn nút Get mail
    document.getElementById('getmail').onclick = function(e) {
      e.preventDefault();
      var name = document.getElementById('email_name').value.trim();
      var domain = document.getElementById('email_domain').value.trim();
      if(!name) { alert('Please enter email name'); return; }
      var email = name + '@' + domain;
      // Gửi qua htmx như input cũ
      htmx.ajax('POST', '/api/address', {target:'#main', values:{email:email}});
    };
    // Enter trong input cũng trigger nút
    document.getElementById('email_name').addEventListener('keydown', function(e){
      if(e.key==='Enter') document.getElementById('getmail').click();
    });
  </script>
</body>

</html>