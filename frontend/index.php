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
                                <?php if ($authentication->user->position == 1) { ?>
                                    <a href="users.php" class="text-center box">
                                        <i class="s7-users"></i>
                                        <p>Потребители</p>
                                    </a>
                                    <a href="inquiries.php" class="text-center box">
                                        <i class="s7-mail-open"></i>
                                        <p>Запитвания</p>
                                    </a>
                                <?php } ?>
                                <?php if ($authentication->user->position == 2) { ?>
                                    <a href="prescriptions_for_written.php" class="text-center box">
                                        <i class="s7-id"></i>
                                        <p>Рецепти за изписване</p>
                                    </a>
                                    <a href="patients.php" class="text-center box">
                                        <i class="s7-id"></i>
                                        <p>Пациенти</p>
                                    </a>
                                <?php } ?>
                                <?php if ($authentication->user->position == 2 || $authentication->user->position == 3) { ?>
                                    <a href="written_prescriptions.php" class="text-center box">
                                        <i class="s7-id"></i>
                                        <p>Изписани рецепти</p>
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