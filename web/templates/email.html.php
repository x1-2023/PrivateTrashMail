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

<script>history.pushState({urlpath:"/read/<?= $email ?>/<?= $mailid ?>"}, "", "/read/<?= $email ?>/<?= $mailid ?>");</script>
