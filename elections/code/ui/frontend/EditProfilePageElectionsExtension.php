<?php
/**
 * Copyright 2018 Open Infrastructure Foundation
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

final class EditProfilePageElectionsExtension extends Extension
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
     * EditProfilePageElectionsExtension constructor.
     * @param IFoundationMemberRepository $member_repository
     * @param IElectionManager $manager
     */
    public function __construct(IFoundationMemberRepository $member_repository, IElectionManager $manager)
    {
        $this->member_repository = $member_repository;
        $this->manager = $manager;
    }

    private static $allowed_actions = [
        'CandidateApplication',
        'CandidateApplicationForm',
        'saveCandidateApplicationForm',
        'election',
    ];

    public function onBeforeInit()
    {
        Config::inst()->update(get_class($this), 'allowed_actions', self::$allowed_actions);
        Config::inst()->update(get_class($this->owner), 'allowed_actions', self::$allowed_actions);
    }

    public function onAfterInit()
    {

    }

    public function getNavActionsExtensions(&$html)
    {
        $view = new SSViewer('EditProfilePage_ElectionsNav');
        $html .= $view->process($this->owner);
    }

    function CurrentElection()
    {
        return Election::getCurrent();
    }

    function CurrentElectionPage()
    {
        return ElectionPage::getCurrent();
    }

    // Candidate Application Form

    function CandidateApplicationForm()
    {
        if (!Member::currentUser())
            return $this->owner->httpError(404, 'you need to be logged!');

        $current_election = Election::getCurrent();

        if(is_null($current_election))
            return $this->owner->httpError(404, 'there is not current election!');

        $form = new CandidateApplicationForm($this->owner, 'CandidateApplicationForm', ElectionPage::getCurrent());
        $form->disableSecurityToken();

        $current_candidate = Candidate::get()->filter(
            [
                'MemberID' => Member::currentUserID(),
                'ElectionID' => $current_election->ID
            ])->first();

        if ($data = Session::get("FormInfo.{$form->FormName()}.userData")) {
            $form->loadDataFrom($data);
        } else {
            // Fill in the form$CurrentElection
            if ($current_candidate) {
                $form->loadDataFrom($current_candidate, false);
            }

            $form->loadDataFrom(Member::currentUser(), false);
        }

        return $form;
    }

    public function election(SS_HTTPRequest $request)
    {
        return $this->owner->getViewer('election')->process($this->owner);
    }

    // Save an edited candidate
    function saveCandidateApplicationForm($data, $form)
    {
        try {

            Session::set("FormInfo.{$form->FormName()}.userData", $data);

            if (!Member::currentUser())
                return $this->owner->httpError(404, 'you need to be logged!');

            $candidate = $this->manager->registerCandidate(Member::currentUser(), Election::getCurrent(), $data);
            Session::clear("FormInfo.{$form->FormName()}.userData", $data);
            if ($candidate->HasAcceptedNomination) {
                $form->clearMessage();
                $this->owner->redirect($this->owner->Link() . 'election/');
                return;
            }
            $form->clearMessage();
            $form->sessionMessage("Your edits have been saved but you will need to provide full answers to all these questions to be eligible as a candidate.", "bad");
            $this->owner->redirectBack();
        } catch (EntityValidationException $ex1) {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            $form->clearMessage();
            $form->sessionMessage($ex1->getMessage(), "bad");
            $this->owner->redirectBack();
        } catch (Exception $ex) {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            $form->clearMessage();
            $form->sessionMessage('There was an error saving your edits.', "bad");
            $this->owner->redirectBack();
        }
    }
}