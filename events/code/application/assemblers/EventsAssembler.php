<?php
/**
 * Class EventsAssembler
 */
final class EventsAssembler {
	/**
	 * @param IEventRegistrationRequest $request
	 * @return array
	 */
	public static function convertEventRegistrationRequestToArray(IEventRegistrationRequest $request){
		$res                           = array();
		$main_info                     = $request->getMainInfo();
		$res['title']                  = $main_info->getTitle();
		$res['url']                    = $main_info->getUrl();
		$res['label']                  = $main_info->getLabel();
		$point_of_contact              = $request->getPointOfContact();
		$res['point_of_contact_name']  = $point_of_contact->getName();
		$res['point_of_contact_email'] = $point_of_contact->getEmail();
		$location                      = $request->getLocation();
		$res['city']                   = $location->getCity();
		$res['state']                  = $location->getState();
		$res['country']                = $location->getCountry();
		$duration                      = $request->getDuration();
		$res['start_date']             = $duration->getStartDate()->format('Y-m-d');
		$res['end_date']               = $duration->getEndDate()->format('Y-m-d');
		return $res;
	}
}