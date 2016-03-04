<?php
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

/**
 * Defines the SurveyReportPage
 */
class SurveyReportPage extends Page
{
    static $db = array(
    );

    static $has_one = array();

}

class SurveyReportPage_Controller extends Page_Controller
{

    private static $allowed_actions = array(
    );

    function init()
    {
        parent::init();

        Requirements::css("themes/openstack/bower_assets/jqplot-bower/dist/jquery.jqplot.min.css");
        //jqplot and plugins ...
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/jquery.jqplot.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.canvasAxisTickRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.dateAxisRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.cursor.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.categoryAxisRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.canvasTextRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.canvasOverlay.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.enhancedLegendRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.json2.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.logAxisRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.pointLabels.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.trendline.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.barRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.pieRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.bubbleRenderer.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.canvasAxisLabelRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.highlighter.min.js");
        //other js
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('themes/openstack/bower_assets/jspdf/dist/jspdf.min.js');
        Requirements::javascript('themes/openstack/bower_assets/html2canvas/build/html2canvas.min.js');
        // require custom CSS
        Requirements::css("survey_builder/css/survey-report.css");
        Requirements::javascript('survey_builder/js/survey_report.js');

    }

    function getSurveyTemplates() {
        $templates = SurveyTemplate::get('SurveyTemplate')->filter(array('ClassName'=>'SurveyTemplate'));
        return $templates;
    }


}
