<?php

class PresentationVotingFeatures extends DataExtension 
{

    /**
     * @var array
     */
    private static $has_many = [
        'Votes' => 'PresentationVote'
    ];

    /**
     * Sets a vote for this presentation by the current user
     *
     * @param  $vote int
     */
    public function setUserVote($vote)
    {
        $v = $this->owner->Votes()->filter('MemberID', Member::currentUserID())->first() ?: PresentationVote::create();
        $v->MemberID = Member::currentUserID();
        $v->PresentationID = $this->owner->ID;
        $v->Vote = $vote;
        $v->write();
    }



    /**
     * Gets the vote on this presentation by the current user
     * @return int
     */
    public function getUserVote()
    {
        return $this->owner->Votes()->filter(array(
            'MemberID' => Member::currentUserID()
        ))->first();
    }


    /**
     * @return string
     */
    public function CalcTotalPoints()
    {
        $sqlQuery = new SQLQuery(
            "SUM(Vote)",
            "PresentationVote",
            "PresentationID = " . $this->owner->ID
        );
        return $sqlQuery->execute()->value();
    }

    /**
     * @return string
     */
    public function CalcVoteCount()
    {
        $sqlQuery = new SQLQuery(
            "COUNT(ID)",
            "PresentationVote",
            "PresentationID = " . $this->owner->ID
        );
        return $sqlQuery->execute()->value();
    }

    /**
     * @return float
     */
    public function CalcVoteAverage()
    {
        $sqlQuery = new SQLQuery(
            "AVG(Vote)",
            "PresentationVote",
            "PresentationID = " . $this->owner->ID
        );
        return round($sqlQuery->execute()->value(), 2);
    }

}