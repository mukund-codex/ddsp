var token = $('.save-form').find('[name="token"]').val();
var user_id = $('#user_id').val();
var role = $('#role_id');
var division = $('#division_id');
var national_zone = $('#national_zone_id');
var zone = $('#zone_id');
var region = $('#region_id');

var brand = $('.brand_id');
var pageController = controller;
var controller = (controller != 'customer') ? 'customer': controller;

role.on('change', function() {
    $this = $(this);
    role_id = $this.val();
    // $('#add-doctor-link').hide();
    
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

        shootAjax(
            $('#region_id'),
            { region_id: $(this).val(), national_zone_id: national_zone.val(), division_id: data.division_id, zone_id: zone.val(), role: $("#role_id").val(), token: token },
            baseUrl + 'customer/getBudgetDetails',
            'fill_user_budget',
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

function fill_user_budget(response, element) {
    console.log(response.usedBudget);
    $("#totalUnits").html(response.totalBudget);
    $("#usedUnits").html(response.usedBudget);
    $("#balanceUnits").html(response.balanceAmount);
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



$(document).on('click', '.plan-save-btn', function() {
    $this = $(this);
    parent_row = $this.closest('.plan-row');

    var brand_id = parent_row.find('.brand').val();
    var activity_id = parent_row.find('.activity').val();
    var amount = parent_row.find('.number').val();
    var doctor_id = parent_row.attr('data-doctor');
    var region_id = $('#region_id').val();
    var module_name = $('#module').val();

    var brand_data = {
        brand_id: brand_id, 
        activity_id: activity_id, 
        amount: amount, 
        doctor_id: doctor_id,
        region_id: region_id,
        module: module_name,
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

                if(response.show_pool) {
                    var poolBtn = $('<button></button>')
                    .addClass('btn btn-primary pool col-sm-12 m-b-5')
                    .attr('data-id', response.plan_id)
                    .attr('data-doctor-id', response.doctor_id)
                    .attr('data-brand-id', response.brand_id)
                    .attr('data-activity-id', response.activity_id)
                    .attr('data-amount', response.amount)
                    .attr('type', 'button')
                    .text('Pool');
                }
                

                parent_row.siblings('p.error_msg').html('');
                alert('Record Saved!!');

                var newRow = parent_row.clone().removeClass('plan-row clearfix').addClass('plan-data-row clearfix');

                newRow.find('select.brand').find('option[value='+ response.brand_id +']').attr('selected', 'selected').end().attr('id', 'brand_'+ response.plan_id).end()
                .find('select.activity').find('option[value='+ response.activity_id +']').attr('selected', 'selected').end().attr('id', 'activity_'+ response.plan_id).end()
                .find('[name="number"]').attr('id', 'amount_'+ response.plan_id).end()
                .find('.col-sm-3').last().html('')
                
                editBtn.appendTo(newRow.find('.col-sm-3').last());
                deleteBtn.appendTo(newRow.find('.col-sm-3').last());
                /* 
                if(response.show_pool) {
                    poolBtn.appendTo(newRow.find('.col-sm-3').last());
                } */
                
                newRow.appendTo(parent_row.siblings('.planned-data'));

                parent_row.find('select').val('').end().find('input[name="number"]').val('');

                addUnits(amount);
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
    var module_name = $('#module').val();
    var amount = $this.closest('.plan-data-row').find('.number').val();

    var delete_status = window.confirm('Are you sure, you want to delete the planned record?');

    if(delete_status) {
        $.ajax({
            url: baseUrl + 'doctor/delete_plan',
            data: {plan_id: plan_id, token: token, module: module_name},
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


                    if(pageController == 'customer') {
                        $this.closest('.plan-data-row').remove();
                    } else if(pageController == 'pool') {
                        $("[name=keywords]").trigger('keyup');
                    }

                    
                    removeUnits(amount);
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
    var module_name = $('#module').val();

    $.ajax({
        url: baseUrl + 'doctor/update_plan',
        data: {
            plan_id: plan_id, 
            token: token, 
            brand_id: brand_id, 
            activity_id: activity_id, 
            amount: amount,
            module: module_name
        },
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function(xhr, opts){
            // $('#preloader').show();
        },
        success: function(response) {
            console.log(response);
            // $('#preloader').hide();
            if(! response.status){
                parent_row.siblings('p.error_msg').html(response.message);
            }else {
                alert('Record Updated')
                // var oldElement = $('#amount_'+plan_id).siblings('.existingValue');
                var oldValue = $('#amount_'+plan_id).siblings('.existingValue').val();                
                var newValue = $('#amount_'+plan_id).val();


                if(oldValue > newValue) {
                    calculatedValue = parseInt(oldValue) - parseInt(newValue);
                    removeUnits(calculatedValue);
                    
                } else if(oldValue < newValue) {
                    calculatedValue = parseInt(newValue) - parseInt(oldValue);
                    addUnits(calculatedValue);
                }

                $('#amount_'+plan_id).siblings('.existingValue').val(newValue);
                // $('[name=keywords]').trigger('keyup');

                if(response.show_pool) {
                    var poolBtn = $('<button></button>')
                    .addClass('btn btn-primary pool col-sm-12 m-b-5')
                    .attr('data-id', response.plan_id)
                    .attr('data-doctor-id', response.doctor_id)
                    .attr('data-brand-id', response.brand_id)
                    .attr('data-activity-id', response.activity_id)
                    .attr('data-amount', response.amount)
                    .attr('type', 'button')
                    .text('Pool');
                }
                
                $this.siblings('.pool').remove();
                if(response.show_pool) {
                    poolBtn.appendTo($this.parent());
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert('Oops ! there might be some problem, please try after some time');
            // $('#preloader').hide();
        }
    })
})