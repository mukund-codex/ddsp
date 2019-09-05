(function($){
    //var controller = $('.custom-table').attr('id');
    var keywords = $("input[name=keywords]");
    var from_d = $("input[name=from_date]");
    var to_d = $("input[name=to_date]");
    
    var role_id = $("[name='role_id']");
    var division_id = $("[name='division_id']");
    var national_zone_id = $("[name='national_zone_id']");
    var zone_id = $("[name='zone_id']");
    var region_id = $("[name='region_id']");
    var doctor_name = $("[name='doctor_name']");
    var abcd = $("[name='abcd']");
    var speciality_by_practice = $("[name='speciality_by_practice']");
    var speciality_by_qualification = $("[name='speciality_by_qualification']");
    var hq_id = $("[name='hq_id']");
    var brand_id = $("#brand_id");
    var activity_id = $("#activity_id");
    var colspan = $('#tbody').find('tr').last().find('td').attr('colspan');

    var total_activity_count = $('#total_activity_count');
    var plan_amount_total = $('#plan_amount_total');

    var data = {};

    /*search data as per keyword input & other filters*/
    function search(count, pagecount, controller){
        data.search = 1;

        data.keywords = keywords.val();
        data.token = $('input[name="token"]').val();
        data.from = (from_d.length) ? from_d.val() : '';
        data.to = (to_d.length) ? to_d.val() : '';
        
        data.role_id = (role_id.length) ? role_id.val() : '';
        data.division_id = (division_id.length) ? division_id.val() : '';
        data.national_zone_id = (national_zone_id.length) ? national_zone_id.val() : '';
        data.zone_id = (zone_id.length) ? zone_id.val() : '';
        data.region_id = (region_id.length) ? region_id.val() : '';
        data.doctor_name = (doctor_name.length) ? doctor_name.val() : '';
        data.abcd = (abcd.length) ? abcd.val() : '';
        data.speciality_by_practice = (speciality_by_practice.length) ? speciality_by_practice.val() : '';
        data.speciality_by_qualification = (speciality_by_qualification.length) ? speciality_by_qualification.val() : '';
        data.hq_id = (hq_id.length) ? hq_id.val() : '';
        data.brand_id = (brand_id.length) ? brand_id.val() : '';
        data.activity_id = (activity_id.length) ? activity_id.val() : '';
        
        data.total_activity_count = (total_activity_count.length) ? total_activity_count.val() : '';
        data.plan_amount_total = (plan_amount_total.length) ? plan_amount_total.val() : '';

        data.page = pagecount;
       
        $('#checkall').prop("checked", false);

        $.post(baseUrl + listing_url + '/' + count , data , function(data){
            $('#tbody').html(data); 
            window.scrollTo(0,0);
        });
    }

    /*pagination bullets clicked call the search function*/
    $(document).on('click', '.page-bullets', function(e){
        var count = $(this).attr('data-count');
        var pagecount = $(this).attr('data-page');

        search(count, pagecount, controller);
    });

    /*search on keyword updatation while typing into the omni search box*/
    keywords.on('keyup', function(){
        data.page = 0;
        data.search = 1;
        data.keywords = $(this).val();
        data.token = $('input[name="token"]').val();
        data.from = (from_d.length) ? from_d.val() : '';
        data.to = (to_d.length) ? to_d.val() : '';

        data.role_id = (role_id.length) ? role_id.val() : '';
        data.division_id = (division_id.length) ? division_id.val() : '';
        data.national_zone_id = (national_zone_id.length) ? national_zone_id.val() : '';
        data.zone_id = (zone_id.length) ? zone_id.val() : '';
        data.region_id = (region_id.length) ? region_id.val() : '';
        data.doctor_name = (doctor_name.length) ? doctor_name.val() : '';
        data.abcd = (abcd.length) ? abcd.val() : '';
        data.speciality_by_practice = (speciality_by_practice.length) ? speciality_by_practice.val() : '';
        data.speciality_by_qualification = (speciality_by_qualification.length) ? speciality_by_qualification.val() : '';
        data.hq_id = (hq_id.length) ? hq_id.val() : '';
        data.brand_id = (brand_id.length) ? brand_id.val() : '';
        data.activity_id = (activity_id.length) ? activity_id.val() : '';

        data.total_activity_count = (total_activity_count.length) ? total_activity_count.val() : '';
        data.plan_amount_total = (plan_amount_total.length) ? plan_amount_total.val() : '';

        var query_str = '?xcel=1';

        if(data.keywords !== ''){
            query_str += '&keywords=' + data.keywords;
        }
        if(data.from !== ''){
            query_str += '&from='+data.from;
        }
        if(data.to !== ''){
            query_str += '&to='+data.to;
        }
        
        if(data.role_id !== '') {
            query_str += '&role_id='+data.role_id;
        }

        if(data.division_id !== '') {
            query_str += '&division_id='+data.division_id;
        }
        
        if(data.national_zone_id !== '') {
            query_str += '&national_zone_id='+data.national_zone_id;
        }
        if(data.zone_id !== '') {
            query_str += '&zone_id='+data.zone_id;
        }

        if(data.region_id !== '') {
            query_str += '&region_id='+data.region_id;
        }

        if (data.doctor_name !== '') {
            query_str += '&doctor_name=' + data.doctor_name;
        }

        if (data.abcd !== '') {
            query_str += '&abcd=' + data.abcd;
        }

        if (data.speciality_by_practice !== '') {
            query_str += '&speciality_by_practice=' + data.speciality_by_practice;
        }

        if (data.speciality_by_qualification !== '') {
            query_str += '&speciality_by_qualification=' + data.speciality_by_qualification;
        }

        if (data.hq_id !== '') {
            query_str += '&hq_id=' + data.hq_id;
        }

        if (data.brand_id !== '') {
            query_str += '&brand_id=' + data.brand_id;
        }

        if (data.activity_id !== '') {
            query_str += '&activity_id=' + data.activity_id;
        }

        if (data.total_activity_count !== '') {
            query_str += '&total_activity_count=' + data.total_activity_count;
        }

        if (data.plan_amount_total !== '') {
            query_str += '&plan_amount_total=' + data.plan_amount_total;
        }

        var download_url_with_params = baseUrl + download_url + query_str;
        $('a#export').attr('href', download_url_with_params);
        
        //var url = baseUrl + controller + '/lists/';
        var url = baseUrl + listing_url;

        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            beforeSend: function(xhr, opts){
                $('#tbody').html('<tr><td colspan="'+ colspan +'"><center><b>Searching....</b></center></td><tr>');
            },
            success: function(data) {
                if($('#checkall').length){
                    $('#checkall').prop("checked", false);    
                }
                
                $('#tbody').html(data);
            },
            error: function(jqXHR, textStatus, errorThrown){
                //alert('Oops ! there might be some problem, please try after some time');
            }
        })
    })



    /*Initialize from_date & to_date with condition(i.e to_date must be greater than from_date)*/
    if($('#from_date').length){
        
        
        
   $('#from_date').datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function(date){
                
                var pattern = /(\d{2})\-(\d{2})\-(\d{4})/;
                var dt = new Date(date.replace(pattern,'$3-$2-$1'));

                var selectedDate = new Date(dt);
                //var msecsInADay = 86400000;
                var msecsInADay = 0;

                var endDate = new Date(selectedDate.getTime() + msecsInADay);

                $('#to_date').datepicker('option', 'minDate', endDate);

                $(this).change();
            }
        });

        $('#to_date').datepicker({ 
            dateFormat: 'yy-mm-dd',
            onSelect: function() {
                $(this).change();
            } 
        });
    }

    $(document).ready(function(){
        // keywords.trigger('keyup');
        if( $('.double-scroll').length ){
            $('.double-scroll').doubleScroll();
        }    
    });


    $("[name='doctor_name']").keyup(function() {
        keywords.trigger('keyup');
    });
    
    $("[name='abcd'],[name='hq_id'], [name='speciality_by_practice'], [name='speciality_by_qualification']").change(function() {
        keywords.trigger('keyup');
    });

    if(total_activity_count.length) {
        total_activity_count.on('change', function() {
            keywords.trigger('keyup')
        })
    }

    if(plan_amount_total.length) {
        plan_amount_total.on('change', function() {
            keywords.trigger('keyup')
        })
    }

})(jQuery);