<?php

class CandidatePage extends Page
{

	private static $db = array();

	private static $has_one = array();

	private static $has_many = array(
		'Candidates' => 'Candidate'
	);

	function getCMSFields()
	{
		$fields = parent::getCMSFields();
		$candidateTable = new GridField('Candidates', 'Candidates', $this->Candidates());
		$fields->addFieldToTab('Root.Candidates', $candidateTable);
		return $fields;
	}

}

class CandidatePage_Controller extends Page_Controller
{

	public static $allowed_actions = array(
		'emails' => 'ADMIN'
	);

	function init()
	{
		parent::init();
	}

	function Candidates()
	{
		return Candidate::get()->filter(array('HasAcceptedNomination' => TRUE))->sort('LastName ASC');
	}
}