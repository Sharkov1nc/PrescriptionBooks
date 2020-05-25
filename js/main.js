let errorHandler = (error) => {
    let modal = $("#error-handler").modal();
    $("#error-handler .modal-body").text(error);
    modal.show();
};