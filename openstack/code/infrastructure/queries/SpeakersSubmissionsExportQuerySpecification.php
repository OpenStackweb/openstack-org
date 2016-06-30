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
    public function __construct($selectedSummits){
        $this->selectedSummits = $selectedSummits;
    }
    /**
     * @return array
     */
    public function getSpecificationParams()
    {
        return array(
            "selectedSummits" => $this->selectedSummits,
        );
    }

}