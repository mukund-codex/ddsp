(function ($) {
    //var controller = $('.custom-table').attr('id');

    var filters = $('.filters');
    var colspan = $('#tbody').find('tr').last().find('td').attr('colspan');

    $('#collectinfo').on('submit', function (e) {
        e.preventDefault();
    })

    var data = {};

    /*search data as per keyword input & other filters*/
    function search(count, pagecount, controller) {
        data.search = 1;
        data.token = $('input[name="token"]').val();

        filters.each(function (i, value) {
            $this = $(this);
            if ($this.val()) {
                data[$this.attr('name')] = $this.val();
            }
        });

        data.page = pagecount;

        $('#checkall').prop("checked", false);

        $.post(baseUrl + listing_url + '/' + count, data, function (data) {
            $('#tbody').html(data);
            window.scrollTo(0, 0);
        });
    }

    /*pagination bullets clicked call the search function*/
    $(document).on('click', '.page-bullets', function (e) {
        var count = $(this).attr('data-count');
        var pagecount = $(this).attr('data-page');

        search(count, pagecount, controller);
    });

    /*search on keyword updatation while typing into the omni search box*/
    filters.on('keyup', function () {

        data.page = 0;
        data.search = 1;

        data.token = $('input[name="token"]').val();

        var query_str = '?xcel=1';

        filters.each(function (i, value) {
            $this = $(this);
            data[$this.attr('name')] = $this.val();
            query_str += '&' + $this.attr('name') + '=' + $this.val();
        });

        var download_url_with_params = baseUrl + download_url + query_str;
        $('a#export').attr('href', download_url_with_params);

        //var url = baseUrl + controller + '/lists/';
        var url = baseUrl + listing_url;

        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            beforeSend: function (xhr, opts) {
                $('#tbody').html('<tr><td colspan="' + colspan + '"><center><b>Searching....</b></center></td><tr>');
            },
            success: function (data) {
                if ($('#checkall').length) {
                    $('#checkall').prop("checked", false);
                }

                $('#tbody').html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                //alert('Oops ! there might be some problem, please try after some time');
            }
        })
    })

    /*Initialize from_date & to_date with condition(i.e to_date must be greater than from_date)*/
    var from_date = $('#from_date');
    var to_date = $('#to_date');
    if (from_date.length) {

        // condition block will work only if from & to date filters are available

        from_date.datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function (date) {

                var pattern = /(\d{2})\-(\d{2})\-(\d{4})/;
                var dt = new Date(date.replace(pattern, '$3-$2-$1'));

                var selectedDate = new Date(dt);
                //var msecsInADay = 86400000;
                var msecsInADay = 0;
                var endDate = new Date(selectedDate.getTime() + msecsInADay);
                to_date.datepicker('option', 'minDate', endDate);
                $(this).change();
            }
        });

        to_date.datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function () {
                $(this).change();
            }
        });

        $('#from_date, #to_date').on('change', function () {
            filters.eq(0).trigger('keyup');
        })
    }


    /*delete records - select for delete*/
    $(document).on('click', '#checkall', function () {

        if ($(this).prop("checked"))
            $("input[type='checkbox']").prop("checked", true);
        else
            $("input[type='checkbox']").prop("checked", false);
    });

    /*delete selected records*/
    $('.deleteAction').on('click', function (e) {
        e.preventDefault();
        $this = $(this).closest('form');

        if (!$('input[name="ids[]"]:checked').length) {
            swal('No records selected!');
            return;
        }

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
                var data = $this.serialize();

                $.ajax({
                    url: $this.attr('action'),
                    data: data,
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function (xhr, opts) {},
                    success: function (data) {
                        filters.trigger('keyup');
                        if (data.msg) {
                            swal('Deleted!', data.msg)
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {}
                })
            }
        })
    });

    /*Upload CSV file for importing data*/
    $('#uploadForm').on('submit', function (e) {
        e.preventDefault();

        var $this = jQuery(this);
        var formUrl = $this.attr('action');

        $('[name=csvfile]').parent().removeClass('error').siblings('label.error').remove();

        if (window.FormData != 'undefined') {
            var formData = new FormData($this[0]);

            jQuery.ajax({
                url: formUrl,
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                mimeType: 'multipart/form-data',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function (xhr, opts) {
                    $('#preloader').show();
                },
                success: function (data, textStatus, jqXHR) {
                    if (data.newrows) {
                        str = data.newrows;
                        if (data.csv_error) {
                            str += '\n'
                            str += data.csv_error;
                        }
                        $('#show_msg').html(str).show().siblings().hide();
                        $('#upload-btn').hide();

                        //$("input[name=keywords]").trigger('keyup');
                        // filters.eq(0).trigger('keyup');

                        $this[0].reset();
                        filters.trigger('keyup');
                    } else {
                        if (data.csv_error) {
                            alert(data.csv_error);
                        }

                        if (data.errors) {
                            $('[name=csvfile]')
                                .parent().addClass('error')
                                .siblings('label.error').remove().end()
                                .after(data.errors.csvfile);
                        }
                    }
                    $('#preloader').hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Error: There might be issues while uploading, please check the fields!');
                    $('#preloader').hide();
                }
            });
        }
    })

    $('.reset-upload').on('click', function () {
        var uploadForm = $(this).parent().parent('form');
        uploadForm[0].reset();

        uploadForm.find('label.error').remove();
        uploadForm.find('div.form-line').removeClass('error');

        $('#show_msg').html('').hide().siblings().show();
        $('#upload-btn').show();
        $('#show_msg_1').html('').hide().siblings().show();
        $('#upload-btn_1').show();
    })

    $(document).ready(function () {
        // keywords.trigger('keyup');
        if ($('.double-scroll').length) {
            $('.double-scroll').doubleScroll();
        }
    });

    $('a.asc_desc').on('click', function (e) {
        e.preventDefault();
        var $this = $(this);

        data.order_by = $this.attr('data-column');
        data.asc_or_desc = $this.attr('data-order');

        $('a.asc_desc').find('i').remove();

        if ($this.attr('data-order') == 'asc') {
            $this.append('<i class="fa fa-caret-up"></i>');
            $this.attr('data-order', 'desc');
        } else {
            $this.append('<i class="fa fa-caret-down"></i>');
            $this.attr('data-order', 'asc');
        }

        filters.trigger('keyup');
    });

})(jQuery);