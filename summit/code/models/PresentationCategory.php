<?php


class PresentationCategory extends DataObject
{

	private static $db = array (
		'Title' => 'Varchar',
        'Description' => 'Text',
		'SessionCount' => 'Int',
		'AlternateCount' => 'Int'
	);


	private static $has_one = array (
		'Summit' => 'Summit',
		'TrackChair' => 'Member'
	);


	private static $summary_fields = array (
		'Title' => 'Title'
	);


	private static $searchable_fields = array (
		'Title'
	);

	public function getCMSFields() {
		return FieldList::create(TabSet::create('Root'))
			->text('Title')
            ->textarea('Description')
			->numeric('SessionCount','Number of sessions')
			->numeric('AlternateCount','Number of alternates');
	}
    
    public function getFormattedTitleAndDescription() {
        return '<h4 class="category-label">' . $this->Title . '</h4> <p>' . $this->Description . '</p>';
    }
}