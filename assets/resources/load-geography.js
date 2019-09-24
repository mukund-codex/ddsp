(function ($) {

    var zone = $('#zone_id');
    var area = $('#area_id');
    var city = $('#city_id');
    var users_city_id = $('#users_city_id');

    /*counter to reset select2 values on a change event of the element*/
    var a_load_cnt = 0; //Area: 0 counter stands for when no change event is triggered
    var c_load_cnt = 0; //City: 0 counter stands for when no change event is triggered

    var load = function (elem, placeholder_txt, controller, change_trigger = false, attempt, data = '') {
        console.log("Data", data);
        $('#' + elem).select2({
            placeholder: "Select " + placeholder_txt,
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

                    if (data.id) {
                        query['id'] = data.id;
                    }
                    
                    console.log(query);
                    return query;
                },
                cache: true
            }
        });

        if (change_trigger) {
            if (attempt == 'reset') {
                $('#' + elem).val(null).trigger('change');
            } else {
                $('#' + elem).trigger('change');
            }
        }
    }
/* 
    if (region.length) {

        zone.on('change', function () {
            r_load_cnt++;
            var r_attempt_to = (r_load_cnt > 1) ? 'reset' : 'load';

            data = {
                id: $(this).val()
            }
            load('region_id', 'Region', 'geography/region', true, r_attempt_to, data);
        });
    } */

    /* if (area.length) {
        region.on('change', function () {
            a_load_cnt++;
            var a_attempt_to = (a_load_cnt > 1) ? 'reset' : 'load';

            data = {
                id: $(this).val()
            }
            load('area_id', 'Area', 'geography/area', true, a_attempt_to, data);
        })
    } */

    if (area.length) {
        zone.on('change', function () {
            a_load_cnt++;
            var a_attempt_to = (a_load_cnt > 1) ? 'reset' : 'load';

            data = {
                id: $(this).val()
            }

            load('area_id', 'Area', 'geography/area', true, a_attempt_to, data);
        })
    }

    if (city.length) {
        area.on('change', function () {
            c_load_cnt++;
            var c_attempt_to = (c_load_cnt > 1) ? 'reset' : 'load';

            data = {
                id: $(this).val()
            }
            load('city_id', 'City', 'geography/city', true, c_attempt_to, data);
        })
    }

    load('national_zone_id', 'National Zone', 'geography/national_zone', true);
    load('zone_id', 'Zone', 'geography/zone', true);
    // load('area_id', 'Area', 'geography/area', true);
    load('users_city_id', 'City', 'geography/city', true);
    load('state_id', 'State', 'state', true);

})(jQuery);