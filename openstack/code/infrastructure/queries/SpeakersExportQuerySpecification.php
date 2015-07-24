<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 7/24/15
 * Time: 9:50 AM
 */

class SpeakersExportQuerySpecification implements IQuerySpecification {

    /**
     * @var array
     */
    private $selectedSummits;
    private $onlyApprovedSpeakers;
    private $affiliation;

    /**
     * @param int $submmit_id
     */
    public function __construct($selectedSummits, $onlyApprovedSpeakers, $affiliation){
        $this->selectedSummits = $selectedSummits;
        $this->onlyApprovedSpeakers = $onlyApprovedSpeakers;
        $this->affiliation = $affiliation;
    }
    /**
     * @return array
     */
    public function getSpecificationParams()
    {
        return array(
            "selectedSummits" => $this->selectedSummits,
            "onlyApprovedSpeakers" => $this->onlyApprovedSpeakers,
            "affiliation" => $this->affiliation
        );
    }

}