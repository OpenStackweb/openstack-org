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
 * Class SummitAppSpeakersApi
 */
class SummitAppSpeakersApi extends AbstractRestfulJsonApi {


    /**
     * @var ISpeakerRepository
     */
    private $speaker_repository;

    /**
     * @var ISummitRepository
     */
    private $summit_repository;

    /**
     * @var ISummitService
     */
    private $summit_service;


    public function __construct
    (
        ISummitRepository $summit_repository,
        ISpeakerRepository $speaker_repository,
        ISummitService $summit_service
    )
    {
        parent::__construct();
        $this->summit_repository             = $summit_repository;
        $this->speaker_repository            = $speaker_repository;
        $this->summit_service                = $summit_service;
    }

    protected function isApiCall(){
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return true;
    }

    /**
     * @return bool
     */
    protected function authorize(){
        if(!Permission::check('ADMIN_SUMMIT_APP_FRONTEND_ADMIN')) return false;
        return $this->checkOwnAjaxRequest();
    }

    protected function authenticate() {
        return true;
    }

    static $url_handlers = array(
        'GET byID/$SPEAKER_ID!'        => 'getSpeakerByID',
        'GET only/$TERM!'              => 'getSpeakersOnlyByTerm',
        'GET $TERM!'                   => 'getSpeakersByTerm',
        'GET '                         => 'getSpeakers',
        'POST '                        => 'addSpeaker',
        'POST merge/$ID_ONE!/$ID_TWO!' => 'mergeSpeakers',
        'PUT $SPEAKER_ID!'             => 'updateSpeaker',
        'POST $SPEAKER_ID!/pic'        => 'uploadSpeakerPic',
    );

    static $allowed_actions = array(
        'getSpeakers',
        'getSpeakersByTerm',
        'updateSpeaker',
        'addSpeaker',
        'uploadSpeakerPic',
        'getSpeakerByID',
        'mergeSpeakers',
        'getSpeakersOnlyByTerm',
    );

    // this is called when typing a Speakers name to add as a tag on edit event
    public function getSpeakersByTerm(SS_HTTPRequest $request){
        try
        {
            $term         = Convert::raw2sql($request->param('TERM'));
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = Summit::get_by_id('Summit',$summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            return $this->ok($this->speaker_repository->searchByTerm($term), false);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    // this is called when typing a Speakers name to add as a tag on edit event
    public function getSpeakersOnlyByTerm(SS_HTTPRequest $request){
        try
        {
            $term         = Convert::raw2sql($request->param('TERM'));
            // need to do this so you can search the '+' sign if not SS will just decode '+' as space
            //$url_pieces = explode('/', $_SERVER["QUERY_STRING"]);
            //$term = array_pop($url_pieces);
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = Summit::get_by_id('Summit',$summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            return $this->ok($this->speaker_repository->searchSpeakersOnlyByTerm($term), false);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getSpeakers(SS_HTTPRequest $request){
        try
        {
            $query_string = $request->getVars();
            $page         = (isset($query_string['page'])) ? intval(Convert::raw2sql($query_string['page'])) : '';
            $page_size    = (isset($query_string['items'])) ? intval(Convert::raw2sql($query_string['items'])) : '';
            $term         = (isset($query_string['term'])) ? trim(Convert::raw2sql($query_string['term'])) : '';
            $sort_by      = (isset($query_string['sort_by'])) ? trim(Convert::raw2sql($query_string['sort_by'])) : '';
            $sort_dir     = (isset($query_string['sort_dir'])) ? trim(Convert::raw2sql($query_string['sort_dir'])) : '';
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            list($page, $page_size, $count, $speakers) = $this->speaker_repository->searchByTermPaginated
            (
                $page,
                $page_size,
                $term,
                $sort_by,
                $sort_dir
            );
            $data = array();

            foreach($speakers as $speaker) {
                $promo_code  = $speaker->getSummitPromoCode($summit_id);
                $data[] = array(
                    'id'                 => $speaker->ID,
                    'member_id'          => $speaker->MemberID,
                    'name'               => $speaker->getName(),
                    'email'              => $speaker->getEmail(),
                    'onsite_phone'       => $speaker->getOnSitePhoneFor($summit_id),
                    'presentation_count' => $speaker->Presentations()->count(),
                    'registration_code'  => !is_null($promo_code) ? $promo_code->Code : '',
                );
            }

            return $this->ok(array('page' => $page, 'page_size' => $page_size, 'count' => $count, 'speakers' => $data));
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function getSpeakerByID(SS_HTTPRequest $request){
        try
        {
            $speaker_id   = intval($request->param('SPEAKER_ID'));
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = Summit::get_by_id('Summit',$summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $speaker = PresentationSpeaker::get_by_id('PresentationSpeaker',$speaker_id);
            $speaker_array = array(
                'Title' => $speaker->Title,
                'FirstName' => $speaker->FirstName,
                'LastName' => $speaker->LastName,
                'Email' => $speaker->RegistrationRequest()->Email,
                'Member' => ($speaker->Member()->Exists()) ? $speaker->Member()->toMap() : null,
                'Twitter' => $speaker->TwitterName,
                'IRC' => $speaker->IRCHandle,
                'Bio' => $speaker->Bio,
                'PicUrl' => $speaker->ProfilePhoto(50),
                'Expertise' => $speaker->AreasOfExpertise()->toNestedArray(),
                'Presentations' => $speaker->Presentations()->toNestedArray(),
                'OtherPresentations' => $speaker->OtherPresentationLinks()->toNestedArray(),
                'TravelPreferences' => $speaker->TravelPreferences()->toNestedArray(),
                'Languages' => $speaker->Languages()->toNestedArray(),
                'Promocodes' => $speaker->PromoCodes()->toNestedArray(),
                'Assistances' => $speaker->SummitAssistances()->toNestedArray(),
                'OrganizationalRoles' => $speaker->OrganizationalRoles()->toNestedArray(),
                'ActiveInvolvements' => $speaker->ActiveInvolvements()->toNestedArray(),
            );

            return $this->ok($speaker_array, false);
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $ex->getMessage();
        }
    }

    public function addSpeaker(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $data         = $this->getJsonRequest();
            $this->summit_service->createSpeaker($summit, $data);
            return $this->ok();
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(EntityAlreadyExistsException $ex3)
        {
            SS_Log::log($ex3->getMessage(), SS_Log::WARN);
            return $this->validationError(array(
                array('message' => $ex3->getMessage())
            ));
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function updateSpeaker(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $speaker_id   = intval($request->param('SPEAKER_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $data         = $this->getJsonRequest();
            $data['speaker_id'] = $speaker_id;
            $this->summit_service->updateSpeaker
            (
                $summit,
                HTMLCleanner::cleanData
                (
                    $data,
                    array('title', 'first_name', 'last_name', 'bio', 'twitter_name', 'irc_name')
                )
            );
            return $this->ok();
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function uploadSpeakerPic(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $speaker_id   = intval($request->param('SPEAKER_ID'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $image = $this->summit_service->uploadSpeakerPic($summit, $speaker_id, $_FILES['file']);
            return $this->ok($image->ID);
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function mergeSpeakers(SS_HTTPRequest $request){
        try
        {
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $speaker_1    = intval($request->param('ID_ONE'));
            $speaker_2    = intval($request->param('ID_TWO'));
            $summit       = $this->summit_repository->getById($summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));
            $data         = $this->getJsonRequest();
            $changes = $this->summit_service->mergeSpeakers($summit, $speaker_1, $speaker_2, $data);
            return $this->ok($changes);
        }
        catch(EntityValidationException $ex1)
        {
            SS_Log::log($ex1->getMessage(), SS_Log::WARN);
            return $this->validationError($ex1->getMessages());
        }
        catch(NotFoundEntityException $ex2)
        {
            SS_Log::log($ex2->getMessage(), SS_Log::WARN);
            return $this->notFound($ex2->getMessage());
        }
        catch(Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return $this->serverError();
        }
    }
}