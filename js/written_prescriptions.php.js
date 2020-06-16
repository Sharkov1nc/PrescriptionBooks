$(document).ready(function() {

    $(".delete-recipe").on("click", function () {
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

});