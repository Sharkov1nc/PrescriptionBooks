$(document).ready(function(){
    let excelForm = $("#excel-import");
    let form = $("#add-drugs");
    let searchForm = $("#drug-search");
    let drugModal = $("#add-drug-modal");
    let searchModal = $("#search-modal");
    let tr, tBody = $("#drugs-table tbody");

    var excel = $("#excel-import input");
    $(".excel-import-button").on("click", function () {
        excel.click();
    });

    excel.on("change", function () {
        excelForm.submit();
    });

    excelForm.submit(function (e) {
        e.preventDefault();
        let data = new FormData(excelForm[0]);
        $.ajax({
            type: excelForm.attr('method'),
            url: excelForm.attr('action'),
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: data,
            success: function (data) {
                data = JSON.parse(data);
                if(data.status){
                    location.reload();
                } else {
                    errorHandler(data.message);
                }
            }
        });
    });

    form.submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            success: function (data) {
                data = JSON.parse(data);
                if(data.status){
                    if(data.changes){
                        $('#drug-'+ data.changes.id + ' .col-name').text(data.changes.name);
                    } else {
                        tr = '<tr id="drug-' + data.drug.id + '"> ' +
                            '<th class="id-th">' + (tBody[0].rows.length + 1) + '</th>' +
                            ' <td class="col-name">' + data.drug.name + '</td>' +
                            ' <td>' + data.drug.date + '</td>' +
                            ' <td>' +
                            ' <a class="btn icon-button edit-drug" id="' + data.drug.id + '" data-action="edit"> <i class="s7-edit"></i></a> ' +
                            ' <a class="btn icon-button remove-drug" id="' + data.drug.id + '"> <i class="s7-trash"></i></a> </td> </tr>';
                        tBody.append(tr);
                    }
                } else {
                    errorHandler(data.message);
                }
                drugModal.modal('hide');
            }
        });
    });

    searchForm.submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: searchForm.attr('method'),
            url: searchForm.attr('action'),
            data: searchForm.serialize(),
            success: function (data) {
                data = JSON.parse(data);
                if(data.length === 0){
                    searchModal.modal('hide');
                    errorHandler("Няма намерени резултати от търсенето");
                } else {
                    tBody.empty();
                    $.each(data, function(key, drug){
                        tr = '<tr id="drug-' + drug.id + '"> ' +
                            '<th class="id-th">' + (tBody[0].rows.length + 1) + '</th>' +
                            ' <td class="col-name">' + drug.name + '</td>' +
                            ' <td>' + drug.date + '</td>' +
                            ' <td>' +
                            ' <a class="btn icon-button edit-drug" id="' + drug.id + '" data-action="edit"> <i class="s7-edit"></i></a> ' +
                            ' <a class="btn icon-button remove-drug" id="' + drug.id + '"> <i class="s7-trash"></i></a> </td> </tr>';
                        tBody.append(tr);
                    });
                }
                searchModal.modal('hide');
            }
        });
    });

    $(document).on('click', '.remove-drug' , function() {
        let drugId = this.id;
        $.ajax({
            type: "POST",
            url: "../backend/drugs_controller.php",
            data: {action: "delete", drug_id : drugId},
            success: function (data) {
                data = JSON.parse(data);
                if (data.status){
                    $("#drug-" + drugId).remove();
                } else {
                    errorHandler(data.message);
                }
            }
        });
    });

    $(document).on('click', '.edit-drug' , function() {
        let drugId = this.id;
        $.ajax({
            url:"../backend/drugs_controller.php",
            method:"GET",
            data:{action: 'search', drug_id: drugId},
            success:function(data){
                data = JSON.parse(data);
                $(".modal-title").text('Редакция на лекарство');
                $("#drug-name-field").val(data[0].name)
                $(".add-drug-button").text("Редактирай");
                $("#drug-action").val("edit");
                $("#drug-id").val(data[0].id);
                drugModal.modal();
            }
        })
    });

});
