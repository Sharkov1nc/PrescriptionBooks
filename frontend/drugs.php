<?php
include_once '../backend/PrescriptionsBooks.php';
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
                                <table class="table" id="users-table">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Име на лекарство</th>
                                        <th scope="col">Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (PrescriptionsBooks::getInstance()->getDrugs() as $key => $drug) { ?>
                                        <tr>
                                            <th><?= $key+1 ?></th>
                                            <th><?= $drug['name'] ?></th>
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
                        <form action="../backend/prescriptions_controller.php" method="POST" id="excel-import" class="d-none" enctype="multipart/form-data">
                            <input type="file" name="drugs_excel" id="file" accept=".xls,.xlsx">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'includes/footer.php'?>
</div>
</body>
</html>