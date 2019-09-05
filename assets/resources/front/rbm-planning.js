var token = $('.save-form').find('[name="token"]').val();
var user_id = $('#user_id').val();
var role = $('#role_id');
var division = $('#division_id');
var national_zone = $('#national_zone_id');
var zone = $('#zone_id');
var region = $('#region_id');
var brand = $('#brand_id');
var activity = $('#activity_id');

var act_controller = controller;

var controller = (controller != 'user') ? 'user': controller;


role.on('change', function() {
    $this = $(this);
    role_id = $this.val();

    if(role_id != 6 ) {
        $('#add-doctor-link').hide();
    }
    else{
        $('#add-doctor-link').show();
    }

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

    shootAjax(
        $('#brand_id'), 
        {role: role_id, user_id: user_id, division_id: division_id, token: token } ,
        baseUrl + controller + '/get_tagged_brands',
        'fill_user_brands',
        'failed_callback',
        'POST'
    );

    shootAjax(
        $('#activity_id'), 
        {role: role_id, user_id: user_id, division_id: division_id, token: token } ,
        baseUrl + controller + '/get_tagged_activities',
        'fill_user_activity',
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

if(brand.length) {
    brand.on('change', function() {
        data = {};
        data.role = role.val();
        data.user_id = user_id;
        data.division_id = division.val();
        data.brand_id = brand.val();
        data.token = token;

        shootAjax(
            $('#activity_id'), 
            data ,
            baseUrl + controller + '/get_tagged_activities',
            'fill_user_activity',
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
    console.log('IN here');
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


        totalBudgetStrip = $("#totalBudgetStrip");
        if(totalBudgetStrip.length) {
            shootAjax(
                $('#region_id'),
                { region_id: $(this).val(), national_zone_id: national_zone.val(), division_id: data.division_id, zone_id: zone.val(), role: $("#role_id").val(), token: token },
                baseUrl + 'customer/getBudgetDetails',
                'fill_user_budget',
                'failed_callback',
                'POST'
            );
        }
        load_data(data);
    })
}

function fill_user_budget(response, element) {
    console.log(response.usedBudget);
    $("#totalUnits").html(response.totalBudget);
    $("#usedUnits").html(response.usedBudget);
    $("#balanceUnits").html(response.balanceAmount);
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

function fill_user_brands(response, brand) {
    brand.html(response.data).trigger('change');
}

function fill_user_activity(response, activity) {
    activity.html(response.data).trigger('change');
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
    $("#add-doctor-link").attr('href', baseUrl + 'doctor/addDoctorForRSM/'+$(this).val());
    $('[name="keywords"]').trigger('keyup');
})

$("#brand_id, #activity_id").on('change', function() {
    $('[name="keywords"]').trigger('keyup');
});

$(document).ready(function() {
    $('#role_id').trigger('change');
});

$(document).on('change', '.sp_by_prac', function() {
    var region_id = $('#region_id').val();
    var speciality_id = $(this).val();
    var doctor_id = $(this).attr('data-doctor');

    var sp_prac_data = {
        region_id : region_id, speciality_id: speciality_id, 
        doctor_id: doctor_id, token: token
    };

    $.ajax({
        url: baseUrl + 'doctor/update_sp_by_prac',
        data: sp_prac_data,
        type: 'POST',
        dataType: 'json',
        beforeSend: function(xhr, opts){
            // $('#preloader').show();
        },
        success: function(response) {
            // $('#preloader').hide();
            if(response.status) {
                alert('Speciality Updated!!')
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert('Oops ! there might be some problem, please try after some time');
            // $('#preloader').hide();
        }
    })

});

$(document).on('change', '.sp_by_qual', function() {
    var region_id = $('#region_id').val();
    var speciality_id = $(this).val();
    var doctor_id = $(this).attr('data-doctor');

    var sp_qual_data = {
        region_id : region_id, speciality_id: speciality_id, 
        doctor_id: doctor_id, token: token
    };

    $.ajax({
        url: baseUrl + 'doctor/update_sp_by_qual',
        data: sp_qual_data,
        type: 'POST',
        dataType: 'json',
        beforeSend: function(xhr, opts){
            // $('#preloader').show();
        },
        success: function(response) {
            // $('#preloader').hide();
            if(response.status) {
                alert('Speciality Updated!!')
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert('Oops ! there might be some problem, please try after some time');
            // $('#preloader').hide();
        }
    })

});

$(document).on('change', '.category', function() {
    var region_id = $('#region_id').val();
    var category_id = $(this).val();
    var doctor_id = $(this).attr('data-doctor');

    var category_data = {
        region_id : region_id, category_id: category_id, 
        doctor_id: doctor_id, token: token
    };

    $.ajax({
        url: baseUrl + 'doctor/update_category',
        data: category_data,
        type: 'POST',
        dataType: 'json',
        beforeSend: function(xhr, opts){
            // $('#preloader').show();
        },
        success: function(response) {
            // $('#preloader').hide();
            if(response.status) {
                alert('Category Updated!!')
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert('Oops ! there might be some problem, please try after some time');
            // $('#preloader').hide();
        }
    })

});

$(document).on('change', '.brand', function() {
    $this = $(this);
    var brand_id = $this.val();
    var brand_data = {
        brand_id: brand_id, 
        token: token
    };

    $.ajax({
        url: baseUrl + 'activity/get_activity',
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



$(document).on('click', '.plan-save-btn', function() {
    $this = $(this);
    parent_row = $this.closest('.plan-row');

    var brand_id = parent_row.find('.brand').val();
    var activity_id = parent_row.find('.activity').val();
    var amount = parent_row.find('.number').val();
    var doctor_id = parent_row.attr('data-doctor');
    var region_id = $('#region_id').val();

    var brand_data = {
        brand_id: brand_id, 
        activity_id: activity_id, 
        amount: amount, 
        doctor_id: doctor_id,
        region_id: region_id,
        token: token
    };

    $.ajax({
        url: baseUrl + 'doctor/save_plan',
        data: brand_data,
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function(xhr, opts){
            // $('#preloader').show();
        },
        success: function(response) {
            // $('#preloader').hide();
            if(! response.status){
                parent_row.siblings('p.error_msg').html(response.message);
            }else {
                var editBtn = $('<button></button>')
                .addClass('btn btn-primary edit-plan col-sm-12 m-b-5')
                .attr('data-id', response.plan_id)
                .attr('type', 'button')
                .text('Update');

                var deleteBtn = $('<button></button>')
                .addClass('btn btn-danger delete-plan col-sm-12 m-b-5')
                .attr('data-id', response.plan_id)
                .attr('type', 'button')
                .text('Delete');

                parent_row.siblings('p.error_msg').html('');
                alert('Record Saved!!');

                var newRow = parent_row.clone().removeClass('plan-row clearfix').addClass('plan-data-row clearfix');

                newRow.find('select.brand').find('option[value='+ response.brand_id +']').attr('selected', 'selected').end().attr('id', 'brand_'+ response.plan_id).end()
                .find('select.activity').find('option[value='+ response.activity_id +']').attr('selected', 'selected').end().attr('id', 'activity_'+ response.plan_id).end()
                .find('[name="number"]').attr('id', 'amount_'+ response.plan_id).end()
                .find('.col-sm-2').last().html('')
                
                editBtn.appendTo(newRow.find('.col-sm-2').last());
                deleteBtn.appendTo(newRow.find('.col-sm-2').last());
                
                newRow.appendTo(parent_row.siblings('.planned-data'));

                parent_row.find('select').val('').end().find('input[name="number"]').val('');
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert('Oops ! there might be some problem, please try after some time');
            // $('#preloader').hide();
        }
    })

});

$(document).on('click', '.delete-plan', function() {
    $this = $(this);
    plan_id = $this.attr('data-id');

    var delete_status = window.confirm('Are you sure, you want to delete the planned record?');

    if(delete_status) {
        $.ajax({
            url: baseUrl + 'doctor/delete_plan',
            data: {plan_id: plan_id, token: token},
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function(xhr, opts){
                // $('#preloader').show();
            },
            success: function(response) {
                // $('#preloader').hide();
                if(! response.status){
                    $this.closest('.plan-data-row').siblings('p.error_msg').html(response.message);
                }else {
                    // parent_row.siblings('p.error_msg').html('');
                    // $('[name=keywords]').trigger('keyup');
                    $this.closest('.plan-data-row').remove();
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                alert('Oops ! there might be some problem, please try after some time');
                // $('#preloader').hide();
            }
        })
    }
})

$(document).on('click', '.edit-plan', function() {
    $this = $(this);
    plan_id = $this.attr('data-id');
    
    brand_id = $('#brand_'+plan_id).val();
    activity_id = $('#activity_'+plan_id).val();
    amount = $('#amount_'+plan_id).val();

    $.ajax({
        url: baseUrl + 'doctor/update_plan',
        data: {plan_id: plan_id, token: token, brand_id: brand_id, activity_id: activity_id, amount: amount},
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function(xhr, opts){
            // $('#preloader').show();
        },
        success: function(response) {
            // $('#preloader').hide();
            if(! response.status){
                parent_row.siblings('p.error_msg').html(response.message);
            }else {
                alert('Record Updated')
                // $('[name=keywords]').trigger('keyup');
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert('Oops ! there might be some problem, please try after some time');
            // $('#preloader').hide();
        }
    })
})

var load_data = function(data) {    
    ajaxCall(baseUrl+'ho/show_records',data,function (result){
        if(result.activity_wise_amount.length == 0) {
            $('#chartdiv_1').hide();
        } else {
            $('#chartdiv_1').show();
        }

        if(result.speciality_18_19.length == 0) {
            $('#chartdiv_2').hide();
        } else {
            $('#chartdiv_2').show();
        }

        if(result.speciality_19_20.length == 0) {
            $('#chartdiv_3').hide();
        } else {
            $('#chartdiv_3').show();
        }

        chart_1.data = result.activity_wise_amount;
        chart_1.validateData();
    
        chart_2.data = result.speciality_18_19;
        chart_2.validateData();
        
        chart_3.data = result.speciality_19_20;
        chart_3.validateData();

        setTimeout(function(){
            chart_1.exporting.menu._element.style.display = 'none';
            chart_2.exporting.menu._element.style.display = 'none';
            chart_3.exporting.menu._element.style.display = 'none';
        }, 100);

    });
}


function ajaxCall(url, data, callback) {
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