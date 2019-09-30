(function ($) {
    $(document).on('click', '.approve', function (e) {
        e.preventDefault();
        
        var id = $(this).attr('data-id');
        var status = $(this).attr('data-status');

        var url = baseUrl + "/asm_lists/change_doctor_status";
        
        var data = {
            id:id,
            token: $('meta[name="csrf-token"]').attr('content'),
            status: status
        };
        
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function (xhr, opts) {},
            success: function (data) {
                console.log(data);
                if (data) {
                    swal({
                        title: 'Success',
                        text: data.message,
                    }).then((result) => {

                        if (!window.redirect) {
                            if (data.redirectTo) {
                                var redirectUrl = baseUrl + data.redirectTo;
                            } else if (data.redirect) {
                                var redirectUrl = baseUrl + controller + '/' + data.redirect;
                            } else {
                                var redirectUrl = baseUrl + controller + '/lists';
                            }

                            var loc = redirectUrl + '?ab=' + new Date().getTime(); // or a new URL
                            window.location.href = loc;
                        } 
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {}
        }); 
    });

    $(document).on('click', '.doctorAction', function (e) {
        e.preventDefault();
        $this = $(this).closest('form');

        if (!$('input[name="id[]"]:checked').length) {
            swal('No records selected!');
            return;
        }

        var status = $(this).attr('data-status');
        
        swal({
            title: 'Are you sure?',
            text: "",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No, cancel!',
            showLoaderOnConfirm: true
        }).then(function (result) {
            if (result.value) {
                
                
                var data = $this.serialize() + `&status=${status}`;
                var url = baseUrl + "/"+ controller +"/change_doctor_status";
        
                $.ajax({
                    url: url,
                    data: data,
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function (xhr, opts) {},
                    success: function (data) {
                        console.log(data);
                        if (data) {
                            swal({
                                title: 'Success',
                                text: data.message,
                            }).then((result) => {
        
                                if (!window.redirect) {
                                    if (data.redirectTo) {
                                        var redirectUrl = baseUrl + data.redirectTo;
                                    } else if (data.redirect) {
                                        var redirectUrl = baseUrl + controller + '/' + data.redirect;
                                    } else {
                                        var redirectUrl = baseUrl + controller + '/lists';
                                    }
        
                                    var loc = redirectUrl + '?ab=' + new Date().getTime(); // or a new URL
                                    window.location.href = loc;
                                } 
                            });
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {}
                })
            }
        });
    });

    $(document).on('click', '.asm_approve', function (e) {
        e.preventDefault();
        
        var id = $(this).attr('data-id');
        var status = $(this).attr('data-status');

        var url = baseUrl + "/mr_lists/change_doctor_status";
        
        var data = {
            id:id,
            token: $('meta[name="csrf-token"]').attr('content'),
            status: status
        };
        
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function (xhr, opts) {},
            success: function (data) {
                console.log(data);
                if (data) {
                    swal({
                        title: 'Success',
                        text: data.message,
                    }).then((result) => {

                        if (!window.redirect) {
                            if (data.redirectTo) {
                                var redirectUrl = baseUrl + data.redirectTo;
                            } else if (data.redirect) {
                                var redirectUrl = baseUrl + controller + '/' + data.redirect;
                            } else {
                                var redirectUrl = baseUrl + controller + '/lists';
                            }

                            var loc = redirectUrl + '?ab=' + new Date().getTime(); // or a new URL
                            window.location.href = loc;
                        } 
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {}
        }); 
    });

})(jQuery);