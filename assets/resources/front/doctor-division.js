$(document).on('click', '.pool', function () {
    var division_id = $("#division_id").val();
    var activity_id = $(this).attr('data-activity-id');
    var token = $("[name='token']").val();
    var data = { token: token, division_id: division_id, activity_id: activity_id};
    var url = baseUrl + controller + '/getPooledActivitiesForDivision';
    shootAjax($(this), data, url, 'showpopup', 'errorFunction', 'POST');
});

$(document).on('click', '.reject', function () {
    var str = 'Once rejected you wont be able to participate. Are you sure you want to reject pool request? ';
    if (confirm(str)) {
        var region_id = $("#region_id").val();
        var token = $("[name='token']").val();
        var pool_id = $(this).attr('data-id');
        var data = { token: token, region_id: region_id, pool_id : pool_id};
        var url = baseUrl + controller + '/rejectPool';
        shootAjax($(this), data, url, 'showRejectAlert', 'errorFunction', 'POST');
    }
});

function showRejectAlert(response, element) {
    alert('Rejection completed!');
    $('[name=keywords]').trigger('keyup');
}

$(document).on('click', '.participate', function () {
    var pool_id = $(this).attr('data-id');
    var division_id = $("#division_id").val();
    var region_id = $("#region_id").val();
    
    var token = $("[name='token']").val();
    var data = { pool_id: pool_id, token: token, division_id: division_id, region_id: region_id };
    var url = baseUrl + controller + '/getBrandsForParticipate';
    shootAjax($(this), data, url, 'showParticipantPopup', 'errorFunction', 'POST');
});

$('.pool_activity_id').change(function() {
    var activity_id = $(this).val();
    var token = $("[name='token']").val();
    var division_id = $("#division_id").val();
    var doctor_id = $('#pool_doctor_id').val();

    console.log("Doctor ID:"+doctor_id);

    var data = { activity_id: activity_id, division_id: division_id, doctor_id: doctor_id, token: token };
    var url = baseUrl + controller + '/getBrandsFromActivities';
    shootAjax($(this), data, url, 'fillBrands', 'errorFunction', 'POST');
});

function fillBrands(response, element) {
    console.log(element);
    if(response.message.brands) {
        $('#brand_id').html(response.message.brands);
        // $('#brand_id').attr('disabled', true);
    }
    if(response.message.divisions) {
        $('.divisions').html('').append(response.message.divisions);
    }
}

function showpopup(response, element) {
    $("#brand_name").text(element.attr('data-brand-name'));
    $("#activity_name").text(element.attr('data-activity-name'));
    $("#amount").text(element.attr('data-amount'));
    $("#plan_id").val(element.attr('data-id'));
    $("#pool_region_id").val($("#region_id").val());
    $("#pool_doctor_id").val(element.attr('data-doctor-id'));

    $('.pool_activity_id').html('<option value="">-- Select Activity --</option>').append(response.message);
    $("#myModal").modal('show');
}

function showParticipantPopup(response, element) {

    if(response.showPopup) {
        $("#participant_activity_name").val(element.attr('data-activity-name'));
        $("#participant_pool_id").val(element.attr('data-id'));
        $("#participant_region_id").val($("#region_id").val());
        $('.brands').html('<option value="">--Select Brand--</option>').append(response.message);
        $("#participantModal").modal('show');
    } else {
        alert(response.message);
    }
}

function errorFunction() {
    alert('Something Went Wrong!');
}