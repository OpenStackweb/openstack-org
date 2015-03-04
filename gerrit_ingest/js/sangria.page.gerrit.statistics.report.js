/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
jQuery(document).ready(function($){

    //init map widget

    var places = [];

    for(var code in countries_with_commits){
        var country        = countries_with_commits[code];
        var country_data   = countries_data[code];
        if(country != undefined && country_data != undefined) {
            var country_color = country.color;
            country_data.label = country.name + ' (' + country.commits + ')';
            country_data.color = country_color
            country_data.url = '#';
            places.push(country_data);
        }
    }

    $('#map').google_map({
        places : places,
        getInfo:function(place){
            return '<b>'+place.label+'</b>';
        }
    });

    // Get the context of the canvas element we want to select
    var ctx1 = document.getElementById("myChart").getContext("2d");
    var ctx2 = document.getElementById("myChart2").getContext("2d");

    var options = {
        segmentShowStroke : true,
        segmentStrokeColor : "#fff",
        segmentStrokeWidth : 2,
        animationSteps : 100,
        animationEasing : "easeOutBounce",
        legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%>&nbsp;(<%if(segments[i].value){%><%=segments[i].value%><%}%>)</li><%}%></ul>"
    }

    var myPieChart = new Chart(ctx1).Pie(data_countries_with_commits, options);
    var myPieChart2 = new Chart(ctx2).Pie(data_users_with_commits, options);

    $("#myChart").click(
        function(evt){
            var activePoints = myPieChart.getSegmentsAtEvent(evt);
            var code         = countries_names[activePoints[0].label];
            if(code != 'NOT SET' ) {
                var country_data = countries_data[code];
                $('#map').google_map('setCenter', country_data.lat, country_data.lng)
            }
        }
    );

    var legend = myPieChart.generateLegend();
    $("#legend").html(legend);

    var legend2 = myPieChart2.generateLegend();
    $("#legend2").html(legend2);

    $('#collapse_coutries').click(buttonBehavior);
    $('#collapse_users').click(buttonBehavior);
});

function buttonBehavior() {
    if($('span',$(this)).hasClass('glyphicon-chevron-down'))
    {
        $(this).html('<span class="glyphicon glyphicon-chevron-up"></span>');
    }
    else
    {
        $(this).html('<span class="glyphicon glyphicon-chevron-down"></span>');
    }
}