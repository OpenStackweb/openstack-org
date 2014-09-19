<?php
/**
 * Class PublisherSubscriberManager
 */
final class PublisherSubscriberManager {
	/**
	 * @var PublisherSubscriberManager
	 */
	private static $instance;

	private $events = array(); // all subscriptions

	private function __construct(){}

	private function __clone(){}

	/**
	 * @return PublisherSubscriberManager
	 */
	public static function getInstance(){
		if(!is_object(self::$instance)){
			self::$instance = new PublisherSubscriberManager();
		}
		return self::$instance;
	}

	/**
	 * @param string   $event_name
	 * @param $callback
	 */
	public function subscribe($event_name, $callback){

		// Make sure the subscription isn't null
		if ( empty( $this->events[ $event_name ] ) )
			$this->events[ $event_name ] = array();
		// push the $callback onto the subscription stack
		array_push( $this->events[ $event_name ], $callback );
	}

	/**
	 * @param string$event_name
	 * @param array $params
	 * @return bool
	 */
	public function publish( $event_name, array $params = array())
	{
		// Check to see if the subscribe isn't null
		if ( empty($this->events[$event_name] ) )
			return false;

		// Loop through all the events and call them
		foreach ( $this->events[$event_name] as $event )
		{
			if ( is_callable( $event ) )
				call_user_func_array( $event, $params );
		}
	}

	/**
	 * @param string $event_name
	 */
	public function unsubscribe($event_name){
		if ( !empty( $this->events[$event_name] ) )
			unset($this->events[$event_name] );
	}

} 