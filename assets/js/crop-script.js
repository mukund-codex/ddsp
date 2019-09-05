(function ($) {

    function setCoordinates(obj) {
        img = preview.find('img');

        jQuery('input[name="x1"]').val((obj.x * img[0].naturalWidth) / img.outerWidth());
        jQuery('input[name="y1"]').val((obj.y * img[0].naturalHeight) / img.outerHeight());
        jQuery('input[name="x2"]').val((obj.w * img[0].naturalWidth) / img.outerWidth());
        jQuery('input[name="y2"]').val((obj.h * img[0].naturalHeight) / img.outerHeight());
    }

    if ($('#selectArea').length) {

        var preview = $('.preview');
        var cropContainer = $('.cropcontainer');
        var dropBox = $('.dropBox');

        cropContainer.hide();
        preview.hide();

        const jCrop = Jcrop.attach('selectArea');

        jCrop.setOptions({
            aspectRatio: 1,
        });

        jCrop.listen('crop.update', (widget, e) => {
            setCoordinates(widget.pos);
        });

        $('.changebutton').on('click', function () {
            preview.hide();
            dropBox.show();
            cropContainer.hide();
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
                    },
                    success: function (data) {
                        if (!data.error) {
                            image = preview.find('img');
                            image.attr('src', data.path);
                            jQuery("#imageName").val(data.filename);


                            preview.onImagesLoad(function ($selector) {

                                $this.siblings('.loadpicture').eq(0).hide();
                                dropBox.hide();

                                preview.show();
                                cropContainer.show();

                                vWidth = image.outerWidth();
                                vHeight = image.outerHeight();

                                imgWidth = image[0].naturalWidth;
                                imgHeight = image[0].naturalHeight;

                                // var datasize = (vWidth > vHeight) ? vHeight : vWidth;
                                var actSize = (imgWidth > imgHeight) ? imgHeight : imgWidth;

                                rect = Jcrop.Rect.create(0, 0, actSize, actSize);
                                jCrop.newWidget(rect.center(actSize, actSize));
                                jCrop.focus();
                            })

                        } else {
                            if (data.error) {
                                $.each(data.error, function (key, val) {
                                    var elem = $('[name="' + key + '"]', $this).parent();
                                    $('[name="' + key + '"]').removeClass('error')
                                        .next('label.error').remove()
                                        .end()
                                        .addClass('error').after(val);

                                });
                            }
                            $this.siblings('.loadpicture').hide();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('Oops ! there might be some problem, please try after some time');
                        $this.siblings('.loadpicture').hide();
                    }
                });
            }
        });
    }

})(jQuery);