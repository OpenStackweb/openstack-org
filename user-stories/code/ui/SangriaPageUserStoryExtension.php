<?php
/**
 * Copyright 2017 Openstack Foundation
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
 * Class SangriaPageUserStoryExtension
 */
final class SangriaPageUserStoryExtension extends Extension {

	private $repository;

    static $url_handlers = array(
        'user-stories/new'          => 'addUserStory',
        'user-stories/edit/$ID'     => 'editUserStory',
        'user-stories/delete/$ID'   => 'deleteUserStory',
        'user-stories'              => 'manageUserStories'
    );

	public function __construct(){
		parent::__construct();
		$this->repository = new SapphireUserStoryRepository;
	}

	public function onBeforeInit(){
        $allowed_actions = array('manageUserStories','addUserStory','editUserStory','saveUserStory','deleteUserStory','UserStoryForm');

		Config::inst()->update(get_class($this), 'allowed_actions', $allowed_actions);
		Config::inst()->update(get_class($this->owner), 'allowed_actions', $allowed_actions);
	}

    public function getUserStories() {
        return $this->repository->getAllStories();
    }

    public function UserStoryForm() {
        $this->commonScripts();
        JSChosenDependencies::renderRequirements();

        Requirements::css('user-stories/css/user.story.form.css');
        SweetAlert2Dependencies::renderRequirements();
        BootstrapTagsInputDependencies::renderRequirements();;
        Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
        Requirements::javascript("user-stories/js/user.story.form.js");

        $story_id = intval($this->owner->request->param('ID'));
        $user_story = $this->repository->getById($story_id);

        $form = new UserStoryForm($this->owner, 'UserStoryForm');
        $data = Session::get("FormInfo.{$form->FormName()}.data");

        // we should also load the data stored in the session. if failed
        if(is_array($data)) {
            $form->loadDataFrom($data);
        } else if ($user_story) {
            $form->loadDataFrom($user_story);
            $form->loadTagsData($user_story);
        }
        // Optional spam protection
        if(class_exists('SpamProtectorManager')) {
            SpamProtectorManager::update_form($form);
        }

        return $form;
    }

    public function manageUserStories(){
        $this->commonScripts();
        if ($this->owner->request->getVar('saved')) {
            Requirements::customScript("var saved = 1;");
        }
        Requirements::javascript('node_modules/notifyjs-browser/dist/notify.js');
        Requirements::javascript('user-stories/js/user.story.manage.js');
        Requirements::css('user-stories/css/user.story.manage.css');
        return $this->owner->getViewer('manageUserStories')->process($this->owner);
    }

    public function editUserStory() {
        $story_id = (int)$this->owner->request->param('ID');
        return $this->owner->getViewer('editUserStory')->process($this->owner->customise(['StoryID' => $story_id]));
    }

    public function addUserStory() {
        return $this->owner->getViewer('editUserStory')->process($this->owner);
    }

    public function saveUserStory($data, $form) {
        try {
            $story = ($data['ID'] != 0) ? UserStoryDO::get()->byID($data['ID']) : new UserStoryDO();
            $form->saveInto($story);
            $story->write();
            $redirect = HTTP::setGetVar('saved', 1, $this->owner->Link('user-stories'));
            return $this->owner->redirect($redirect);
        }
        catch(EntityValidationException $ex1){
            Form::messageForForm($form->FormName(), $ex1->getMessage(), 'bad');
            //Return back to form
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->redirectBack();
        }
        catch(Exception $ex){
            Form::messageForForm($form->FormName(), "There was an error with your request, please contact your admin.", 'bad');
            //Return back to form
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->redirectBack();
        }
    }

    public function deleteUserStory() {
        $story_id = (int)$this->owner->request->param('ID');
        UserStoryDO::delete_by_id('UserStoryDO',$story_id);
        return $this->owner->redirectBack();
    }

	private function commonScripts(){
        Requirements::block('themes/openstack/css/deployment.survey.page.css');
        Requirements::block('themes/openstack/css/blueprint/screen.css');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');
	}





} 
