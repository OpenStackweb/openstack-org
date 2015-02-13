<?php

/**
 * Adds functionality to Member to support presentations and summit features
 *
 * @author  Uncle Cheese <unclecheese@leftandmain.com>
 */
class PresentationMemberExtension extends DataExtension
{

    private static $db = array (
        'PresentationList' => 'Text',        
    );


    private static $has_one = array (
        'ActiveSummit' => 'Summit'
    );

    private static $has_many = array (
        'Presentations' => 'Presentation',
        'PresentationPriorities' => 'PresentationPriority',
       // 'SummitStates' => 'SpeakerSummitState'
    );


    /**
     * Gets presentations, ordered in a persistent random fashion
     *         
     * @return DataList
     */
    public function getRandomisedPresentations($categoryid = null) {
        $mid = Member::currentUserID();
        $currentSummit = Summit::CurrentSummit();
        if($this->owner->PresentationPriorities()->count() != $currentSummit->Talks()->count()) {            
            DB::query("DELETE FROM PresentationPriority WHERE MemberID = {$mid}");
            $list = Summit::CurrentSummit()
                        ->Talks()
                        ->sort("RAND()")
                        ->column('ID');
            
            foreach($list as $priority => $id) {
                PresentationPriority::create(array(
                    'TalkID' => $id,
                    'MemberID' => $this->owner->ID,
                    'Priority' => $priority
                ))->write();
            }            
        }

        // Set the filter to limit to a category if one is provided
        $filter = array();
        
        if ($categoryid) {
            $filter = array(
                            'MarkedToDelete' => FALSE,
                            'PresentationPriority.MemberID' => $mid,
                            'SummitCategoryID' => $categoryid
                        );
        } else {
            $filter = array(
                'MarkedToDelete' => FALSE,
                'PresentationPriority.MemberID' => $mid
            );
        }

        return Talk::get()
                ->innerJoin("PresentationPriority", "PresentationPriority.TalkID = Talk.ID")
                ->filter($filter)
                ->sort('Priority ASC');
    }

    /**
     * Gets presentations that this user has voted on
     * @return DataList 
     */
    public function getVotedPresentations() {
        return PresentationVote::get()->filter('MemberID',Member::currentUserID());
    }


    /**
     * Removes a presentation from the user's random list
     * @param  int $id The presentation ID     
     */
    public function removePresentation($id) {
        if(!$this->owner->PresentationList) return;

        $ids = Convert::json2array($this->owner->PresentationList);
        unset($ids[$id]);

        $this->owner->PresentationList = Convert::array2json($ids);
        $this->owner->write();
    }

}