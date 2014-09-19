<?php
/**
 * Defines the DeploymentSurveyPage
 */

class DeploymentSurveyPage extends Page
{
    static $db = array();
    static $has_one = array();

    function getCMSFields()
    {
        $fields = parent::getCMSFields();

        return $fields;
    }
}

class DeploymentSurveyPage_Controller extends Page_Controller
{

    static $allowed_actions = array(
        'Login',
        'OrgInfo',
        'AppDevSurvey',
        'Deployments',
        'DeploymentDetails',
        'MoreDeploymentDetails',
        'ThankYou',
        'Form',
        'RemoveDeployment',
        'AddDeployment',
        'SkipDeployments',
        'logout',
        'StartSurvey',
        'RegisterForm',
        'SavePasswordForm',
        'SavePassword',
        'CheckEmail',
        'MemberStart'
    );


    public function CheckEmail()
    {
        $email = $this->request->getVar('Email');
        //Check for existing member email address
        $res = true;
        if ($member =Member::get()->filter('Email',Convert::raw2sql($email))->first() ) {
            $res = false;
        }
        echo json_encode($res);
    }

    function init()
    {
        parent::init();

        // require custom CSS
        Requirements::css("themes/openstack/css/user-survey.css");

        Requirements::css("themes/openstack/css/chosen.css", "screen,projection");
        Requirements::css("themes/openstack/css/jquery.autocomplete.css");


        Requirements::block(SAPPHIRE_DIR . '/thirdparty/behaviour/behaviour.js');
        Requirements::block(SAPPHIRE_DIR . '/thirdparty/prototype/prototype.js');
        Requirements::block(SAPPHIRE_DIR . '/javascript/prototype_improvements.js');


      Requirements::javascript("themes/openstack/javascript/chosen.jquery.min.js");
      Requirements::javascript("themes/openstack/javascript/jquery.autocomplete.min.js");


        Requirements::customScript("
                jQuery(document).ready(function() {

                $('#DeploymentSurveyOrgInfoForm_Form_PrimaryCountry').chosen();

                $('#DeploymentSurveyOrgInfoForm_Form_Organization').autocomplete('/join/register/results', {
                      minChars: 3,
                      selectFirst: true,
                      autoFill: true,
                  });

            });

      ");

        // No one is logged in
        if (!Member::currentUser()) {

            // These are the actions available to non-logged in members
            $nonLoggedInAvailablActions = array('Login', 'MemberStart', 'StartSurvey', 'DeploymentSurveyRegistrationForm', 'RegisterForm', 'faq', 'CheckEmail');

            if (!in_array($this->request->param('Action'), $nonLoggedInAvailablActions)) {
                $this->redirect($this->Link() . 'Login');
            }

            // A Member is logged in
        } else {

            if ($this->request->param('Action') == NULL) {
                // If there's no action (step) specified, look for the current step using the function below
                $CurrentStep = $this->CurrentStep();
                // Go to the new step
                $this->redirect($this->Link() . $CurrentStep);

            } else {
                // Use the controller action as the current step and set in session
                $DesiredStep = $this->request->param('Action'); // the page requested
                if ($DesiredStep == 'Login') return;

                $survey = $this->GetCurrentSurvey(); // the current survey

                $DesiredStepIndex = array_search($DesiredStep, DeploymentSurvey::$steps); // The index of this step in the list
                $HighestStepAllowedIndex = array_search($survey->HighestStepAllowed, DeploymentSurvey::$steps); // The index of the highest allowed step in the list

                // Set the current step to the new desired step as long as the previous step was completed.
                if ($DesiredStepIndex !== FALSE && $DesiredStepIndex <= ($HighestStepAllowedIndex)) {
                    $survey->CurrentStep = $DesiredStep;
                    $survey->write();
                } elseif ($DesiredStepIndex !== FALSE) {
                    $this->redirect($this->Link() . $survey->CurrentStep);
                }
            }

        }


    }

    function GetCurrentSurvey() {
        // Look for an existing survey
        if ($CurrentUserID = Member::currentUserID()) {
            // look for a deployment survey for this user

            $DeploymentSurvey =  DeploymentSurvey::get()->filter('MemberID',$CurrentUserID)->first();
            if (!$DeploymentSurvey) {
                // Create a new deployment survey
                $DeploymentSurvey = new DeploymentSurvey();
                $DeploymentSurvey->MemberID = $CurrentUserID;
                $DeploymentSurvey->CurrentStep = 'OrgInfo';
	            $DeploymentSurvey->UpdateDate = SS_Datetime::now()->Rfc2822();
                $DeploymentSurvey->Write();
            }

            return $DeploymentSurvey;

        }
    }

    // Looks up the current step of the process
    function CurrentStep() {
        // Check the database for a DeploymentSurvey with a current step
        if ($CurrentUserID = Member::currentUserID()) {
            // look for a deployment survey for this user
            $DeploymentSurvey =  DeploymentSurvey::get()->filter('MemberID',$CurrentUserID)->first();
            if ($DeploymentSurvey && $DeploymentSurvey->CurrentStep != NULL) {
                $CurrentStep = $DeploymentSurvey->CurrentStep;
            } else {
                // member is logged in, but has no current step in a deployment survey
                $CurrentStep = 'OrgInfo'; // 1st Step of survey
            }

        } else {
            // No one logged in
            $CurrentStep = 'Login';
        }

        return $CurrentStep;
    }

    public function NextStep($data, $form) {
        // Save our work
        $survey = $this->GetCurrentSurvey();
        $form->saveInto($survey);
        //Update Member if need be
        if (isset($data['Organization'])){
	        $org_data  = Convert::raw2sql(trim($data['Organization']));
	        if(!empty($org_data)){
		        $org = Org::get()->filter('Name',$org_data)->first();
			    if(!$org){
				        $org = new Org;
				        $org->Name = $org_data;
				        $org->IsStandardizedOrg = false;
				        $org->write();
				        //register new request
				        $new_request = new OrganizationRegistrationRequest();
				        $new_request->MemberID = Member::currentUser()->ID;
				        $new_request->OrganizationID = $org->ID;
				        $new_request->write();
		        }
		        $this->updateMember($org_data);
		        $survey->OrgID      = $org->ID;
		        $survey->UpdateDate = SS_Datetime::now()->Rfc2822();
		        $survey->write();
	        }
        }
        $newIndex = array_search($this->CurrentStep(), DeploymentSurvey::$steps) + 1;
        if ($newIndex > count(DeploymentSurvey::$steps)) $newIndex = count(DeploymentSurvey::$steps);
        $CurrentStep = DeploymentSurvey::$steps[$newIndex];
        Session::set('CurrentStep', $CurrentStep);
        $survey->CurrentStep = $CurrentStep;
        $survey->HighestStepAllowed = $CurrentStep;
	    $survey->UpdateDate         = SS_Datetime::now()->Rfc2822();
        $survey->write();
        $this->redirect($this->Link() . $CurrentStep);
    }

    public function Login()
    {
        return array();
    }

    public function MemberStart() {
        $member = null;
		if (isset($_REQUEST['m'])) {
			$member =Member::get()->byID((int)$_REQUEST['m']);
		}

		// Check whether we are merely changin password, or resetting.
		if(isset($_REQUEST['t']) && $member && $member->validateAutoLoginToken($_REQUEST['t'])) {
		    $member->logIn();
            return $this->redirect($this->Link() . "OrgInfo");
		} elseif(Member::currentUser()) {
            return $this->redirect($this->Link() . "OrgInfo");
		} else {
            return $this->redirect($this->Link() . "OrgInfo");
		}

    }

    public function RegisterForm()
    {
        return new DeploymentSurveyRegistrationForm($this, 'RegisterForm');
    }

    public function SavePasswordForm()
    {
        return new DeploymentSurveySavePasswordForm($this, 'SavePasswordForm');
    }

    // Populate the template's $Form area with the proper form depending on the current step for the user
    public function Form()
    {

        $currentForm = 'DeploymentSurvey' . $this->currentStep() . 'Form';
        $form = new $currentForm($this, 'Form');

        // Load the member's survey to use populate the form
        $survey = $this->GetCurrentSurvey();

        // But check to see if there are any form errors (otherwise you'll overwrite the session data)
        $errors = Session::get('FormInfo.' . $form->FormName() . '.errors');

        if ($survey && !$errors) $form->loadDataFrom($survey->data());
        return $form;
    }

    // Used in the DeploymentSurveyPage_Deployments.ss template
    public function DeploymentList()
    {
        $survey = $this->GetCurrentSurvey();
        if ($survey && $survey->Deployments()) {
            $survey->HighestStepAllowed = 'DeploymentDetails';
	        $survey->UpdateDate         = SS_Datetime::now()->Rfc2822();
            $survey->write();
            return $survey->Deployments();
        }

    }

    public function DeploymentDetails()
    {
        if (isset($_GET['DeploymentID'])) {
            Session::set('CurrentDeploymentID', $_GET['DeploymentID']);
        } else {
            Session::clear('CurrentDeploymentID');
        }

        return array();
    }

    public function LoadDeployment($id)
    {
        if ($id && is_numeric($id) && $CurrentDeployment =  Deployment::get()->byID($id)) {
            $DeploymentSurvey = $CurrentDeployment->DeploymentSurvey();
            if ($DeploymentSurvey && $DeploymentSurvey->MemberID == Member::currentUserID()) return $CurrentDeployment;
        }

        return NULL;

    }

    public function LoadAppDevSurvey()
    {
        $survey = $this->GetCurrentSurvey();
        if ($survey && $survey->AppDevSurveys() && $survey->AppDevSurveys()->Count() > 0) {
            return $survey->AppDevSurveys()->First();
        }

        return NULL;
    }

    public function RemoveDeployment()
    {

        if (isset($_GET['DeploymentID'])) $id = convert::raw2sql($_GET['DeploymentID']);

        if ($id && $Deployment = $this->LoadDeployment($id)) {
            $Deployment->Delete();
            $this->redirectBack();
        }
    }

    public function UpdateMember($Organization)
    {
        $Member = Member::currentUser();
        $NewOrg = convert::raw2sql($Organization);

        // If a new org name was provided for the member, find / create the new org and update the member record
        if (!is_null($NewOrg) && !$Member->hasCurrentAffiliation($NewOrg)) {
            $newAffiliation = new StdClass;
            $newAffiliation->StartDate = date('Y-m-d');
            $newAffiliation->EndDate   = null;
            $newAffiliation->Current   = 1;
            $newAffiliation->JobTitle  = "";
            $newAffiliation->Role      = "";
            AffiliationController::Save(new Affiliation(),$newAffiliation,$NewOrg,$Member);
        }
    }

    function AddDeployment()
    {
        $survey = $this->GetCurrentSurvey();
        $survey->CurrentStep = 'DeploymentDetails';
        $survey->HighestStepAllowed = 'DeploymentDetails';
	    $survey->UpdateDate         = SS_Datetime::now()->Rfc2822();
        $survey->write();
        $this->redirect($this->Link() . 'DeploymentDetails');
    }

    function SkipDeployments()
    {
        $survey                     = $this->GetCurrentSurvey();
        $survey->CurrentStep        = 'ThankYou';
        $survey->HighestStepAllowed = 'ThankYou';
	    $survey->UpdateDate         = SS_Datetime::now()->Rfc2822();
        $survey->write();
        $this->redirect($this->Link() . 'ThankYou');
    }

    function logout()
    {
        Security::logout(true);
    }

    function ThankYou()
    {

        $survey = $this->GetCurrentSurvey();

        if ($survey->BeenEmailed != TRUE && EmailValidator::validEmail($survey->Member()->Email)) {

            //Send email to submitter
            $To = $survey->Member()->Email;
            $Subject = "The OpenStack User Survey: Thank You!";
            $email = EmailFactory::getInstance()->buildEmail(DEPLOYMENT_SURVEY_THANK_U_FROM_EMAIL, $To, $Subject);
            $email->setTemplate('DeploymentSurveyEmail');
            $email->populateTemplate($survey);
            $email->send();

            // Set flag in DB that this user has been emailed
            $survey->BeenEmailed = TRUE;
	        $survey->UpdateDate = SS_Datetime::now()->Rfc2822();
            $survey->write();
        }

        return array();

    }

}