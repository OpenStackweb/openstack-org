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
 * Class SpeakerBureauApi
 */
final class SpeakerBureauApi
	extends AbstractRestfulJsonApi {

	const ApiPrefix = 'api/v1/speaker-bureau';

	protected function isApiCall(){
		$request = $this->getRequest();
		if(is_null($request)) return false;
		return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
	}

	/**
	 * @var SpeakerBureauManager
	 */
	private $speaker_bureau_manager;

	public function __construct(){
		parent::__construct();

        $this->securityToken     = new SecurityToken();

		$this->speaker_bureau_manager = new SpeakerBureauManager(
			new SapphirePresentationSpeakerRepository,
			new SapphireSpeakerContactEmailRepository,
            new SpeakerContactEmailFactory,
			SapphireTransactionManager::getInstance()
		);

        $this_var           = $this;
        $security_token     = $this->securityToken;


        $this->addBeforeFilter('sendSpeakerEmail','check_access_reject',function ($request) use($this_var, $security_token){
            $data = $this_var->getJsonRequest();
            if (!$data) return $this->serverError();
            if (!$security_token->checkRequest($request)) return $this->forbiddenError();
            if ($data['field_98438688'] != '') return $this->forbiddenError();
        });
	}

    /**
     * @return bool
     */
    protected function authorize()
    {
        return true;
    }

	/**
	 * @var array
	 */
	static $url_handlers = array(
		'PUT email' => 'sendSpeakerEmail',
	);

	/**
	 * @var array
	 */
	static $allowed_actions = array(
		'sendSpeakerEmail',
	);

    public function sendSpeakerEmail() {
        try{
            $data = $this->getJsonRequest();
            if (!$data) return $this->serverError();

            $speaker_id = $data['speaker_id'];

            $this->speaker_bureau_manager->sendEmail($speaker_id, $data);
            return $this->ok();
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::ERR);
            return $this->notFound($ex1->getMessage());
        }
        catch(EntityValidationException $ex2){
            SS_Log::log($ex2,SS_Log::NOTICE);
            return $this->validationError($ex2->getMessages());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }
}