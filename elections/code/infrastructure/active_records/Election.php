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
 * Class Election
 */
final class Election extends DataObject implements IElection {

    static $db = [
        'Name'                  => 'VarChar',
        'NominationsOpen'       => 'SS_Datetime', // When Individual Member Nominations Start
        'NominationsClose'      => 'SS_Datetime', // When Individual Member Nomination CLose
        'NominationAppDeadline' => 'SS_Datetime', // When a candidate must have completed the application in order to be listed
        'ElectionsOpen'         => 'SS_Datetime', // The day elections start
        'ElectionsClose'        => 'SS_Datetime', // The day they close
        'TimeZone'              => 'Text',
    ];

    static $has_one = [
        'VoterFile' => 'ElectionVoterFile'
    ];

    static $has_many = [
        'Candidates'           => 'Candidate', // Candidates for the current election
        'CandidateNominations' => 'CandidateNomination',
        'Votes'                => 'ElectionVote'
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Name'                          => 'Name',
        'Status'                        => 'Status',
        'NominationsOpenLocalFriendly'  => 'Nominations Opens',
        'NominationsCloseLocalFriendly' => 'Nominations Closes',
        'ElectionsOpenLocalFriendly'    => 'Election Opens',
        'ElectionsCloseLocalFriendly'   => 'Election Closes'
    ];

	/**
	 * @return DateTime
	 */
	public function startDate()
	{
		return new DateTime($this->getField('ElectionsOpen'));
	}

	/**
	 * @return DateTime
	 */
	public function endDate()
	{
		return new DateTime($this->getField('ElectionsClose'));
	}


    /**
     * @return string
     */
	public function getStatus(){
	    $status = IElection::StatusClosed;
	    if($this->NominationsAreOpen()){
	        $status = IElection::StatusNominationsOpen;
        }
        if($this->isOnVotingPeriod()){
            $status= IElection::StatusElectionOpen;
        }
        if($this->isUpcomingVotingPeriod()){
            $status= IElection::StatusElectionUpcoming;
        }
	    return $status;
    }

    /**
     * @param Member $candidate
     * @return bool
     */
	public function isNominated(Member $candidate){
        return $this->CandidateNominations("`MemberID` = " . Member::currentUserID() . " AND `CandidateID` = " . $candidate->ID)->Count() >= 1;
    }

    /**
     * @param Member $candidate
     * @return mixed
     */
    public function getNominationsFor(Member $candidate){
        return $this->CandidateNominations(" `CandidateID` = " . $candidate->ID);
    }
	/**
	 * @return int
	 */
	public function getIdentifier()
	{
		return (int)$this->getField('ID');
	}

    function getCMSFields()
    {
        // don't overwrite the main fields
        $fields = parent::getCMSFields();
        //clear all fields
        $oldFields = $fields->toArray();
        foreach($oldFields as $field){
            $fields->remove($field);
        }


        $fields->add($rootTab = new TabSet("Root", $tabMain = new Tab('Main')));

        $fields->addFieldToTab('Root.Main', new TextField('Name', 'Name'));

        // Dates

        $fields->addFieldsToTab('Root.Dates',
        $ddl_timezone = new DropdownField('TimeZone', 'Time Zone', DateTimeZone::listIdentifiers()));
        $ddl_timezone->setEmptyString('-- Select a Timezone --');

        $election_time_zone = null;
        if($this->TimeZone) {
            $time_zone_list = timezone_identifiers_list();
            $election_time_zone = $time_zone_list[$this->TimeZone];
        }

        if($election_time_zone) {
            $fields->addFieldToTab('Root.Dates', new HeaderField("All dates below are in <span style='color:red;'>$election_time_zone</span> time."));
        }

        $fields->addFieldToTab('Root.Dates', $date = new DatetimeField('NominationsOpen', "Date the nominations open"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('showdropdown', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');

        $fields->addFieldToTab('Root.Dates', $date = new DatetimeField('NominationsClose', "Date the nominations closes"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('showdropdown', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');

        $fields->addFieldToTab('Root.Dates', $date = new DatetimeField('NominationAppDeadline', "Date candidates must have completed the application in order to be listed"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('showdropdown', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');

        $fields->addFieldToTab('Root.Dates', $date = new DatetimeField('ElectionsOpen', "Date the elections open"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('showdropdown', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');

        $fields->addFieldToTab('Root.Dates', $date = new DatetimeField('ElectionsClose', "Date the elections close"));
        $date->getDateField()->setConfig('showcalendar', true);
        $date->getDateField()->setConfig('showdropdown', true);
        $date->getDateField()->setConfig('dateformat', 'dd/MM/yyyy');


        if ($this->ID > 0) {
            // votes
            $config = GridFieldConfig_RecordEditor::create(100);
            $config->addComponent(new GridFieldAjaxRefresh(1000, false));
            $config->addComponent(new GridFieldClearElectionVotersAction());
            $config->removeComponentsByType(new GridFieldDetailForm());
            $edit_form = new GridFieldDetailForm();
            $edit_form->setFields(FieldList::create(
                ReadonlyField::create('ElectionID','Election'),
                new MemberAutoCompleteField("Voter", "Voter")
            ));

            $config->getComponentByType('GridFieldDataColumns')->setDisplayFields
            (
                [
                    'Voter.FullName' => 'Voter Name',
                    'Voter.Email'    => 'Voter Email',
                ]
            );

            $config->addComponent($edit_form);

            $votes    = new GridField('Votes', 'Votes', $this->Votes(), $config);
            $importer = new GridFieldImporter('before');
            $importer->setHandler(new GridFieldImporterElectionVoters_Request($votes, $importer, Controller::curr()));
            $config->addComponent($importer);
            $fields->addFieldToTab('Root.Votes', $votes);


            // Candidates

            $config     = GridFieldConfig_RecordEditor::create(100);

            $config->removeComponentsByType(new GridFieldDetailForm());
            $edit_form = new GridFieldDetailForm();
            $edit_form->setFields(FieldList::create(
                ReadonlyField::create('ElectionID','Election'),
                new MemberAutoCompleteField("Member", "Member"),
                new CheckboxField('HasAcceptedNomination', 'Has Accepted Nomination?'),
                new CheckboxField('IsGoldMemberCandidate', 'Is Gold Member Candidate?'),
                new HtmlEditorField('RelationshipToOpenStack', 'Relationship To OpenStack'),
                new HtmlEditorField('Experience', 'Experience'),
                new HtmlEditorField('BoardsRole', 'Boards Role'),
                new HtmlEditorField('TopPriority', 'Top Priority')
            ));

            $config->getComponentByType('GridFieldDataColumns')->setDisplayFields
            (
                [
                    'Member.FullName'       => 'Candidate Name',
                    'Member.Email'          => 'Candidate Email',
                    'HasAcceptedNomination' => 'Has Accepted Nomination',
                ]
            );

            $config->addComponent($edit_form);


            $candidates = new GridField('Candidates', 'Candidates', $this->Candidates(), $config);
            $fields->addFieldToTab('Root.Candidates', $candidates);

            // Candidates
            $config = GridFieldConfig_RecordEditor::create(100);

            $config->removeComponentsByType(new GridFieldDetailForm());
            $edit_form = new GridFieldDetailForm();
            $edit_form->setFields(FieldList::create(
                ReadonlyField::create('ElectionID','Election'),
                new MemberAutoCompleteField("Member", "Proposer"),
                new MemberAutoCompleteField("Candidate", "Candidate")
            ));

            $config->getComponentByType('GridFieldDataColumns')->setDisplayFields
            (
                [
                    'Candidate.FullName'    => 'Candidate Name',
                    'Candidate.Email'       => 'Candidate Email',
                    'Member.FullName'       => 'Proposer Name',
                    'Member.Email'          => 'Proposer Email',
                ]
            );
            $config->addComponent($edit_form);
            $nominations = new GridField('Nominations', 'Nominations', $this->CandidateNominations(), $config);
            $fields->addFieldToTab('Root.Nominations', $nominations);
        }
        return $fields;
    }

    public static $validation_enabled = true;

    protected function validate()
    {
        if (!self::$validation_enabled) return ValidationResult::create();

        $valid = parent::validate();
        if (!$valid->valid()) {
            return $valid;
        }
        $name = trim($this->Name);
        if (empty($name)) {
            return $valid->error('Name is required!');
        }


        $time_zone = $this->TimeZone;
        if (empty($time_zone)) {
            return $valid->error('Time Zone is required!');
        }

        $start_date                = $this->NominationsOpen;
        $end_date                  = $this->NominationsClose;

        if (!is_null($start_date) && !is_null($end_date)) {
            $start_date = new DateTime($start_date);
            $end_date   = new DateTime($end_date);

            if ($start_date > $end_date) {
                return $valid->error('Nominations Close Date must be greather than Nominations Open Date');
            }
        }

        $start_date = $this->ElectionsOpen;
        $end_date   = $this->ElectionsClose;

        if (!is_null($start_date) && !is_null($end_date)) {
            $start_date = new DateTime($start_date);
            $end_date = new DateTime($end_date);
            if ($start_date > $end_date) {
                return $valid->error('Elections Close Date must be greather than Elections Open Date');
            }
        }

        return $valid;
    }

    // These are in the model layer to be available to other pages.

    // Return the nominations made for the logged-in user
    public function NominationsForCurrentMember() {
        return $this->CandidateNominations('CandidateID = '.Member::currentUser()->ID);
    }

    // Return the nominations made for the logged-in user
    public function NominationsByCurrentMember() {
        return $this->CandidateNominations('MemberID = '.Member::currentUser()->ID);
    }


    // Used to determine plural wording (0 times, 1 time, 2 times, etc.)
    public function PluralNominations() {
        $CurrentMemberID = Member::currentUser()->ID;
        return $this->CandidateNominations('CandidateID = '.$CurrentMemberID)->Count() <> 1;
    }

    // Find the current user and see if they've accepted a candidate nomination
    public function CurrentMemberHasAccepted() {
        $CurrentUserCandidate = Candidate::get()->filter
        (
            [
                'ElectionID' => $this->ID,
                'MemberID' => Member::currentUser()->ID
            ])->first();

        if ($CurrentUserCandidate) return $CurrentUserCandidate->HasAcceptedNomination;
    }

    // Return whether the nominations are open by looking at the dates provided
    function NominationsAreOpen()
    {

        $start_date = $this->getField('NominationsOpen');
        $end_date = $this->getField('NominationsClose');

        if (empty($start_date) || empty($end_date)) {
            return false;
        }

        $start_date = new DateTime($start_date, new DateTimeZone('UTC'));
        $end_date = new DateTime($end_date, new DateTimeZone('UTC'));
        $now = new \DateTime('now', new DateTimeZone('UTC'));

        return ($now >= $start_date && $now <= $end_date);
    }

    // Return whether the election is currently running by looking at the dates provided
    function isOnVotingPeriod() {

        $start_date = $this->getField('ElectionsOpen');
        $end_date   = $this->getField('ElectionsClose');

        if (empty($start_date) || empty($end_date)) {
            return false;
        }
        $start_date = new DateTime($start_date, new DateTimeZone('UTC'));
        $end_date   = new DateTime($end_date, new DateTimeZone('UTC'));
        $now        = new \DateTime('now', new DateTimeZone('UTC'));

        return ($now >= $start_date && $now <= $end_date);
    }

    /**
     * @return bool
     */
    public function isOpen() {
        return $this->NominationsAreOpen() || $this->isOnVotingPeriod() || $this->isUpcomingVotingPeriod();
    }

    public function isUpcomingVotingPeriod(){
        $start_date = $this->getField('ElectionsOpen');
        $start_date = new DateTime($start_date, new DateTimeZone('UTC'));
        $now        = new \DateTime('now', new DateTimeZone('UTC'));

        return ($now < $start_date);
    }

    // Returns candidates for this election that have accepted the nomination
    function AcceptedCandidatesList() {
        return $this->Candidates("HasAcceptedNomination = 1");
    }

    // Returns candidates for this election that have accepted the nomination
    function GoldCandidatesList() {
        return $this->Candidates("IsGoldMemberCandidate = 1");
    }

    function IamGoldCandidate(){
        return $this->Candidates("IsGoldMemberCandidate = 1 AND MemberID = ".Member::currentUserID())->count() > 0;
    }

    /**
     * @return Election
     */
    public static function getCurrent(){
        $res = self::getOpen();

        if(is_null($res))
            $res = self::getActive();

        if(is_null($res))
            $res = self::getLatest();

        return $res;
    }

    public static function getActive(){
        return Election::get()
            ->where(' ElectionsOpen <= UTC_TIMESTAMP() AND ElectionsClose >= UTC_TIMESTAMP() ')
            ->sort('ElectionsOpen DESC')->first();
    }

    public static function getOpen(){
          return Election::get()
            ->where(' NominationsOpen <= UTC_TIMESTAMP() AND NominationsClose >= UTC_TIMESTAMP() ')
            ->sort('NominationsOpen DESC')->first();
    }


    /**
     * @return Election
     */
    public static function getLatest(){
        return Election::get()
            ->sort('ElectionsOpen DESC')->first();
    }

    // dates

    use TimeZoneEntity;

    public function setNominationsOpen($value)
    {
        $this->setDateTimeFromLocalToUTC($value, 'NominationsOpen');
    }

    public function getNominationsOpen($format = "Y-m-d H:i:s")
    {
        return $this->getFromUTCtoLocal('NominationsOpen', $format);
    }

    public function getNominationsOpenLocalFriendly(){
        $res = $this->getFromUTCtoLocal('NominationsOpen', "Y-m-d H:i:s");
        if(empty($res)) return 'NOT SET';
        return sprintf("%s (%s)", $res, $this->getTimeZoneName());
    }

    public function setNominationsClose($value)
    {
        $this->setDateTimeFromLocalToUTC($value, 'NominationsClose');
    }

    public function getNominationsClose($format = "Y-m-d H:i:s")
    {
        return $this->getFromUTCtoLocal('NominationsClose', $format);
    }

    public function getNominationsCloseLocalFriendly(){
        $res = $this->getFromUTCtoLocal('NominationsClose', "Y-m-d H:i:s");
        if(empty($res)) return 'NOT SET';
        return sprintf("%s (%s)", $res, $this->getTimeZoneName());
    }

    public function setNominationAppDeadline($value)
    {
        $this->setDateTimeFromLocalToUTC($value, 'NominationAppDeadline');
    }

    public function getNominationAppDeadline($format = "Y-m-d H:i:s")
    {
        return $this->getFromUTCtoLocal('NominationAppDeadline', $format);
    }

    public function setElectionsOpen($value)
    {
        $this->setDateTimeFromLocalToUTC($value, 'ElectionsOpen');
    }

    public function getElectionsOpen($format = "Y-m-d H:i:s")
    {
        return $this->getFromUTCtoLocal('ElectionsOpen', $format);
    }

    public function getElectionsOpenLocalFriendly(){
        $res = $this->getFromUTCtoLocal('ElectionsOpen', "Y-m-d H:i:s");
        if(empty($res)) return 'NOT SET';
        return sprintf("%s (%s)", $res, $this->getTimeZoneName());
    }

    public function setElectionsClose($value)
    {
        $this->setDateTimeFromLocalToUTC($value, 'ElectionsClose');
    }

    public function getElectionsClose($format = "Y-m-d H:i:s")
    {
        return $this->getFromUTCtoLocal('ElectionsClose', $format);
    }

    public function getElectionsCloseLocalFriendly(){
        $res = $this->getFromUTCtoLocal('ElectionsClose', "Y-m-d H:i:s");
        if(empty($res)) return 'NOT SET';
        return sprintf("%s (%s)", $res, $this->getTimeZoneName());
    }

    /**
     * @return $this
     */
    public function clearVoters(){
        $this->Votes()->removeAll();
        $this->VoterFileID = 0;
        return $this;
    }

}