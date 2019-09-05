var token = $('meta[name="csrf-token"]').attr('content');
var division = $('#division_id');
var national_zone = $('#national_zone_id');
var zone = $('#zone_id');
var region = $('#region_id');

var brand = $('.brand_id');
var controller = (controller != 'user') ? 'user': controller;

var load_data = function(data) {    
    ajaxCall(baseUrl+'ho/show_records',data,function (result){
        if (result.activity_wise_amount) {
            if (result.activity_wise_amount.length == 0) {
                $('#chartdiv_1').hide();
            } else {
                $('#chartdiv_1').show();
            }
            if ($('#chartdiv_1').length) {
                chart_1.data = result.activity_wise_amount;
                chart_1.validateData();
            }
        }

        if (result.speciality_18_19) {
            if (result.speciality_18_19.length == 0) {
                $('#chartdiv_2').hide();
            } else {
                $('#chartdiv_2').show();
            }
            if ($('#chartdiv_2').length) {
                chart_2.data = result.speciality_18_19;
                chart_2.validateData();
            }
        }


        if (result.speciality_19_20) {
            if (result.speciality_19_20.length == 0) {
                $('#chartdiv_3').hide();
            } else {
                $('#chartdiv_3').show();
            }
            if ($('#chartdiv_3').length) {
                chart_3.data = result.speciality_19_20;
                chart_3.validateData();
            }
        }
    });
}

load_data({token: token});

division.on('change', function() {
    $this = $(this);
    division_id = $this.val();

    data = {};
    data.division_id = division.val();
    data.token = token;

    shootAjax(
        $('#national_zone_id'), 
        data,
        baseUrl + 'ho/get_tagged_national_zone',
        'fill_user_national_zone',
        'failed_callback',
        'POST'
    );

    load_data(data);
});

if(national_zone.length) {
    national_zone.on('change', function() {

        data = {};
        data.division_id = division.val();
        data.national_zone_id = national_zone.val();
        data.token = token;

        shootAjax(
            $('#zone_id'), 
            data ,
            baseUrl + 'ho/get_tagged_zone',
            'fill_user_zone',
            'failed_callback',
            'POST'
        );

        load_data(data);
    })
}

if(zone.length) {
    zone.on('change', function() {

        data = {};
        data.division_id = division.val();
        if(national_zone.length) {
            data.national_zone_id = national_zone.val();
        }
        data.zone_id = zone.val();
        data.token = token;


        shootAjax(
            $('#region_id'), 
            data,
            baseUrl + 'ho/get_tagged_region',
            'fill_user_region',
            'failed_callback',
            'POST'
        );

        load_data(data);
    })
}

if(region.length) {
    region.on('change', function(){
        data = {};
        data.division_id = division.val();
        data.national_zone_id = national_zone.val();
        data.zone_id = zone.val();
        if(region.length) {
            data.region_id = region.val();
        }
        data.token = token;

        load_data(data);
        $("[name=keywords]").trigger('keyup');
    });
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
    national_zone.html('<option value="">-- All --</option>'+response.data).trigger('change');
}

function fill_user_zone(response, region) {
    zone.html('<option value="">-- All --</option>' +response.data).trigger('change');
}

function fill_user_region(response, region) {
    region.html('<option value="">-- All --</option>' +response.data).trigger('change');
}

function failed_callback() {
    alert('Something went wrong, please refresh & try again');
} 

function ajaxCall(url, data, callback) {
    console.log(data);
    $.ajax({
        url: url,
        data: data,
        type: 'POST',
        dataType: 'JSON',
        cache: false,
        beforeSend: function() {
            $('#preloader').show();
        },
        success: function(data) {
            $('#preloader').hide();
            return callback(data);            
        },
        error: function(jqXHR, textStatus, errorThrown){
            return callback(errorThrown);
        }
    })
}

