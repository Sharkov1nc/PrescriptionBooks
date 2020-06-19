$(document).ready(function(){

    let form = $('#users-form');
    let userModal = $('#user-modal');
    let searchForm = $('#user-search');
    let searchModal = $("#search-modal");
    let tr, tBody = $("#users-table tbody");

    $('#position-field').on('change', function(){
       if(this.value == 3){
           $('.doctor-field-container').css('display', 'block');
       } else {
           $('.doctor-field-container').css('display', 'none');
       }
    });

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
                        $('#user-'+ data.changes.user_id + ' .col-name').text(data.changes.fname + ' ' + data.changes.lname);
                        $('#user-'+ data.changes.user_id + ' .col-email').text(data.changes.email);
                    } else {
                        tr = '<tr id="user-' + data.user.id + '"> ' +
                            '<th class="id-th">' + (tBody[0].rows.length + 1) + '</th>' +
                            ' <td class="col-name">' + data.user.fname + ' ' + data.user.lname + '</td>' +
                            ' <td class="col-email">' + data.user.email + '</td>' +
                            ' <td>' + data.user.date + '</td>' +
                            ' <td>' + data.user.position + '</td>' +
                            ' <td>' +
                            ' <a class="btn icon-button info-user" id="' + data.user.id + '" data-action="show"> <i class="s7-id"></i></a>' +
                            ' <a class="btn icon-button edit-user" id="' + data.user.id + '" data-action="edit"> <i class="s7-edit"></i></a> ' +
                            ' <a class="btn icon-button remove-user" id="' + data.user.id + '"> <i class="s7-trash"></i></a> </td> </tr>';
                        tBody.append(tr);
                    }
                    $('#user-modal').modal('hide');
                } else {
                    errorHandler(data.message);
                }
            }
        });
    });

    $(document).on('click', '.remove-user' , function() {
        let userId = this.id;
        $.ajax({
            type: "POST",
            url: "../backend/users_controller.php",
            data: {action: "delete", user_id : userId},
            success: function (data) {
                data = JSON.parse(data);
                if (data.status){
                    $("#user-" + userId).remove();
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
                            ' <td>'+ user.position +'</td>' +
                            ' <td>' +
                            ' <a class="btn icon-button info-user" id="'+ user.id +'" data-action="show"> <i class="s7-id"></i></a>' +
                            ' <a class="btn icon-button edit-user" id="'+ user.id +'" data-action="edit"> <i class="s7-edit"></i></a> ' +
                            ' <a class="btn icon-button remove-user" id="'+ user.id +'"> <i class="s7-trash"></i></a> </td> </tr>';
                        tBody.append(tr);
                        searchModal.modal('hide');
                    });
                }
            }
        });
    });

    $(document).on('click', '.info-user, .edit-user' , function() {
        let userId = this.id;
        let action = this.dataset.action;
        $.ajax({
            url:"../backend/users_controller.php",
            method:"GET",
            data:{action: 'search', user_id: userId},
            success:function(data){
                data = JSON.parse(data);
                if(action === 'show'){
                    $(".modal-title").text('Информация за потребител');
                    $("#fname-field").val(data[0].fname).attr("readonly", true);
                    $("#lname-field").val(data[0].lname).attr("readonly", true);
                    $("#email-field").val(data[0].email).attr("readonly", true);
                    $("#egn-field").val(data[0].egn).attr("readonly", true);
                    $("#submit-btn").css('display','none');
                    $('#position-field').val(data[0].position_id).prop('disabled', 'disabled');
                } else {
                    $(".modal-title").text('Редакция на потребител');
                    $("#fname-field").val(data[0].fname).removeAttr("readonly");
                    $("#lname-field").val(data[0].lname).removeAttr("readonly");
                    $("#email-field").val(data[0].email).removeAttr("readonly");
                    $("#egn-field").val(data[0].egn).removeAttr("readonly");
                    $("#action").val("edit");
                    $("#user-id").val(data[0].id);
                    $('#position-field').val(data[0].position_id).prop('disabled', 'disabled');
                    $("#submit-btn").css('display','block');
                    $("#submit-btn").text("Редактирай");
                }
                userModal.modal();
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
    $('.doctor-field-container').css('display', 'none');
    $("#submit-btn").text("Добави потребител");
    $("#form-action").val("add");
    $("#user-id").val("");
};
