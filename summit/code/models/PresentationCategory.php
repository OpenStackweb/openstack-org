<?php


class PresentationCategory extends DataObject
{

	private static $db = array (
		'Title' => 'Varchar',
        'Description' => 'Text',
		'SessionCount' => 'Int',
		'AlternateCount' => 'Int',
		'VotingVisible' => 'Boolean',
		'ChairVisible' => 'Boolean'		
	);

	private static $defaults = array(
		'VotingVisible' => TRUE,
		'ChairVisible' => TRUE
	);	

	private static $has_one = array (
		'Summit' => 'Summit'
	);

	private static $has_many = array (
		'ChangeRequests' => 'SummitCategoryChange'
	);

    private static $belongs_many_many = array (
    	'TrackChairs' => 'SummitTrackChair',
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
			->numeric('AlternateCount','Number of alternates')
			->checkbox('VotingVisible',"This category is visible to voters")
			->checkbox('ChairVisible',"This category is visible to track chairs");			
	}
    
    public function getFormattedTitleAndDescription() {
        return '<h4 class="category-label">' . $this->Title . '</h4> <p>' . $this->Description . '</p>';
    }
}
