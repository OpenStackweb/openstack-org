<?php

/**
 * Copyright 2016 OpenStack Foundation
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
 * Class COALandingPage
 */
class COALandingPage extends Page
{
    static $db = array(
        'BannerTitle'            => 'Text',
        'BannerText'             => 'HTMLText',
        'ExamDetails'            => 'HTMLText',
        'HandBookLink'           => 'Text',
        'GetStartedURL'          => 'Text',
        'GetStartedLabel'        => 'Text',
        'HideFee'                => 'Boolean',
        'AlreadyRegisteredURL'   => 'Text',
        'ExamCost'               => 'Text',
        'ExamSpecialCost'        => 'Text',
        'ExamCostSpecialOffer'   => 'HTMLText',
        'ExamFormat'             => 'HTMLText',
        'ExamIDRequirements'     => 'HTMLText',
        'ExamRetake'             => 'HTMLText',
        'ExamDuration'           => 'Text',
        'ExamSystemRequirements' => 'HTMLText',
        'ExamScoring'            => 'HTMLText',
        'ExamLanguage'           => 'HTMLText',
        'ExamHowLongSchedule'    => 'HTMLText',
        'GetStartedText'         => 'HTMLText',
        'HidePurchaseExam'       => 'Boolean',
        'HideVirtualExam'        => 'Boolean',
        'HideHowGetStarted'      => 'Boolean',
    );

    static $has_one = array
    (
        'HeroImage' => 'BetterImage',
    );

    static $many_many = array
    (
        'TrainingPartners' => 'Company'
    );

    static $many_many_extraFields = array(
        'TrainingPartners' => array(
            'Order' => "Int",
        ),
    );

    public function getBannerTitle(){
        $html = $this->getField('BannerTitle');
        if(empty($html)){
            $html = "Certified OpenStack Administrator";
        }
        return $html;
    }

    public function getBannerText(){
        $html = $this->getField('BannerText');
        if(empty($html)){
            $html = <<<HTML
<p>OpenStack skills are in high demand as thousands of companies around the world adopt and productize OpenStack.</p>
<p>Certified OpenStack Administrator (COA) is the first professional certification offered by the OpenStack Foundation. It’s designed to help companies identify top talent in the industry, and help job seekers demonstrate their skills.</p>
HTML;
        }
        return $html;
    }

    public function getExamDetails(){
        $html = $this->getField('ExamDetails');
        if(empty($html)){
            $html = <<<HTML
    <p>The Certified OpenStack Administrator is a professional typically with at least six months OpenStack experience, and has the skills required to provide day-to-day operation and management of an OpenStack cloud. To learn more about the knowledge requirements and domains covered in the exam, go to <a href="www.openstack.org/coa/requirements">www.openstack.org/coa/requirements</a>.</p>
HTML;
        }
        return $html;
    }

    public function getExamCost(){
        $html = $this->getField('ExamCost');
        if(empty($html)){
            $html = "$300";
        }
        return $html;
    }

    public function getExamSpecialCost(){
        $html = $this->getField('ExamSpecialCost');
        return $html;
    }

    public function getExamCostSpecialOffer(){
        $html = $this->getField('ExamCostSpecialOffer');
        return $html;
    }

    public function getExamDuration(){
        $html = $this->getField('ExamDuration');
        if(empty($html)){
            $html = "2.5 hours";
        }
        return $html;
    }

    public function getExamFormat(){
        $html = $this->getField('ExamFormat');
        if(empty($html)){
            $html = <<<HTML
     <p>The COA is a performance-based exam and Candidates will need to perform tasks or solve problems using the command line interface and Horizon dashboard. For exam security, Candidates are monitored virtually by a proctor during the exam session via streaming audio, video, and screensharing feeds. The screensharing feed allows proctors to view candidates' desktops (including all monitors). The audio, video and screensharing feeds will be stored for a limited period of time in the event that there is a subsequent need for review.</p>
HTML;
        }
        return $html;
    }

    public function getExamIDRequirements(){
        $html = $this->getField('ExamIDRequirements');
        if(empty($html)){
            $html = <<<HTML
    <p>Candidates are required to provide a means of photo identification before the Exam can be launched. Acceptable forms of photo ID include current, non-expired: passport, government-issued driver's license/permit, national ID card, state or province-issued ID card, or other form of government issued identification. If acceptable proof of identification is not provided to the exam proctor prior to the exam, entry to the exam will be refused. Candidates who are refused entry due to lack of sufficient ID will not be eligible for a refund or rescheduling.</p>
HTML;
        }
        return $html;
    }

    public function getExamRetake(){
        $html = $this->getField('ExamRetake');
        if(empty($html)){
            $html = <<<HTML
    <p>One (1) free retake per Exam purchase will be granted in the event that a passing score is not achieved and Candidate has not otherwise been deemed ineligible for Certification or retake. The free retake must be taken within 12 months of the date of the original Exam purchase.</p>
HTML;
        }
        return $html;
    }

    public function getExamSystemRequirements(){
        $html = $this->getField('ExamSystemRequirements');
        if(empty($html)){
            $html = <<<HTML
    <p>Candidates are required to provide their own front-end hardware (laptop or workstation) with Chrome or Chromium browser, reliable internet access, and a webcam and microphone in order to take exams. Candidates do not need to provide their own Linux installation or VM; they will be presented with a VM in their browser window using a terminal emulator. Candidates should use the <a href="https://www.examslocal.com/ScheduleExam/Home/CompatibilityCheck" target="_blank">compatibility check tool</a> to verify that their system and testing environment meet the minimum requirements.</p>
HTML;
        }
        return $html;
    }

    public function getExamScoring(){
        $html = $this->getField('ExamScoring');
        if(empty($html)){
            $html = <<<HTML
   <p>Upon completion, exams are scored automatically and a score report will be made available within three (3) business days. If a passing score of 76 or higher is achieved and other applicable requirements for Certification have been fulfilled, a notification indicating the Candidate has been successfully Certified will follow the score report. Candidate will receive a certificate and logo for personal use.</p>
HTML;
        }
        return $html;
    }

    public function getExamLanguage(){
        $html = $this->getField('ExamLanguage');
        if(empty($html)){
            $html = <<<HTML
     <p>The COA exam is currently offered in English.</p>
HTML;
        }
        return $html;
    }

    public function getExamHowLongSchedule(){
        $html = $this->getField('ExamHowLongSchedule');
        if(empty($html)){
            $html = <<<HTML
       <p>Exam may be scheduled anytime within <strong>12 months</strong> of purchase.</p>
HTML;
        }
        return $html;
    }

    public function getHandBookLink(){
        $html = $this->getField('HandBookLink');
        if(empty($html)){
            $html = <<<HTML
    #
HTML;
        }
        return $html;
    }

    public function getGetStartedURL(){
        $html = $this->getField('GetStartedURL');
        if(empty($html)){
            $html = "https://identity.linuxfoundation.org/openstack/pid/317";
        }
        return $html;
    }

    public function getGetStartedLabel(){
        $html = $this->getField('GetStartedLabel');
        if(empty($html)){
            $html = "How To Get Started";
        }
        return $html;
    }

    public function getAlreadyRegisteredURL(){
        $html = $this->getField('AlreadyRegisteredURL');
        if(empty($html)){
            $html = 'https://identity.linuxfoundation.org/portal/openstack';
        }
        return $html;
    }

    public function getHeroImageUrl(){
        $default_url = '/themes/openstack/images/coa/coa-bkgd2.jpg';
        if($this->HeroImage()->exists()){
            return $this->HeroImage()->Link();
        }
        return $default_url;
    }

    public function getGetStartedText(){
        $html = $this->getField('GetStartedText');
        if(empty($html)){
            $html = <<<HTML
       <p>The Certified OpenStack Administrator exam is the only professional certification offered by the OpenStack Foundation. It was written for OpenStack professionals with at least six months of experience managing an OpenStack cloud environment. You can learn more details about the exam below, or visit our Training Marketplace to find companies that can help you prepare and often bundle the exam with their training courses. To get started with a new exam purchase or to redeem a code, you'll be prompted to log into the COA portal with an OpenStackID or equivalent.</p>
HTML;
        }
        return $html;
    }

    function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Content');

        $fields->addFieldToTab('Root.Main', new LiteralField('HideSections','<label>Hide Sections</label><hr>'));
        $fields->addFieldToTab('Root.Main', new CheckboxField('HideFee','Hide Exam Fee and Pricing'));
        $fields->addFieldToTab('Root.Main', new CheckboxField('HidePurchaseExam','Hide Purchase Exam Section'));
        $fields->addFieldToTab('Root.Main', new CheckboxField('HideVirtualExam','Hide Virtual Exam Section'));
        $fields->addFieldToTab('Root.Main', new CheckboxField('HideHowGetStarted','Hide How to get started Section'));

        $fields->addFieldToTab('Root.Main', new TextField('BannerTitle', 'Banner Title'));
        $fields->addFieldToTab('Root.Main', $banner_html = new HtmlEditorField('BannerText', 'Banner Text'));
        $banner_html->setRows(5);
        $fields->addFieldToTab('Root.Main', $how_html = new HtmlEditorField('GetStartedText', 'How to Get Started'));
        $how_html->setRows(5);
        $fields->addFieldToTab('Root.Main', new TextField('HandBookLink', 'HandBook Link'));
        $fields->addFieldToTab('Root.Main', new TextField('GetStartedURL', 'Get Started URL'));
        $fields->addFieldToTab('Root.Main', new TextField('GetStartedLabel', 'Get Started Label'));

        $fields->addFieldToTab('Root.Main', new TextField('AlreadyRegisteredURL', 'Already Registered URL'));
        // exam details
        $fields->addFieldToTab('Root.ExamDetails', $html_details = new HtmlEditorField('ExamDetails', 'Details Title'));
        $html_details->setRows(5);
        $fields->addFieldToTab('Root.ExamDetails', new TextField('ExamCost', 'Price of Exam (include currency sign)'));
        $fields->addFieldToTab('Root.ExamDetails', new TextField('ExamSpecialCost', 'Discount Price (leave blank if not special)'));
        $fields->addFieldToTab('Root.ExamDetails', $html_format = new HtmlEditorField('ExamCostSpecialOffer', 'Special Offer (appears under price)'));
        $html_format->setRows(2);
        $fields->addFieldToTab('Root.ExamDetails', new TextField('ExamDuration', 'Duration ( include time unit)'));
        $fields->addFieldToTab('Root.ExamDetails', $html_format = new HtmlEditorField('ExamFormat', 'Format'));
        $html_format->setRows(5);
        $fields->addFieldToTab('Root.ExamDetails', $html_req = new HtmlEditorField('ExamIDRequirements', 'ID Requirements'));
        $html_req->setRows(5);
        $fields->addFieldToTab('Root.ExamDetails', $html_system = new HtmlEditorField('ExamSystemRequirements', 'System Requirements'));
        $html_system->setRows(5);
        $fields->addFieldToTab('Root.ExamDetails', $html_score = new HtmlEditorField('ExamScoring', 'Scoring'));
        $html_score->setRows(5);
        $fields->addFieldToTab('Root.ExamDetails', $html_lang = new HtmlEditorField('ExamLanguage', 'Language'));
        $html_lang->setRows(5);
        $fields->addFieldToTab('Root.ExamDetails', $html_duration = new HtmlEditorField('ExamHowLongSchedule', 'How long do I have to schedule my exam?'));
        $html_duration->setRows(5);
        $fields->addFieldToTab('Root.ExamDetails', $html_retake = new HtmlEditorField('ExamRetake', 'Retake'));
        $html_retake->setRows(5);

        if ($this->ID > 0) {

            $logo_field = new UploadField('HeroImage', 'Hero Image');
            $logo_field->setAllowedMaxFileNumber(1);
            $logo_field->setAllowedFileCategories('image');
            $logo_field->setFolderName('coa/hero_images/');
            $logo_field->getValidator()->setAllowedMaxFileSize(1048576);
            $fields->addFieldToTab('Root.Main', $logo_field);

            $config = GridFieldConfig_RelationEditor::create();

            $config->removeComponentsByType('GridFieldEditButton');
            $config->removeComponentsByType('GridFieldAddNewButton');
            $config->addComponent($sort = new GridFieldSortableRows('Order'));

            $partners = new GridField('TrainingPartners', 'Training Partners', $this->TrainingPartners(), $config);
            $fields->addFieldsToTab('Root.TrainingPartners',$partners);

        }

        return $fields;
    }


}

/**
 * Class COALandingPage_Controller
 */
class COALandingPage_Controller extends Page_Controller
{
    static $allowed_actions = array
    (
        'index',
        'getStarted',
        'alreadyRegistered'
    );

    static $url_handlers = array
    (
        'get-started'        => 'getStarted',
        'already-registered' => 'alreadyRegistered',
    );

    function init()
    {
        parent::init();
    }

    protected function handleAction($request, $action)
    {
        return parent::handleAction($request, $action);
    }

    public function index(){
        Requirements::css('coa/css/coa.css');
        return $this->getViewer('index')->process($this);
    }

    public function getStarted(){
        if(Member::currentUser()){
            $this->redirect($this->GetStartedURL);
        }
        OpenStackIdCommon::doLogin($this->Link('get-started'));
    }

    public function alreadyRegistered(){
        if(Member::currentUser()){
            $this->redirect($this->AlreadyRegisteredURL);
        }
        OpenStackIdCommon::doLogin($this->Link('already-registered'));
    }

}