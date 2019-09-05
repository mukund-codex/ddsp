(function ($) {
	// var division = $('#division_id');
	var national_zone = $('#national_zone_id');
	var zone = $('#zone_id');
	var region = $('#region_id');
	var reporting_mgr = $('#users_parent_id');
	var area = $('#area_id');
	var city = $('#city_id');

	var users_city_id = $('#users_city_id');
	var doctor_users_id = $('#doctor_users_id');
	var users_name = $('#users_name');

	var get_mgr_data = function (id, role, elem) {
		$.ajax({
			url: baseUrl + 'manpower/rsm/user_info',
			data: {
				id: id,
				role: role,
				token: $('.save-form').find('input[name=token]').val()
			},
			type: 'POST',
			dataType: 'JSON',
			success: function (data, textStatus, jqXHR) {
				if (elem == "users_city_id") {
					users_name.val(data.users_name);
					doctor_users_id.val(data.users_id);
				} else {
					reporting_mgr.val(data.users_id);
				}
			}
		});
	}


	if (users_city_id.length) {
		users_city_id.on('change', function () {
			id = users_city_id.val();
			role = 'mr';
			if (id) {
				get_mgr_data(id, role, 'users_city_id');

			}
		});
	} else if (zone.length && region.length && area.length && city.length) {
		area.on('change', function () {
			id = area.val();
			role = 'asm';
			if (id) {
				get_mgr_data(id, role, 'area');
			}
		});
	} else if (zone.length && region.length && area.length) {
		region.on('change', function () {
			id = region.val();
			role = 'rsm';
			if (id) {
				get_mgr_data(id, role, 'region');
			}
		});
	} else if (zone.length && region.length) {
		zone.on('change', function () {
			id = zone.val();
			role = 'zsm';
			if (id) {
				get_mgr_data(id, role, 'zone');
			}
		});
	}

})(jQuery);