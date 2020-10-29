$(document).ready(function() {
    let editPrescriptionModal = $("#edit-prescription-modal");
    let editPrescriptionForm = $("#edit-prescription-form");
    let searchForm = $('#prescription-search');
    let searchModal = $("#search-modal");
    let tr, tBody = $("#written-prescriptions-table tbody");
    let drugs = $(".drugs-select .list-group-item.drugs-item");
    let selectedDrugsContainer = $("#selected-drugs");
    let selectedDrugs = [];
    let recipe_id = null;

    $(document).on("click", ".delete-recipe", function () {
        let recipeId = this.id;
        $.ajax({
            type: "POST",
            url: "../backend/prescriptions_controller.php",
            data: {action: "delete", recipe_id : recipeId},
            success: function (data) {
                data = JSON.parse(data);
                if (data.status){
                    $("#recipe-row-" + recipeId).remove();
                } else {
                    errorHandler(data.message);
                }
            }
        });
    });


    $(document).on("click", ".edit-recipe",function () {
        let recipeId = this.id;
        $.ajax({
            url:"../backend/prescriptions_controller.php",
            method:"GET",
            data:{action: 'search_written', recipe_id: recipeId},
            success:function(data){
                data = JSON.parse(data)[0];
                recipe_id = data.recipe_id;
                if(data.drugs){
                    selectedDrugs = data.drugs;
                    let drugsText = '';
                    $.each(data.drugs, function (key, val) {
                        drugsText += " <div class='badge badge-light badge-drugs' id='" + val.id + "'>" + (val.quantity > 1 ? val.quantity +'x ' : '') + val.name + "</div> "
                    });
                    $("#selected-drugs").html(drugsText);
                }
                $("#patient-name").val(data.user_fname + ' ' + data.user_lname).attr("readonly", true);
                $("#additional_info").text(data.additional_information);
                editPrescriptionModal.modal();
            }
        })
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
                    $.each(data, function(key, val){
                        tr = '<tr id="recipe-row-'+ val.recipe_id +'"> ' +
                            '<td class="id-th">'+ (tBody[0].rows.length + 1) +'</td>' +
                            '<td>'+ val.user_fname + ' ' + val.user_lname +'</td>' +
                            '<td><a href="../backend/prescriptions_controller.php?action=print&recipe_id='+ val.recipe_id +'" target="_blank"><span class="badge badge-success">Преглед на рецепта</span></a></th></td>' +
                            '<td>'+ (val.recipe_date ? val.recipe_date : "-")  +'</td>' +
                            '<td>' +
                            '<a id="' + val.recipe_id +'" class="btn icon-button edit-recipe"> <i class="s7-edit"></i></a> ' +
                            '<a id="' + val.recipe_id +'" class="btn icon-button delete-recipe"> <i class="s7-trash"></i></a> ' +
                            '</td> </tr>';
                        tBody.append(tr);
                        searchModal.modal('hide');
                    });
                }
            }
        });
    });


    $("#live-search").on('keyup', function(){
        if($(this).val() !== ''){
            let val = $(this).val().toLowerCase();
            $.each(drugs, function(key, elm){
                if($(elm).text().toLowerCase().indexOf(val) === -1){
                    $(elm).css("display", "none");
                } else {
                    $(elm).css("display", "block");
                }
            });
        } else {
            $.each(drugs, function(key, elm){
                $(elm).css("display", "block");
            });
        }
    });

    drugs.on("click", function () {
        let drugId = parseInt(this.id);
        var isDrugExists = false;
        $.each(selectedDrugs, function(key, val) {
            if (val.id === drugId) {
                let elm = $(".badge-drugs#" + drugId);
                val.quantity += 1;
                elm.text(val.quantity+'x '+ val.name);
                isDrugExists = true;
            }
        });
        if(!isDrugExists){
            selectedDrugsContainer.append("<div class='badge badge-light badge-drugs' id='" + this.id + "'>" + $(this).text() + "</div>");
            selectedDrugs.push({
                id: drugId,
                quantity: 1,
                name: $(this).text()
            });
        }
    });

    editPrescriptionModal.on("hidden.bs.modal", function () {
        selectedDrugsContainer.empty();
        selectedDrugs = [];
    });

    selectedDrugsContainer.on('click', '.badge-drugs', function () {
        let drug = this;
        $.each(selectedDrugs, function(key, val) {
            if (val && val.id == drug.id) {
                selectedDrugs.splice(key, 1);
                drug.remove();
            }
        });
    });

    editPrescriptionForm.on('submit', function (e) {
        e.preventDefault();
        let additionalInfo = $(this).find('textarea[name="additional_information"]').val();
        $.ajax({
            type: editPrescriptionForm.attr('method'),
            url: editPrescriptionForm.attr('action'),
            data: {recipe_id: recipe_id, additional_info: additionalInfo, drugs: selectedDrugs, action: 'edit'},
            success: function (data) {
                data = JSON.parse(data);
                if(data.status){
                    editPrescriptionModal.modal('hide');
                } else {
                    editPrescriptionModal.modal('hide');
                    errorHandler(data.message);
                }
            }
        });
    });

});