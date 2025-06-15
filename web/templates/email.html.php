<div class="email-view-header">
  <div class="email-breadcrumb">
    <a href="/address/<?= $email ?>" hx-get="/api/address/<?= $email ?>" hx-target="#main-content">
      <span class="material-symbols-outlined">arrow_back</span>
      <?= escape($email) ?>
    </a>
  </div>
  
  <div class="email-actions">
    <button role="button" class="outline" onclick="window.history.back()">
      <span class="material-symbols-outlined">arrow_back</span> Quay lại
    </button>
    <a role="button" class="outline delete-btn" href="#" hx-get="/api/delete/<?= $email ?>/<?= $mailid ?>" hx-confirm="Bạn có chắc chắn muốn xóa?" hx-target="#main-content" hx-swap="innerHTML" hx-push-url="/address/<?= $email ?>">
      <span class="material-symbols-outlined">delete</span> Xóa
    </a>
  </div>
</div>

<article class="email-detail">
    <header>
        <h2><?= escape($emaildata['parsed']['subject']) ?></h2>
        
        <div class="email-meta">
            <div class="meta-item">
                <span class="material-symbols-outlined">schedule</span>
                <span id="date2-<?= $mailid ?>">
                    <script>document.getElementById('date2-<?= $mailid ?>').innerHTML = moment.unix(parseInt(<?=$mailid?>/1000)).format('<?= $dateformat; ?>');</script>
                </span>
            </div>
            
            <div class="meta-item">
                <span class="material-symbols-outlined">person</span>
                <span><?= escape($emaildata['parsed']['from']) ?></span>
            </div>
            
            <div class="meta-item">
                <span class="material-symbols-outlined">alternate_email</span>
                <span>
                    <?php foreach ($emaildata['rcpts'] as $to) : ?>
                        <span class="badge"><?= escape($to) ?></span>
                    <?php endforeach; ?>
                </span>
            </div>
        </div>
    </header>
    
    <div id="emailbody" class="email-body">
        <?php if($emaildata['parsed']['htmlbody']): ?>
            <div class="html-view-option">
                <a href="#" hx-confirm="Cảnh báo: HTML có thể chứa chức năng theo dõi hoặc script. Bạn có muốn tiếp tục?" hx-get="/api/raw-html/<?= $email ?>/<?= $mailid ?>" hx-target="#emailbody" role="button" class="secondary outline">
                    <span class="material-symbols-outlined">html</span> Hiển thị dạng HTML
                </a>
            </div>
        <?php endif; ?>
        <div class="email-content">
            <pre><?= nl2br(escape($emaildata['parsed']['body'])) ?></pre>
        </div>
    </div>
    
    <footer>
        <h3>
            <span class="material-symbols-outlined">attachment</span>
            Tệp đính kèm
        </h3>
        <div class="attachments-list">
            <?php if (count($emaildata['parsed']['attachments']) == 0) : ?>
                <p class="no-attachments">Không có tệp đính kèm</p>
            <?php endif; ?>
            <ul>
                <?php foreach ($emaildata['parsed']['attachments'] as $attachment) : ?>
                    <li>
                        <a target="_blank" href="/api/attachment/<?= $email ?>/<?= $attachment ?>">
                            <span class="material-symbols-outlined">description</span>
                            <?= escape($attachment) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </footer>
</article>

<article class="raw-email">
    <header>
        <h3>
            <span class="material-symbols-outlined">code</span>
            Email gốc
        </h3>
    </header>
    <div class="raw-email-actions">
        <a href="/api/raw/<?= $email ?>/<?= $mailid ?>" target="_blank" role="button" class="outline">
            <span class="material-symbols-outlined">open_in_new</span> Mở trong cửa sổ mới
        </a>
    </div>
    <pre class="raw-email-content"><button hx-get="/api/raw/<?= $email ?>/<?= $mailid ?>" hx-swap="outerHTML">
        <span class="material-symbols-outlined">download</span> Tải email gốc
    </button></pre>
</article>

<!-- 
<script>history.pushState({email:"<?= $email ?>",id:"<?= $mailid ?>"}, "", "/read/<?= $email ?>/<?= $mailid ?>");</script> -->
