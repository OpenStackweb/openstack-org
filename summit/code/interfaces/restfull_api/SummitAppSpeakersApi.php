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
        'GET $TERM!'                   => 'getSpeakersByTerm',
        'GET '                         => 'getSpeakers',
        'POST '                        => 'addSpeaker',
        'PUT $SPEAKER_ID!'             => 'updateSpeaker',
        'POST $SPEAKER_ID!/pic'        => 'uploadSpeakerPic',
    );

    static $allowed_actions = array(
        'getSpeakers',
        'getSpeakersByTerm',
        'updateSpeaker',
        'addSpeaker',
        'uploadSpeakerPic',
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

            list($page, $page_size, $count, $speakers) = $this->speaker_repository->getBySummit
            (
                $summit,
                $page,
                $page_size,
                $term,
                $sort_by,
                $sort_dir
            );
            $data = array();

            foreach($speakers as $speaker) {
                $data[] = array(
                    'id'                 => $speaker->ID,
                    'member_id'          => $speaker->MemberID,
                    'name'               => $speaker->getName(),
                    'email'              => $speaker->getEmail(),
                    'onsite_phone'       => $speaker->getOnSitePhoneFor($summit_id),
                    'presentation_count' => $speaker->Presentations()->count(),
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
            $this->summit_service->updateSpeaker($summit, $data);
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
}