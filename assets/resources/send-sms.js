(function($){
    var token = $('meta[name="csrf-token"]').attr('content');
    $('#group-select-form').hide();
    $('#individual-select-form').hide();
    $('#group-select-doctor-patient').hide();


    $('input[name=sms_group]').on('change',function(){
        $('[name="group_id"]').val('').trigger('change');
        
        $('#group-select-form').show();
        // $('#fill_records').hide();
    });

    console.log("here");

    $(document).on('change', '[name="group_id"]', function(){
        $this = $(this); 
        var sms_to = $('input[name=sms_group]:checked').val();

        requestController = '';
        $('#selected_roles').val(null).select2();
        
        if(sms_to == 'single') {
            $('#fill_records').show()
        }else{
            $('#fill_records').hide()
        }

        if(sms_to == 'group'){
            $('#selected_roles').removeAttr('multiple');
            $('#patient_records').hide();

            $('#selected_roles').select2({
                placeholder: "Select " + $this.val(),
                allowClear: true,
                ajax: {
                    url: baseUrl + 'doctor/options',
                    dataType: 'json',
                    type: 'POST',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            token: $('#save-form').find('input[name=token]').val(),
                        }
                        return query;
                    },
                    cache: true
                }
            });

        }else if(sms_to == 'single'){
            $('#role_label').text($this.val());

            if($this.val() == 'MR'){
                requestController = 'manpower/mr';
                $('#selected_roles').attr('multiple','multiple');
            }else if($this.val() == 'ASM'){
                requestController = 'manpower/asm';
                $('#selected_roles').attr('multiple','multiple');
            }else if($this.val() == 'ZSM'){
                requestController = 'manpower/zsm';
                $('#selected_roles').attr('multiple','multiple');
            }
            else if($this.val() == 'DOCTOR' || $this.val() == 'PATIENT'){
                requestController = 'doctor'; 
                if($this.val() == 'PATIENT'){
                    $('#role_label').text('Doctor');
                    $('#selected_roles').removeAttr('multiple');
                }
                else{
                    $('#patient_records').hide();
                    $('#selected_roles').attr('multiple','multiple');
                }
            } 
            $('#selected_roles').select2({
                placeholder: "Select " + $this.val(),
                allowClear: true,
               
                ajax: {
                    url: baseUrl + requestController + '/options_data',
                    dataType: 'json',
                    type: 'POST',
                    data: function (params) {
                        var query = {
                            search: params.term,
                            page: params.page || 1,
                            token: $('.save-form').find('input[name=token]').val(),
                            role: $this.val()
                        }
                        return query;
                    },
                    cache: true
                }
            });
        }
    });

    $(document).on('change', '#selected_roles', function(){
        $this = $(this);
        var sms_to = $('input[name=sms_group]:checked').val();

        if(sms_to == 'single' && $('#group_id').val() == 'PATIENT'){
            $('#patient_records').show();
        }else{
            $('#patient_records').hide();
        }

        $('#selected_patients').select2({
            placeholder: "Select Patients",
            allowClear: true,
            ajax: {
                url: baseUrl + 'patient/options',
                dataType: 'json',
                type: 'POST',
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1,
                        token: $('#save-form').find('input[name=token]').val(),
                        id: $this.val()
                    }
                    return query;
                },
                cache: true
            }
        });
    });

})(jQuery);
