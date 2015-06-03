<?php


class Summit extends DataObject
{

	private static $db = array (
		'Title' => 'Varchar',

		'SummitBeginDate' => 'Date',
		'SummitEndDate' => 'Date',
		'SubmissionBeginDate' => 'Date',
		'SubmissionEndDate' => 'Date',
		'VotingBeginDate' => 'Date',
		'VotingEndDate' => 'Date',
		'SelectionBeginDate' => 'Date',
		'SelectionEndDate' => 'Date',
		'DateLabel' => 'Varchar',
        'Link' => 'Varchar',
        'RegistrationLink' => 'Text',

		'Active' => 'Boolean',
	);


	private static $has_many = array (
		'Presentations' => 'Presentation',
		'Categories' => 'PresentationCategory',
	);


	private static $summary_fields = array (
		'Title' => 'Title',
		'Status' => 'Status',
	);


	private static $searchable_fields = array (

	);


	public static function get_active() {
		$summit = Summit::get()->filter(array(
			'Active' => true
		))->first();

		return $summit ?: Summit::create();
	}


	public function getCMSFields() {
		$f = FieldList::create(TabSet::create('Root'));

		return $f
			->text('Title')
			->text('Link','Summit Page Link')
				->configure()
					->setDescription('The link to the site page for this summit. Eg: <em>/summit/vancouver-2015/</em>')
				->end()                        
			->checkbox('Active','This is the active summit')
			->text('DateLabel','Date label')
				->configure()
					->setDescription('A readable piece of text representing the date, e.g. <em>May 12-20, 2015</em> or <em>December 2016</em>')
				->end()
			->text('RegistrationLink','Registration Link')
				->configure()
					->setDescription('Link to the site where tickets can be purchased.')
				->end()                 
			->date('SummitBeginDate')
				->configure()->setConfig('showcalendar', true)->end()
			->date('SummitEndDate')
				->configure()->setConfig('showcalendar', true)->end()
			->date('SubmissionBeginDate')
				->configure()->setConfig('showcalendar', true)->end()
			->date('SubmissionEndDate')
				->configure()->setConfig('showcalendar', true)->end()
			->date('VotingBeginDate')
				->configure()->setConfig('showcalendar', true)->end()
			->date('VotingEndDate')
				->configure()->setConfig('showcalendar', true)->end()
			->date('SelectionBeginDate')
				->configure()->setConfig('showcalendar', true)->end()
			->date('SelectionEndDate')
				->configure()->setConfig('showcalendar', true)->end()
			->tab('Categories')
				->hasManyGrid('Categories','Categories', $this->Categories())			
		;

	}


	public function checkRange($key) {
		$beginField = "{$key}BeginDate";
		$endField = "{$key}EndDate";

		if(!$this->hasField($beginField) || !$this->hasField($endField)) return false;

		return (time() > $this->obj($beginField)->format('U')) && (time() < $this->obj($endField)->format('U'));
	}


	public function getStatus() {
		if(!$this->Active) return "INACTIVE";

		if($this->checkRange("Submission")) return "ACCEPTING SUBMISSIONS";
		if($this->checkRange("Voting")) return "COMMUNITY VOTING";
		if($this->checkRange("Selection")) return "TRACK CHAIR SELECTION";
		if($this->checkRange("Summit")) return "SUMMIT IS ON";

		return "DRAFT";
	}


	public function onAfterWrite() {
		parent::onAfterWrite();

		if($this->Active) {
			foreach(Presentation::get()->exclude('ID', $this->ID) as $p) {
				$p->Active = false;
				$p->write();
			}
		}
	}
}