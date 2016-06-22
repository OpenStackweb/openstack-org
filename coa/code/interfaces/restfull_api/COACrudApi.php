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
 * Class COACrudApi
 */
final class COACrudApi
	extends AbstractRestfulJsonApi {

	const ApiPrefix = 'api/v1/coa';

	protected function isApiCall(){
		$request = $this->getRequest();
		if(is_null($request)) return false;
		return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
	}

	/**
	 * @var IEntityRepository
	 */
	private $repository;

	public function __construct(){
		parent::__construct();

		$this->repository = new SapphireCOAExamRepository();

	}

	/**
	 * @return bool
	 */
	protected function authorize()
	{
		return true;
	}

    /**
     * @return bool|Member
     */
    protected function authenticate()
    {
        return true;
    }

	/**
	 * @var array
	 */
	static $url_handlers = array(
        'GET $CERT_ID!/$LAST_NAME!' => 'getCOAExam',
	);

	/**
	 * @var array
	 */
	static $allowed_actions = array(
		'getCOAExam',
	);

	public function getCOAExam(){
		$cert_id   = Convert::raw2sql($this->request->param('CERT_ID'));
		$last_name = Convert::raw2sql(html_entity_decode($this->request->param('LAST_NAME')));
		try{
			$exam = $this->repository->getByCertAndLastName($cert_id,$last_name);
            if ($exam->count() > 0) {
                $exam_obj = $exam->first();
                $exam_map = $exam_obj->toMap();
                $exam_map['OwnerName'] = $exam_obj->Owner()->FirstName.' '.$exam_obj->Owner()->Surname;
                $exam_map['PassFailDate'] = date('M jS Y',strtotime($exam_obj->PassFailDate));
                return $this->ok($exam_map);
            }

			return $this->ok();
		}
		catch(Exception $ex){
			SS_Log::log($ex,SS_Log::ERR);
			return $this->serverError();
		}
	}


}