if($('.dates').length){
$('.dates').datepicker({
    	dateFormat: 'yy-mm-dd', 
	});
}

function shootAjax(element, postdata, posturl, success_callback, failure_callback, type) {
    $.ajax({
        url: posturl,
        data: postdata,
        type: type,
        dataType: 'json',
        beforeSend: function(xhr, opts){
            // $('#preloader').show();
        },
        success: function(response) {
            // $('#preloader').hide();
            if(response.status) {
                window[success_callback](response, element);
            } else {
                window[failure_callback](response, element);
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            //alert('Oops ! there might be some problem, please try after some time');
            
            // $('#preloader').hide();
        }
    })
}

var load = function(elem, placeholder_txt, requestTo, data = {}, change_trigger = false, attempt){
	$('#'+ elem).select2({
		placeholder: "Select "+ placeholder_txt,
		allowClear: true,
		ajax: {
			url: baseUrl + requestTo,
			dataType: 'json',
			type: 'POST',
			data: function (params) {
				var query = {
					search: params.term,
					page: params.page || 1,
					token: $('.save-form').find('input[name=token]').val()
				}
				if(! $.isEmptyObject(data)){ 
					$.extend( query, data );
				}
				// Query parameters will be ?search=[term]&page=[page]
				return query;
				// Additional AJAX parameters go here; see the end of this chapter for the full code of this example
			},
			cache: true
		}
	});

	if(change_trigger){
		if(attempt == 'reset'){
			$('#' + elem).val(null).trigger('change');	
		}
		else{
			$('#' + elem).trigger('change');	
		}
	}
}

$(document).on('click', '.deleteButton', function (e) {
	e.preventDefault();
	$this = $(this);

	swal({
		title: 'Are you sure?',
		text: "You won't be able to revert this!",
		showCancelButton: true,
		confirmButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!',
		cancelButtonText: 'No, cancel!',
		showLoaderOnConfirm: true
	}).then(function (result) {
		if (result.value) {
			var data = {token: $("[name='token']").val()};

			$.ajax({
				url: $this.attr('href'),
				data: data,
				type: 'GET',
				dataType: 'JSON',
				beforeSend: function (xhr, opts) {
				},
				success: function (data) {
					if (data.status) {
						swal('Deleted!', data.msg)
						$('[name="keywords"]').trigger('keyup');
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
				}
			})
		}
	})
});

$('#reports_link').on('click', function (e){
    e.preventDefault();
    $this = $(this);
    $this.siblings('.submenu').show().parent().addClass('active-menu');
});

$('#reports_link').parent('li').on('click', function() {
    $(this).addClass('active-menu').find('.submenu').show();
})

$(document).on('click', function(e) {
    if($(e.target).attr('id') == 'report-menu' || $(e.target).attr('id') == 'reports_link' || $(e.target).hasClass('submenu-item')) {
        return;
    }
    $('#reports_link').parent().removeClass('active-menu').end().siblings('.submenu').hide();
})

function checkDebugTools() {
    if ((window.outerHeight - window.innerHeight) > 100) {
        // alert('Please Close Debug Tool!');
    }
}

if($('#role_id').length) {
	var division_id = $("#division_id").val();
    $('#role_id').on('change', function() {
        if($(this).val() == 6) {
            $('#pool_menu').show();
        }else {
            $('#pool_menu').show();
        }
    })
}
