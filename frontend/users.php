<?php $pageTitle = "Потребители"; ?>
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
                                                <th scope="col">#</th>
                                                <th scope="col">Име и фамилия</th>
                                                <th scope="col">Имейл адрес</th>
                                                <th scope="col">Позиция</th>
                                                <th scope="col">Действия</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal" id="search-modal">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h4 class="modal-title">Търсене на потребител</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>

                                        <div class="modal-body">
                                            <form class="form-inline" action="../backend/user_ajax.php" method="get" id="user-search">
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

                            <div class="modal" id="user-data-modal">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h4 class="modal-title">Добавяне на потребител</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times</button>
                                        </div>

                                        <div class="modal-body">
                                            <form action="../backend/user_ajax.php" method="post" id="users-form">
                                                <div class="form-group form-row w-100">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="inputAddress">Потребителско име</label>
                                                            <input type="text" id="username-field" class="form-control" name="username" placeholder="Въведете потребителско име">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="inputAddress">Имейл Адрес</label>
                                                            <input type="text" id="email-field" class="form-control" name="email" placeholder="Въведете имейл адрес">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="inputAddress">Парола</label>
                                                            <input type="text" id="password-field" class="form-control" name="password" placeholder="Въведете парола">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="inputAddress">Позиция</label>
                                                            <select name="position" id="position-field" class="form-control">
                                                                <?php $positions = $home->getPositions(); // извикваме метод, който зарежда позициите
                                                                if($positions) {
                                                                    foreach ($positions as $position) {
                                                                        ?>
                                                                        <option value="<?= $position['id'] ?>"> <?= $position['position'] ?></option>
                                                                        <?php
                                                                    }
                                                                }?>
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="user-id" id="user-id" value="0">
                                                        <input type="hidden" name="action" id="action" value="add">
                                                        <div class="form-group mt-4">
                                                            <button type="submit" id="submit-btn" class="btn btn-block btn-danger btn-lg">Добави Потребител</button>
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

<!--<script>-->
<!---->
<!--    let form = $('#users-form');-->
<!--    let searchForm = $('#user-search');-->
<!--    let md = $("#user-data-modal");-->
<!--    let searchModal = $("#search-modal");-->
<!--    let tr, tBody = $("#users-table tbody");-->
<!---->
<!--    // ajax заявка, която се използва за зареждане на списъка с потребители, ако няма потребители показва надпис за празна таблица-->
<!--    $(document).ready(function () {-->
<!--        $.ajax({-->
<!--            type: 'get', //  метод, който заявката използва-->
<!--            url: '../backend/user_ajax.php', // адрес, до който се изпраща заявката-->
<!--            data: {action: 'load'}, // данни, които предаваме с заявката-->
<!--            success: function (data) { // data е вързнатия резултат(масив от обекти) от заявката, който обработваме и зареждаме в таблицата с потребители-->
<!--                data = JSON.parse(data);-->
<!--                console.log(data);-->
<!--                if (data.length === 0) {-->
<!--                    tr = '<tr><th colspan="5" class="text-center">Таблицата с потребители е празна</th></tr>';-->
<!--                    tBody.append(tr);-->
<!--                } else {-->
<!--                    // зареждаме резултатите в таблицата с потребители-->
<!--                    tBody.empty();-->
<!--                    $.each(data, function(key, val){-->
<!--                        tr = '<tr> <th class="id-th">'+ val.id +'</th> <td>'+ val.username +'</td> <td>'+ val.email +'</td> <td>'+ val.position +'</td> <td class="action-column">  <a class="btn icon-button info-user" id="'+ val.id +'" data-action="show"> <i class="s7-id"></i></a> <a class="btn icon-button edit-user" id="'+ val.id +'" data-action="edit"> <i class="s7-edit"></i></a> <a class="btn icon-button remove-user" id="'+ val.id +'"> <i class="s7-trash"></i></a> </td> </tr>';-->
<!--                        tBody.append(tr);-->
<!--                        searchModal.modal('hide');-->
<!--                    });-->
<!--                }-->
<!--            }-->
<!--        });-->
<!--    });-->
<!---->
<!--    // При изпращане на формата за добавяне или редакция на потребител използваме ajax за предаване на въведените данни за създаване на нов потребител-->
<!--    form.submit(function (e) {-->
<!---->
<!--        e.preventDefault();-->
<!---->
<!--        $.ajax({-->
<!--            type: form.attr('method'), // метод, който заявката използва-->
<!--            url: form.attr('action'), // адрес, до който се изпраща заявката-->
<!--            data: form.serialize(), // данни, които предаваме с заявката-->
<!--            success: function (data) {-->
<!--                data = JSON.parse(data);-->
<!--                if (data.status){-->
<!--                    location.reload(); // презареждаме страницата-->
<!--                } else {-->
<!--                    md.modal('hide');-->
<!--                    errorHandler(data.message); //извикваме метода за прихващане на грешки-->
<!--                }-->
<!--            }-->
<!--        });-->
<!--    });-->
<!---->
<!--    // При изпращане на формата за тръсене на потребител използваме ajax заявка за предаване на данните, която връща потребители,-->
<!--    // които показваме в таблицата или грешка, която прихващаме с метода за прихващане на грешки-->
<!--    searchForm.submit(function (e) {-->
<!---->
<!--        e.preventDefault();-->
<!---->
<!--        let tr, tBody = $("#users-table tbody");-->
<!---->
<!--        $.ajax({-->
<!--            type: searchForm.attr('method'), // метод, който заявката използва-->
<!--            url: searchForm.attr('action'), // адрес, до който се изпраща заявката-->
<!--            data: searchForm.serialize(), // данни, които предаваме с заявката-->
<!--            success: function (data) {-->
<!--                data = JSON.parse(data);-->
<!--                if(data.length === 0){-->
<!--                    searchModal.modal('hide');-->
<!--                    errorHandler("Няма намерени резултати от търсенето"); //извикваме метода за прихващане на грешки-->
<!--                } else {-->
<!--                    // зареждаме резултатите в таблицата с потребители-->
<!--                    tBody.empty();-->
<!--                    $.each(data, function(key, val){-->
<!--                        tr = '<tr> <th class="id-th">'+ val.id +'</th> <td>'+ val.username +'</td> <td>'+ val.email +'</td> <td>'+ val.position +'</td> <td class="action-column">  <a class="btn icon-button info-user" id="'+ val.id +'" data-action="show"> <i class="s7-id"></i></a> <a class="btn icon-button edit-user" id="'+ val.id +'" data-action="edit"> <i class="s7-edit"></i></a> <a class="btn icon-button remove-user" id="'+ val.id +'"> <i class="s7-trash"></i></a> </td> </tr>';-->
<!--                        tBody.append(tr);-->
<!--                        searchModal.modal('hide');-->
<!--                    });-->
<!--                }-->
<!--            }-->
<!--        });-->
<!--    });-->
<!---->
<!--    // Създаваме event listener, който следи за натискане на бутона за изтриване на потребител, при натискане на бутона се изпраща-->
<!--    // ajax заявка, която изтрива потребител-->
<!--    $(document).on('click', '.remove-user' , function() {-->
<!--        let userId = this.id;-->
<!--        $.ajax({-->
<!--            type: "POST", // метод, който заявката използва-->
<!--            url: "../backend/user_ajax.php", // адрес, до който се изпраща заявката-->
<!--            data: {action: "delete", id : userId}, // данни, които предаваме с заявката-->
<!--            success: function (data) {-->
<!--                data = JSON.parse(data);-->
<!--                if (data.status){-->
<!--                    location.reload(); // презареждаме страницата-->
<!--                } else {-->
<!--                    errorHandler(data.message); //извикваме метода за прихващане на грешки-->
<!--                }-->
<!--            }-->
<!--        });-->
<!--    });-->
<!---->
<!--    // Създаваме event listener, който следи за натискане на бутона за редактиране и преглед на потребител, при натискане на бутона се изпраща-->
<!--    // ajax заявка, която получава и зарежда данните за потребителя-->
<!--    $(document).on('click', '.info-user, .edit-user' , function() {-->
<!--        let userId = this.id;-->
<!--        let action = this.dataset.action;-->
<!--        $.ajax({-->
<!--            url:"../backend/user_ajax.php", // адрес, до който се изпраща заявката-->
<!--            method:"GET", // метод, който заявката използва-->
<!--            data:{action: 'search' ,id: userId}, // данни, които предаваме с заявката-->
<!--            success:function(data){-->
<!--                // зареждаме резултатите в таблицата с потребители-->
<!--                data = JSON.parse(data);-->
<!--                if(action === 'show'){-->
<!--                    // отваряме модалния прозорец в режим на преглед и забраняваме редактирането на полетата-->
<!--                    $(".modal-title").text('Информация за потребител');-->
<!--                    $("#username-field").val(data[0].username).attr("readonly", true);-->
<!--                    $("#email-field").val(data[0].email).attr("readonly", true);-->
<!--                    $("#password-field").val(data[0].password).attr("readonly", true);-->
<!--                    $("#submit-btn").css('display','none');-->
<!--                    $('#position-field').val(data[0].position_id).prop('disabled', 'disabled');-->
<!--                } else {-->
<!--                    // отваряме модалния прозорец в режим на редактиране и разрешаваме редактирането на полетата-->
<!--                    $(".modal-title").text('Редакция на потребител');-->
<!--                    $("#username-field").val(data[0].username).removeAttr("readonly");-->
<!--                    $("#email-field").val(data[0].email).removeAttr("readonly");-->
<!--                    $("#password-field").val(data[0].password).removeAttr("readonly");-->
<!--                    $('#position-field').val(data[0].position_id).prop('disabled', false);-->
<!--                    $("#action").val("edit");-->
<!--                    $("#user-id").val(data[0].id);-->
<!--                    $("#submit-btn").css('display','block');-->
<!--                    $("#submit-btn").text("Редактирай");-->
<!--                }-->
<!--                md.modal();-->
<!--            }-->
<!--        })-->
<!--    });-->
<!---->
<!--    // Създаваме event listener, който следи за натискане на бутона за добавяне на потребител и извиква метод за изчистване-->
<!--    // на данните от формата и връщането им в първоначалния вид-->
<!--    $(document).on('click', '.add-user' , function() {-->
<!--        clearForm(); // извикваме метода за зачистване и връщането на данните от формата в първоначалния им вид-->
<!--    });-->
<!---->
<!--    // метод за изчистване и връщане на данни от формата в първоначалния им вид-->
<!--    let clearForm = () => {-->
<!--        $(".modal-title").text('Добавяне на потребител');-->
<!--        $("input[type=text]").val("").removeAttr("readonly");-->
<!--        $("#submit-btn").css('display','block');-->
<!--        $("#submit-btn").text("Добави потребител");-->
<!--        $("#form-action").val("add");-->
<!--        $("#user-id").val("");-->
<!--    };-->
<!--</script>-->