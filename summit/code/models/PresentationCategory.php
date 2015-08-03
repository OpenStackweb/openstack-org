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

    public function isTrackChair($memberid) {
    	$r = $this->TrackChairs()->filter('MemberID', $memberid);
    	return $r->count();
    }

    public function MemberList($memberid) {

          // See if there's a list for the current member
          $MemberList = SummitSelectedPresentationList::get()->filter(array(
              'MemberID' => $memberid,
              'CategoryID' => $this->ID
          ))->first();

          // if a selection list doesn't exist for this member and category, create it
          if (!$MemberList && $this->isTrackChair($memberid)) {
              $MemberList = new SummitSelectedPresentationList();
              $MemberList->ListType = 'Individual';
              $MemberList->CategoryID = $this->ID;
              $MemberList->MemberID = $memberid;
              $MemberList->write();
          }

          if($MemberList->exists()) return $MemberList;


    }

    public function GroupList() {

    	  // See if there's a list for the group
          $GroupList = SummitSelectedPresentationList::get()->filter(array(
              'ListType' => 'Group',
              'CategoryID' => $this->ID
          ));

          // if a group selection list doesn't exist for this category, create it
          if (!$GroupList->exists()) {
              $GroupList = new SummitSelectedPresentationList();
              $GroupList->ListType = 'Group';
              $GroupList->CategoryID = $this->ID;
              $GroupList->write();
          }

          return $GroupList->first();

    }


}
