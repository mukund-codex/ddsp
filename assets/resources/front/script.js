$("#legacy_code").keyup(function() {
    legacy_code = $(this).val();
    var token = $("[name='token']").val();
    var data = { legacy_code: legacy_code, token: token };
    var url = baseUrl + controller + '/loadDoctorByLegacy';
    shootAjax($(this), data, url, 'setValues', 'errorFunction', 'POST');
});

function setValues(data) {
    console.log(data);
    if (data.doctor_name) {
        $("[name='doctor_name']").val(data.doctor_name);
        $("[name='doctor_name']").attr('readonly', true);
    } else {
        $("[name='doctor_name']").val('');
        $("[name='doctor_name']").attr('readonly', false);
    }
}

function errorFunction() {
    alert('Something Went Wrong!');
}