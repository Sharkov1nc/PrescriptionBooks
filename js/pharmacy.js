$(document).ready(function() {
    let searchModal = $("#search-modal");
    let searchForm = $("#recipe-search");
    let tr, tBody = $("#written-prescriptions-table tbody");
    let foundResults = false;
    searchModal.modal("show");

    searchForm.submit(function (e) {
        if(!foundResults){
            e.preventDefault();
        } else {
            foundResults = false;
            return;
        }
        let hash = $(this).find('#hash').val();
        $.ajax({
            type: "GET",
            url: "../backend/prescriptions_controller.php",
            data: {hash: hash, action: "search_hash"},
            success: function (data) {
                data = JSON.parse(data);
                if(data && data.recipe_id){
                    tBody.empty();
                    tr = '<tr id="recipe-row-'+ data.recipe_id +'"> ' +
                        '<td class="id-th">'+ (tBody[0].rows.length + 1) +'</td>' +
                        '<td>'+ data.user_fname + ' ' + data.user_lname +'</td>' +
                        '<td><a href="../backend/prescriptions_controller.php?action=print&recipe_id='+ data.recipe_id +'" target="_blank"><span class="badge badge-success">Преглед на рецепта</span></a></th></td>' +
                        '<td>'+ data.recipe_date  +'</td>' +
                        '<td class="recipe-status"><a id="'+ data.recipe_id +'" class="mark-as-taken"><span class="badge badge-success">Маркирай като взета</span></a></td>' +
                        '</tr>';
                    tBody.append(tr);
                    foundResults = true;
                    searchForm.submit();
                }
                searchModal.modal('hide');
            }
        });
    });

    $(document).on("click", ".mark-as-taken", function () {
        let recipeId = this.id;
        $.ajax({
            type: "POST",
            url: "../backend/prescriptions_controller.php",
            data: {recipe_id: recipeId, action: "mark-as-taken"},
            success: function (status) {
                if(JSON.parse(status)) {
                    $(".recipe-status").empty().html('<span class="badge badge-success">Взета от пациент</span>');
                }
            }
        });
    });
});