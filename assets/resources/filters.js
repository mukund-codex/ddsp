am4core.ready(function() {
	am4core.useTheme(am4themes_animated);
	var chartAsm = am4core.create('chartdiv_asm', am4charts.XYChart);
	// Themes begin

	// Create axes
	var categoryAxis = chartAsm.xAxes.push(new am4charts.CategoryAxis());
	categoryAxis.dataFields.category = 'users_name';
	categoryAxis.title.text = 'ASM Name';
	categoryAxis.renderer.grid.template.location = 0;
	categoryAxis.renderer.minGridDistance = 20;
	categoryAxis.renderer.cellStartLocation = 0.1;
	categoryAxis.renderer.cellEndLocation = 0.9;

	chartAsm.data = [];

	var valueAxis = chartAsm.yAxes.push(new am4charts.ValueAxis());
	valueAxis.min = 0;
	valueAxis.title.text = 'MR Counts';

	// Create series
	function createSeries(field, name, stacked) {
		var series = chartAsm.series.push(new am4charts.ColumnSeries());
		series.dataFields.valueY = field;
		series.dataFields.categoryX = 'users_name';
		series.name = name;
		series.columns.template.tooltipText = '{name}: [bold]{valueY}[/]';
		series.stacked = stacked;
		series.columns.template.width = am4core.percent(95);
	}

	createSeries('count_less', 'Less Than 15', false);
	createSeries('count_greater', 'Greater Than 15', false);
	createSeries('count_equal', 'Equal to 15', false);

	// Add legend
	chartAsm.legend = new am4charts.Legend();

	$('#zone_id, #from_date_asm, #to_date_asm').change(function() {
		$.ajax({
			url: baseUrl + 'dashboard/user/getZoneWiseRecords',
			type: 'POST',
			dataType: 'JSON',
			data: {
				zone_id: $('#zone_id').val(),
				from_date: $('#from_date_asm').val(),
				to_date: $('#to_date_asm').val(),
				token: $('input[name=token]').val()
			}
		}).done(function(result) {
			chartAsm.data = result;
			chartAsm.validateData();
			console.log(chartAsm);
		});
	});
	$('#zone_id').trigger('change');
}); // end am4core.ready()
