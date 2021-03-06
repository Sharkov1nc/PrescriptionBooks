<?php
include_once '../backend/PrescriptionBooks.php';
include_once '../backend/Drugs.php';
$pageTitle = "Рецепти за изписване";
?>
<!doctype html>
<html lang="en">
<head>
    <?php include_once  'includes/libs.php'; ?>
    <script src="../js/prescriptions_for_written.js"></script>
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
                            <a href="written_prescriptions.php" class="text-center box">
                                <i class="s7-note2"></i>
                                <p>Изписани рецепти</p>
                            </a>
                            <a data-toggle="modal" data-target="#search-modal" class="text-center box">
                                <i class="s7-search"></i>
                                <p>Търсене на рецепти за изписване</p>
                            </a>
                        </div>
                        <div class="row table-container">
                            <div class="col-12">
                                <table class="table" id="prescriptions-table">
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
                                    <?php foreach (PrescriptionBooks::getInstance()->getRecipeForWritten() as $key => $pb) { ?>
                                    <tr id="prescrition-row-<?= $pb['id'] ?>">
                                        <td><?= $key+1 ?></td>
                                        <td><?= $pb['user_fname'] . ' ' . $pb['user_lname'] ?></td>
                                        <td> <?php if(!$pb['recipe_id']) { ?>
                                                <span class="badge badge-info">Няма изписани рецепти</span>
                                            <? } else { ?>
                                                <a href="../backend/prescriptions_controller.php?action=print&previous=true&recipe_id=<?= $pb['recipe_id'] ?>" target="_blank"><span class="badge badge-success">Преглед на рецепта</span></a>
                                        <?php } ?>
                                        </td>
                                        <td><?= $pb['recipe_date'] ? $pb['recipe_date'] : '-'?></td>
                                        <td>
                                            <a id="<?= $pb['id'] ?>" data-user="<?= $pb['user_fname'] . ' ' . $pb['user_lname'] ?>" class="btn icon-button add-prescription" data-toggle="modal" data-target="#add-prescription-modal"> <i class="s7-note"></i></a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="add-prescription-modal" role="dialog" class="modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Добавяне на рецепта</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times</button>
                                </div>
                                <div class="modal-body">
                                    <form action="../backend/prescriptions_controller.php" id="add-prescription-form" method="post">
                                        <div class="form-group">
                                            <label>Пациент:</label>
                                            <input id="patient-name" type="text" class="form-control"  readonly>
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
                                        <input type="hidden" name="patient_id">
                                        <div class="form-group row">
                                            <div class="col-4">
                                                <button id="preview-recipe" type="button" class="btn btn-success btn-block">Прегледай рецепта</button>
                                            </div>
                                            <div class="col-8">
                                                <button type="submit" class="btn btn-danger btn-block">Добави рецепта</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal" id="search-modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Търсене на рецепти за изписване</h4>
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
                                        <input type="hidden" name="action" value="search">
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