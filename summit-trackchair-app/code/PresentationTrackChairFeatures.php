<?php

class PresentationTrackChairFeatures extends DataExtension
{

    /**
     * @var array
     */
    private static $has_many = [
        'Comments' => 'SummitPresentationComment',
        'ChangeRequests' => 'SummitCategoryChange',
        'TrackChairViews' => 'PresentationTrackChairView'
    ];

    /**
     * Used by the track chair app to allow comments on presentations.
     * Comments are only displayed in the track chair interface.
     *
     * @return  SummitPresentationComment
     **/

    public function addComment($commentBody, $MemberID, $isActivity = false)
    {
        $comment = new SummitPresentationComment();
        $comment->Body = $commentBody;
        $comment->CommenterID = $MemberID;
        $comment->PresentationID = $this->owner->ID;
        $comment->IsActivity = $isActivity;
        $comment->write();

        return $comment;
    }

    /**
     * Used by the track chair app to allow chairs to add a presentation to a personal list.
     **/

    public function assignToIndividualList($collection)
    {

        // Check permissions of user on talk
        if (!$this->owner->CanAssign()) return;

    	$maybe = $collection === SummitSelectedPresentation::COLLECTION_MAYBE;
    	$pass = $collection === SummitSelectedPresentation::COLLECTION_PASS;
    	$selected = $collection === SummitSelectedPresentation::COLLECTION_SELECTED;

    	if(!$maybe && !$pass && !$selected && !$alternate) {
    		throw new InvalidArgumentException("assignToIndividualList() must take a collection argument of COLLECTION_MAYBE, COLLECTION_PASS, or COLLECTION_SELECTED per the SummitSelectedPresentation class definition");
    	}

        $mySelections = SummitSelectedPresentationList::getMemberList($this->owner->CategoryID);


        // See if the presentation has already been assigned
        $selectedPresentation = $mySelections->SummitSelectedPresentations()
        							    ->filter('PresentationID', $this->owner->ID)
        							    ->first();

        $category = $this->owner->Category();
        $highestSelection = ($category->SessionCount + $category->AlternateCount);
        $highestOrderInList = $mySelections
            ->SummitSelectedPresentations()
            ->filter('Collection', $collection)
            ->max('Order');
        
        if($selected && $highestOrderInList >= $highestSelection) {
        	throw new Exception("Selection list is full. Curerntly at $highestOrderInList. Limit is $highestSelection.");
        }

        if (!$selectedPresentation) {
            $selectedPresentation = SummitSelectedPresentation::create();
            $selectedPresentation->SummitSelectedPresentationListID = $mySelections->ID;
            $selectedPresentation->PresentationID = $this->owner->ID;
            $selectedPresentation->MemberID = Member::currentUser()->ID;
        }

	    $selectedPresentation->Collection = $collection;
	    $selectedPresentation->Order = $highestOrderInList+1;
	    $selectedPresentation->write();

    }

    /**
     * Used by the track chair app to allow chairs to remove a presentation from a personal list.
     **/

    public function removeFromIndividualList($pass = false)
    {
        // Check permissions of user on talk
        if ($this->owner->CanAssign()) {

            $mySelections = SummitSelectedPresentationList::getMemberList($this->owner->CategoryID);

            // See if the presentation has already been assigned
            $alreadyAssigned = $mySelections->SummitSelectedPresentations()
            							    ->filter('PresentationID', $this->owner->ID);            		

            if($alreadyAssigned->exists()) {
                $alreadyAssigned->delete();

            }
        }
    }


    /**
     * Used by the track chair app to allow chairs to add a presentation to a group list.
     **/

    public function assignToGroupList()
    {

        // Check permissions of user on talk
        if ($this->owner->canAssign()) {

            $groupList = SummitSelectedPresentationList::get()
                ->filter(array(
                    'CategoryID' => $this->owner->CategoryID,
                    'ListType' => 'Group'
                ))
                ->first();

            // See if the presentation has already been assigned
            $alreadyAssigned = $groupList->SummitSelectedPresentations()
            							 ->filter('PresentationID', $this->owner->ID)
            							 ->exists();


            if (!$alreadyAssigned) {

                // Find the higest order value assigned up to this point
                $highestOrderInList = $groupList
                    ->SummitSelectedPresentations()
                    ->sort('Order DESC')
                    ->first()
                    ->Order;

                $selectedPresentation = new SummitSelectedPresentation();
                $selectedPresentation->SummitSelectedPresentationListID = $groupList->ID;
                $selectedPresentation->PresentationID = $this->owner->ID;
                $selectedPresentation->MemberID = Member::currentUser()->ID;
                // Place at bottom of list
                $selectedPresentation->Order = $highestOrderInList + 1;
                $selectedPresentation->Collection = SummitSelectedPresentation::COLLECTION_SELECTED;
                $selectedPresentation->write();
            }
        }
    }

    /**
     * Used by the track chair app to allow chairs to remove a presentation from a group list.
     **/

    public function removeFromGroupList()
    {
        // Check permissions of user on talk
        if ($this->owner->CanAssign()) {

            $groupList = SummitSelectedPresentationList::get()
                ->filter(array(
                    'CategoryID' => $this->owner->CategoryID,
                    'ListType' => 'Group'
                ))
                ->first();


            // See if the presentation has already been assigned
            $alreadyAssigned = $groupList->SummitSelectedPresentations()
            							 ->filter('PresentationID', $this->owner->ID);

            if ($alreadyAssigned->exists()) {
                $alreadyAssigned->delete();
            }
        }
    }

    /**
     * Used by the track chair app see if the presentation has been selected by currently logged in member.
     **/

    public function isSelected()
    {
        $memID = Member::currentUserID();
        $selected = $this->getSelectedPresentation();

        if(!$selected) return false;

        return $selected->isSelected();
    }


    public function isMaybe()
    {
        $memID = Member::currentUserID();
        $selected = $this->getSelectedPresentation();

        if(!$selected) return false;

        return $selected->isMaybe();
    }

    
    public function isPass() 
    {
        $memID = Member::currentUserID();
        $selected = $this->getSelectedPresentation();

        if(!$selected) return false;

        return $selected->isPass();
    }

    public function getSelectionType()
    {
    	if($this->isSelected()) {
    		return SummitSelectedPresentation::COLLECTION_SELECTED;
    	}
    	if($this->isMaybe()) {
    		return SummitSelectedPresentation::COLLECTION_MAYBE;
    	}
    	if($this->isPass()) {    		
    		return SummitSelectedPresentation::COLLECTION_PASS;
    	}

    	return null;
    }

    public function getSelectors()
    {
    	return $this->getSelectingMembers()
    		->filter('Collection', SummitSelectedPresentation::COLLECTION_SELECTED);
    }
    

    public function getLikers()
    {
    	$category = $this->owner->Category();

    	return $this->getSelectingMembers()
			->filter('Collection', SummitSelectedPresentation::COLLECTION_MAYBE);
    }


    public function getPassers()
    {
    	$category = $this->owner->Category();

    	return $this->getSelectingMembers()
			->filter('Collection', SummitSelectedPresentation::COLLECTION_PASS);
    }


    public function getPopularityScore()
    {
    	$config = SummitSelectedPresentation::config();

    	return (
    		($this->getSelectors()->count() * $config->weight_select) +
    		($this->getLikers()->count() * $config->weight_maybe) +
    		($this->getPassers()->count() * $config->weight_pass)
    	);
    }

    /**
     * Used by the track chair app see if the presentation has been selected by the group.
     **/

    public function isGroupSelected()
    {
        $memID = Member::currentUserID();
        $selected = SummitSelectedPresentation::get()
            ->leftJoin("SummitSelectedPresentationList",
                "SummitSelectedPresentationList.ID = SummitSelectedPresentation.SummitSelectedPresentationListID")
            ->filter([
            	'PresentationID' => $this->owner->ID,            	
            	'ListType' => 'Group',
            	'Collection' => SummitSelectedPresentation::COLLECTION_SELECTED
            ]);


        return $selected->exists();
    }


    /**
     * Determines if a track chair can assign this presentation to a seleciton list
     *
     * @return boolean
     */

    public function canAssign()
    {
        // see if they have either of the appropiate permissions
        if (!Permission::check('TRACK_CHAIR')) {
            return false;
        }

        // see if they are a chair of this particular track
        return $this->owner->Category()->TrackChairs()->filter([
        	'MemberID' => Member::currentUserID()
        ])->exists();
    }


    /**
     * Used by the track chair app see if the presentation has been selected by anyone at all.
     * TODO: refactor to combine with isSelected() by passing optional memberID
     **/

    public function isSelectedByAnyone()
    {
        return $this->getSelectors()->exists();
    }

    /**
     * Used by the track chair app see if the presentation was moved to this category.
     **/

    public function movedToThisCategory()
    {
        $completedMove = $this->owner->ChangeRequests()->filter(array(
            'NewCategoryID' => $this->owner->CategoryID,
            'Done' => true
        ));
        if ($completedMove->count()) {
            return true;
        }
    }

    /**
     * @return string
     */
    public function SelectionStatus()
    {

        $selections = SummitSelectedPresentation::get()
            ->leftJoin('SummitSelectedPresentationList',
                'SummitSelectedPresentation.SummitSelectedPresentationListID = SummitSelectedPresentationList.ID')
            ->filter([
                'PresentationID' => $this->owner->ID,
                'ListType' => 'Group',
                'Collection' => SummitSelectedPresentation::COLLECTION_SELECTED
            ]);

        // Error out if a talk has more than one selection
        if ($selections->count() > 1) {
            user_error('There cannot be more than one instance of this talk selected. Talk ID ' . $this->owner->ID);
        }

        $selection = null;
        if ($selections->exists()) {
            $selection = $selections->first();
        }

        // Error out if the category of presentation does not match category of selection
        if ($selection && $this->owner->CategoryID != $selection->SummitSelectedPresentationList()->Category()->ID) {
            user_error('The selection category does not match the presentation category. Presentation ID ' . $this->owner->ID);
        }


        if (!$selection) {
            return IPresentation::SelectionStatus_Unaccepted;
        } 
        if ($selection->Order <= $this->owner->Category()->SessionCount) {
            return IPresentation::SelectionStatus_Accepted;
        } 

        return IPresentation::SelectionStatus_Alternate;
    }

    public function markAsViewedByTrackChair($member = null)
    {
    	if(!$member) {
    		$member = Member::currentUser();
    	}

    	$existing =  $this->owner->TrackChairViews()->filter([
    		'TrackChairID' => $member->ID
    	])->first();

    	if(!$existing) {
    		$this->owner->trackChairViews()->add(PresentationTrackChairView::create([
    			'TrackChairID' => $member->ID
    		]));
    	}
    }

    public function isViewedByTrackChair($member = null) 
    {
    	if(!$member) {
    		$member = Member::currentUser();
    	}

    	return $this->owner->TrackChairViews()->filter([
    		'TrackChairID' => $member->ID
    	])->exists();
    }

    protected function getSelectedPresentation()
    {
        $memID = Member::currentUserID();
        return SummitSelectedPresentation::get()
            ->filter([
            	'PresentationID' => $this->owner->ID,
            	'SummitSelectedPresentation.MemberID' => $memID,
            	'SummitSelectedPresentationList.ListType' => 'Individual'
            ])
            ->first();
    }

    protected function getSelectingMembers()
    {
    	return Member::get()
    		->innerJoin(
    			'SummitSelectedPresentation',
    			'ssp.MemberID = Member.ID',
    			'ssp'
    		)
    		->innerJoin(
    			'SummitSelectedPresentationList',
    			'sspl.ID = ssp.SummitSelectedPresentationListID',
    			'sspl'
    		)
    		->filter([
    			'PresentationID' => $this->owner->ID,    			
    			'ListType' => 'Individual'
    		]);
    }


}