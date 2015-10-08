var timeline_data = null;

function renderTimeline() {
    $(document).ready(function () {
        _createTimeline(timeline_data["timeline"]);
    });
}

function _createTimeline(data) {

    var plot = $.jqplot('timeline', data,
        {
            gridPadding: {
                right: 35
            },
            cursor: {
                show: false
            },
            highlighter: {
                show: true,
                sizeAdjust: 6
            },
            axes: {
                xaxis: {
                    tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                    tickOptions: {
                        fontSize: '8pt',
                        angle: -90,
                        formatString: '%b \'%y'
                    },
                    renderer: $.jqplot.DateAxisRenderer,
                    tickInterval: '1 month'
                },
                yaxis: {
                    min: 0,
                    label: ''
                },
                y2axis: {
                    min: 0,
                    label: ''
                }
            },
            series: [
                {
                    shadow: false,
                    fill: true,
                    fillColor: '#4bb2c5',
                    fillAlpha: 0.3
                },
                {
                    shadow: false,
                    fill: true,
                    color: '#4bb2c5',
                    fillColor: '#4bb2c5'
                },
                {
                    shadow: false,
                    lineWidth: 1.5,
                    showMarker: true,
                    markerOptions: { size: 5 },
                    yaxis: 'y2axis'
                }
            ]
        }
    );
}
