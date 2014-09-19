<?php
  /**
   * Defines the JobPage page type
   */
  class JSONMember extends DataObject {
     static $db = array(
      	'FirstName' => 'Text',
      	'Surname' => 'Text',
      	'IRCHandle' => 'Text',
        'TwitterName' => 'Text',
        'Email' => 'Text',
        'SecondEmail' => 'Text',
        'ThirdEmail' => 'Text',
        'OrgAffiliations' => 'Text',
        'untilDate' => 'Date'
  	);
    
    static $has_one = array(
     );

    Static $defaults = array(
      'untilDate' => NULL
    ); 

    public function canView($member = null) { 
        return true; 
    }

}