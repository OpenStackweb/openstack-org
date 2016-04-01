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
final class SummitAppRegistrationCodesApi extends AbstractRestfulJsonApi
{
    /**
     * @var IEntityRepository
     */
    private $summit_repository;

    public function __construct
    (
        ISummitRepository $summit_repository
    )
    {
        parent::__construct();
        $this->summit_repository = $summit_repository;
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
        'GET $REG_CODE!' => 'getRegistrationCodeByTerm',
    );

    static $allowed_actions = array(
        'getRegistrationCodeByTerm',
    );

    public function getRegistrationCodeByTerm(SS_HTTPRequest $request) {
        try
        {
            $term         = Convert::raw2sql($request->param('REG_CODE'));
            $summit_id    = intval($request->param('SUMMIT_ID'));
            $summit       = Summit::get_by_id('Summit',$summit_id);
            if(is_null($summit)) throw new NotFoundEntityException('Summit', sprintf(' id %s', $summit_id));

            $codes = SpeakerSummitRegistrationPromoCode::get()->filter
            (
                array
                (
                    'SummitID'  => $summit_id,
                    'OwnerID'   => 0,
                    'SpeakerID' => 0,
                )
            )->where(" Code LIKE '{$term}%' ")->limit(25,0);

            $data = array();
            foreach ($codes as $code) {

                $data[] = array
                (
                    'code' => trim($code->Code),
                    'name' => sprintf('%s (%s)', $code->Code, $code->Type )
                );
            }
            return $this->ok($data, false);
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