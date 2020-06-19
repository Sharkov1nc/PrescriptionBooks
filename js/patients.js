$(document).ready(function(){
    let form = $('#patient-form');
    let patientModal = $('#patient-modal');
    let searchForm = $('#patient-search');
    let searchModal = $("#search-modal");
    let tr, tBody = $("#patients-table tbody");

    form.submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            success: function (data) {
                data = JSON.parse(data);
                if (data.status){
                    if(data.changes){
                        $('#patient-'+ data.changes.user_id + ' .col-name').text(data.changes.fname + ' ' + data.changes.lname);
                        $('#patient-'+ data.changes.user_id + ' .col-email').text(data.changes.email);
                    } else {
                        tr = '<tr id="patient-' + data.user.id + '"> ' +
                            '<th class="id-th">' + (tBody[0].rows.length + 1) + '</th>' +
                            ' <td class="col-name">' + data.user.fname + ' ' + data.user.lname + '</td>' +
                            ' <td class="col-email">' + data.user.email + '</td>' +
                            ' <td>' + data.user.date + '</td>' +
                            ' <td>' +
                            ' <a class="btn icon-button info-patient" id="' + data.user.id + '" data-action="show"> <i class="s7-id"></i></a>' +
                            ' <a class="btn icon-button edit-patient" id="' + data.user.id + '" data-action="edit"> <i class="s7-edit"></i></a> ' +
                            ' <a class="btn icon-button remove-patient" id="' + data.user.id + '"> <i class="s7-trash"></i></a> </td> </tr>';
                        tBody.append(tr);
                    }
                   patientModal.modal('hide');
                } else {
                    errorHandler(data.message);
                }
            }
        });
    });

    $(document).on('click', '.remove-patient' , function() {
        let userId = this.id;
        $.ajax({
            type: "POST",
            url: "../backend/users_controller.php",
            data: {action: "delete", user_id : userId},
            success: function (data) {
                data = JSON.parse(data);
                if (data.status){
                    $("#patient-" + userId).remove();
                } else {
                    errorHandler(data.message);
                }
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
                    $.each(data, function(key, user){
                        tr = '<tr id="user-'+ user.id +'"> ' +
                            '<th class="id-th">'+ (tBody[0].rows.length + 1) +'</th>' +
                            ' <td class="col-name">'+ user.fname + ' ' + user.lname +'</td>' +
                            ' <td class="col-email">'+ user.email +'</td>' +
                            ' <td>'+ user.date +'</td>' +
                            ' <td>' +
                            ' <a class="btn icon-button info-patient" id="'+ user.id +'" data-action="show"> <i class="s7-id"></i></a>' +
                            ' <a class="btn icon-button edit-patient" id="'+ user.id +'" data-action="edit"> <i class="s7-edit"></i></a> ' +
                            ' <a class="btn icon-button remove-patient" id="'+ user.id +'"> <i class="s7-trash"></i></a> </td> </tr>';
                        tBody.append(tr);
                        searchModal.modal('hide');
                    });
                }
            }
        });
    });

    $(document).on('click', '.info-patient, .edit-patient' , function() {
        let userId = this.id;
        let action = this.dataset.action;
        $.ajax({
            url:"../backend/users_controller.php",
            method:"GET",
            data:{action: 'search', user_id: userId},
            success:function(data){
                data = JSON.parse(data);
                if(action === 'show'){
                    $(".modal-title").text('Информация за пациент');
                    $("#fname-field").val(data[0].fname).attr("readonly", true);
                    $("#lname-field").val(data[0].lname).attr("readonly", true);
                    $("#email-field").val(data[0].email).attr("readonly", true);
                    $("#egn-field").val(data[0].egn).attr("readonly", true);
                    $("#submit-btn").css('display','none');
                } else {
                    $(".modal-title").text('Редакция на пациент');
                    $("#fname-field").val(data[0].fname).removeAttr("readonly");
                    $("#lname-field").val(data[0].lname).removeAttr("readonly");
                    $("#email-field").val(data[0].email).removeAttr("readonly");
                    $("#egn-field").val(data[0].egn).removeAttr("readonly");
                    $("#action").val("edit");
                    $("#user-id").val(data[0].id);
                    $("#submit-btn").css('display','block');
                    $("#submit-btn").text("Редактирай");
                }
                patientModal.modal();
            }
        })
    });
});

$(document).on('click', '.add-user' , function() {
    clearForm();
});

let clearForm = () => {
    $(".modal-title").text('Добавяне на потребител');
    $("input[type=text]").val("").removeAttr("readonly");
    $("#submit-btn").css('display','block');
    $("#submit-btn").text("Добави потребител");
    $("#form-action").val("add");
    $("#user-id").val("");
};
