(function($){
    if($('#speciality').length){
        load('speciality', 'Select Doctor Speciality', 'speciality/options');
    }

    if($('#study_id').length){
        load('study_id', 'Select Study', 'study/options');
    }

    if($('#doctor_id').length){
        var data = {
            'mr_id': $('input[name="mr_id"]').val()
        };
        load('doctor_id', 'Select Doctor', 'doctor/options', data);
    }

    function ajaxCall(url, type = 'POST', data, callback) {
        $.ajax({
            url: url,
            data: data,
            type: type,
            dataType: 'JSON',
            cache: false,
            success: function(data) {
                return callback(data);            
            },
            error: function(jqXHR, textStatus, errorThrown){
                return callback(errorThrown);
            }
        })
    }
})(jQuery)
