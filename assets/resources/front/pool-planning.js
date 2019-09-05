var token = $('.save-form').find('[name="token"]').val();
var user_id = $('#user_id').val();
var role = $('#role_id');
var division = $('#division_id');
var national_zone = $('#national_zone_id');
var zone = $('#zone_id');
var region = $('#region_id');

var brand = $('.brand_id');
var controller = (controller != 'customer') ? 'customer': controller;

role.on('change', function() {
    $this = $(this);
    role_id = $this.val();
    $('#add-doctor-link').hide();
    
    shootAjax(
        $('#division_id'), 
        {role: role_id, user_id: user_id, token: token } ,
        baseUrl + controller + '/get_tagged_division',
        'fill_user_division',
        'failed_callback',
        'POST'
    );
});

division.on('change', function() {
    $this = $(this);
    division_id = $this.val();
    role_id = $('#role_id').val();

    shootAjax(
        $('#national_zone_id'), 
        {role: role_id, user_id: user_id, division_id: division_id, token: token } ,
        baseUrl + controller + '/get_tagged_national_zone',
        'fill_user_national_zone',
        'failed_callback',
        'POST'
    );
});

if(national_zone.length) {
    national_zone.on('change', function() {

        data = {};
        data.role = role.val();
        data.user_id = user_id;
        data.division_id = division.val();
        data.national_zone_id = national_zone.val();
        data.token = token;

        shootAjax(
            $('#zone_id'), 
            data ,
            baseUrl + controller + '/get_tagged_zone',
            'fill_user_zone',
            'failed_callback',
            'POST'
        );
    })
}

if(zone.length) {
    zone.on('change', function() {

        data = {};
        data.role = role.val();
        data.user_id = user_id;
        data.division_id = division.val();
        if(national_zone.length) {
            data.national_zone_id = national_zone.val();
        }
        data.zone_id = zone.val();
        data.token = token;


        shootAjax(
            $('#region_id'), 
            data,
            baseUrl + controller + '/get_tagged_region',
            'fill_user_region',
            'failed_callback',
            'POST'
        );
    })
}

if(region.length) {
    region.on('change', function() {

        data = {};
        data.role = role.val();
        data.user_id = user_id;
        data.division_id = division.val();
        if(national_zone.length) {
            data.national_zone_id = national_zone.val();
        }
        if(zone.length) {
            data.zone_id = zone.val();
        }
        data.region_id = region.val();
        data.token = token;


        shootAjax(
            $('#hq_id'), 
            data,
            baseUrl + controller + '/get_tagged_hq',
            'fill_user_region',
            'failed_callback',
            'POST'
        );
    })
}

function fill_user_division(response, division) {
    var optionstr = "";

    if(response.data.divisions) {
        for (const key in response.data.divisions) {
            if (response.data.divisions.hasOwnProperty(key)) {
                if(response.data.divisions[key] === null) {
                    continue;
                }
                optionstr += "<option value='"+  key +"'>"+ response.data.divisions[key] +"</option>";
            }
        }
    }

    division.html(optionstr).trigger('change'); 
}

function fill_user_national_zone(response, national_zone) {
    national_zone.html(response.data).trigger('change');
}

function fill_user_zone(response, region) {
    zone.html(response.data).trigger('change');
}

function fill_user_region(response, region) {
    region.html(response.data).trigger('change');
}

function failed_callback() {
    alert('Something went wrong, please refresh & try again');
} 

$('#region_id').on('change', function(){
    // $("#add-doctor-link").attr('href', baseUrl + 'doctor/addDoctorForRSM/'+$(this).val());
    $('[name="keywords"]').trigger('keyup');
})

$(document).ready(function() {
    $('#role_id').trigger('change');
});

$(document).on('change', '.brand', function() {
    $this = $(this);
    var brand_id = $this.val();

    var brand_data = {
        brand_id: brand_id, 
        token: token
    };

    $.ajax({
        url: baseUrl + 'activity_2020/get_activity',
        data: brand_data,
        type: 'POST',
        beforeSend: function(xhr, opts){
            // $('#preloader').show();
        },
        success: function(response) {
            // $('#preloader').hide();
            $this.parent().siblings().find('.activity').html(response);
            // alert('Speciality Updated!!')
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert('Oops ! there might be some problem, please try after some time');
            // $('#preloader').hide();
        }
    })

});