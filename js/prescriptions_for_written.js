$(document).ready(function() {
    let drugs = $(".drugs-select .list-group-item.drugs-item");
    let selectedDrugsContainer = $("#selected-drugs");
    var selectedDrugs = [];
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
        console.log(selectedDrugs);
    });


});