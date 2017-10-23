<?php
/**
 * Copyright 2017 OpenStack Foundation
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

final class JQPlotDependencies
{
    public static function renderRequirements(){
        // this should be moved to NPM but so far there isnt any good npm distribution
        // css
        Requirements::css("themes/openstack/javascript/jquery.jqplot/jquery.jqplot.min.css");
        // js
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/jquery.jqplot.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.dateAxisRenderer.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.cursor.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.categoryAxisRenderer.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.canvasTextRenderer.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.canvasOverlay.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.enhancedLegendRenderer.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.json2.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.logAxisRenderer.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.pointLabels.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.trendline.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.barRenderer.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.pieRenderer.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.bubbleRenderer.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery.jqplot/plugins/jqplot.highlighter.min.js");
    }
}