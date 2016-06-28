<?php
/**
 * Created by PhpStorm.
 * User: claudio
 * Date: 7/24/15
 * Time: 9:50 AM
 */

class SpeakersSubmissionsExportQuerySpecification implements IQuerySpecification {

    /**
     * @var array
     */
    private $selectedSummits;
    private $statusPrimary;
    private $statusAlternate;

    /**
     */
    public function __construct($selectedSummits, $statusPrimary, $statusAlternate){
        $this->selectedSummits = $selectedSummits;
        $this->statusPrimary = $statusPrimary;
        $this->statusAlternate = $statusAlternate;
    }
    /**
     * @return array
     */
    public function getSpecificationParams()
    {
        return array(
            "selectedSummits" => $this->selectedSummits,
            "statusPrimary" => $this->statusPrimary,
            "statusAlternate" => $this->statusAlternate,
        );
    }

}