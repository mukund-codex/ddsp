(function ($) {

    var selectObject = {}
    var preview = $('.preview');
    var cropContainer = $('.cropcontainer');
    var take_snapshot = $('#take_snapshot');

    var bar = $('.bar');
    var percent = $('.percent');
    var progress = $('.progress');
    var status = $('#status');

    take_snapshot.hide();
    cropContainer.hide();
    preview.hide();
    progress.hide();

    function setCoordinates(obj) {
        jQuery('input[name="x1"]').val(obj.x1);
        jQuery('input[name="y1"]').val(obj.y1);
        jQuery('input[name="x2"]').val(obj.width);
        jQuery('input[name="y2"]').val(obj.height);
    }

    selectionImage = jQuery('img#selectArea').imgAreaSelect({
        instance: true,
        handles: true,
        zIndex: 10000,
        aspectRatio: '1:1',
        parent: preview,
        hide: true,
        onSelectEnd: function (img, selection) {
            jQuery('input[name="x1"]').val(selection.x1);
            jQuery('input[name="y1"]').val(selection.y1);
            jQuery('input[name="x2"]').val(selection.width);
            jQuery('input[name="y2"]').val(selection.height);
        },
    });

    $('.changebutton').on('click', function () {
        selectionImage.setOptions({
            hide: true
        });
        selectionImage.update();

        // $(this).parent().hide().siblings('.addcontainer').show().end().siblings('.preview').hide();
        preview.hide();
        cropContainer.hide();
    })

    $('.previewbutton').on('click', function () {
        selectionImage.setOptions({
            show: true
        });
        selectionImage.update();

        $(this).parent().hide().siblings('.cropcontainer, .preview').show();
    })

    $('[name=doctor_photo]').on('change', function () {
        if ($(this).val() === '') {
            alert('No Image');
            return;
        }

        // progress.show();
        $this = $(this);

        if (window.FormData != 'undefined') {
            var formData = new FormData();
            formData.set('doctor_photo', $this[0].files[0]);
            formData.set('token', $('meta[name=csrf-token]').attr('content'));

            jQuery.ajax({
                url: baseUrl + 'poster/save',
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                mimeType: 'multipart/form-data',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function (xhr, opts) {
                    $this.siblings('.loadpicture').eq(0).show();
                    status.empty();
                    var percentVal = '0%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    var percentVal = percentComplete + '%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                success: function (data) {
                    progress.hide();
                    if (!data.error) {
                        preview.find('img').attr('src', data.path);
                        jQuery("#imageName").val(data.filename);

                        $this.siblings('.loadpicture').eq(0).hide();

                        preview.show();

                        preview.onImagesLoad(function ($selector) {
                            var image = preview.find('img');
                            imgWidth = image.outerWidth();
                            imgHeight = image.outerHeight();

                            naturalWidth = image[0].naturalWidth;
                            naturalHeight = image[0].naturalHeight;

                            if (imgWidth > imgHeight)
                                var datasize = imgHeight - 10;
                            else
                                var datasize = imgWidth - 10;

                            selectionImage.setSelection(10, 0, datasize, datasize, true);

                            selectionImage.setOptions({
                                show: true,
                                imageHeight: naturalHeight,
                                imageWidth: naturalWidth
                            });

                            selectionImage.update();
                            var coordinates = selectionImage.getSelection();
                            setCoordinates(coordinates);
                            // console.log(selectionImage.onSelectEnd());

                            /* jQuery('input[name="x1"]').val(0);
                            jQuery('input[name="y1"]').val(0);
                            jQuery('input[name="x2"]').val(datasize);
                            jQuery('input[name="y2"]').val(datasize); */

                            cropContainer.show();

                        })

                    } else {
                        console.log(data.error);
                        if (data.error) {
                            $.each(data.error, function (key, val) {
                                var elem = $('[name="' + key + '"]', $this).parent();
                                $('[name="' + key + '"]').removeClass('error')
                                    .next('label.error').remove()
                                    .end()
                                    .addClass('error').after(val);

                            });
                        }
                    }
                    $this.siblings('.loadpicture').hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('Oops ! there might be some problem, please try after some time');
                    $this.siblings('.loadpicture').hide();
                }
            });
        }
    });

})(jQuery);