<?php $pageTitle = "Начало"; ?>
<!doctype html>
<html lang="en">
    <head>
        <?php include_once  'includes/libs.php'; ?>
    </head>
    <body>
        <div class="container-fluid">
            <?php include_once  'includes/header.php'?>
            <div class="row">
                <?php include_once 'includes/menu.php' ?>
                <div class="col-10 page-content">
                    <div class="row">
                        <div class="col-12 main-content">
                            <p class="welcome"> Добре дошли, <?= $authentication->user->fname . " " . $authentication->user->lname ?> </p>
                            <div class=" icon-menu-container">
                                <?php if ($authentication->user->user_position == 1) { ?>
                                    <a href="users.php" class="text-center box">
                                        <i class="s7-users"></i>
                                        <p>Потребители</p>
                                    </a>
                                    <a href="drugs.php" class="text-center box">
                                        <i class="s7-eyedropper"></i>
                                        <p>Лекарства</p>
                                    </a>
                                <?php } ?>
                                <?php if ($authentication->user->user_position == 2) { ?>
                                    <a href="prescriptions_for_written.php" class="text-center box">
                                        <i class="s7-bookmarks"></i>
                                        <p>Рецепти за изписване</p>
                                    </a>
                                    <a href="patients.php" class="text-center box">
                                        <i class="s7-users"></i>
                                        <p>Пациенти</p>
                                    </a>
                                <?php } ?>
                                <?php if ($authentication->user->user_position == 2) { ?>
                                    <a href="written_prescriptions.php" class="text-center box">
                                        <i class="s7-note2"></i>
                                        <p>Изписани рецепти</p>
                                    </a>
                                <?php } ?>
                                <?php if($authentication->user->user_position == 4) {?>
                                    <a href="pharmacy.php" class="text-center box">
                                        <i class="s7-search"></i>
                                        <p>Търсене на рецепти</p>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once 'includes/footer.php'?>
        </div>
    </body>
</html>