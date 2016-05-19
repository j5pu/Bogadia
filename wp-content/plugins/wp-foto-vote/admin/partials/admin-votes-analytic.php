<?php

?>

<div class="wrap">
    <p><?php _e('There your can see map with the distribution of votes, and your can filter data by contest and single photo. <strong>How it can help to your?</strong>', 'fv') ?></p>
    <p><?php _e('It`s interesting, and if you see a lot of votes from another suspicious country, it may indicate about fraud. (shows max 3000 records)', 'fv') ?></p>

    <div class=" actions  mb20">
            <label for="fv-filter-contest"><?php _e('Contest', 'fv') ?>:</label>
            <select name="contest-filter" id="fv-filter-contest">
                    <option value=""><?php _e('Filter by Contest', 'fv') ?></option>
                    <?php foreach ($contests as $c_id => $c_name): ?>
                            <option
                                value="<?php echo $c_id ?>" <?php echo ($selected_id == $c_id) ? 'selected' : '' ?> ><?php echo $c_name ?></option>
                    <?php endforeach; ?>
            </select>

            <label for="fv-filter-contest-photo"><?php _e('Photo', 'fv') ?>:</label>
            <select name="contest-filter-photo"
                    id="fv-filter-contest-photo" <?php echo (!$selected_id) ? "disabled" : ""; ?>>
                    <?php if (!$selected_id) : ?>
                            <option value=""><?php _e('Select contest', 'fv') ?></option>
                    <?php else: ?>
                            <option value=""><?php _e('Filter by photo', 'fv') ?></option>
                            <?php foreach ($photos as $P): ?>
                                    <option
                                        value="<?php echo $P->id ?>" <?php echo selected($P->id, $selected_photo_id) ?> ><?php echo $P->name . ' (' . $P->votes_count . ')' ?></option>
                            <?php endforeach; ?>
                    <?php endif ?>
            </select>
    </div>

    <body style="background-color:#FFFFFF">
      <div id="chartdiv" style="width:100%; height:600px;"></div>
    </body>

    <div id="world-map" class="bg-light-blue-gradient" style="height: 700px;"></div>

    <script>

            FvLib.addHook('doc_ready', function() {
                    //"use strict";
                            //jvectormap data
                            var visitorsData = {
                                    <?php foreach ($votes_arr as $C => $vote) : ?>
                                            "<?php echo $C ?>": <?php echo $vote ?>,
                                    <?php endforeach; ?>

                            };
                            //World map by jvectormap
                            jQuery('#world-map').vectorMap({
                                    map: 'world_mill_en',
                                    backgroundColor: "transparent",
                                    regionStyle: {
                                            initial: {
                                                    fill: '#e4e4e4',
                                                    "fill-opacity": 1,
                                                    stroke: 'none',
                                                    "stroke-width": 0,
                                                    "stroke-opacity": 1
                                            }
                                    },
                                    series: {
                                            regions: [
                                                    {
                                                            values: visitorsData,
                                                            scale: ["#d7e9f3", "#92c1dc"],
                                                            normalizeFunction: 'polynomial'
                                                    }
                                            ]
                                    },
                                    onRegionTipShow: function (e, el, code) {
                                            if (typeof visitorsData[code] != "undefined")
                                                    el.html(el.html() + ': ' + visitorsData[code] + ' votes');
                                    }
                            });

                    jQuery('select#fv-filter-contest-photo').on('change', function () {
                            var contestFilter = jQuery("select#fv-filter-contest").val();
                            var contestFilterPhoto = jQuery("select#fv-filter-contest-photo").val();

                            if (contestFilter != '') {
                                    document.location.href = 'admin.php?page=fv-vote-analytic&contest_id=' + contestFilter + '&photo_id=' + contestFilterPhoto;
                            } else {
                                    document.location.href = 'admin.php?page=fv-vote-analytic';
                            }
                    });

                    jQuery('select#fv-filter-contest').on('change', function () {
                            var contestFilter = jQuery("select#fv-filter-contest").val();

                            if (contestFilter != '') {
                                    document.location.href = 'admin.php?page=fv-vote-analytic&contest_id=' + contestFilter;
                            } else {
                                    document.location.href = 'admin.php?page=fv-vote-analytic';
                            }
                    });

            });

            var chartData = <?php echo json_encode($chart_votes_arr_res) ?>;
            var chart;
            var chartCursor;

            FvLib.addHook('doc_ready', function() {

                for ( var i = 0; i < chartData.length; i++ ) {
                    generateChartData(chartData[i], i);
                }

                function generateChartData(row, N) {
                    // Split timestamp into [ Y, M, D, h, m, s ]
                    //var t = "2010-06-09 13:12:01".split(/[- :]/);
                    var t = row.date.split(/[- :]/);

                    // Apply each element to the Date function
                    //var d =
                    chartData[N].date = new Date(t[0], t[1]-1, t[2], t[3], 00, 00);
                }


                //AmCharts.ready(function () {
                    // generate some data first
                    //generateChartData();

                    // SERIAL CHART
                    chart = new AmCharts.AmSerialChart();

                    chart.dataProvider = chartData;
                    chart.categoryField = "date";
                    chart.balloon.bulletSize = 5;

                    // listen for "dataUpdated" event (fired when chart is rendered) and call zoomChart method when it happens
                    chart.addListener("dataUpdated", zoomChart);

                    // AXES
                    // category
                    var categoryAxis = chart.categoryAxis;
                    categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
                    categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD
                    categoryAxis.dashLength = 1;
                    categoryAxis.minorGridEnabled = true;
                    categoryAxis.twoLineMode = true;
                    categoryAxis.dateFormats = [{
                        period: 'fff',
                        format: 'JJ:NN:SS'
                    }, {
                        period: 'ss',
                        format: 'JJ:NN:SS'
                    }, {
                        period: 'mm',
                        format: 'JJ:NN'
                    }, {
                        period: 'hh',
                        format: 'JJ:NN'
                    }, {
                        period: 'DD',
                        format: 'DD'
                    }, {
                        period: 'WW',
                        format: 'DD'
                    }, {
                        period: 'MM',
                        format: 'MMM'
                    }, {
                        period: 'YYYY',
                        format: 'YYYY'
                    }];

                    categoryAxis.axisColor = "#DADADA";

                    // value
                    var valueAxis = new AmCharts.ValueAxis();
                    valueAxis.axisAlpha = 0;
                    valueAxis.dashLength = 1;
                    valueAxis.title = "Votes in selected period";
                    chart.addValueAxis(valueAxis);

                    // GRAPH
                    var graph = new AmCharts.AmGraph();
                    graph.id = "g1";
                    graph.title = "red line";
                    graph.valueField = "votes";
                    graph.bullet = "round";
                    graph.bulletBorderColor = "#FFFFFF";
                    graph.bulletBorderThickness = 2;
                    graph.bulletBorderAlpha = 1;
                    graph.lineThickness = 2;
                    graph.lineColor = "#5fb503";
                    graph.negativeLineColor = "#efcc26";
                    graph.hideBulletsCount = 50; // this makes the chart to hide bullets when there are more than 50 series in selection
                    graph.fillAlphas = 0.3; // setting fillAlphas to > 0 value makes it area graph
                    chart.addGraph(graph);

                    // CURSOR
                    chartCursor = new AmCharts.ChartCursor();
                    chartCursor.cursorPosition = "mouse";
                    chartCursor.pan = true; // set it to fals if you want the cursor to work in "select" mode
                    chartCursor.valueLineEnabled = true;
                    chartCursor.valueLineBalloonEnabled = true;
                    chart.addChartCursor(chartCursor);

                    // SCROLLBAR
                    var chartScrollbar = new AmCharts.ChartScrollbar();
                    chartScrollbar.graph = "g1";
                    chartScrollbar.scrollbarHeight = 80;
                    chartScrollbar.backgroundAlpha= 0;
                    chartScrollbar.selectedBackgroundAlpha= 0.1;
                    chartScrollbar.selectedBackgroundColor= "#888888";
                    chartScrollbar.graphFillAlpha= 0;
                    chartScrollbar.graphLineAlpha= 0.5;
                    chartScrollbar.selectedGraphFillAlpha= 0;
                    chartScrollbar.selectedGraphLineAlpha= 1;
                    chartScrollbar.autoGridCount= true;
                    chartScrollbar.color = "#AAAAAA";

                    chart.addChartScrollbar(chartScrollbar);

                    chart.creditsPosition = "bottom-right";

                    // WRITE
                    chart.write("chartdiv");
                //});
/*
                // generate some random data, quite different range
                function generateChartData() {
                    var firstDate = new Date();
                    firstDate.setDate(firstDate.getDate() - 500);

                    for (var i = 0; i < 500; i++) {
                        // we create date objects here. In your data, you can have date strings
                        // and then set format of your dates using chart.dataDateFormat property,
                        // however when possible, use date objects, as this will speed up chart rendering.
                        var newDate = new Date(firstDate);
                        newDate.setDate(newDate.getDate() + i);

                        var visits = Math.round(Math.random() * 40) - 20;

                        chartData.push({
                            date: newDate,
                            visits: visits
                        });
                    }
                }
*/
                // this method is called when chart is first inited as we listen for "dataUpdated" event
                function zoomChart() {
                    // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
                    chart.zoomToIndexes(chartData.length - 40, chartData.length - 1);
                }

                // changes cursor mode from pan to select
                function setPanSelect() {
                    if (document.getElementById("rb1").checked) {
                        chartCursor.pan = false;
                        chartCursor.zoomable = true;
                    } else {
                        chartCursor.pan = true;
                    }
                    chart.validateNow();
                }
            });
    </script>

    <style>
            .mb20 {
                    margin-bottom: 20px;
            }
            .bg-light-blue-gradient {
                    background: #3c8dbc !important;
                    background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #3c8dbc), color-stop(1, #67a8ce)) !important;
                    background: -ms-linear-gradient(bottom, #3c8dbc, #67a8ce) !important;
                    background: -moz-linear-gradient(center bottom, #3c8dbc 0%, #67a8ce 100%) !important;
                    background: -o-linear-gradient(#67a8ce, #3c8dbc) !important;
                    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#67a8ce', endColorstr='#3c8dbc', GradientType=0) !important;
                    color: #fff;
            }
    </style>
</div>