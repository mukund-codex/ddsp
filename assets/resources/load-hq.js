(function($){

	var division = $('#division_id');

	/*counter to reset select2 values on a change event of the element*/
	var r_load_cnt = 0; //Region: 0 counter stands for when no change event is triggered
	var a_load_cnt = 0; //Area: 0 counter stands for when no change event is triggered
	var c_load_cnt = 0;
	var reg_load_cnt = 0;
	var loadRegion = function(elem, placeholder_txt, controller, change_trigger = false, attempt, data = ''){

		$('#'+ elem).select2({
			placeholder: "Select "+ placeholder_txt,
		    allowClear: true,
		    ajax: {
			    url: baseUrl + controller + '/division_wise_regions',
			    dataType: 'json',
			    type: 'POST',
			    data: function (params) {
				    var query = {
				        search: params.term,
				        page: params.page || 1,
				        token: $('.save-form').find('input[name=token]').val()
				    }

				    if(data.id){ query['id'] = data.id; }
				    //console.log(query);
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

	if (division.length) {
		division.on('change', function () {
			c_load_cnt++;
			var c_attempt_to = (c_load_cnt > 1) ? 'reset' : 'load';

			data = { id: $(this).val() }
            loadRegion('region_id', 'Region', 'region', true, c_attempt_to, data);
		});
	}

})(jQuery);
