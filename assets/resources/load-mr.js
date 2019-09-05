(function($){


	var reporting_mgr = $('#reporting-mgr');

	/*counter to reset select2 values on a change event of the element*/
	var z_count = 0; //counter for zone change event
	var r_count = 0; // counter for region change event
	var c_load_cnt = 0;
	var z_load_cnt = 0;
	var r_load_cnt = 0;

	var load = function(elem, placeholder_txt, controller, change_trigger = false, attempt, data = ''){

		$('#'+ elem).select2({
			placeholder: "Select "+ placeholder_txt,
		    allowClear: true,
		    ajax: {
			    url: baseUrl + controller + '/options',
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

	var load_fforce_head = function(role){
		reporting_mgr.select2({
		    placeholder: "Select "+ role,
		    allowClear: true,
		    ajax: {
			    url: baseUrl + 'users/options',
			    dataType: 'JSON',
			    type: 'POST',
			    data: function (params) {
				    var query = {
				        search: params.term,
				        page: params.page || 1,
				        role: role,
				        token: $('.save-form').find('input[name=token]').val()
				    }
				    // Query parameters will be ?search=[term]&page=[page]
				    return query;
				    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
				},
                cache: true
			}
		});

		reporting_mgr.trigger('change');
	};	

	var get_mgr_data = function(id, attempt){

		$.ajax({
			url: baseUrl + 'users/user_info',
			data: {id: id, token: $('.save-form').find('input[name=token]').val()},
			type: 'POST',
			dataType:'JSON',
			success: function(data, textStatus, jqXHR){

				$('.fill').each(function (){
					var fieldName = $(this).attr('name');
					$(this).val(data[fieldName]);
				})

				if(zone.length){
					var z_data = {}
					z_data.id = data['users_national_zone_id'];

					load('zone_id', 'Zone', 'geography/zone', true, attempt, z_data);
				}

				if(region.length){

					var r_data = {}
					r_data.id = data['users_zone_id'];

					load('region_id', 'Region', 'geography/region', true, attempt, r_data);
				}

				if(area.length){

					var a_data = {}
					a_data.id = data['users_region_id'];

					load('area_id', 'Area', 'geography/area', true, attempt, a_data);
				}

				if(city.length){

					var c_data = {}
					c_data.id = data['users_area_id'];

					load('city_id', 'City', 'geography/city', true, attempt, c_data);
				}
			}
		});
	}

	if(reporting_mgr.length){

		var role = reporting_mgr.data('role');
		var attempt = 0;

		reporting_mgr.on('change', function(){
			attempt++;
			var counter = (attempt > 1) ? 'reset' : 'load';

			if( $(this).val() ){
				get_mgr_data($(this).val(), counter);	
			} 
		});

		load_fforce_head(role);
	}

	if($('#reporting-mgr').length) {
        load_fforce_head('MR');
    }

})(jQuery);