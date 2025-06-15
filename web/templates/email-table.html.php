<div class="email-list-container">
    <div class="email-list-header">
        <div class="email-breadcrumb">
            <h2>
                <span class="material-symbols-outlined">mail</span>
                <?= escape($email) ?>
            </h2>
        </div>

        <div class="email-list-actions">
            <button role="button" class="outline" id="copyemailbtn" data-email="<?= escape($email) ?>" onclick="copyEmailToClipboard(this.dataset.email);return false;">
                <span class="material-symbols-outlined">content_copy</span> Sao chép địa chỉ
            </button>
            <a role="button" class="outline" href="/rss/<?= $email ?>" target="_blank">
                <span class="material-symbols-outlined">rss_feed</span> RSS Feed
            </a>
            <a role="button" class="outline" href="/json/<?= $email ?>" target="_blank">
                <span class="material-symbols-outlined">data_object</span> JSON API
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table role="grid">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">
                        <span class="material-symbols-outlined">schedule</span> Ngày
                    </th>
                    <th scope="col">
                        <span class="material-symbols-outlined">person</span> Người gửi
                    </th>
                    <?php if($isadmin==true): ?>
                    <th scope="col">
                        <span class="material-symbols-outlined">alternate_email</span> Đến
                    </th>
                    <?php endif; ?>
                    <th scope="col">
                        <span class="material-symbols-outlined">subject</span> Tiêu đề
                    </th>
                    <th scope="col">
                        <span class="material-symbols-outlined">settings</span> Thao tác
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($emails)==0): ?>
                <tr>
                    <td colspan="<?= $isadmin ? '6' : '5' ?>">
                        <div class="empty-state">
                            <span class="material-symbols-outlined">inbox</span>
                            <p>Chưa có email nào nhận được tại địa chỉ này</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach($emails as $unixtime => $ed): ?>
                    <tr>
                        <th scope="row"><?= ++$i; ?></th>
                        <td id="date-td-<?= $i ?>"><script>document.getElementById('date-td-<?= $i ?>').innerHTML = moment.unix(parseInt(<?=$unixtime?>/1000)).format('<?= $dateformat; ?>');</script></td>
                        <td><?= escape($ed['from']) ?></td>
                        <?php if($isadmin==true): ?><td><?= $ed['email'] ?></td><?php endif; ?>
                        <td><?= escape($ed['subject']) ?></td>
                        <td class="email-actions">
                            <?php if($isadmin==true): ?>
                                <a href="/read/<?= $ed['email'] ?>/<?= $ed['id'] ?>" hx-get="/api/read/<?= $ed['email'] ?>/<?= $ed['id'] ?>" hx-push-url="/read/<?= $ed['email'] ?>/<?= $ed['id'] ?>" hx-target="#main-content" role="button" class="view-btn">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>
                                <a href="#" hx-get="/api/delete/<?= $ed['email'] ?>/<?= $ed['id'] ?>" hx-confirm="Bạn có chắc chắn muốn xóa?" hx-target="closest tr" hx-swap="outerHTML swap:1s" role="button" class="delete-btn">
                                    <span class="material-symbols-outlined">delete</span>
                                </a>
                            <?php else: ?>
                                <a href="/read/<?= $email ?>/<?= $ed['id'] ?>" hx-get="/api/read/<?= $email ?>/<?= $ed['id'] ?>" hx-push-url="/read/<?= $email ?>/<?= $ed['id'] ?>" hx-target="#main-content" role="button" class="view-btn">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>
                                <a href="#" hx-get="/api/delete/<?= $email ?>/<?= $ed['id'] ?>" hx-confirm="Bạn có chắc chắn muốn xóa?" hx-target="closest tr" hx-swap="outerHTML swap:1s" role="button" class="delete-btn">
                                    <span class="material-symbols-outlined">delete</span>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>history.pushState({urlpath:"/address/<?= $email ?>"}, "", "/address/<?= $email ?>");</script>