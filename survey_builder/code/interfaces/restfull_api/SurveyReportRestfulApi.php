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
class SurveyReportRestfulApi extends AbstractRestfulJsonApi
{

    const ApiPrefix = 'api/v1/survey_report';

    protected function isApiCall()
    {
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
    }


    /**
     * @return bool
     */
    protected function authorize()
    {
        return $this->checkOwnAjaxRequest($this->getRequest());
    }

    public function authenticate() {
        return true;
    }

    private static $allowed_actions = array
    (
        'getReportTemplate',
        'getReport',
    );

    static $url_handlers = array
    (
        'GET report_template/$SURVEY_TEMPLATE_ID!'                        => 'getReportTemplate',
        'GET report/$SURVEY_TEMPLATE_ID!'                                 => 'getReport',
    );

    public function getReportTemplate(SS_HTTPRequest $request)
    {
        if (!Director::is_ajax()) return $this->forbiddenError();

        $template_id = (int)$request->param('SURVEY_TEMPLATE_ID');

        try {
            $survey_template = SurveyTemplate::get_by_id('SurveyTemplate',$template_id);
            if(is_null($survey_template)) return $this->httpError(404);

            return $this->ok($survey_template->Report()->mapTemplate());
        }
        catch(Exception $ex)
        {
            return $ex->getMessage();
        }
    }

    public function getReport(SS_HTTPRequest $request)
    {
        if (!Director::is_ajax()) return $this->forbiddenError();

        $template_id = (int)$request->param('SURVEY_TEMPLATE_ID');
        $section_id  = (int)$request->getVar('section_id');
        $filters     = json_decode($request->getVar('filters'));

        try {
            $survey_template = SurveyTemplate::get_by_id('SurveyTemplate',$template_id);
            if(is_null($survey_template)) return $this->httpError(404);

            $section = $survey_template->Report()->Sections()->filter('ID',$section_id)->first();

            return $this->ok($section->mapSection($filters));
        }
        catch(Exception $ex)
        {
            return $ex->getMessage();
        }
    }


}