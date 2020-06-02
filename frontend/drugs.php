<?php
include_once '../backend/Drugs.php';
$pageTitle = "Лекарства";
?>
<!doctype html>
<html lang="en">
<head>
    <?php include_once  'includes/libs.php'; ?>
    <script src="../js/drugs.js"></script>
</head>
<body>
<div class="container-fluid">
    <?php include_once  'includes/header.php'?>
    <div class="row">
        <?php include_once 'includes/menu.php' ?>
        <div class="col-10 page-content">
            <div class="row">
                <div class="col-12 main-content">
                    <div class="drugs-content">
                        <div class="row icon-menu-container">
                            <a data-toggle="modal" data-target="#add-drug-modal" class="text-center box">
                                <i class="s7-eyedropper"></i>
                                <p>Добави лекарство</p>
                            </a>
                            <a class="text-center box excel-import-button">
                                <i class="s7-server"></i>
                                <p>Импортирай лекарства от Excel</p>
                            </a>
                            <a data-toggle="modal" data-target="#search-modal" class="text-center box">
                                <i class="s7-search"></i>
                                <p>Търсене на лекарство</p>
                            </a>
                        </div>
                        <div class="row table-container">
                            <div class="col-12">
                                <table class="table" id="drugs-table">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Име на лекарство</th>
                                        <th scope="col">Добавено на</th>
                                        <th scope="col">Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (Drugs::getInstance()->getDrugs() as $key => $drug) { ?>
                                            <tr id="drug-<?= $drug['id'] ?>">
                                                <th><?= $key+1 ?></th>
                                                <th class="col-name"><?= $drug['name'] ?></th>
                                                <th><?= $drug['date'] ?></th>
                                                <th>
                                                    <a id="<?= $drug['id'] ?>" class="btn icon-button edit-drug"> <i class="s7-edit"></i></a>
                                                    <a id="<?= $drug['id'] ?>" class="btn icon-button remove-drug"> <i class="s7-trash"></i></a>
                                                </th>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <form action="../backend/drugs_controller.php" method="POST" id="excel-import" class="d-none" enctype="multipart/form-data">
                            <input type="file" name="drugs_excel" id="file" accept=".xls,.xlsx">
                        </form>
                    </div>
                    <div class="modal" id="add-drug-modal">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h4 class="modal-title">Добавяне на лекарство</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <div class="modal-body">
                                    <form class="form-inline" action="../backend/drugs_controller.php" method="post" id="add-drugs">
                                        <div class="form-group form-row w-100 m-0">
                                            <div class="col-9">
                                                <input type="text" class="form-control w-100" name="name" id="drug-name-field" placeholder="Въведете име на лекарство">
                                            </div>
                                            <div class="col-3">
                                                <button class="btn btn-success w-100 add-drug-button">Добави</button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="drug_id" id="drug-id" value="0">
                                        <input id="drug-action" type="hidden" name="action" value="add">
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal" id="search-modal">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h4 class="modal-title">Търсене на лекарство</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <div class="modal-body">
                                    <form class="form-inline" action="../backend/drugs_controller.php" method="get" id="drug-search">
                                        <div class="form-group form-row w-100 m-0">
                                            <div class="col-9">
                                                <input type="text" class="form-control w-100" name="name" id="drug-name" placeholder="Въведете име на лекарство">
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