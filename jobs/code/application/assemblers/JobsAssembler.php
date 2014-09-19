<?php

/**
 * Class JobsAssembler
 */
final class JobsAssembler {
	/**
	 * @param IJobRegistrationRequest $request
	 * @return array
	 */
	public static function convertJobRegistrationRequestToArray(IJobRegistrationRequest $request){
		$res                           = array();
		$main_info                     = $request->getMainInfo();
		$res['title']                  = $main_info->getTitle();
		$res['url']                    = $main_info->getUrl();
		$res['description']            = $main_info->getDescription();
		$res['instructions']           = $main_info->getInstructions();
		$res['company_name']           = $main_info->getCompany()->Name;
		$res['location_type']          = $main_info->getLocationType();
		$expiration_date               = $main_info->getExpirationDate();
		if(!is_null($expiration_date))
			$res['expiration_date']    = $expiration_date->format('Y-m-d');
		$point_of_contact              = $request->getPointOfContact();
		$res['point_of_contact_name']  = $point_of_contact->getName();
		$res['point_of_contact_email'] = $point_of_contact->getEmail();
		$locations = array();
		foreach($request->getLocations() as $location){
			$l            = array();
			$l['city']    = $location->city();
			$l['state']   = $location->state();
			$l['country'] = $location->country();
			array_push($locations,$l);
		}
		$res['locations'] = $locations;
		return $res;
	}
} 