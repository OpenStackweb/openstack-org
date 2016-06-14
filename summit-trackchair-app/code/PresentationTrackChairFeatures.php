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

    public function addComment($commentBody, $MemberID, $is_category_change_suggestion = false)
    {
        $comment = new SummitPresentationComment();
        $comment->Body = $commentBody;
        $comment->CommenterID = $MemberID;
        $comment->PresentationID = $this->owner->ID;
        $comment->IsCategoryChangeSuggestion = $is_category_change_suggestion;
        $comment->write();

        return $comment;
    }

    /**
     * Used by the track chair app to allow chairs to add a presentation to a personal list.
     **/

    public function assignToIndividualList($maybe = false)
    {

        // Check permissions of user on talk
        if ($this->owner->CanAssign()) {
            $mySelections = SummitSelectedPresentationList::getMemberList($this->owner->CategoryID);


            // See if the presentation has already been assigned
            $alreadyAssigned = $mySelections->SummitSelectedPresentations()
            							    ->filter('PresentationID', $this->owner->ID)
            							    ->exists();

            if (!$alreadyAssigned) {
            	$category = $this->owner->Category();
            	$maybeThreshold = $category->SessionCount + $category->AlternateCount;
                // Find the higest order value assigned up to this point              
                $highestOrderInList = $mySelections
                    ->SummitSelectedPresentations()
                    ->max('Order');
                if($maybe) {
                	if($highestOrder > $maybeThreshold) {
                		$order = $highestOrderInList+1;
                	}
                	else {
                		$order = $maybeThreshold+1;
                	}
                }
                else {
                	$selectionList = $mySelections
                		->SummitSelectedPresentations()                		
                		->filter([
                			'Order:LessThan' => $maybeThreshold
                		]);

                	if($selectionList->count() >= $$maybeThreshold) {
                		throw new Exception("Cannot add any more presentations. Limit is $maybeThreshold. Currently at {$selectionList->count()}");
                	}
                	
                	$order = $selectionList->max('Order')+1;
                }

                $selectedPresentation = SummitSelectedPresentation::create();
                $selectedPresentation->SummitselectedPresentationListID = $mySelections->ID;
                $selectedPresentation->PresentationID = $this->owner->ID;
                $selectedPresentation->MemberID = Member::currentUser()->ID;                
                $selectedPresentation->Order = $order;
                $selectedPresentation->write();
            }
        }
    }

    /**
     * Used by the track chair app to allow chairs to remove a presentation from a personal list.
     **/

    public function removeFromIndividualList()
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

        if(!$selected->exists()) return false;

        $category = $this->owner->Category();

        return $selected->Order <= $category->SessionCount;
    }

    public function isAlternate()
    {
        $memID = Member::currentUserID();
		$selected = $this->getSelectedPresentation();

        if(!$selected->exists()) return false;

        $category = $this->owner->Category();

        return $selected->Order <= ($category->SessionCount + $category->AlternateCount);
    }

    public function isMaybe()
    {

        $memID = Member::currentUserID();
        $selected = $this->getSelectedPresentation();

        if(!$selected->exists()) return false;

        $category = $this->owner->Category();

        return $selected->Order > ($category->SessionCount + $category->AlternateCount);
    }

    public function getSelectionType()
    {
    	if($this->isSelected()) {
    		return 'selected';
    	}
    	if($this->isAlternate()) {
    		return 'alternate';
    	}
    	if($this->isMaybe()) {
    		return 'maybe';
    	}

    	return null;
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
            	'ListType' => 'Group'
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
        return SummitSelectedPresentation::get()
            ->filter(['PresentationID' => $this->owner->ID])
            ->exists();
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
                'ListType' => 'Group'
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
            ->leftJoin("SummitSelectedPresentationList",
                "SummitSelectedPresentationList.ID = SummitSelectedPresentation.SummitSelectedPresentationListID")
            ->filter([
            	'PresentationID' => $this->owner->ID,
            	'SummitSelectedPresentation.MemberID' => $memID,
            	'ListType' => 'Individual'
            ]);
    }


}