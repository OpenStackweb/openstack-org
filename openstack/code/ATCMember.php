<?php
	
class ATCMember extends DataObject {

	static $db = array(
		'Username' => 'Text',
		'Name' => 'Text',
		'Email' => 'Text',
		'AltEmail' => 'Text',		
		'City' => 'Text',
		'Country' => 'Text',
	);
	

	function IsFoundationMember() {

		// Look to see if there's a foundation member using the first email address
		$FoundationMember = Member::get()->filter('Email',$this->Email)->first();

		// If not a match by the first address, look under the second address
		if(!$FoundationMember) {
			$FoundationMember = Member::get()->filter('Email',$this->AltEmail)->first();
		}

		return $FoundationMember;

	}

	function LoadCityCountry() {

		if($FoundationMember = $this->IsFoundationMember()) {
			$this->City = $FoundationMember->City;
			$this->Country = $FoundationMember->Country;
			$this->write();
		}

	}

}