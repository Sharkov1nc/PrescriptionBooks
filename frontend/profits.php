<div class="documentation-content">
    <div class="row icon-menu-container">
        <?php  if($auth->user->position != 3){ ?> <!-- проверка, която скрива възможноста за добавяне на приход , ако потребителя е клиент  -->
            <a class="text-center box add-profit-button" data-toggle="modal" data-target="#profits-modal">
                <i class="s7-note"></i>
                <p>Добавяне на приход</p>
            </a>
        <?php } ?>
        <a data-toggle="modal" data-target="#search-modal" class="text-center box">
            <i class="s7-search"></i>
            <p>Търсене на приход</p>
        </a>
    </div>
    <div class="row table-container">
        <div class="col-12">
            <table class="table" id="profits-table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Име на документа</th>
                    <th scope="col">Издаден за потребител</th>
                    <th scope="col">Дата на издаване</th>
                    <th scope="col">Действия</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Модален прозорец съдържащ форма за добавяне на приход -->
<div id="profits-modal" role="dialog" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Добавяне на приход</h4>
                <button type="button" class="close" data-dismiss="modal">&times</button>
            </div>
            <div class="modal-body">
                <form action="../backend/profits_ajax.php" id="add-profits-form" method="post">
                    <div class="row form-group">
                        <label class="col-2 control-label">Име на приход:</label>
                        <div class="col-6">
                            <input id="profit-name" type="text" name="name" placeholder="Приход от ..." class="form-control">
                        </div>
                        <label class="col-1 control-label"><span class="user-select-icon s7-users"></span></label>
                        <div class="col-3 user-select">
                            <select name="created_for" id="user" class="form-control">
                                <?php
                                $usersList = $users->getUsers();
                                foreach ($usersList as $user){ ?>
                                    <option value="<?= $user['id'] ?>"><?= $user['username'] ?></option>
                                <?php  }
                                ?>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div id="items-container">
                        <div class="row form-group item-position-1">
                            <label class="col-2 control-label">Предмет на фактуриране:</label>
                            <div class="col-6">
                                <input type="text" name="item-1" placeholder="Стока, услуга или извършена дейност" class="form-control">
                            </div>
                            <div class="col-3">
                                <input type="text" name="item-price-1" placeholder="Цена" class="form-control">
                            </div>
                            <div class="col-1">
                                <button class="btn btn-primary new-item" type="button" ><span class="s7-plus"></span></button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="action" id="action" value="add">
                    <input type="hidden" id="profits-items-count" name="profits-items-count" value="1">
                    <div class="form-group">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-space"><i class="icon s7-check"></i> Добави приход</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Модален прозорец съдържащ форма за търсене на приход по име на прихода -->
<div class="modal" id="search-modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Търсене на приход</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form class="form-inline" id="search-profit-form" action="../backend/profits_ajax.php" method="get">
                    <div class="form-group form-row w-100">
                        <div class="col-9">
                            <input type="text" class="form-control w-100" name="profit-name" id="profit-search" placeholder="Въведете името на прихода">
                        </div>
                        <div class="col-3">
                            <button class="btn btn-success w-100">Търси</button>
                        </div>
                        <input type="hidden" name="action" value="search">
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<script>

    let form = $("#add-profits-form");
    let modal = $("#profits-modal");
    let searchForm = $("#search-profit-form");
    let searchModal = $('#search-modal');
    let tr, tBody = $("#profits-table tbody");
    let elementsCount = 2;
    let createdDynamicFieldsForEdit = false;

    // ajax заявка, която се използва за зареждане на списъка с приходи, ако няма проходи показва надпис за празна таблица
    $(document).ready(function () {
        $.ajax({
            type: 'get', //  метод, който заявката използва
            url: '../backend/profits_ajax.php', // адрес, до който се изпраща заявката
            data: {action: 'load'}, // данни, които предаваме с заявката
            success: function (data) { // data е вързнатия резултат(масив от обекти) от заявката, който обработваме и зареждаме в таблицата с приходи
                data = JSON.parse(data);
                if (data.length === 0) {
                    tr = '<tr><th colspan="5" class="text-center">Таблицата с приходи е празна</th></tr>';
                    tBody.append(tr);
                } else {
                    // зареждаме резултатите в таблицата с приходи
                    tBody.empty();
                    $.each(data, function (key, val) {
                        tr = '<tr> ' +
                            '<th class="id-th">'+ val.profit.id +'</th>' +
                            '<td>'+ val.profit.name +'</td>' +
                            '<td>'+ val.profit.created_for +'</td>' +
                            '<td>'+ val.profit.date +'</td>' +
                            '<td class="action-column">' +
                            '<a href="../backend/profits_ajax.php?action=view&id='+ val.profit.id +'" target="_blank" class="btn icon-button"> <i class="s7-note2"></i></a>';
                        if (val.profit.position_id != 3){
                            tr += ' <a id="'+ val.profit.id +'" class="btn icon-button edit-profit"> <i class="s7-edit"></i></a>' +
                                ' <a id="'+ val.profit.id +'" class="btn icon-button remove-profit"> <i class="s7-trash"></i></a>';
                        }
                        tr += '</td>' +
                            '</tr>';
                        tBody.append(tr);
                    });
                }
            }
        });
    });

    // При изпращане на формата за добавяне на приход използваме ajax за предаване на въведените данни за създаване на нов приход
    form.submit(function (e) {

        e.preventDefault();

        $.ajax({
            type: form.attr('method'), //  метод, който заявката използва
            url: form.attr('action'), // адрес, до който се изпраща заявката
            data: form.serialize(), // данни, които предаваме с заявката
            success: function (data) {
                data = JSON.parse(data);
                if (data.status) {
                    location.reload(); // презареждаме страницата, ако резултата от изпълнението на функционалноста на back-end ниво е успешен
                } else {
                    modal.modal('hide');
                    errorHandler(data.message); // извикваме метода за прихващане на грешки
                }
            }
        });
    });

    // Създаваме event listener, който следи за натискане на бутона за изтриване на приход, при натискане на бутона се изпраща
    // ajax заявка която изтрива прихода
    $(document).on('click', '.remove-profit' , function() {
        let profitId = this.id;
        $.ajax({
            type: "POST", //  метод, който заявката използва
            url: "../backend/profits_ajax.php", // адрес, до който се изпраща заявката
            data: {action: "delete", id : profitId}, // данни, които предаваме с заявката
            success: function () {
                location.reload(); // презареждаме страницата
            }
        });
    });

    // при изпращане на формата за тръсене на приход използваме ajax заявка за предаване на данните, която връща приходи,
    // които показваме в таблицата или грешка, която прихващаме с метода за прихващане на грешки
    searchForm.submit(function (e) {

        e.preventDefault();

        $.ajax({
            type: searchForm.attr('method'), //  метод, който заявката използва
            url: searchForm.attr('action'), // адрес, до който се изпраща заявката
            data: searchForm.serialize(), // данни, които предаваме с заявката
            success: function (data) {
                data = JSON.parse(data);
                if(data.length === 0){
                    searchModal.modal('hide');
                    errorHandler("Не са намерени резултати от търсенето"); // извикваме метода за прихващане на грешки
                } else {
                    // зареждаме резултатите от търсенето в таблицата с приходи
                    tBody.empty();
                    $.each(data, function(key, val){
                        tr = '<tr> ' +
                            '<th class="id-th">'+ val.profit.id +'</th>' +
                            '<td>'+ val.profit.name +'</td>' +
                            '<td>'+ val.profit.created_for +'</td>' +
                            '<td>'+ val.profit.date +'</td>' +
                            '<td class="action-column">' +
                                '<a href="../backend/profits_ajax.php?action=view&id='+ val.profit.id +'" target="_blank" class="btn icon-button"> <i class="s7-note2"></i></a> ';
                            if (val.profit.position_id != 3){
                               tr += ' <a id="'+ val.profit.id +'" class="btn icon-button edit-profit"> <i class="s7-edit"></i></a> ' +
                                ' <a id="'+ val.profit.id +'" class="btn icon-button remove-profit"> <i class="s7-trash"></i></a> ';
                            }
                            tr += '</td>' +
                            '</tr>';
                        tBody.append(tr);
                    });
                    searchModal.modal('hide');
                }
            }
        });
    });

    // създаваме event listener, който следи за натискане на бутона за редакция на приход, при натискане на бутона се изпраща
    // ajax заявка която отваря модален прозорец и зарежда резултата(прихода), който сме избрали да редактираме в формата
    $(document).on('click', '.edit-profit' , function() {

        let elem;
        let cnt = 1;
        let profitId = this.id;
        let container = $("#items-container");
        $.ajax({
            type: "GET", //  метод, който заявката използва
            url: "../backend/profits_ajax.php", // адрес, до който се изпраща заявката
            data: {action: 'search', profitId : profitId}, // данни, които предаваме с заявката
            success: function (data) {
                // отваряме модалния прозорец и зареждаме прихода, който искаме да редактираме в формата
                data = JSON.parse(data);
                modal.modal("show");
                container.empty();
                $("#action").val("edit");
                $("#profits-items-count").val(data[0].profit_items.length);
                let profitId = '<input type="hidden" name="profit_id" value="'+ data[0].profit.id +'"/>';
                form.append(profitId);
                $("#profits-modal .modal-title").text("Редакция на приход");
                $("#profit-name").val(data[0].profit.name);
                $("#user").val(data[0].profit.u_id);
                $.each(data[0].profit_items, function (key, item) {
                    elem = '<div class="row form-group item-position-'+ cnt +'"><label class="col-2 control-label">Предмет на фактуриране:</label> <div class="col-6"> <input type="text" name="item-'+ cnt +'" placeholder="Стока, услуга или извършена дейност" class="form-control" value="'+ item.name +'"> </div> <div class="col-3"> <input type="text" name="item-price-'+ cnt +'" placeholder="Цена" class="form-control" value="'+ item.price +'"> </div> <div class="col-1"> <button class="btn btn-primary new-item" type="button" ><span class="s7-plus"></span></button> </div> </div>';
                    container.append(elem);
                    cnt++;
                });
                // извикваме метод за динамично създаване на инпут полета, в които се зареждат предметите на фактуриране към прихода
                dynamicInputField($(".item-position-" + (cnt-1)));
                elementsCount = cnt;
                createdDynamicFieldsForEdit = true;
            }
        });
    });

    // метод за динамично създаване на инпут полета, който се използват при добавяне и редакция на приход (за предмети на фактуриране)
    let dynamicInputField = (itemPosition) => {
        $(document).on('click', '.new-item' , function() {
            let newItemPosition = '<div class="row form-group item-position-'+ elementsCount +'"><label class="col-2 control-label">Предмет на фактуриране:</label> <div class="col-6"> <input type="text" name="item-'+ elementsCount +'" placeholder="Стока, услуга или извършена дейност" class="form-control"> </div> <div class="col-3"> <input type="text" name="item-price-'+ elementsCount +'" placeholder="Цена" class="form-control"> </div> <div class="col-1"> <button class="btn btn-primary new-item" type="button" ><span class="s7-plus"></span></button> </div> </div>';
            itemPosition.after(newItemPosition);
            let element = ".item-position-" + (elementsCount);
            itemPosition =  $(element);
            $("#profits-items-count").val(elementsCount);
            elementsCount++;
        });
    };

    // създаваме event listener, който следи за натискане на бутона за добавяне на приход и извиква метод за добавяне на динамично поле за фактуриране
    $(document).on('click','.add-profit-button', function (e) {
        dynamicInputField($(".item-position-1"));
    });


    // метод, който премахва всички добавени динамични полета за фактуриране
    let clearDynamicInputFields = () => {
        if (elementsCount > 1){
            for (let i= elementsCount-1; i > 1; i--){
                let element = ".item-position-" + i;
                $(element).remove();
                elementsCount--;
            }
            if (createdDynamicFieldsForEdit){
                $("input[name$='item-1']").val('');
                $("input[name$='item-price-1']").val('');
            }
        }
    };

    // създаваме event listener, който следи за затваряне на модалния прозорец, при затваряне на модалния прозорец се
    // изчистват всички въведени записи в полета на формата и се премахват всички динамични полета за фактуриране
    modal.on('hidden.bs.modal', function () {
        $(".wizard-previous").click();
        form[0].reset();
        clearDynamicInputFields();
        $("#profits-items-count").val(1);
        $("#action").val("add");
    });

</script>