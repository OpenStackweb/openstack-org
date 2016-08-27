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
        'election',
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
        'SummitAttendeeInfoForm',
        'saveSummitAttendeeInfo',
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
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');

        $css_files = array(
            "themes/openstack/css/chosen.css",
            'registration/css/edit.profile.page.css',
        );

        foreach ($css_files as $css_file)
            Requirements::css($css_file);

        Requirements::combine_files('edit_profile_page.js', array(
            "themes/openstack/javascript/chosen.jquery.min.js",
            "themes/openstack/javascript/pure.min.js",
            "themes/openstack/javascript/jquery.serialize.js",
            "themes/openstack/javascript/jquery.cleanform.js",
            "themes/openstack/javascript/jquery.ui.datepicker.validation.package-1.0.1/jquery.ui.datepicker.validation.js",
            "themes/openstack/javascript/jquery.validate.custom.methods.js",
            'registration/javascript/edit.profile.page.js'
        ));

        $this->course_repository = new SapphireCourseRepository;
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
            //Populate the form with the current members data
            $EditProfileForm->loadDataFrom($CurrentMember->data());
            return $EditProfileForm;
        }
    }

    public function EditSpeakerProfileForm()
    {
        if ($CurrentMember = Member::currentUser()) {
            Requirements::css("registration/css/speaker.profile.form.css");
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

        //Check for another member with the same email address
        if (Member::get()->filter(array('Email' => Convert::raw2sql($data['Email']), 'ID:not' => $CurrentMember->ID))->count() > 0) {
            $form->addErrorMessage("Email", 'Sorry, that email address already exists.', "bad");
            Session::set("FormInfo.Form_EditProfileForm.data", $data);
            return $this->redirect($this->Link('?error=1'));
        }
        //Otherwise save profile
        // Clean up bio
        if ($data["Bio"]) {
            $config = HTMLPurifier_Config::createDefault();
            // Remove any CSS or inline styles
            $config->set('CSS.AllowedProperties', array());
            $purifier = new HTMLPurifier($config);
            $cleanedBio = $purifier->purify($data["Bio"]);
        }
        $form->saveInto($CurrentMember);
        if (isset($cleanedBio)) $CurrentMember->Bio = $cleanedBio;
        if ($data['Gender'] == 'Specify') {
            $CurrentMember->Gender = $data['GenderSpecify'];
        }
        $email_updated = $CurrentMember->isChanged('Email');
        $CurrentMember->write();

        $speaker = PresentationSpeaker::get()->filter('MemberID', $CurrentMember->ID)->first();

        if ($speaker) {
            if ($data['ReplaceName'] == 1) {
                $speaker->FirstName = $data['FirstName'];
            }
            if ($data['ReplaceSurname'] == 1) {
                $speaker->Surname = $data['Surname'];
            }
            if ($data['ReplaceBio'] == 1) {
                $speaker->Bio = $data['Bio'];
            }

            $speaker->write();
        }

        // If they do not have a photo uploaded, but they have provided a twitter URL, attempt to grab a photo from twitter
        if ($CurrentMember->TwitterName && !$CurrentMember->Photo()->Exists()) {
            $this->ProfilePhotoFromTwitter($CurrentMember);
        }

        if ($email_updated) {
            $this->member_manager->resetEmailVerification($CurrentMember, new MemberRegistrationSenderService());
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
                    $thumbnailObject = Object::create('BetterImage');
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
        return $this->request->getVar('error');
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

    // look up the current election if there is one

    function CompanyAdmin()
    {
        return Member::currentUser()->getManagedCompanies();
    }


    // Uses the twitter name of the person to fetch and set a profile image

    function CurrentElection()
    {

        // Query the election system to look for a current election
        $Elections = ElectionSystem::get()->first();
        if ($Elections) {
            // See if the current election is open.
            // An election is open if either nominations are open or the election voting is open.
            if (!$Elections->CurrentElectionID == 0) {
                return $Elections->CurrentElection();
            }
        }
    }

    // Candidate Application Form

    function CandidateApplicationForm()
    {

        $CandidateApplicationForm = new CandidateApplicationForm($this, 'CandidateApplicationForm');
        $CandidateApplicationForm->disableSecurityToken();

        // Load the election system
        $Elections = ElectionSystem::get()->first();
        $CurrentElection = $Elections->CurrentElection();
        $currentMember = Member::currentUser();


        // Check for login
        if ($currentMember) {

            $Candidate = Candidate::get()->filter(array('MemberID' => $currentMember->ID, 'ElectionID' => $CurrentElection->ID))->first();

            // Fill in the form
            if ($Candidate) {
                $CandidateApplicationForm->loadDataFrom($Candidate, False);
                $CandidateApplicationForm->loadDataFrom($currentMember, False);
                return $CandidateApplicationForm;
            } elseif ($this->request->isPost()) {
                // SS is returning to the form controller to post data
                return $CandidateApplicationForm;
            } else {
                // No candidate for this member; create a new candidate entry
                $Candidate = new Candidate();
                $Candidate->MemberID = $currentMember->ID;
                $Candidate->ElectionID = $CurrentElection->ID;
                $Candidate->write();
                $CandidateApplicationForm->loadDataFrom($currentMember, False);
                return $CandidateApplicationForm;
            }
        }


    }

    // Save an edited candidate
    function saveCandidateApplicationForm($data, $form)
    {


        $currentMember = Member::currentUser();

        // A user is logged in
        if ($currentMember) {

            // Load the election system
            $Elections = ElectionSystem::get()->first();
            $CurrentElection = $Elections->CurrentElection();

            if ($Candidate = Candidate::get()->filter(array('MemberID' => $currentMember->ID, 'ElectionID' => $CurrentElection->ID))->first()) {
                // Candidate profile exists

                // Clean up entries ////////////////

                // Set up HTML Purifier

                $config = HTMLPurifier_Config::createDefault();

                // Remove any CSS or inline styles
                $config->set('CSS.AllowedProperties', array());
                $purifier = new HTMLPurifier($config);

                // Clean Bio field
                if ($data["Bio"]) {
                    $currentMember->Bio = $purifier->purify($data["Bio"]);
                    $currentMember->write();
                }

                // Clean RelationshipToOpenStack field
                if ($toClean = $data["RelationshipToOpenStack"]) {
                    $Candidate->RelationshipToOpenStack = $purifier->purify($toClean);
                }

                // Clean Experience field
                if ($toClean = $data["Experience"]) {
                    $Candidate->Experience = $purifier->purify($toClean);
                }

                // Clean BoardsRole field
                if ($toClean = $data["BoardsRole"]) {
                    $Candidate->BoardsRole = $purifier->purify($toClean);
                }

                // Clean HasAcceptedNomination field
                if ($toClean = $data["TopPriority"]) {
                    $Candidate->TopPriority = $purifier->purify($toClean);
                }

                $Candidate->write();
                $questions = array('Bio' => 'Bio', 'RelationshipToOpenStack' => 'RelationshipToOpenStack', 'Experience' => 'Experience', 'BoardsRole' => 'BoardsRole', 'TopPriority' => 'TopPriority');
                // Must answer all questions, but can save work as they go, so we're going to check here rather than set up validators

                if (
                    (strlen($data['Bio'])) < 4 ||
                    (strlen($data['RelationshipToOpenStack'])) < 4 ||
                    (strlen($data['Experience'])) < 4 ||
                    (strlen($data['BoardsRole'])) < 4 ||
                    (strlen($data['TopPriority'])) < 4

                ) {

                    $Candidate->HasAcceptedNomination = FALSE;
                    $form->saveInto($Candidate);
                    $Candidate->write();

                    $form->clearMessage();
                    $form->sessionMessage("Your edits have been saved but you will need to provide full answers to all these questions to be eligible as a candidate.", "bad");
                    $this->redirectBack();
                    return;
                }

                $Candidate->HasAcceptedNomination = TRUE;
                $Candidate->write();
                $form->clearMessage();
                $this->redirect($this->Link() . 'election/');
            } else {
                $form->clearMessage();
                $form->sessionMessage('There was an error saving your edits.', "bad");
                $this->redirectBack();
            }

        }

    }


    // Resigning membership in the foundation removes the member from the database entirely.
    function resign()
    {
        $current_user = Member::currentUser();
        if ($current_user && isset($_GET['confirmed'])) {
            $current_user->resign();
            // Logout and delete the user
            Session::set('delete_member_id', $current_user->ID);
            $this->setMessage('Success', 'You have resigned your membership to the OpenStack Foundation.');
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

                $Country[$i] = new DropdownField('Country[' . $i . ']', $dto->getCountry(), CountryCodes::$iso_3166_countryCodes, $dto->getCountry());
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

        if ($show_blank_schedule) {
            $City = new TextField('City[]', "City");
            $City->addExtraClass('city_name');

            $State = new TextField('State[]', "State");
            $State->addExtraClass('state');

            $Country = new DropdownField('Country[]', 'Country', CountryCodes::$iso_3166_countryCodes);
            $Country->setEmptyString('-- Select One --');
            $Country->addExtraClass('country');

            $StartDate = new TextField('StartDate[]', "Start Date");
            $StartDate->addExtraClass('dateSelector start');
            $EndDate = new TextField('EndDate[]', "End Date");
            $EndDate->addExtraClass('dateSelector end');
            $LinkS = new TextField('LinkS[]', "Link");
            $LinkS->addExtraClass('url');
        }

        $fields = new FieldList(
            $Name,
            $Description,
            $Link,
            new LiteralField('break', '<hr/><div class="horizontal-fields">'),
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

        } else {
            $fields->push(new LiteralField('scheduleDiv', '<div class="scheduleRow">'));
            $fields->push($City);
            $fields->push($State);
            $fields->push($Country);
            $fields->push($StartDate);
            $fields->push($EndDate);
            $fields->push($LinkS);
            $fields->push(new LiteralField('scheduleDiv', '</div>'));
        }

        $fields->push(new LiteralField('scheduleDivC', '</div>'));
        $fields->push(new LiteralField('addSchedule', '<button id="addSchedule" class="action">Add Another</button>'));

        $actions = new FieldList(
            new FormAction('AddCourse', 'Submit')
        );
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