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
    static $db = [];
    static $has_one  = [];

    /**
     * @return SurveyReportPage
     */
    static public function getLive(){
        $page = Versioned::get_by_stage('SurveyReportPage', 'Live')->first();
        if(is_null($page))
            $page = Versioned::get_by_stage('SurveyReportPage', 'Stage')->first();
        return $page;
    }
}

class SurveyReportPage_Controller extends Page_Controller
{

    private static $allowed_actions = array(
    );

    function init()
    {
        parent::init();

        //if(!Permission::checkMember(Member::currentUser(),"ADMIN")) Security::permissionFailure($this);

        JQPlotDependencies::renderRequirements();
        //other js
        Requirements::javascript('themes/openstack/javascript/jquery-ajax-loader.js');
        Requirements::javascript('node_modules/jspdf/dist/jspdf.min.js');
        Requirements::javascript('node_modules/html2canvas/dist/html2canvas.min.js');
        // require custom CSS
        Requirements::css("survey_builder/css/survey-report.css");
        //Requirements::javascript('survey_builder/js/survey_report.js');
    }

    function getSurveyTemplates() {
        $curDate = date("Y-m-d");

        $templates = SurveyTemplate::get('SurveyTemplate')
            ->filter(array('ClassName'=>'SurveyTemplate'))
            ->exclude('StartDate:GreaterThan', $curDate);

        $available_report_templates = new ArrayList();

        foreach ($templates as $template) {
            if ($template->Report()->Exists() && $template->Report()->Display) {
                $available_report_templates->push($template);
            }
        }
        
        return $available_report_templates;
    }


}
