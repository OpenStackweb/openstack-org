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
 * Class GerritAPI
 */
final class GerritAPI
	implements IGerritAPI  {

	/**
	 * @var string
	 */
	private $user;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $domain;

	/**
	 * @param string $domain
	 * @param string $user
	 * @param string $password
	 */
	public function __construct($domain, $user, $password){
		$this->domain   = $domain;
		$this->user     = $user;
		$this->password = $password;
	}

	/**
	 * @param string $group_id
	 * @return array|mixed
	 * @throws UnauthorizedRestfullAPIException
	 */
	public function listAllMembersFromGroup($group_id){
		$url  = sprintf("%s/a/groups/%s/members", $this->domain, $group_id);
		$json = $this->fixGerritJsonResponse($this->request('GET', $url));
		if(strtolower($json)==='unauthorized')
			throw new UnauthorizedRestfullAPIException($json);

		return json_decode($json, true);
	}

    /**
     * @param string $method
     * @param string $url
     * @param string|null $query
     * @return string
     * @throws EntityValidationException
     */
	private function request($method, $url, $query = null){
        try {
            $client   = new \GuzzleHttp\Client(
                [
                    'defaults'            => [
                        'timeout'         => 60,
                        'allow_redirects' => false,
                        'verify'          => true
                    ]
                ]
            );

            $options  = [
                'auth' => [
                    $this->user,
                    $this->password,
                    "Digest"
                ]
            ];

            if(!empty($query)){
                $options['query'] = $query;
            }

            $request  = $client->createRequest($method, $url , $options);

            $response = $client->send($request);

            if($response->getStatusCode() == 200)
                return $response->getBody()->getContents();

            throw new Exception();

        } catch (Exception $ex) {
            SS_Log::log($ex, SS_Log::ERR);
            throw new EntityValidationException
            (
                [
                    ['message' => 'server error']
                ]
            );
        }
    }


	private function fixGerritJsonResponse($json){
		$json = str_replace(")]}'",'',$json);
		return $json;
	}

    /**
     * @param string $gerrit_user_id
     * @param string $status
     * @param int $batch_size
     * @param null $start
     * @return mixed|null
     */
	public function getUserCommits($gerrit_user_id, $status, $batch_size = 500, $start = null)
	{
	    $query         =[

            //'q' => sprintf("status:%s+owner:%s", $status, $gerrit_user_id),
            //'n' => $batch_size
            'q' => sprintf("owner:%s status:%s", $gerrit_user_id, $status),
            'n' => intval($batch_size)
        ];

        if(!is_null($start)){
            // The S or start query parameter can be supplied to skip a number of changes from the list.
            $query['S'] = $start;
        }

		$url           = sprintf("%s/a/changes/", $this->domain);
		$json          = $this->fixGerritJsonResponse($this->request('GET', $url, $query));

		$response = json_decode($json, true);
		if(is_array($response) && count($response) > 0){
			return $response;
		}
		return null;
	}
}