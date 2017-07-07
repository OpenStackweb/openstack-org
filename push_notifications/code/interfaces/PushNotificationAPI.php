<?php

/**
 * Class PushNotificationAPI
 */
class PushNotificationAPI extends AbstractRestfulJsonApi
{
    use RestfulJsonApiResponses;
    /**
     * @var array
     */
    private static $url_handlers = [
        'POST subscribe/$Token/$Topic' => 'subscribeToTopic',
    ];

    /**
     * @var array
     */
    private static $allowed_actions = [
        'subscribeToTopic',
    ];

    /**
     * @var array
     */
    private static $extensions = [
        'MemberTokenAuthenticator'
    ];

    private $tx_manager;

    private $firebase_api;

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->checkAuthenticationToken(false);
        $this->tx_manager = SapphireTransactionManager::getInstance();
        $this->firebase_api = new FireBaseGCMApi(FIREBASE_GCM_SERVER_KEY);
    }

    /**
     * @return bool
     */
    protected function isApiCall()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function authorize()
    {
        return !!Member::currentUser();
    }


    /**
     * @param SS_HTTPRequest $r
     * @return string
     */
    public function subscribeToTopic(SS_HTTPRequest $r)
    {    	
        $token = $r->postVar('Token');
        $topic = $r->postVar('Topic');

        $response = $this->firebase_api->subscribeToTopicWeb($token, $topic);

        return $response;
    }
}

