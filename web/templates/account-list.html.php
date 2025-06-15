<div class="account-list-container">
    <div class="account-list-header">
        <h2>
            <span class="material-symbols-outlined">manage_accounts</span>
            Danh sách tài khoản
        </h2>
        
        <div class="account-list-actions">
            <a role="button" class="outline" href="/json/listaccounts" target="_blank">
                <span class="material-symbols-outlined">data_object</span> JSON API
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th scope="col">
                        <span class="material-symbols-outlined">alternate_email</span>
                        Địa chỉ Email
                    </th>
                    <th>
                        <span class="material-symbols-outlined">mail</span>
                        Email trong hộp thư
                    </th>
                    <th>
                        <span class="material-symbols-outlined">settings</span>
                        Thao tác
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($emails as $email): ?>
                    <tr>
                        <td>
                            <a href="/address/<?= $email; ?>" hx-get="/api/address/<?= $email; ?>" hx-push-url="/address/<?= $email; ?>" hx-target="#main-content" class="email-address">
                                <span class="material-symbols-outlined">alternate_email</span>
                                <?= escape($email) ?>
                            </a>
                        </td>
                        <td>
                            <span class="email-count"><?= countEmailsOfAddress($email); ?></span>
                        </td>
                        <td class="account-actions">
                            <a href="/address/<?= $email; ?>" hx-get="/api/address/<?= $email; ?>" hx-push-url="/address/<?= $email; ?>" hx-target="#main-content" role="button" class="view-btn">
                                <span class="material-symbols-outlined">visibility</span>
                            </a>
                            <a href="#" role="button" class="delete-btn" hx-get="/api/deleteaccount/<?= $email ?>" hx-confirm="Bạn có chắc chắn muốn xóa tài khoản này và tất cả email của nó?" hx-target="closest tr" hx-swap="outerHTML swap:1s">
                                <span class="material-symbols-outlined">delete</span>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>