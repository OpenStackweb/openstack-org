<?php


class SchedAPI extends Object {


	protected $restfulService = null;


	protected $apiKey = null;


	protected $format = "json";


	public function __construct($restfulService, $apiKey) {
		$this->restfulService = $restfulService;
		$this->apiKey = $apiKey;
	}



	public function getSessions($since = null) {
		$query = array ();
		if($since) {
			$query['since'] = $since;
		}

		return $this->callRestfulService('session/list', $query);
	}



    protected function callRestfulService($endpoint, $query = array ()) {
        $req = $this->restfulService;
        $req->httpHeader("Accept-Charset: utf-8");
        $query['api_key'] = $this->apiKey;
        $query['format'] = $this->format;
        $req->setQueryString($query);
        
        $response = $req->request($endpoint);
        
        if(!$response) {
            return false;
        }

        return Convert::json2array($response->getBody(), true);        
    }


    public function setFormat($format) {
    	$this->format = $format;

    	return $this;
    }


}