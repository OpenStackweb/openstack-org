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
class EditProfilePage extends Page
{
}

class EditProfilePage_Controller extends Page_Controller
{
    /**
     * @var IMemberManager
     */
    private $member_manager;

    /**
     * @return IMemberManager
     */
    public function getMemberManager()
    {
        return $this->member_manager;
    }

    /**
     * @param IMemberManager $manager
     */
    public function setMemberManager(IMemberManager $manager)
    {
        $this->member_manager = $manager;
    }

    static $allowed_actions = array
    (
        'EditProfileForm',
        'LoginForm',
        'agreements',
        'resign',
        'CandidateApplication',
        'CandidateApplicationForm',
        'training',
        'trainingAddCourse',
        'AddTrainingCourseForm',
        'trainingEdit',
        'trainingDelete',
        'marketplace_administration',
        'SaveProfile',
        'marketplace',
        'speaker',
        'EditSpeakerProfileForm',
        'downgrade2communitymember',
        'upgrade2foundationmember',
    );

    /**
     * @var TrainingManager
     */
    private $training_manager;
    /**
     * @var ICourseRepository
     */
    private $course_repository;

    /**
     * @var ITrainingRepository
     */
    private $training_repository;

    /**
     * @var CourseManager
     */
    private $course_manager;

    function init()
    {
        parent::init();

        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');
        JSChosenDependencies::renderRequirements();
        JQueryValidateDependencies::renderRequirements();

        $css_files = array(
            'registration/css/edit.profile.page.css',
        );

        foreach ($css_files as $css_file)
            Requirements::css($css_file);

        Requirements::combine_files('edit_profile_page.js', array(
            "node_modules/pure/libs/pure.min.js",
            "themes/openstack/javascript/jquery.serialize.js",
            "themes/openstack/javascript/jquery.cleanform.js",
            'registration/javascript/edit.profile.page.js'
        ));

        $this->course_repository   = new SapphireCourseRepository;
        $this->training_repository = new SapphireTrainingServiceRepository;

        $this->course_manager = new CourseManager(
            $this->training_repository,
            new SapphireTrainingCourseTypeRepository,
            new SapphireTrainingCourseLevelRepository,
            new SapphireCourseRelatedProjectRepository,
            $this->course_repository,
            new TrainingFactory,
            SapphireTransactionManager::getInstance());

        $this->training_manager = new TrainingManager($this->training_repository,
            new SapphireMarketPlaceTypeRepository,
            new TrainingAddPolicy,
            new TrainingShowPolicy,
            new SessionCacheService,
            new MarketplaceFactory,
            SapphireTransactionManager::getInstance()
        );
    }


    // The first tab of the profile page. This is called in the EditProfilePage.ss template.
    public function EditProfileForm()
    {
        if ($CurrentMember = Member::currentUser()) {
            $EditProfileForm = new EditProfileForm($this, 'EditProfileForm');

            if (!$this->Error()) {
                //Populate the form with the current members data
                $EditProfileForm->loadDataFrom($CurrentMember->data());
            }

            return $EditProfileForm;
        }
    }

    public function EditSpeakerProfileForm()
    {
        if ($CurrentMember = Member::currentUser()) {
            Requirements::css("registration/css/speaker.profile.form.css");
            Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');
            BootstrapTagsInputDependencies::renderRequirements();

            // languages
            $languages_source = 'var language_source = [];'.PHP_EOL;
            foreach(Language::get() as $lang){
                $languages_source .= sprintf("language_source.push({id: %s, name:'%s'});".PHP_EOL, $lang->ID, $lang->Name);
            }

            Requirements::customScript($languages_source);

            $speaker = PresentationSpeaker::get()->filter('MemberID', $CurrentMember->ID)->first();
            $SpeakerProfileForm = New EditSpeakerProfileForm($this, 'EditSpeakerProfileForm', $speaker, $CurrentMember, null);
            return $SpeakerProfileForm;
        }
    }


    //Save profile
    function SaveProfile($data, $form)
    {
        //Check for a logged in member
        $CurrentMember = Member::currentUser();
        if (!$CurrentMember)
            return Security::PermissionFailure($this->controller, 'You must be <a href="/join">registered</a> and logged in to edit your profile:');


        $form->saveInto($CurrentMember);
        if (isset($cleanedBio)) $CurrentMember->Bio = $cleanedBio;
        if ($data['Gender'] == 'Specify') {
            $CurrentMember->Gender = $data['GenderSpecify'];
        }
        $CurrentMember->ProfileUpdated();
        Session::set("Member.showUpdateProfileModal", false);
        $CurrentMember->write();
        // If they do not have a photo uploaded, but they have provided a twitter URL, attempt to grab a photo from twitter
        if ($CurrentMember->TwitterName && !$CurrentMember->Photo()->Exists()) {
            $this->ProfilePhotoFromTwitter($CurrentMember);
        }

        return $this->redirect($this->Link('?saved=1'));
    }

    //Check for just saved

    function ProfilePhotoFromTwitter($Member)
    {

        $thumbnailURL = "https://api.twitter.com/1/users/profile_image?screen_name=" . $Member->TwitterName . "&size=bigger";

        $folderToSave = 'assets/profile-images/'; //to save into another folder add one via the cms then change this path. Folders are a type of Folder DataObject.
        $folderObject = Folder::get()->filter('Filename', 'folderToSave')->first();

        if ($folderObject) {
            //get image from url and save to folder
            $thumbnailToCopy = @file_get_contents($thumbnailURL);
            $thumbnailName = $Member->TwitterName . '.png';

            // Make sure something was returned from twitter.
            if ($thumbnailToCopy !== false) {

                $thumbnailFile = fopen('./../' . $folderToSave . $thumbnailName, 'w'); // opens existing or creates a new file
                fwrite($thumbnailFile, $thumbnailToCopy); //overwrites file
                fclose($thumbnailFile); //close file

                if (!Image::get()->filter('Name', $thumbnailName)->count()) //checks if dataObject already exists, stops multiple records being created.
                {
                    $thumbnailObject = SS_Object::create('CloudImage');
                    $thumbnailObject->ParentID = $folderObject->ID; //assign folder of image as parent
                    $thumbnailObject->Name = $thumbnailName; //this function also sets the images Filename and title in a round about way. (see setName() in File.php)
                    $thumbnailObject->OwnerID = (Member::currentUser() ? Member::currentUser()->ID : 0); //assign current user as Owner
                    $thumbnailObject->write();
                } else { // Data object exists. Assign it to $thumbnailObject.

                    $thumbnailObject = Image::get()->filter('Name', $thumbnailName)->first();

                }

                // Set and save the profile image
                if ($thumbnailObject) {
                    $Member->PhotoID = $thumbnailObject->ID;
                    $Member->write();
                }

            }
        }

    }

    //Check for error

    function Saved()
    {
        return $this->request->getVar('saved');
    }

    //Check for success status

    function Error()
    {
        $errors = Session::get("FormInfo.EditProfileForm_EditProfileForm.errors");
        $qs_error = $this->request->getVar('error');

        return !empty($errors) || !empty($qs_error);
    }

    function Success()
    {
        return $this->request->getVar('success');
    }

    public function SetCurrentTab($tab)
    {
        $this->CurrentTab = $tab;
    }

    function LegalAgreements()
    {
        $CurrentMember = Member::currentUser();
        $LegalAgreements = LegalAgreement::get()->filter('MemberID', $CurrentMember->ID);
        if ($LegalAgreements->count() > 0) {
            $LegalAgreements->sort('Created');
            return $LegalAgreements;

        } else {
            return NULL;
        }
    }

    function CompanyAdmin()
    {
        return Member::currentUser()->getManagedCompanies();
    }

    // Resigning membership in the foundation removes the member from the database entirely.
    function resign()
    {
        $current_user = Member::currentUser();
        if ($current_user && isset($_GET['confirmed'])) {
            $current_user->resign();
            // Logout and delete the user
            Session::set('delete_member_id', $current_user->ID);
            $this->setMessage('Success', 'You have resigned your membership to the Open Infrastructure Foundation.');
            $this->redirect('/Security/logout' . "?BackURL=" . urlencode('/profile'));

        } else if ($current_user) {
            return $this->renderWith(array('EditProfilePage_resign', 'Page'));
        }
    }

    // Training

    // Helpers
    function Trainings()
    {
        return new ArrayList($this->training_manager->getAllowedTrainings(Member::currentUser()));
    }

    function AddTrainingCourseForm()
    {

        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');

        Requirements::javascript("datepicker/javascript/datepicker.js");
        Requirements::javascript('registration/javascript/edit.profile.training.form.js');

        // Name Set
        $Name = new TextField('Name', "Name");
        $Name->addExtraClass('course-name');

        $Link = new TextField('Link', "Link");
        $Link->addExtraClass('course-online-link url');

        $Description = new TextareaField('Description', "Description");
        $Description->addExtraClass('course-description');

        $Online = new CheckboxField('Online', "Is Online?");
        $Online->addExtraClass('course-online-checkbox');

        $Paid = new CheckboxField('Paid', "Is Paid?");
        $Level = new DropdownField('LevelID', 'Level', TrainingCourseLevel::get()->map('ID', 'Level'));
        $Projects = new CheckboxSetField('Projects', '', Project::get()->map('ID', 'Name'));
        $Projects->setTemplate('BootstrapAwesomeCheckboxsetField');

        $Program = new HiddenField('TrainingServiceID', "TrainingServiceID", $this->training_id);
        $Course = new HiddenField('ID', "course", 0);

        $show_blank_schedule = true;

        if (isset($this->EditCourseID)) {
            $locations_dto = $this->course_repository->getLocations($this->EditCourseID);
            for ($i = 0; $i < count($locations_dto); $i++) {
                $dto = $locations_dto[$i];
                $show_blank_schedule = false;
                $City[$i] = new TextField('City[' . $i . ']', "City", $dto->getCity());
                $City[$i]->addExtraClass('city_name');

                $State[$i] = new TextField('State[' . $i . ']', "State", $dto->getState());
                $State[$i]->addExtraClass('state');

                $Country[$i] = new DropdownField('Country[' . $i . ']', "Country", CountryCodes::$iso_3166_countryCodes, $dto->getCountry());
                $Country[$i]->setEmptyString('-- Select One --');
                $Country[$i]->addExtraClass('country');

                $LinkS[$i] = new TextField('LinkS[' . $i . ']', "Link", $dto->getLink());
                $LinkS[$i]->addExtraClass('url');

                $StartDate[$i] = new TextField('StartDate[' . $i . ']', "Start Date", is_null($dto->getStartDate()) ? '' : $dto->getStartDate());
                $StartDate[$i]->addExtraClass('dateSelector start');
                $EndDate[$i] = new TextField('EndDate[' . $i . ']', "End Date", is_null($dto->getEndDate()) ? '' : $dto->getEndDate());
                $EndDate[$i]->addExtraClass('dateSelector end');

            }
        }

        // fields for template
        $TemplateCity = new TextField('City[]', "City");
        $TemplateCity->addExtraClass('city_name');

        $TemplateState = new TextField('State[]', "State");
        $TemplateState->addExtraClass('state');

        $TemplateCountry = new DropdownField('Country[]', 'Country', CountryCodes::$iso_3166_countryCodes);
        $TemplateCountry->setEmptyString('-- Select One --');
        $TemplateCountry->addExtraClass('country');

        $TemplateStartDate = new TextField('StartDate[]', "Start Date");
        $TemplateStartDate->addExtraClass('dateSelector start');
        $TemplateEndDate = new TextField('EndDate[]', "End Date");
        $TemplateEndDate->addExtraClass('dateSelector end');
        $TemplateLinkS = new TextField('LinkS[]', "Link");
        $TemplateLinkS->addExtraClass('url');

        $fields = new FieldList(
            $Name,
            $Description,
            $Link,
            new LiteralField('break', '<br><hr/><div class="horizontal-fields">'),
            $Online,
            $Paid,
            $Level,
            $Program,
            $Course,
            new LiteralField('break', '</div><hr/>'),
            new LiteralField('projects', '<h4>Projects</h4>'),
            $Projects,
            new LiteralField('schedule', '<h4>Schedule</h4>'),
            new LiteralField('instruction', '<p class="note_online">City, State and Country can\'t be edited when a course is marked <em>Online</em>.</p>'),
            new LiteralField('noSchedule', '<div id="no_schedules">No schedules set.</div>'),
            new LiteralField('scheduleDiv', '<div id="schedules">')
        );


        if (!$show_blank_schedule) {

            for ($j = 0; $j < $i; $j++) {

                $fields->push(new LiteralField('scheduleDiv', '<div class="scheduleRow">'));
                $fields->push($City[$j]);
                $fields->push($State[$j]);
                $fields->push($Country[$j]);
                $fields->push($StartDate[$j]);
                $fields->push($EndDate[$j]);
                $fields->push($LinkS[$j]);
                $fields->push(new LiteralField('scheduleDiv', '</div>'));

            }

        }

        $fields->push(new LiteralField('scheduleDivC', '</div>'));
        $fields->push(new LiteralField('addSchedule', '<button id="addSchedule" class="btn btn-default action">Add Another</button>'));

        // schedule template
        $fields->push(new LiteralField('scheduleTemplate', '<div class="schedule_template">'));
        $fields->push($TemplateCity);
        $fields->push($TemplateState);
        $fields->push($TemplateCountry);
        $fields->push($TemplateStartDate);
        $fields->push($TemplateEndDate);
        $fields->push($TemplateLinkS);
        $fields->push(new LiteralField('scheduleTemplate', '</div>'));

        $actions = new FieldList(
            $submit = new FormAction('AddCourse', 'Submit')
        );
        $submit->addExtraClass('btn btn-primary');
        $validators = new ConditionalAndValidationRule(array(new RequiredFields('Name', 'Level'), new HtmlPurifierRequiredValidator('Description')));
        $form = new Form($this, 'AddTrainingCourseForm', $fields, $actions, $validators);
        if (isset($this->EditCourseID)) {
            $form->loadDataFrom($this->course_repository->getById($this->EditCourseID));
            unset($this->EditCourseID);
        } else {
            $form->loadDataFrom($this->request->postVars());
        }
        return $form;
    }

    /**
     * @param $data
     * @param $form
     */
    function AddCourse($data, $form)
    {

        $config = HTMLPurifier_Config::createDefault();
        $config->set('CSS.AllowedProperties', array());
        $purifier = new HTMLPurifier($config);
        $data['Description'] = $purifier->purify($data['Description']);
        $this->course_manager->register($data);
        $this->redirect('training');
    }


    function trainingEdit()
    {
        Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');

        $this->EditCourseID = intval($_GET['course_id']);
        return $this->renderWith(array('EditProfilePage_TrainingAddCourse', 'Page'));
    }

    function trainingDelete()
    {
        $course_id = intval(Convert::raw2sql(@$_GET['course_id']));

        if ($course_id > 0) {
            $course = $this->course_repository->getById($course_id);
            $training = $course->getTraining();
            if (Member::currentUser()->canEditTraining($training->getIdentifier())) {
                $this->setMessage('Success', 'Training Course deleted.');
                $this->course_manager->unRegister($course_id);
            } else {
                $this->setMessage('Danger', "You don't have the permission required to edit this Training Curse");
            }
        }

        $this->redirect('training');
    }

    // Views
    function training()
    {
        Requirements::javascript('registration/javascript/edit.profile.training.js');
        return $this->renderWith(array('EditProfilePage_Training', 'Page'));
    }

    function trainingAddCourse()
    {
        Requirements::css('node_modules/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css');

        if ($this->request->postVars()) {
            $data = $this->request->postVars();
            $training_id = $data['training_id'];
        } else {
            $training_id = intval(@$_GET['training_id']);
        }
        // Validation if it belongs to the program
        if (Member::currentUser()) {
            if (Member::currentUser()->canEditTraining($training_id)) {
                $context = array('Training' => $this->training_repository->getById($training_id));
                $this->training_id = $training_id;
                return $this->renderWith(array('EditProfilePage_TrainingAddCourse', 'Page'), $context);

            } else {
                echo "You are not allowed to do this.";
                die();
            }
        } else {
            return Controller::curr()->redirect('/Security/login?BackURL=/profile/TrainingAddCourse?training_id=' . $training_id);
        }

    }

    function getMarketPlaceManagerLink()
    {
        $marketplace_admin_page = MarketPlaceAdminPage::get()->first();
        return $marketplace_admin_page ? $marketplace_admin_page->URLSegment : '#';
    }

    function getNavActionsExtensions()
    {
        $html = '';
        $this->extend('getNavActionsExtensions', $html);
        return $html;
    }

    function getNavMessageExtensions()
    {
        $html = '';
        $this->extend('getNavMessageExtensions', $html);
        return $html;
    }

    public function LogoutUrl()
    {
        return $this->Link('logout');
    }

    public function ResignUrl()
    {
        return $this->Link('resign');
    }

    public function RenewMembershipUrl()
    {
        return 'https://openinfra.org/a/renew-membership';
    }

    public function downgrade2communitymember()
    {
        $CurrentMember = Member::currentUser();
        if ($CurrentMember && isset($_GET['confirmed'])) {
            $CurrentMember->convert2SiteUser();
            $this->setMessage('Success', 'You have downgraded your membership to Community Member.');
            $this->redirect('profile/');
        } else if ($CurrentMember) {
            return $this->renderWith(array('EditProfilePage_downgrade2communitymember', 'Page'));
        }
    }

    public function upgrade2foundationmember()
    {
        $CurrentMember = Member::currentUser();
        if ($CurrentMember && isset($_GET['confirmed'])) {
            $CurrentMember->upgradeToFoundationMember();
            $this->setMessage('Success', 'You have upgraded your membership to Foundation Member.');
            $this->redirect('profile/');
        } else if ($CurrentMember) {
            return $this->renderWith(array('EditProfilePage_upgrade2foundationmember', 'Page'));
        }
    }

    public function getRenderUITopExtensions()
    {
        $html = '';
        $this->extend('getRenderUITopExtensions', $html);
        return $html;
    }
}