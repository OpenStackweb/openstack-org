<?php


class SummitHomePage extends SummitPage {

	private static $db = array (
		'IntroText' => 'Varchar(255)'
	);


	private static $hide_ancestor = 'SummitPage';


	public function getCMSFields() {
		$f = parent::getCMSFields();
		return $f
			->text('IntroText')
		;
	}
    
}


class SummitHomePage_Controller extends SummitPage_Controller {


}