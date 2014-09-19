<?php
/**
 * Defines the ArticlePage page type
 */
class ArticlePage extends Page {
   static $db = array(
    	'Date' => 'Date',
    	'Author' => 'Text'
	);
   static $has_one = array(
   );
   static $icon = "themes/tutorial/images/treeicons/news";
   
 	function getCMSFields() {
    	$fields = parent::getCMSFields();
    	
    	$datefield = new DateField('Date');
    	$datefield->setConfig('showcalendar', true);
    	$datefield->setConfig('showdropdown', true);

    	$fields->addFieldToTab('Root.Main', $datefield, 'Content');
    	$fields->addFieldToTab('Root.Main', new TextField('Author'), 'Content');

    	return $fields;
 	}   
}
 
class ArticlePage_Controller extends Page_Controller {
 
}