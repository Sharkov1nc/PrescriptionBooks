$(document).ready(function(){
    var excelForm = $("#excel-import");

    var excel = $("#excel-import input");
    $(".excel-import-button").on("click", function () {
        excel.click();
    });

    excel.on("change", function () {
        excelForm.submit();
    });

    excelForm.submit(function (e) {
        e.preventDefault();
        var data = new FormData(excelForm[0]);
        $.ajax({
            type: excelForm.attr('method'),
            url: excelForm.attr('action'),
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            data: data,
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                if(data.status){
                    location.reload();
                } else {
                    errorHandler(data.message);
                }
            }
        });
    });

});
