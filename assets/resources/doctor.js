function showModel(response, element) {
	console.log(response);
	console.log(element);
	$('.modal-body').html(response.data);
	$('#myModal').modal();
}

function errorFunction() {
	alert('Something Went Wrong!');
}

(function($) {
	$(document).ready(function() {
		loadsku();
	});

	$(document).on('click', '.category_popup', function() {
		var data = {
			doctor_id: $(this).attr('doctor-id'),
			category_id: $(this).attr('category-id'),
			token: $('meta[name="csrf-token"]').attr('content')
		};
		var url = baseUrl + 'mr_lists/getBrandMolecules';
		shootAjax($(this), data, url, 'showModel', 'errorFunction', 'POST');
	});

	$('#img_preview').on('click', function(e) {
		e.preventDefault();
		var formObj = $(this).closest('form');
		$.each(formObj.find('input, select, textarea'), function(i, field) {
			var elem = $('[name="' + field.name + '"]').parent();

			if (elem.hasClass('select2-hidden-accessible')) {
				elem.next()
					.removeClass('error')
					.siblings('label')
					.remove();
			} else {
				elem.removeClass('error')
					.next('label.error')
					.remove();
			}
		});

		if (window.FormData != 'undefined') {
			var formData = new FormData(formObj[0]);
			$.ajax({
				url: baseUrl + 'doctor/preview',
				type: 'POST',
				data: formData,
				dataType: 'JSON',
				mimeType: 'multipart/form-data',
				contentType: false,
				cache: false,
				processData: false,
				beforeSend: function(xhr, opts) {
					$('#preloader').show();
				},
				success: function(data, textStatus, jqXHR) {
					if (data.status == true) {
						if (data.popup) {
							$('#image_modal').attr('src', data.image);
							$('#image_modal').on('load', function() {
								$('#myModal').modal({
									backdrop: 'static',
									keyboard: false,
									show: true
								});
							});
						}
					} else {
						if (data.errors) {
							$.each(data.errors, function(key, val) {
								var elem = $('[name="' + key + '"]', formObj).parent();

								if (elem.hasClass('select2-hidden-accessible')) {
									elem.next()
										.addClass('error')
										.siblings('label')
										.remove()
										.end()
										.after(val);
								} else {
									elem.removeClass('error')
										.next('label.error')
										.remove()
										.end()
										.addClass('error')
										.after(val);
								}
							});

							$('.form-line.error')
								.eq(0)
								.addClass('focused');
						}

						if (data.message) {
							swal({
								title: 'Error!',
								text: data.message
							});
						}
					}
					$('#preloader').hide();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert('Problems while saving data!');
					$('#preloader').hide();
				}
			});
		}
	});

	$(document).on('click', '.download-btn', function(e) {
		e.preventDefault();
		$this = $(this);

		$.ajax({
			url: baseUrl + 'doctor/download',
			data: { id: $this.attr('data-id'), token: $('meta[name="csrf-token"]').attr('content') },
			type: 'POST',
			dataType: 'JSON',
			beforeSend: function(xhr, opts) {
				$('#preloader').show();
			},
			success: function(data) {
				$('#preloader').hide();

				if (data.status) {
					var evt = new MouseEvent('click');
					var cb = $this.next()[0];
					cb.dispatchEvent(evt);
				}

				if (data.msg && !data.status) {
					swal(data.msg);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('#preloader').hide();
			}
		});
	});

	$(document).on('click', '.share-btn', function(e) {
		e.preventDefault();
		$this = $(this);

		$.ajax({
			url: baseUrl + 'doctor/whatsapp',
			data: { id: $this.attr('data-id'), token: $('meta[name="csrf-token"]').attr('content') },
			type: 'POST',
			dataType: 'JSON',
			beforeSend: function(xhr, opts) {
				$('#preloader').show();
			},
			success: function(data) {
				$('#preloader').hide();

				if (data.status) {
					var evt = new MouseEvent('click');
					var cb = $this.next()[0];
					cb.dispatchEvent(evt);
				}

				if (data.msg && !data.status) {
					swal(data.msg);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$('#preloader').hide();
			}
		});
	});

	var text_max = 250;
	var data_chars_count = $('textarea.max-count').attr('data-max');
	var max_count = data_chars_count ? Number.parseInt(data_chars_count) : text_max;
	$('<div/>')
		.attr('id', 'textarea_feedback')
		.addClass('pull-right')
		.insertAfter('textarea.max-count');
	$('#textarea_feedback').html('<span class="badge">' + max_count + '</span>' + ' characters remaining');

	$('textarea.max-count').on('keyup', function() {
		var text_length = $('textarea.max-count').val().length;
		var text_remaining = max_count - text_length;
		$('#textarea_feedback').html('<span class="badge">' + text_remaining + '</span>' + ' characters remaining');
	});

	load('category_id', 'Category Name', 'category/options', true);
	load('molecule_id', 'Molecule Name', 'molecule/options', true);
	load('brand_id', 'Brand Name', 'brand/options', true);
	load('speciality_id', 'Speciality Name', 'speciality/options', true);

	$('#brand_id').on('change', function() {
		loadsku();
	});

	$("input[name='group1']").click(function() {
		if ($('#skuYes').is(':checked')) {
			$('#skuLabel').show();
			$('#skuDiv').show();
		}
	});
	$("input[name='group1']").click(function() {
		if ($('#skuNo').is(':checked')) {
			$('#skuLabel').hide();
			$('#skuDiv').hide();
		}
	});

	function loadsku() {
		var r_load_cnt = 0;

		r_load_cnt++;
		var r_attempt_to = r_load_cnt > 1 ? 'reset' : 'load';

		data = {
			id: $('#brand_id').val()
		};

		load('sku_id', 'SKU', 'sku/options', data);
	}
})(jQuery);
