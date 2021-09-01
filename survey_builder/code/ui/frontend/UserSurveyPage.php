<?php

/**
 * Copyright 2015 Open Infrastructure Foundation
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
class UserSurveyPage extends SurveyPage
{
    static $db = array
    (
        'LoginPageTitle'         => 'HTMLText',
        'LoginPageContent'       => 'HTMLText',
        'LoginPageSlide1Content' => 'HTMLText',
        'LoginPageSlide2Content' => 'HTMLText',
        'LoginPageSlide3Content' => 'HTMLText',
    );

    function getCMSFields()
    {
        $fields = parent::getCMSFields();
        //login page content
        $fields->addFieldToTab('Root.Login', new HtmlEditorField('LoginPageTitle', 'Page Main Title', 10));
        $fields->addFieldToTab('Root.Login', new HtmlEditorField('LoginPageContent', 'Content'));
        $fields->addFieldToTab('Root.Login', new HtmlEditorField('LoginPageSlide1Content', 'Slide #1 Content', 20));
        $fields->addFieldToTab('Root.Login', new HtmlEditorField('LoginPageSlide2Content', 'Slide #2 Content', 20));
        $fields->addFieldToTab('Root.Login', new HtmlEditorField('LoginPageSlide3Content', 'Slide #3 Content', 20));

        return $fields;
    }

    public function getLoginPageTitle()
    {
        $res = (string)$this->getField('LoginPageTitle');
        if (empty($res)) {
            $res = 'OpenStack User Survey: Welcome!';
        }
        return $res;
    }

    public function getLoginPageContent()
    {
        $link = Controller::curr()->Link();
        $res = (string)$this->getField('LoginPageContent');
        if (empty($res)) {
            $res = <<< HTML
			<p>The OpenStack User Survey provides users an opportunity to influence the community and software direction. The anonymized information is shared with OpenStack project team leads as well as the OpenStack Technical Committee. By sharing information about your configuration and requirements, the Open Infrastructure Foundation and OpenStack community leaders will be able to advocate on your behalf. The 2022 survey results will be presented in Q4 2022. Take the OpenStack User Survey by Wednesday, August 31, 2022 to be included in this round of analysis.</p>
		<p>Learn more about <a href="https://superuser.openstack.org/articles/operate-openstack-take-the-survey-heres-why-it-matters/" target="_blank">why taking the OpenStack Survey is important</a> for the software development, and take a look at the <a href="https://governance.openstack.org/tc/user_survey/analysis-12-2019.html" target="_blank">OpenStack Technical Committee’s 2019 feedback analysis</a>.</p>
		<hr/>
HTML;
        }
        return $res;
    }

    public function getLoginPageSlide1Content()
    {
        $res = (string)$this->getField('LoginPageSlide1Content');
        if (empty($res)) {
            $res = 'This is the <strong>OpenStack User Survey</strong> for OpenStack cloud users and operators.';
        }
        return $res;
    }


    public function getLoginPageSlide2Content()
    {
        $res = (string)$this->getField('LoginPageSlide2Content');
        if (empty($res)) {
            $res = 'For most users, it does not take long to complete. Timing may vary based on number of deployments.';
        }
        return $res;
    }

    public function getLoginPageSlide3Content()
    {
        $res = (string)$this->getField('LoginPageSlide3Content');
        if (empty($res)) {
            $res = 'All of the information you provide is <strong>confidential</strong> to the Foundation (unless you specify otherwise).';
        }
        return $res;
    }

}

class UserSurveyPage_Controller extends SurveyPage_Controller
{

    static $allowed_actions = array
    (
        'LandingPage',
        'RenderSurvey',
        'SurveyStepForm',
        'SkipStep',
        'RegisterForm',
        'MemberStart',
        'StartSurvey',
        'NextStep',
        'SurveyDynamicEntityStepForm',
        'NextDynamicEntityStep',
        'AddEntity',
        'EditEntity',
        'DeleteEntity',
    );

    function init()
    {
        parent::init();
    }
        /**
     * @return HTMLText
     */
    public function LandingPage()
    {
        return $this->customise(array('BackURL' => $this->request->requestVar('BackURL')))->renderWith(['UserSurveyPage_LandingPage', 'Page']);
    }

}
