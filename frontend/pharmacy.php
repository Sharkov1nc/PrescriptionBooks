<?php
include_once "../backend/PrescriptionBooks.php";
include_once "../backend/Drugs.php";
$pageTitle = "Търсене на рецепти";
?>
<!doctype html>
<html lang="en">
<head>
    <?php include_once  'includes/libs.php'; ?>
    <script src="../js/pharmacy.js"></script>
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
                            <a data-toggle="modal" data-target="#search-modal" class="text-center box">
                                <i class="s7-search"></i>
                                <p>Търсене на рецепти</p>
                            </a>
                        </div>
                        <div class="row table-container">
                            <div class="col-12">
                                <table class="table" id="written-prescriptions-table">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Име и фамилия</th>
                                        <th>Рецепта</th>
                                        <th>Дата на изписване</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="no-results">
                                                Няма намерени резултати
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal" id="search-modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Търсене на рецепти</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form class="form-inline" action="../backend/prescriptions_controller.php" method="POST" id="recipe-search" target="_blank">
                                        <div class="form-group form-row w-100 m-0">
                                            <div class="col-9">
                                                <input type="text" class="form-control w-100" id="hash" name="hash" placeholder="Въведете код на рецептата">
                                            </div>
                                            <div class="col-3">
                                                <button class="btn btn-success w-100">Търси</button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="action" value="search_hash">
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