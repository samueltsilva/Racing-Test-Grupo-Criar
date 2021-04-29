var requestUtils = {
    showProgress: true,
    showError: true,
    url: 'http://localhost:8000/',
    doPostFile: function (path, dataItens, callback) {
        loading();
        $.ajax({
            url: requestUtils.url + path,
            type: "POST",
            // dataType: 'json',
            headers: { },
            data: dataItens,
            processData: false,
            contentType: false,
            statusCode: {
                201: callback,
                400: function () {
                    if (requestUtils.showError) {

                    }
                },
                500: function (error) {}
            },
        });
    },
};
function loading() {
    if (requestUtils.showProgress) {
        Swal.fire({
            title: 'Processando ...',
            timerProgressBar: true,
            onBeforeOpen: () => {
                Swal.showLoading()
            }
        });
    }
}
