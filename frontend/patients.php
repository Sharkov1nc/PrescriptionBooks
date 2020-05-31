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
                    <div class="users-content">
                        <div class=" row icon-menu-container">
                            <a data-toggle="modal" data-target="#user-modal" class="text-center box add-user">
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
                                <table class="table" id="users-table">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Име и фамилия</th>
                                        <th scope="col">Имейл адрес</th>
                                        <th scope="col">Добавен на</th>
                                        <th scope="col">Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach (Users::getInstance()->getPatients() as $key => $user) { ?>
                                        <tr>
                                            <th scope="col"><?= ++$key ?></th>
                                            <th scope="col"><?= $user['fname'] . ' ' . $user['lname'] ?></th>
                                            <th scope="col"><?= $user['email'] ?></th>
                                            <th scope="col"><?= $user['date'] ?></th>
                                            <th scope="col">
                                                <a id="'<?= $user['id'] ?>'" class="btn icon-button edit-expense"> <i class="s7-id"></i></a>
                                                <a id="'<?= $user['id'] ?>'" class="btn icon-button edit-expense"> <i class="s7-edit"></i></a>
                                                <a id="'<?= $user['id'] ?>'" class="btn icon-button edit-expense"> <i class="s7-trash"></i></a>
                                            </th>
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
                                    <form class="form-inline" action="../backend/users_controller.php" method="get" id="user-search">
                                        <div class="form-group form-row w-100">
                                            <div class="col-9">
                                                <input type="text" class="form-control w-100" name="username" id="user-search" placeholder="Въведете потребителско име">
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

                    <div class="modal" id="user-modal">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h4 class="modal-title">Добавяне на пациент</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times</button>
                                </div>

                                <div class="modal-body">
                                    <form action="../backend/users_controller.php" method="post" id="users-form">
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
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>ЕГН</label>
                                                            <input type="text" id="egn-field" class="form-control" name="egn" placeholder="Въведете енг">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>Позиция</label>
                                                            <select name="position" id="position-field" class="form-control">
                                                                <?php foreach (MainController::getInstance()->getPositions() as $position) { ?>
                                                                    <option value="<?= $position['id'] ?>"> <?= $position['position'] ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>E-mail адрес</label>
                                                    <input type="text" id="email-field" class="form-control" name="email" placeholder="Въведете e-mail">
                                                </div>
                                                <div class="form-group">
                                                    <label>Парола</label>
                                                    <input type="text" id="password-field" class="form-control" name="password" placeholder="Въведете парола">
                                                </div>
                                                <input type="hidden" name="user-id" id="user-id" value="0">
                                                <input type="hidden" name="action" id="action" value="add">
                                                <div class="form-group mt-4 mb-0">
                                                    <button type="submit" id="submit-btn" class="btn btn-block btn-lg form-confirm-button">Добави Потребител</button>
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