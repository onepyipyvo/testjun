function handleForm(form, formData) {
    $.ajax({
        type: 'POST',
        contentType: false,
        processData: false,
        url: $(form).attr('action'),
        data: formData
    })
        .done(function(response) {
            var d = $.parseJSON(response);
            if(d.success) {
                buildTable(d.success);
            } else {
                showMessage('danger', d.error);
            }
        })
        .fail(function(data) {
            var d = $.parseJSON(response);
            if(d.success) {
                showMessage('success', 'Done')
            } else {
                showMessage('danger', d.error);
            }
        });
}
function showMessage(type, message) {
    var text_class = 'alert-danger';
    if (type == 'success') {
        text_class = 'alert-success';
    }
    $('.container').append('<div class="alert '+text_class+'" role="alert">' +
        message +
        '</div>');
}

function buildTable(data) {
    var t = '<table class="table">\n' +
        '  <thead><tr><th scope="col">Date</th><th scope="col">Temperature</th><th scope="col">Humidity</th><th scope="col">Pressure</th></tr></thead>';
    t+= '<tbody><tr><td>'+data.forecast_date+'</td><td>'+data.temperature+'</td><td>'+data.humidity+'</td><td>'+data.pressure+'</td></tr></tbody></table>';
    $("#result").html(t);
}

$(function() {
    $('#checkCity').submit(function (event) {
        event.preventDefault();
        var formData = new FormData($(this)[0]);
        handleForm($(this), formData);
    });
})