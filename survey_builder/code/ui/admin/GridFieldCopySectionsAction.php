<?php

/**
 * Copyright 2015 OpenStack Foundation
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
class GridFieldCopySectionsAction implements GridField_HTMLProvider, GridField_URLHandler
{
    protected $targetFragment;

    protected $gridField;

    protected $survey_report_id;

    private static $allowed_actions = array
    (
        'handleCopySectionsAction',
    );

    public function __construct($survey_report_id) {
        $this->targetFragment = 'header';
        $this->survey_report_id = $survey_report_id;
    }

    public function getHTMLFragments($gridField)
    {
        $this->gridField = $gridField;
        Requirements::javascript('survey_builder/js/GridFieldCopySectionsAction.js');
        Requirements::css('survey_builder/css/GridFieldCopySectionsAction.css');

        $reports = SurveyReport::get()
            ->filter(['ID:not' => $this->survey_report_id])
            ->limit(3)
            ->map('ID','Title');

        $field = new DropdownField(sprintf('%s[ReportID]', __CLASS__), '', $reports);
        $field->setEmptyString("-- select a report --");
        $field->addExtraClass('no-change-report');
        $field->addExtraClass('select-report-source');

        $data = new ArrayData(array(
            'Title'          => "Copy Sections from Report",
            'Link'           => Controller::join_links($gridField->Link(), 'copySectionsAction', '{ReportID}'),
            'ClassField' => $field,
        ));

        return array(
            $this->targetFragment => $data->renderWith(__CLASS__)
        );
    }

    public function getURLHandlers($gridField)
    {
        return array(
            'copySectionsAction/$ReportID!' => 'handleCopySectionsAction',
        );
    }

    public function handleCopySectionsAction($gridField, $request)
    {
        $source_report_id = intval($request->param('ReportID'));
        $report_id        = intval($request->param("ID"));
        $this->gridField  = $gridField;

        $source_report = SurveyReport::get()->byID($source_report_id);
        $report = SurveyReport::get()->byID($report_id);

        foreach($source_report->Sections() as $section) {
            if ( !$new_section = $report->Sections()->find('Name', $section->Name)) {
                $new_section = $section->duplicate(false);
                $new_section->ReportID = $report_id;
                $new_section->Graphs()->removeAll();
                $new_section->write();
            }
        }

        $response = new SS_HTTPResponse();
        $response->setStatusCode(200);
        return $response;
    }

}