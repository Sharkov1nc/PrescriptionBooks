
$(document).ready(function(){

    let form = $('#users-form');
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
                    location.reload();
                } else {
                    errorHandler(data.message);
                }
            }
        });
    });

});



// searchForm.submit(function (e) {
//
//     e.preventDefault();
//
//     let tr, tBody = $("#users-table tbody");
//
//     $.ajax({
//         type: searchForm.attr('method'), // метод, който заявката използва
//         url: searchForm.attr('action'), // адрес, до който се изпраща заявката
//         data: searchForm.serialize(), // данни, които предаваме с заявката
//         success: function (data) {
//             data = JSON.parse(data);
//             if(data.length === 0){
//                 searchModal.modal('hide');
//                 errorHandler("Няма намерени резултати от търсенето"); //извикваме метода за прихващане на грешки
//             } else {
//                 // зареждаме резултатите в таблицата с потребители
//                 tBody.empty();
//                 $.each(data, function(key, val){
//                     tr = '<tr> <th class="id-th">'+ val.id +'</th> <td>'+ val.username +'</td> <td>'+ val.email +'</td> <td>'+ val.position +'</td> <td class="action-column">  <a class="btn icon-button info-user" id="'+ val.id +'" data-action="show"> <i class="s7-id"></i></a> <a class="btn icon-button edit-user" id="'+ val.id +'" data-action="edit"> <i class="s7-edit"></i></a> <a class="btn icon-button remove-user" id="'+ val.id +'"> <i class="s7-trash"></i></a> </td> </tr>';
//                     tBody.append(tr);
//                     searchModal.modal('hide');
//                 });
//             }
//         }
//     });
// });
//
// // Създаваме event listener, който следи за натискане на бутона за изтриване на потребител, при натискане на бутона се изпраща
// // ajax заявка, която изтрива потребител
// $(document).on('click', '.remove-user' , function() {
//     let userId = this.id;
//     $.ajax({
//         type: "POST", // метод, който заявката използва
//         url: "../backend/user_ajax.php", // адрес, до който се изпраща заявката
//         data: {action: "delete", id : userId}, // данни, които предаваме с заявката
//         success: function (data) {
//             data = JSON.parse(data);
//             if (data.status){
//                 location.reload(); // презареждаме страницата
//             } else {
//                 errorHandler(data.message); //извикваме метода за прихващане на грешки
//             }
//         }
//     });
// });
//
// // Създаваме event listener, който следи за натискане на бутона за редактиране и преглед на потребител, при натискане на бутона се изпраща
// // ajax заявка, която получава и зарежда данните за потребителя
// $(document).on('click', '.info-user, .edit-user' , function() {
//     let userId = this.id;
//     let action = this.dataset.action;
//     $.ajax({
//         url:"../backend/user_ajax.php", // адрес, до който се изпраща заявката
//         method:"GET", // метод, който заявката използва
//         data:{action: 'search' ,id: userId}, // данни, които предаваме с заявката
//         success:function(data){
//             // зареждаме резултатите в таблицата с потребители
//             data = JSON.parse(data);
//             if(action === 'show'){
//                 // отваряме модалния прозорец в режим на преглед и забраняваме редактирането на полетата
//                 $(".modal-title").text('Информация за потребител');
//                 $("#username-field").val(data[0].username).attr("readonly", true);
//                 $("#email-field").val(data[0].email).attr("readonly", true);
//                 $("#password-field").val(data[0].password).attr("readonly", true);
//                 $("#submit-btn").css('display','none');
//                 $('#position-field').val(data[0].position_id).prop('disabled', 'disabled');
//             } else {
//                 // отваряме модалния прозорец в режим на редактиране и разрешаваме редактирането на полетата
//                 $(".modal-title").text('Редакция на потребител');
//                 $("#username-field").val(data[0].username).removeAttr("readonly");
//                 $("#email-field").val(data[0].email).removeAttr("readonly");
//                 $("#password-field").val(data[0].password).removeAttr("readonly");
//                 $('#position-field').val(data[0].position_id).prop('disabled', false);
//                 $("#action").val("edit");
//                 $("#user-id").val(data[0].id);
//                 $("#submit-btn").css('display','block');
//                 $("#submit-btn").text("Редактирай");
//             }
//             md.modal();
//         }
//     })
// });
//
// // Създаваме event listener, който следи за натискане на бутона за добавяне на потребител и извиква метод за изчистване
// // на данните от формата и връщането им в първоначалния вид
// $(document).on('click', '.add-user' , function() {
//     clearForm(); // извикваме метода за зачистване и връщането на данните от формата в първоначалния им вид
// });
//
// // метод за изчистване и връщане на данни от формата в първоначалния им вид
// let clearForm = () => {
//     $(".modal-title").text('Добавяне на потребител');
//     $("input[type=text]").val("").removeAttr("readonly");
//     $("#submit-btn").css('display','block');
//     $("#submit-btn").text("Добави потребител");
//     $("#form-action").val("add");
//     $("#user-id").val("");
// };
