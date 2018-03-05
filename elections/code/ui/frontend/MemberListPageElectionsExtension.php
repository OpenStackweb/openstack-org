<?php
/**
 * Copyright 2018 OpenStack Foundation
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

final class MemberListPageElectionsExtension extends Extension
{

    /**
     * @var IElectionManager
     */
    private $manager;

    /**
     * @var IFoundationMemberRepository
     */
    private $member_repository;

    /**
     * MemberListPageElectionsExtension constructor.
     * @param IFoundationMemberRepository $member_repository
     * @param IElectionManager $manager
     */
    public function __construct(IFoundationMemberRepository $member_repository, IElectionManager $manager){
        $this->member_repository = $member_repository;
        $this->manager           = $manager;
    }

    private static $allowed_actions = [
        'confirmNomination',
        'saveNomination',
        'CurrentElection',
        'candidateStats',
        'Candidate',
        'SelectedMember'
    ];

    public function onBeforeInit()
    {
        Config::inst()->update(get_class($this), 'allowed_actions', self::$allowed_actions);
        Config::inst()->update(get_class($this->owner), 'allowed_actions', self::$allowed_actions);
    }

    public function onAfterInit()
    {

    }

    function confirmNomination(SS_HTTPRequest $request)
    {

        $results = [];
        try {
            $current_member = Member::currentUser();
            if(is_null($current_member))
                return $this->owner->httpError(413,'User is not logged');
            // Grab candidate ID from the URL
            $candidate_id = $request->param("ID");
            $res = $this->manager->isValidNomination($candidate_id);

            $nominee = Member::get()->filter(array('ID' => $candidate_id))->first();
            $results["Success"] = true;
            $results["Candidate"] = $nominee;
            $results["NominateLink"] = $this->owner->Link() . "saveNomination/" . $candidate_id;
            $results["BackLink"] = $this->owner->Link() . "profile/" . $candidate_id . '/' . $nominee->getNameSlug();
        }
        catch (EntityValidationException $ex1){
            $validation_res = $ex1->getMessage();
            switch($validation_res){
                case '* ALREADY NOMINATED':{
                    $Nominee = Member::get()->filter(array('ID' => $candidate_id))->first();

                    $results["Election"]      = $this->CurrentElection();
                    $results["ElectionPage"]  = $this->CurrentElectionPage();
                    $results["Success"]       = false;
                    $results["NominatedByMe"] = true;
                    $results["Candidate"]     = $Nominee;
                    $results["BackLink"]      = $this->owner->Link() . "profile/" . $candidate_id . '/' . $Nominee->getNameSlug();
                }
                break;
                case '* LIMIT EXCEEDED':{
                    $Nominee = Member::get()->filter(array('ID' => $candidate_id))->first();

                    $results["Success"] = false;
                    $results["LimitExceeded"] = true;
                    $results["Candidate"] = $Nominee;
                    $results["BackLink"] = $this->owner->Link() . "profile/" . $candidate_id . '/' . $Nominee->getNameSlug();
                }
                    break;
                default:
                {
                    $results["Success"] = false;
                    $results["BackLink"] = $this->owner->Link() ;
                }
                    break;
            }
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
        }
        catch (NotFoundEntityException $ex2){
            $results["Success"] = false;
            $results["BackLink"] = $this->owner->Link();
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
        }
        catch(SS_HTTPResponse_Exception $ex3){
            throw $ex3;
        }
        catch (Exception $ex){
            $results["Success"] = false;
            $results["BackLink"] = $this->owner->Link() . "profile/" . $candidate_id;
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
        }

        return $results;
    }

    function findMember($member_id)
    {
        $member  = Member::get()->byID(intval($member_id));
        if(!is_null($member))
        {
            // Check to make sure they are in the foundation membership group
            If ($member->inGroup(5, true) && $member->isActive())
            {
                return $member;
            }
        }
    }

    /**
     * @param SS_HTTPRequest $request
     */
    function saveNomination(SS_HTTPRequest $request)
    {

        try{
            $candidate_id = $request->param("ID");
            $candidate = $this->member_repository->getById($candidate_id);
            if(is_null($candidate))
                throw new NotFoundEntityException('Candidate');

            $this->manager->nominateMemberOnCurrentElection($candidate_id, new NominationEmailSender);

            $this->owner->setMessage('Success',
                "You've just nominated " . $candidate->FullName . ' for the OpenStack Board.');
            $this->owner->redirect($this->owner->Link('candidateStats/' . $candidate_id));
        }
        catch (NotFoundEntityException $ex1){
            $this->owner->setMessage('Error', $ex1->getMessage());
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            $this->owner->redirect($this->owner->Link());
        }
        catch (EntityValidationException $ex2){
            $this->owner->setMessage('Error', $ex2->getMessage());
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            $this->owner->redirect($this->owner->Link());
        }
        catch(Exception $ex){
            $this->setMessage('Error', "There was an error logging your nomination.");
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            $this->owner->redirect($this->owner->Link());
        }

    }

    // Return the currently running election
    // This simple function primarily exists to be used in the template
    function CurrentElection()
    {
        return Election::getCurrent();
    }

    // Return the currently running election
    // This simple function primarily exists to be used in the template
    function CurrentElectionPage()
    {
        $election = Election::getCurrent();
        if(!is_null($election))
            return ElectionPage::get()->filter('CurrentElectionID', $election->ID)->first();
        return false;
    }

    function Candidate(){
        $current_election = $this->CurrentElection();
        if(!$current_election) return false;
        $selected_member_id = intval(Convert::raw2sql($this->owner->request->param("MemberID")));
        return Candidate::get()->filter(array(
            'MemberID'   => $selected_member_id,
            'ElectionID' => $current_election->ID
        ))->first();
    }

    function SelectedMember(){
        $selected_member_id = intval(Convert::raw2sql($this->owner->request->param("MemberID")));
        return Member::get()->byID($selected_member_id);
    }

    function alreadyNominated($candidateID, $CurrentElection)
    {

        $memberID = Member::currentUserID();
        $NominationsForThisCandidate = $CurrentElection->CandidateNominations("`MemberID` = " . $memberID . " AND `CandidateID` = " . $candidateID);

        if ($NominationsForThisCandidate->Count() >= 1) {
            return true;
        }
    }

    function candidateStats(SS_HTTPRequest $request)
    {

        // Grab candidate ID from the URL
        $CandidateID = $request->param("ID");

        // Check to see if the candidate is valid
        if (is_numeric($CandidateID) && $this->findMember($CandidateID)) {

            $CurrentElection = $this->CurrentElection();
            $Candidate = $CurrentElection->Candidates("MemberID = " . $CandidateID);

            $results["Success"] = true;
            $results["Candidate"] = $Candidate;

            return $results;

        } else {

            //Member not found
            return $this->owner->httpError(404, 'Sorry that candidate could not be found');
        }
    }

    /**
     *  Extensions Points
     */

    public function getHeaderExtensions(&$html){
        $view = new SSViewer('MemberListPage_ElectionsNominationsOpen');
        $html .= $view->process($this->owner);
    }

    public function getProfileExtensions(&$html){
        $view = new SSViewer('MemberListPage_ElectionsCandidateAcceptedNominations');
        $html .= $view->process($this->owner);
    }

    public function getProfileExtensionsFooter(&$html){
        $view = new SSViewer('MemberListPage_ElectionsNominateProfile');
        $html .= $view->process($this->owner);
    }
}