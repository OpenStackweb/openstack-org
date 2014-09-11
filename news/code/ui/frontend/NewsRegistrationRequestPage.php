<?php

/**
 * Class NewsRegistrationRequestPage_Controller
 */
final class NewsRegistrationRequestPage_Controller extends Page_Controller {

    //Allow our form as an action
    static $allowed_actions = array(
        'NewsRegistrationRequestForm',
        'saveNewsArticle',
    );

    /**
     * @var NewsRegistrationRequestManager
     */
    private $manager;

	public function __construct(){
		parent::__construct();
		$this->news_repository = new SapphireNewsRepository();
        $this->manager = new NewsRegistrationRequestManager(
            new SapphireNewsRepository,
            new NewsFactory,
            new NewsValidationFactory,
            new SapphireNewsPublishingService,
            SapphireTransactionManager::getInstance()
        );
	}

	public function logout(){
		$current_member = Member::currentUser();
		if($current_member){
			$current_member->logOut();
			return Controller::curr()->redirect("Security/login?BackURL=" . urlencode($_SERVER['HTTP_REFERER']));
		}
		return Controller::curr()->redirectBack();
	}

    public function index(){
        return $this->renderWith(array('NewsRegistrationRequestPage','Page'));
    }

    public function NewsRegistrationRequestForm() {
        $this->commonScripts();
        Requirements::css('news/code/ui/frontend/css/news.form.css');
        Requirements::javascript("news/code/ui/frontend/js/news.form.js");
        $data = Session::get("FormInfo.Form_NewsRegistrationRequestForm.data");
        $form = new NewsRegistrationRequestForm($this, 'NewsRegistrationRequestForm',false);
        // we should also load the data stored in the session. if failed
        if(is_array($data)) {
            $form->loadDataFrom($data);
        }
        // Optional spam protection
        if(class_exists('SpamProtectorManager')) {
            SpamProtectorManager::update_form($form);
        }
        return $form;
    }

    private function commonScripts(){
        Requirements::css("themes/openstack/css/chosen.css", "screen,projection");
        Requirements::css("themes/openstack/javascript/jquery-ui-1.10.3.custom/css/smoothness/jquery-ui-1.10.3.custom.min.css");
        Requirements::css("events/css/sangria.page.view.event.details.css");
        Requirements::javascript("themes/openstack/javascript/chosen.jquery.min.js");
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol()."ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript("themes/openstack/javascript/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js");
        Requirements::javascript("themes/openstack/javascript/jquery.ui.datepicker.validation.package-1.0.1/jquery.ui.datepicker.validation.js");
        Requirements::javascript("themes/openstack/javascript/jquery.validate.custom.methods.js");
        Requirements::javascript('marketplace/code/ui/admin/js/utils.js');
        Requirements::javascript('themes/openstack/javascript/jquery.cleanform.js');
    }

    function saveNewsArticle($data, Form $form){
        try{
            $this->manager->registerNewsRegistrationRequest($data);
            Session::clear("FormInfo.Form_NewsRegistrationRequestForm.data");
            return Director::redirect($this->Link('?saved=1'));
        }
        catch(EntityValidationException $ex1){
            $messages = $ex1->getMessages();
            $msg = $messages[0];
            $form->addErrorMessage('Headline',$msg['message'] ,'bad');
            SS_Log::log($msg['message'] ,SS_Log::ERR);
            // Load errors into session and post back
            Session::set("FormInfo.Form_NewsRegistrationRequestForm.data", $data);
            return $this->redirectBack();
        }
        catch(Exception $ex){
            $form->addErrorMessage('Headline','Server Error','bad');
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            // Load errors into session and post back
            Session::set("FormInfo.Form_NewsRegistrationRequestForm.data", $data);
            return $this->redirectBack();
        }
    }

    //Check for just saved
    function Saved(){
        return $this->request->getVar('saved');
    }
} 