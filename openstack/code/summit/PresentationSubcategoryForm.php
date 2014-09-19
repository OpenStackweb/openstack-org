<?php

class PresentationSubcategoryForm extends Form {
 
   function __construct($controller, $name) {
   

		$SubcategoryField = new TextField('Subcategory', 'Subcategories');
   
		$fields = new FieldList(
		     $SubcategoryField
		);

      $saveButton = new FormAction('doSubcategory', 'Save');
      $saveButton->addExtraClass('btn btn-default btn-sm');
	 
       $actions = new FieldList(
          $saveButton
       );
   
      parent::__construct($controller, $name, $fields, $actions);
   }
 
   function forTemplate() {
      return $this->renderWith(array(
         $this->class,
         'Form'
      ));
   }  
  
}