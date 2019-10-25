var ajaxCall = function(url, data, chart, callback) {
	$.ajax({
		url: url,
		data: data,
		type: 'POST',
		dataType: 'JSON',
		cache: false,
		beforeSend: function() {
			$('#preloader').show();
		},
		success: function(data) {
			$('#preloader').hide();
			return callback(data, chart);
		},
		error: function(jqXHR, textStatus, errorThrown) {
			return callback(errorThrown);
		}
	});
};

var loadPie = function(chart) {
	var url = baseUrl + 'dashboard/user/chemist_count_for_day';
	var data = {
		token: $('meta[name="csrf-token"]').attr('content'),
		from_date: $('#from_date').val(),
		to_date: $('#to_date').val()
	};

	ajaxCall(url, data, chart, successFn);
};

function successFn(data, chart) {
	console.log(data.length);
	var count = 0;
	for (i = 0; i < data.length; i++) {
		if (data[i].value == null) {
			count++;
		}
	}
	console.log(count);
	if (count == 3) {
		$('#chartdiv').css('display', 'none');
	}
	if (count != 3) {
		$('#chartdiv').css('display', 'block');
		chart.data = data;
		chart.validateData();
	}
}

am4core.ready(function() {
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	// Create chart instance
	var chart = am4core.create('chartdiv', am4charts.PieChart);

	// Add data
	chart.data = [];

	// Add and configure Series
	var pieSeries = chart.series.push(new am4charts.PieSeries());
	pieSeries.dataFields.value = 'value';
	pieSeries.dataFields.category = 'key';
	pieSeries.slices.template.stroke = am4core.color('#fff');
	pieSeries.slices.template.strokeWidth = 2;
	pieSeries.slices.template.strokeOpacity = 1;

	// This creates initial animation
	pieSeries.hiddenState.properties.opacity = 1;
	pieSeries.hiddenState.properties.endAngle = -90;
	pieSeries.hiddenState.properties.startAngle = -90;

	$('#from_date, #to_date').change(function() {
		loadPie(chart);
	});
	$('#from_date').trigger('change');
}); // end am4core.ready()
