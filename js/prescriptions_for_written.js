$(document).ready(function() {
    let drugs = $(".drugs-select .list-group-item.drugs-item");
    let selectedDrugsContainer = $("#selected-drugs");
    let addPrescriptionModal = $("#add-prescription-modal");
    let addPrescriptionForm = $("#add-prescription-form");
    let searchForm = $('#prescription-search');
    let searchModal = $("#search-modal");
    let tr, tBody = $("#prescriptions-table tbody");
    let selectedDrugs = [];
    let prescriptionBookId = null;

    $(document).on("click", '.add-prescription', function () {
        prescriptionBookId = this.id;
        $("#patient-name").val(this.dataset.user);
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

    selectedDrugsContainer.on('click', '.badge-drugs', function () {
        let drug = this;
        $.each(selectedDrugs, function(key, val) {
            if (val && val.id == drug.id) {
                selectedDrugs.splice(key, 1);
                drug.remove();
            }
        });
    });

    addPrescriptionModal.on("hidden.bs.modal", function () {
        selectedDrugsContainer.empty();
        selectedDrugs = [];
    });

    addPrescriptionForm.on('submit', function (e) {
        e.preventDefault();
        let additionalInfo = $(this).find('textarea[name="additional_information"]').val();
        $.ajax({
            type: addPrescriptionForm.attr('method'),
            url: addPrescriptionForm.attr('action'),
            data: {prescription_id: prescriptionBookId, additional_info: additionalInfo, drugs: selectedDrugs, action: 'add'},
            success: function (data) {
                data = JSON.parse(data);
                if(data.status){
                    addPrescriptionModal.modal('hide');
                    $("#prescrition-row-" + prescriptionBookId).remove();
                }
            }
        });
    });

    $("#preview-recipe").on("click", function () {

        var form = document.createElement("form");
        form.setAttribute("method", addPrescriptionForm.attr('method'));
        form.setAttribute("action", addPrescriptionForm.attr('action'));

        form.setAttribute("target", "_blank");

        let additionalInfo = document.createElement("input");
        additionalInfo.setAttribute("name", "additional_info");
        additionalInfo.setAttribute("value", $('textarea[name="additional_information"]').val());
        let names = document.createElement("input");
        names.setAttribute("name", "names");
        names.setAttribute("value", $('#patient-name').val());
        let action = document.createElement("input");
        action.setAttribute("name", "action");
        action.setAttribute("value", "preview");
        let selectedItems = document.createElement("input");
        selectedItems.setAttribute("name", "drugs");
        selectedItems.setAttribute("value", JSON.stringify(selectedDrugs));

        form.appendChild(additionalInfo);
        form.appendChild(names);
        form.appendChild(action);
        form.appendChild(selectedItems);
        document.body.appendChild(form);
        form.submit();

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

                        tr = '<tr id="prescrition-row-'+ val.id +'"> ' +
                            '<td class="id-th">'+ (tBody[0].rows.length + 1) +'</td>' +
                            '<td>'+ val.user_fname + ' ' + val.user_lname +'</td>' +
                            '<td>'+ (!val.recipe_id ? "<span class='badge badge-info'>Няма изписани рецепти</span>" : "<span class='badge badge-success'>Преглед на рецепта</span></th>") +'</td>' +
                            '<td>'+ (val.recipe_date ? val.recipe_date : "-")  +'</td>' +
                            '<td>' +
                            '<a class="btn icon-button add-prescription" id="'+ val.id +'" data-user="'+ val.user_fname + ' ' + val.user_lname +'" data-toggle="modal" data-target="#add-prescription-modal" > <i class="s7-note"></i></a> </td> </tr>';
                        tBody.append(tr);
                        searchModal.modal('hide');
                    });
                }
            }
        });
    });

});