<?php
$pageTitle = "Потребители";
include_once  '../backend/Users.php';
?>
<!doctype html>
<html lang="en">
    <head>
    <?php include_once  'includes/libs.php'; ?>
     <script src="../js/users.js"></script>
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
                                        <p>Добавяне на потребител</p>
                                    </a>
                                    <a class="text-center box excel-import-button">
                                        <i class="s7-server"></i>
                                        <p>Импортирай пациенти от Excel</p>
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
                                                <th>Имейл адрес</th>
                                                <th>Добавен на</th>
                                                <th>Позиция</th>
                                                <th>Действия</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach (Users::getInstance()->getUsers() as $key => $user) { ?>
                                            <tr id="user-<?= $user['id'] ?>">
                                                <td><?= ++$key ?></td>
                                                <td class="col-name"><?= $user['fname'] . ' ' . $user['lname'] ?></td>
                                                <td class="col-email"><?= $user['email'] ?></td>
                                                <td><?= $user['date'] ?></td>
                                                <td><?= $user['position'] ?></td>
                                                <td>
                                                    <a id="<?= $user['id'] ?>" data-action="show" class="btn icon-button info-user"> <i class="s7-id"></i></a>
                                                    <a id="<?= $user['id'] ?>" data-action="edit" class="btn icon-button edit-user"> <i class="s7-edit"></i></a>
                                                    <a id="<?= $user['id'] ?>" class="btn icon-button remove-user"> <i class="s7-trash"></i></a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <form action="../backend/users_controller.php" method="POST" id="excel-import" class="d-none" enctype="multipart/form-data">
                                    <input type="file" name="users_excel" id="file" accept=".xls,.xlsx">
                                </form>
                            </div>
                            <div class="modal" id="search-modal">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h4 class="modal-title">Търсене на потребител</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>

                                        <div class="modal-body">
                                            <form class="form-inline" action="../backend/users_controller.php" method="get" id="user-search">
                                                <div class="form-group form-row w-100 m-0">
                                                    <div class="col-9">
                                                        <input type="text" class="form-control w-100" name="names" id="user-search" placeholder="Въведете име и фамилия">
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
                                            <h4 class="modal-title">Добавяне на потребител</h4>
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
                                                        <div class="form-group doctor-field-container">
                                                            <label>Лекар</label>
                                                            <select name="doctor" id="doctor-field" class="form-control">
                                                                <?php foreach (Users::getInstance()->getUsers('doctors') as $user) { ?>
                                                                    <option value="<?= $user['id'] ?>"> др.  <?= $user['fname'] . ' ' . $user['lname'] ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>E-mail адрес</label>
                                                            <input type="text" id="email-field" class="form-control" name="email" placeholder="Въведете e-mail">
                                                        </div>
                                                        <input type="hidden" name="user_id" id="user-id" value="0">
                                                        <input type="hidden" name="action" id="action" value="add">
                                                        <div class="form-group mt-4 mb-0">
                                                            <button type="submit" id="submit-btn" class="btn btn-block btn-danger">Добави Потребител</button>
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