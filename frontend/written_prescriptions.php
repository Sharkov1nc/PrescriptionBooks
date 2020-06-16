<?php
include_once "../backend/PrescriptionBooks.php";
$pageTitle = "Изписани рецепти";
?>
<!doctype html>
<html lang="en">
<head>
    <?php include_once  'includes/libs.php'; ?>
    <script src="../js/written_prescriptions.php.js"></script>
</head>
<body>
<div class="container-fluid">
    <?php include_once  'includes/header.php'?>
    <div class="row">
        <?php include_once 'includes/menu.php' ?>
        <div class="col-10 page-content">
            <div class="row">
                <div class="col-12 main-content">
                    <div class="prescription-for-written-content">
                        <div class=" row icon-menu-container">
                            <a href="prescriptions_for_written.php" class="text-center box">
                                <i class="s7-bookmarks"></i>
                                <p>Рецепти за изписване</p>
                            </a>
                            <a data-toggle="modal" data-target="#search-modal" class="text-center box">
                                <i class="s7-search"></i>
                                <p>Търсене на изписани рецепти</p>
                            </a>
                        </div>
                        <div class="row table-container">
                            <div class="col-12">
                                <table class="table" id="users-table">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Име и фамилия</th>
                                        <th>Последна рецепта</th>
                                        <th>Дата на последна рецепта</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach (PrescriptionBooks::getInstance()->getWrittenRecipe() as $key => $pb) { ?>
                                        <tr id="recipe-row-<?= $pb['recipe_id'] ?>">
                                            <td><?= $key+1 ?></td>
                                            <td><?= $pb['fname'] . ' ' . $pb['lname'] ?></td>
                                            <td>
                                                <span class="badge badge-success">Преглед на рецепта</span>
                                            </td>
                                            <td><?= $pb['recipe_date'] ?></td>
                                            <td>
                                                <a id="<?= $pb['recipe_id'] ?>" class="btn icon-button edit-recipe"> <i class="s7-edit"></i></a>
                                                <a id="<?= $pb['recipe_id'] ?>" class="btn icon-button delete-recipe"> <i class="s7-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'includes/footer.php'?>
</div>
</body>
</html>