(function ($) {

    var zone = $('#bunch_id');

    /*counter to reset select2 values on a change event of the element*/
    var r_load_cnt = 0; //Region: 0 counter stands for when no change event is triggered

    var load = function (elem, placeholder_txt, controller, change_trigger = false, attempt, data = '') {

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

    load('bunch_id', 'Bunch Code', 'bunch', true);

})(jQuery);