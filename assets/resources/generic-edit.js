(function($){

	$('.save-form').on('submit',function(e){
		e.preventDefault();		
		var formObj = $(this);
		var formUrl = formObj.attr('action');
		var users_type = formObj.data('usertype');
		
		$.each(formObj.find('input, select, textarea'), function(i, field) {
  			var elem = $('[name="'+ field.name +'"]').parent();
			
			if(elem.hasClass('select2-hidden-accessible')){
				elem.next().removeClass('error').siblings('p').remove()
			}		
  			else{
  				elem.removeClass('error')
					.next('label.error').remove();
  			}
  		});

		if(window.FormData != 'undefined'){
            var formData = new FormData(formObj[0]);
			$.ajax({
				url: formUrl,
				type: 'POST',
				data: formData,
				dataType: 'JSON',
				mimeType: 'multipart/form-data',
                contentType: false,
                cache: false,
                processData: false,
				beforeSend: function(xhr, opts){
					$('#preloader').show();
				},
				success: function(data){										
					if (data.status == true) {
						/*change to redirect to listing page*/
						$('#preloader').hide();
						
						if(data.redirect){
							var redirectUrl = baseUrl + controller + '/' + data.redirect;
						}
						else{
							var redirectUrl = baseUrl + controller + '/lists';	
						}
						var loc = redirectUrl + '?ab=' + new Date().getTime();

						var resp_msg = (data.message) ? data.message : 'Record added successfully';
						swal({
							title: 'Success', 
							text: resp_msg,
						}).then((result) => {
							window.location.href = loc  
						});
						

					}
					else{
						if(data.errors)	{
							$.each(data.errors, function(key, val) {
			                	var elem = $('[name="'+ key +'"]', formObj).parent();
								
								if(elem.hasClass('select2-hidden-accessible')){
									elem.next().addClass('error').siblings('p').remove().end().after(val);
								}
								else{
									elem.removeClass('error')
										.next('label.error').remove()
									.end()
										.addClass('error').after(val);	
								}
				            });

				            $('.form-line.error').eq(0).addClass('focused');
						}

			            if(data.message){
			            	swal({ 
			            		title: 'Error!', 
			            		text: data.message
			            	});
			            }

			            $('#preloader').hide();
			        }			        			       	
				},
				error: function(jqXHR, textStatus, errorThrown){
					alert('Problems while updating data!')
					$('#preloader').hide();
				}
			});
		}
	});
})(jQuery)