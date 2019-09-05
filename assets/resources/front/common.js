$(document).ready(function() {
    if($("#region_id").length) {
        $('#region_id').on('change', function () {
            data = { region_id : $("#region_id").val(), token: $('[name=token]').val() };
            shootAjax(
                $('#region_id'),
                data,
                baseUrl + 'notification/getNotificationCount',
                'fillNotificationCount',
                'errorFunction',
                'POST'
            );
        });
    }
});

function fillNotificationCount(response) {
    if (response.status) {
        $("#notification-count").text(response.data);
    }
}

function errorFunction() {
    alert('something Went Wrong');
}

function addUnits(units = 0) {
    units = parseInt(units);

    var usedUnits = $('#usedUnits').text();
    var balanceUnits = $('#balanceUnits').text();

    updatedUsedUnits = parseInt($('#usedUnits').text()) + units;
    updatedBalanceUnits = parseInt($('#balanceUnits').text()) - units;
    
    $('#usedUnits').text(updatedUsedUnits);
    $('#balanceUnits').text(updatedBalanceUnits);
}

function removeUnits(units = 0) {
    units = parseInt(units);

    var usedUnits = $('#usedUnits').text();
    var balanceUnits = $('#balanceUnits').text();

    updatedUsedUnits = parseInt(usedUnits) - units;
    updatedBalanceUnits = parseInt(balanceUnits) + units;

    $('#usedUnits').text(updatedUsedUnits);
    $('#balanceUnits').text(updatedBalanceUnits);
}