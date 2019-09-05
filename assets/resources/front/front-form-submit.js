(function($){

	$('.save-form').on('submit',function(e){
		e.preventDefault();
        console.log('comes in');
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
				success: function(data, textStatus, jqXHR){
					if (data.status == true) {				
						/*change to redirect to listing page*/
                        var resp_msg = (data.message) ? data.message : 'Record added successfully';
						alert(resp_msg);
                        if (!window.redirect) {
                            if (data.redirectTo) {
                                var redirectUrl = baseUrl + '/' + data.redirectTo;
                            }
                            var loc = redirectUrl + '?ab=' + new Date().getTime();
                            window.location.href = loc;
                        }
                        else {
                            formObj[0].reset();
                        }
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
			            	alert(data.message);
			            }
			        }
			        $('#preloader').hide();					
				},
				error: function(jqXHR, textStatus, errorThrown){
					alert('Problems while saving data!')
					$('#preloader').hide();
				}
			});
		}
	});
})(jQuery)	