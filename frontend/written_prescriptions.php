<?php
include_once "../backend/PrescriptionBooks.php";
include_once "../backend/Drugs.php";
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
                    <div class="written-prescriptions-content">
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
                                <table class="table" id="written-prescriptions-table">
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
                    <div class="modal" id="search-modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Търсене на изписани рецепти</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form class="form-inline" action="../backend/prescriptions_controller.php" method="get" id="prescription-search">
                                        <div class="form-group form-row w-100 m-0">
                                            <div class="col-9">
                                                <input type="text" class="form-control w-100" name="names" placeholder="Въведете име и фамилия">
                                            </div>
                                            <div class="col-3">
                                                <button class="btn btn-success w-100">Търси</button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="action" value="search_written">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="edit-prescription-modal" role="dialog" class="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Редактиране на рецепта</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times</button>
                                </div>
                                <div class="modal-body">
                                    <form action="../backend/prescriptions_controller.php" id="edit-prescription-form" method="post">
                                        <div class="form-group">
                                            <label>Пациент:</label>
                                            <input id="patient-name" type="text" class="form-control" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>Избрани лекарства:</label>
                                            <div id="selected-drugs"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Списък с лекарства:</label>
                                            <div class="drugs-select">
                                                <ul class="list-group list-group-flush">
                                                    <input type="text" class="form-control" id="live-search" placeholder="Търсене по име на лекарство">
                                                    <?php foreach (Drugs::getInstance()->getDrugs() as $drug){  ?>
                                                        <li class="list-group-item drugs-item" id="<?= $drug['id'] ?>"><?= $drug['name'] ?></li>
                                                    <?php   } ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Допълнителна информация:</label>
                                            <textarea class="form-control" name="additional_information" rows="3"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success btn-block">Запази промените</button>
                                        </div>
                                    </form>
                                </div>
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