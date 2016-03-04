<?php

/**
 * Copyright 2015 OpenStack Foundation
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
class SurveysRestfulApi extends AbstractRestfulJsonApi
{

    const ApiPrefix = 'api/v1/surveys';

    protected function isApiCall()
    {
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
    }

    /**
     * @var ISurveyManager
     */
    private $survey_manager;

    /**
     * @var ISurveyRepository
     */
    private $survey_repository;

    /**
     * @return ISurveyManager
     */
    public function getSurveyManager()
    {
        return $this->survey_manager;
    }

    /**
     * @param ISurveyManager $survey_manager
     */
    public function setSurveyManager(ISurveyManager $survey_manager)
    {
        $this->survey_manager = $survey_manager;
    }

    /**
     * @return ISurveyRepository
     */
    public function getSurveyRepository()
    {
        return $this->survey_repository;
    }

    /**
     * @param ISurveyRepository $survey_repository
     */
    public function setSurveyRepository(ISurveyRepository $survey_repository)
    {
        $this->survey_repository = $survey_repository;
    }


    /**
     * @return bool
     */
    protected function authorize()
    {
        return $this->checkOwnAjaxRequest($this->getRequest());
    }

    private static $allowed_actions = array
    (
        'suggestMember',
        'suggestOrganization',
        'getTeamMembers',
        'addTeamMember',
        'deleteTeamMember',
    );

    static $url_handlers = array
    (
        'GET team-members/suggest'                                        => 'suggestMember',
        'GET organizations/suggest'                                       => 'suggestOrganization',
        'GET entity-surveys/$ENTITY_SURVEY_ID/team-members'               => 'getTeamMembers',
        'POST entity-surveys/$ENTITY_SURVEY_ID/team-members/$MEMBER_ID'   => 'addTeamMember',
        'DELETE entity-surveys/$ENTITY_SURVEY_ID/team-members/$MEMBER_ID' => 'deleteTeamMember',
    );

    public function suggestMember(SS_HTTPRequest $request)
    {
        if (!Director::is_ajax()) return $this->forbiddenError();

        $term       = Convert::raw2sql($request->getVar('term'));
        $split_term = explode(' ', $term);

        if(!Member::currentUser()) return $this->forbiddenError();

        $current_user_id = Member::currentUserID();

        $full_name_condition = " FirstName LIKE '%{$term}%' OR Surname LIKE '%{$term}%' ";
        if(count($split_term) == 2)
        {
            $full_name_condition = " (FirstName LIKE '%{$split_term[0]}%' OR Surname LIKE '%{$split_term[1]}%') ";
        }

        $members = Member::get()
            ->where("ID <> {$current_user_id} AND Email <> '' AND ( {$full_name_condition} )")
            ->sort
            (
                array
                (
                    'Surname' => 'ASC',
                    'FirstName' => 'ASC',
                )
            )
            ->limit(100);

        $items = array();

        foreach ($members as $member)
        {
            $items[] = array(
                'id'    => $member->ID,
                'label' => sprintf('%s, %s (%s)',$member->Surname, $member->FirstName,($member->getCurrentAffiliation())? $member->getCurrentAffiliation()->Organization()->Name:'N/A') ,
                'value' => sprintf('%s, %s (%s)',$member->Surname, $member->FirstName,($member->getCurrentAffiliation())? $member->getCurrentAffiliation()->Organization()->Name:'N/A')
            );
        }

        return $this->ok($items);
    }

    public function suggestOrganization(SS_HTTPRequest $request)
    {
        if (!Director::is_ajax()) return $this->forbiddenError();
        if(!Member::currentUser()) return $this->forbiddenError();

        $term = Convert::raw2sql($request->getVar('term'));
        $orgs = Org::get()->filter('Name:PartialMatch', $term)
            ->sort('Name')
            ->limit(100);

        $items = array();
        foreach($orgs as $org)
        {
            $items[] = array(
                'id'    => $org->ID,
                'label' => $org->Name ,
                'value' => $org->Name
            );
        }

        return $this->ok($items);
    }

    public function addTeamMember(SS_HTTPRequest $request)
    {
        if (!Director::is_ajax()) return $this->forbiddenError();
        if(!Member::currentUser()) return $this->forbiddenError();

        $entity_survey_id  = (int)$request->param('ENTITY_SURVEY_ID');
        $member_id         = (int)$request->param('MEMBER_ID');

        try {
            $this->survey_manager->registerTeamMemberOnEntitySurvey
            (
                $entity_survey_id,
                $member_id,
                new EntitySurveyTeamMemberEmailSenderService
            );
            return $this->deleted();
        }
        catch(Exception $ex)
        {
            return $this->serverError();
        }
    }

    public function deleteTeamMember(SS_HTTPRequest $request)
    {
        if (!Director::is_ajax()) return $this->forbiddenError();
        if(!Member::currentUser()) return $this->forbiddenError();

        $entity_survey_id = (int)$request->param('ENTITY_SURVEY_ID');
        $member_id        = (int)$request->param('MEMBER_ID');

        try {
            $this->survey_manager->unRegisterTeamMemberOnEntitySurvey(
                $entity_survey_id,
                $member_id
            );
           return $this->deleted();
        }
        catch(Exception $ex)
        {
            return $this->serverError();
        }
    }

    public function getTeamMembers(SS_HTTPRequest $request)
    {
        if (!Director::is_ajax()) return $this->forbiddenError();
        if(!Member::currentUser()) return $this->forbiddenError();

        $entity_survey_id = (int)$request->param('ENTITY_SURVEY_ID');

        try {
            $entity_survey = $this->survey_repository->getById($entity_survey_id);
            if(is_null($entity_survey) || !$entity_survey instanceof IEntitySurvey) return $this->httpError(404);
            $items = array();
            foreach($entity_survey->getTeamMembers() as $member)
            {
                $items[] = array
                (
                    'id'      => $member->ID,
                    'fname'   => $member->FirstName ,
                    'lname'   => $member->Surname ,
                    'pic_url' => $member->ProfilePhotoUrl(100)
                );
            }
            return $this->ok($items);
        }
        catch(Exception $ex)
        {
            return $this->serverError();
        }
    }

}