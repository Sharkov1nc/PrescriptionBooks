<?php
$pageTitle = "Пациенти";
include_once '../backend/Users.php';
?>
<!doctype html>
<html lang="en">
<head>
    <?php include_once  'includes/libs.php'; ?>
    <script src="../js/patients.js"></script>
</head>
<body>
<div class="container-fluid">
    <?php include_once  'includes/header.php'?>
    <div class="row">
        <?php include_once 'includes/menu.php' ?>
        <div class="col-10 page-content">
            <div class="row">
                <div class="col-12 main-content">
                    <div class="patients-content">
                        <div class=" row icon-menu-container">
                            <a data-toggle="modal" data-target="#patient-modal" class="text-center box add-user">
                                <i class="s7-add-user"></i>
                                <p>Добавяне на пациент</p>
                            </a>
                            <a data-toggle="modal" data-target="#search-modal" class="text-center box">
                                <i class="s7-search"></i>
                                <p>Търсене на пациент</p>
                            </a>
                        </div>
                        <div class="row table-container">
                            <div class="col-12">
                                <table class="table" id="patients-table">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Име и фамилия</th>
                                        <th>Имейл адрес</th>
                                        <th>Добавен на</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach (Users::getInstance()->getUsers('patients') as $key => $user) { ?>
                                        <tr id="patient-<?= $user['id'] ?>">
                                            <td><?= ++$key ?></td>
                                            <td class="col-name"><?= $user['fname'] . ' ' . $user['lname'] ?></td>
                                            <td class="col-email"><?= $user['email'] ?></td>
                                            <td><?= $user['date'] ?></td>
                                            <td>
                                                <a id="<?= $user['id'] ?>" data-action="show" class="btn icon-button info-patient"> <i class="s7-id"></i></a>
                                                <a id="<?= $user['id'] ?>" data-action="edit" class="btn icon-button edit-patient"> <i class="s7-edit"></i></a>
                                                <a id="<?= $user['id'] ?>" class="btn icon-button remove-patient"> <i class="s7-trash"></i></a>
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
                                    <h4 class="modal-title">Търсене на пациент</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <div class="modal-body">
                                    <form class="form-inline" action="../backend/users_controller.php" method="get" id="patient-search">
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

                    <div class="modal" id="patient-modal">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h4 class="modal-title">Добавяне на пациент</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times</button>
                                </div>

                                <div class="modal-body">
                                    <form action="../backend/users_controller.php" method="post" id="patient-form">
                                        <div class="form-group form-row w-100">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>Име</label>
                                                            <input type="text" id="fname-field" class="form-control" name="fname" placeholder="Въведете име">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>Фамилия</label>
                                                            <input type="text" id="lname-field" class="form-control" name="lname" placeholder="Въведете фамилия">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>ЕГН</label>
                                                            <input type="text" id="egn-field" class="form-control" name="egn" placeholder="Въведете енг">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>E-mail адрес</label>
                                                    <input type="text" id="email-field" class="form-control" name="email" placeholder="Въведете e-mail">
                                                </div>
                                                <input type="hidden" name="user_id" id="user-id" value="0">
                                                <input type="hidden" name="action" id="action" value="add">
                                                <input type="hidden" name="position" value="3">
                                                <div class="form-group mt-4 mb-0">
                                                    <button type="submit" id="submit-btn" class="btn btn-block btn-danger">Добави Пациент</button>
                                                </div>
                                            </div>
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