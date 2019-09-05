// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

/*
// cointainer to hold both charts
var chart_1 = am4core.create("chartdiv_1", am4charts.PieChart);

// Add data
chart_1.data = activity_wise_amount_data;

// Add and configure Series
var pieSeries_1 = chart_1.series.push(new am4charts.PieSeries());
pieSeries_1.dataFields.value = "number";
pieSeries_1.dataFields.category = "activity_name";

chart_1.exporting.menu = new am4core.ExportMenu();
var title_1 = chart_1.titles.create();
title_1.text = "Activity wise Amount Graph";
title_1.fontSize = 15;
title_1.marginBottom = 30;

chart_1.legend = new am4charts.Legend();
chart_1.legend.useDefaultMarker = true;
chart_1.legend.fontSize = 12;
chart_1.legend.position = "bottom";

var temp1 = chart_1.legend.markers.template;
temp1.width = 15;
temp1.height = 15;

var marker_1 = temp1.children.getIndex(0);
marker_1.cornerRadius(12, 12, 12, 12);
marker_1.strokeWidth = 2;
marker_1.strokeOpacity = 1;
marker_1.stroke = am4core.color("#ccc");
*/

var chart_1 = am4core.create("chartdiv_1", am4charts.XYChart);

chart_1.exporting.menu = new am4core.ExportMenu();
var title = chart_1.titles.create();
title.text = "Activity wise Amount Graph (19-20)";
title.fontSize = 20;
title.marginBottom = 30;

// Add data
chart_1.data = [];
// Create axes

var categoryAxis_1 = chart_1.xAxes.push(new am4charts.CategoryAxis());
categoryAxis_1.dataFields.category = "activity_name";
categoryAxis_1.renderer.grid.template.location = 0;
categoryAxis_1.renderer.minGridDistance = 10;
categoryAxis_1.renderer.labels.template.horizontalCenter = "right";
categoryAxis_1.renderer.labels.template.verticalCenter = "middle";
categoryAxis_1.renderer.labels.template.rotation = -90;
categoryAxis_1.tooltip.disabled = true;
categoryAxis_1.renderer.minHeight = 11;
categoryAxis_1.renderer.fontSize = 11;

categoryAxis_1.renderer.labels.template.adapter.add("dy", function (dy, target) {
    if (target.dataItem && target.dataItem.index & 2 == 2) {
        return dy + 25;
    }
    return dy;
});

var valueAxis = chart_1.yAxes.push(new am4charts.ValueAxis());

// Create series
var series_1 = chart_1.series.push(new am4charts.ColumnSeries());
series_1.dataFields.valueY = "number";
series_1.dataFields.categoryX = "activity_name";
series_1.name = "Amount";
series_1.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
series_1.columns.template.fillOpacity = .8;

var columnTemplate_1 = series_1.columns.template;
columnTemplate_1.strokeWidth = 2;
columnTemplate_1.strokeOpacity = 1;

// cointainer to hold both charts
var chart_2 = am4core.create("chartdiv_2", am4charts.PieChart);

// Add data
chart_2.data = [];

// Add and configure Series
var pieSeries_2 = chart_2.series.push(new am4charts.PieSeries());
pieSeries_2.dataFields.value = "number";
pieSeries_2.dataFields.category = "speciality_name";

chart_2.exporting.menu = new am4core.ExportMenu();
var title_2 = chart_2.titles.create();
title_2.text = "Speciality wise Amount (18-19) Graph";
title_2.fontSize = 20;
title_2.marginBottom = 30;

chart_2.legend = new am4charts.Legend();
chart_2.legend.useDefaultMarker = true;
chart_2.legend.fontSize = 12;
chart_2.legend.position = "bottom";

var temp2 = chart_2.legend.markers.template;
temp2.width = 15;
temp2.height = 15;

var marker_2 = temp2.children.getIndex(0);
marker_2.cornerRadius(12, 12, 12, 12);
marker_2.strokeWidth = 2;
marker_2.strokeOpacity = 1;
marker_2.stroke = am4core.color("#ccc");


// cointainer to hold both charts
var chart_3 = am4core.create("chartdiv_3", am4charts.PieChart);

// Add data
chart_3.data = [];

// Add and configure Series
var pieSeries_3 = chart_3.series.push(new am4charts.PieSeries());
pieSeries_3.dataFields.value = "number";
pieSeries_3.dataFields.category = "speciality_name";

chart_3.exporting.menu = new am4core.ExportMenu();
var title_3 = chart_3.titles.create();
title_3.text = "Speciality wise Amount (19-20) Graph";
title_3.fontSize = 20;
title_3.marginBottom = 30;

chart_3.legend = new am4charts.Legend();
chart_3.legend.useDefaultMarker = true;
chart_3.legend.fontSize = 12;
chart_3.legend.position = "bottom";

var temp3 = chart_3.legend.markers.template;
temp3.width = 15;
temp3.height = 15;

var marker_3 = temp3.children.getIndex(0);
marker_3.cornerRadius(12, 12, 12, 12);
marker_3.strokeWidth = 2;
marker_3.strokeOpacity = 1;
marker_3.stroke = am4core.color("#ccc");

