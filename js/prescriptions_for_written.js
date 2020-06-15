$(document).ready(function() {
    let drugs = $(".drugs-select .list-group-item.drugs-item");
    let selectedDrugsContainer = $("#selected-drugs");
    let addPrescriptionModal = $("#add-prescription-modal");
    let addPrescriptionForm = $("#add-prescription-form");
    let selectedDrugs = [];
    let prescriptionBookId = null;

    $(".add-prescription").on("click", function () {
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
            data: {prescription_id: prescriptionBookId, additional_info: additionalInfo, drugs: selectedDrugs, action: 'add_prescription'},
            success: function (data) {
                data = JSON.parse(data);
                if(data.status){
                    addPrescriptionModal.modal('hide');
                    $("#prescrition-row-" + prescriptionBookId).remove();
                }
            }
        });
    });

});