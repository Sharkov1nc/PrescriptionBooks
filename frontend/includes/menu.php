<div class="col-2 left-menu">
    <div class="list-group">
        <div class="row avatar-container">
            <div class="col-4 avatar">
                <img src="../images/avatar.jpg" width="100%">
            </div>
            <div class="col-8 description">
                <?= $authentication->user->fname . " " . $authentication->user->lname ?>
                <span class="mail"><?= $authentication->user->email ?></span>
            </div>
        </div>
        <a href="index.php" class="list-group-item list-group-item-action">
            <i class="s7-home"></i>
            <span class="item-menu-title">Начало</span>
        </a>
        <?php if ($authentication->user->user_position == 1) { ?>
            <a href="users.php" class="list-group-item list-group-item-action">
                <i class="s7-users"></i>
                <span class="item-menu-title">Потребители</span>
            </a>
            <a href="drugs.php" class="list-group-item list-group-item-action">
                <i class="s7-eyedropper"></i>
                <span class="item-menu-title">Лекарства</span>
            </a>
        <?php } ?>
        <?php if ($authentication->user->user_position == 2) { ?>
            <a href="prescriptions_for_written.php" class="list-group-item list-group-item-action">
                <i class="s7-bookmarks"></i>
                <span class="item-menu-title">Рецепти за изписване</span>
            </a>
            <a href="patients.php" class="list-group-item list-group-item-action">
                <i class="s7-users"></i>
                <span class="item-menu-title">Пациенти</span>
            </a>
        <?php } ?>
        <?php if ($authentication->user->user_position == 2 || $authentication->user->user_position == 4) { ?>
            <a href="written_prescriptions.php" class="list-group-item list-group-item-action">
                <i class="s7-note2"></i>
                <span class="item-menu-title">Изписани рецепти</span>
            </a>
        <?php } ?>
        <a href="../backend/authentication_controller.php?logout=true" class="list-group-item list-group-item-action">
            <i class="s7-power"></i>
            <span class="item-menu-title">Изход</span>
        </a>
    </div>
</div>