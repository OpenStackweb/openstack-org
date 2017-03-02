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
    public function addComment($commentBody, $MemberID)
    {
        $comment = new SummitPresentationComment();
        $comment->Body = $commentBody;
        $comment->CommenterID = $MemberID;
        $comment->PresentationID = $this->owner->ID;
        $comment->IsActivity = false;
        $comment->write();
        return $comment;
    }
    public function addNotification($text)
    {
        $text = str_replace(
            [
                '{member}',
                '{presentation}'
            ],
            [
                Member::currentUser()->getName(),
                $this->owner->Title
            ],
            $text
        );
        $comment = SummitPresentationComment::create([
            'Body' => $text,
            'CommenterID' => Member::currentUserID(),
            'PresentationID' => $this->owner->ID,
            'IsActivity' => true
        ]);
        $comment->write();
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
        if(!$maybe && !$pass && !$selected) {
            throw new InvalidArgumentException("assignToIndividualList() must take a collection argument of COLLECTION_MAYBE, COLLECTION_PASS, or COLLECTION_SELECTED per the SummitSelectedPresentation class definition");
        }

        // tricky part here, if presentation is lightning talk or lightning wannabe we have to add it to Lightning List
        $lists_to_add_to = array();
        if ($this->owner->Type()->Type == IPresentationType::LightingTalks) {
            $lists_to_add_to[] = SummitSelectedPresentationList::Lightning;
        } else {
            $lists_to_add_to[] = SummitSelectedPresentationList::Session;
            if ($this->owner->LightningTalk) {
                $lists_to_add_to[] = SummitSelectedPresentationList::Lightning;
            }
        }

        // error message
        $msg = '';

        foreach ($lists_to_add_to as $list_class) {
            $mySelections = SummitSelectedPresentationList::getMemberList($this->owner->CategoryID, $list_class);
            // See if the presentation has already been assigned
            $selectedPresentation = $mySelections->SummitSelectedPresentations()
                ->filter('PresentationID', $this->owner->ID)
                ->first();

            $highestSelection = $mySelections->maxPresentations();

            $highestOrderInList = $mySelections
                ->SummitSelectedPresentations()
                ->filter('Collection', $collection)
                ->max('Order');

            if($selected && $highestOrderInList >= $highestSelection) {
                $list_class_name = SummitSelectedPresentationList::getListClassName($list_class);
                $msg = "$list_class_name Selection list is full. Currently at $highestOrderInList. Limit is $highestSelection.";
                //check if there is space for selected in the other list
                if ($this->owner->isLightningWannabe()) {
                    $other_list_class = ($list_class == SummitSelectedPresentationList::Lightning) ? SummitSelectedPresentationList::Session : SummitSelectedPresentationList::Lightning;
                    $myOtherSelections = SummitSelectedPresentationList::getMemberList($this->owner->CategoryID, $other_list_class);
                    $highestOtherSelection = $myOtherSelections->maxPresentations();
                    $highestOtherOrderInList = $myOtherSelections
                        ->SummitSelectedPresentations()
                        ->filter('Collection', $collection)
                        ->max('Order');
                    // lightning wannabes first we add session and then lightning, so it depends
                    if ($list_class == SummitSelectedPresentationList::Lightning) {
                        $should_add_declaimer = $highestOtherOrderInList == $highestOtherSelection;
                    } else {
                        $should_add_declaimer = $highestOtherOrderInList < $highestOtherSelection;
                    }
                    if($should_add_declaimer) {
                        $other_list_class_name = SummitSelectedPresentationList::getListClassName($other_list_class);
                        $msg .= "However it will be added to {$other_list_class_name} Selection.";
                    }
                }

                // will not add this presentation, list is full
                continue;
            }

            if (!$selectedPresentation) {
                $selectedPresentation = SummitSelectedPresentation::create();
                $selectedPresentation->SummitSelectedPresentationListID = $mySelections->ID;
                $selectedPresentation->PresentationID = $this->owner->ID;
                $selectedPresentation->MemberID = Member::currentUser()->ID;
            }

            $previous_collection = $selectedPresentation->Collection;
            $selectedPresentation->Collection = $collection;
            $selectedPresentation->Order = $highestOrderInList+1;
            $selectedPresentation->write();
            // reorder list from where it was removed
            if ($previous_collection != SummitSelectedPresentation::COLLECTION_PASS) {
                $left_selections = $mySelections->SummitSelectedPresentations()
                    ->filter('Collection', $previous_collection);
                foreach ($left_selections as $order => $selection) {
                    $selection->Order = $order+1;
                    $selection->write();
                }
            }
        }

        if ($msg) {
            throw new EntityValidationException($msg);
        }
    }
    /**
     * Used by the track chair app to allow chairs to remove a presentation from a personal list.
     **/
    public function removeFromIndividualList($pass = false)
    {
        // Check permissions of user on talk
        if ($this->owner->CanAssign()) {
            // tricky part here, if presentation is lightning talk or lightning wannabe we have to add it to Lightning List
            $lists_to_add_to = array();
            if ($this->owner->Type()->Type == IPresentationType::LightingTalks) {
                $lists_to_add_to[] = SummitSelectedPresentationList::Lightning;
            } else {
                $lists_to_add_to[] = SummitSelectedPresentationList::Session;
                if ($this->owner->LightningTalk) {
                    $lists_to_add_to[] = SummitSelectedPresentationList::Lightning;
                }
            }
            foreach ($lists_to_add_to as $list_class) {
                $mySelections = SummitSelectedPresentationList::getMemberList($this->owner->CategoryID, $list_class);
                // See if the presentation has already been assigned
                $alreadyAssigned = $mySelections->SummitSelectedPresentations()
                    ->filter('PresentationID', $this->owner->ID);
                if($alreadyAssigned->exists()) {
                    $alreadyAssigned->delete();
                }
            }
        }
    }
    /**
     * Used by the track chair app to allow chairs to add a presentation to a group list.
     **/
    public function assignToGroupList($type = SummitSelectedPresentationList::Group)
    {
        // Check permissions of user on talk
        if ($this->owner->canAssign()) {
            $groupList = SummitSelectedPresentationList::get()
                ->filter(array(
                    'CategoryID' => $this->owner->CategoryID,
                    'ListType' => $type
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
    public function removeFromGroupList($type = SummitSelectedPresentationList::Group)
    {
        // Check permissions of user on talk
        if ($this->owner->CanAssign()) {
            $groupList = SummitSelectedPresentationList::get()
                ->filter(array(
                    'CategoryID' => $this->owner->CategoryID,
                    'ListType' => $type
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
    public function isSelected($list_class = SummitSelectedPresentationList::Session)
    {
        $memID = Member::currentUserID();
        $selected = $this->getSelectedPresentation($list_class);
        if(!$selected) return false;
        return $selected->isSelected();
    }
    public function isMaybe($list_class = SummitSelectedPresentationList::Session)
    {
        $memID = Member::currentUserID();
        $selected = $this->getSelectedPresentation($list_class);
        if(!$selected) return false;
        return $selected->isMaybe();
    }
    
    public function isPass($list_class = SummitSelectedPresentationList::Session)
    {
        $memID = Member::currentUserID();
        $selected = $this->getSelectedPresentation($list_class);
        if(!$selected) return false;
        return $selected->isPass();
    }
    public function getSelectionType($list_class = SummitSelectedPresentationList::Session)
    {
        $list_class = ($this->owner->isOfType(IPresentationType::LightingTalks)) ? SummitSelectedPresentationList::Lightning : $list_class;
        if($this->isSelected($list_class)) {
            return SummitSelectedPresentation::COLLECTION_SELECTED;
        }
        if($this->isMaybe($list_class)) {
            return SummitSelectedPresentation::COLLECTION_MAYBE;
        }
        if($this->isPass($list_class)) {
            return SummitSelectedPresentation::COLLECTION_PASS;
        }
        return false;
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
    public function isGroupSelected($list_class = SummitSelectedPresentationList::Session)
    {
        $memID = Member::currentUserID();
        $selected = SummitSelectedPresentation::get()
            ->leftJoin("SummitSelectedPresentationList",
                "SummitSelectedPresentationList.ID = SummitSelectedPresentation.SummitSelectedPresentationListID")
            ->filter([
                'PresentationID' => $this->owner->ID,               
                'ListClass' => $list_class,
                'ListType' => SummitSelectedPresentationList::Group,
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
                'Collection' => SummitSelectedPresentation::COLLECTION_SELECTED,
                'ListType' => SummitSelectedPresentationList::Group,
            ]);
        $session_sel = $selections->filter('ListClass',SummitSelectedPresentationList::Session);
        $lightning_sel = $selections->filter('ListClass',SummitSelectedPresentationList::Lightning);
        // Error out if a talk has more than one selection
        if ($session_sel->count() > 1) {
            $selectionsList = [];
            foreach($session_sel as $s) {
                $l = $s->SummitSelectedPresentationList();
                $selectionsList[] = "List: {$l->ListName} ID: {$l->ID} Category: ({$l->Category()->Title})";                
            }
            SS_Log::log(
                'There is more than one instance of a talk selected. Talk ID ' . $this->owner->ID . ' appears in: ' . implode(', ', $selectionsList),
                SS_Log::WARN
            );
        }
        if ($lightning_sel->count() > 1) {
            $selectionsList = [];
            foreach($lightning_sel as $s) {
                $l = $s->SummitSelectedPresentationList();
                $selectionsList[] = "List: {$l->ListName} ID: {$l->ID} Category: ({$l->Category()->Title})";
            }
            SS_Log::log(
                'There is more than one instance of a talk selected. Talk ID ' . $this->owner->ID . ' appears in: ' . implode(', ', $selectionsList),
                SS_Log::WARN
            );
        }
        $selection = null;
        if ($session_sel->exists()) {
            $selection = $session_sel->first();
        } else if ($lightning_sel->exists()) {
            $selection = $lightning_sel->first();
        }
        // Error out if the category of presentation does not match category of selection
        if ($selection && $this->owner->CategoryID != $selection->SummitSelectedPresentationList()->Category()->ID) {
            SS_Log::log(
                "The selection category does not match the presentation category. 
                Presentation {$this->owner->Title} is in list {$selection->SummitSelectedPresentationList()->Title} for {$selection->SummitSelectedPresentationList()->Category()->Title},
                but the presentation itself is in {$this->owner->Category()->Title}",
                SS_Log::WARN
            );
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
    protected function getSelectedPresentation($list_class = SummitSelectedPresentationList::Session)
    {
        $memID = Member::currentUserID();
        return SummitSelectedPresentation::get()
            ->filter([
                'PresentationID' => $this->owner->ID,
                'SummitSelectedPresentation.MemberID' => $memID,
                'SummitSelectedPresentationList.ListClass' => $list_class,
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
                'sspl.ListType' => SummitSelectedPresentationList::Individual,
            ]);
    }
}