<?php
/**
 * Created by JetBrains PhpStorm.
 * User: smarcet
 * Date: 10/2/13
 * Time: 1:00 PM
 * To change this template use File | Settings | File Templates.
 */

class Affiliation extends DataObject {
    static $db = array(
        'StartDate' => 'Date',
        'EndDate' => 'Date',
        'JobTitle'=>'Text',
        'Role'=>'Text',
        'Current'=>'Boolean'
    );

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator= new RequiredFields(array('StartDate'));
        return $validator;
    }

    static $has_one = array(
        'Member' => 'Member',
        'Organization'=>'Org',
    );

    public function getDuration(){
        $end = $this->Current==true?'(Current)':"To {$this->EndDate}";
        return "From {$this->StartDate} {$end}";
    }
}