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
                            <a data-toggle="modal" data-target="#user-data-modal" class="text-center box add-user">
                                <i class="s7-add-user"></i>
                                <p>Добавяне на потребител</p>
                            </a>
                            <a data-toggle="modal" data-target="#search-modal" class="text-center box">
                                <i class="s7-search"></i>
                                <p>Търсене на потребител</p>
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
                                    <?php foreach (PrescriptionBooks::getInstance()->getWrittenPrescription() as $key => $pb) { ?>
                                        <tr>
                                            <th><?= $key+1 ?></th>
                                            <th><?= $pb['fname'] . ' ' . $pb['lname'] ?></th>
                                            <th> <?php if(!$pb['recipe_id']) { ?>
                                                <span class="badge badge-info">Няма изписани рецепти</span></th>
                                            <? } else { ?>
                                                <span class="badge badge-success">Преглед на рецепта</span></th>
                                            <?php } ?>
                                            <th><?= $pb['recipe_date'] ?></th>
                                            <th>
                                                <a id="<?= $pb['id'] ?>" class="btn icon-button edit-drug"> <i class="s7-edit"></i></a>
                                                <a id="<?= $pb['id'] ?>" class="btn icon-button remove-drug"> <i class="s7-trash"></i></a>
                                            </th>
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