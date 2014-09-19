<?php

/**
 * Class MarketPlaceTypeAdminUI
 */
class MarketPlaceTypeAdminUI extends DataExtension {

	/**
	 * @param FieldList $fields
	 * @return FieldList|void
	 */
	public function updateCMSFields(FieldList $fields){

		$oldFields = $fields->toArray();
		foreach($oldFields as $field){
			$fields->remove($field);
		}
		$fields->push(new LiteralField("Title","<h2>Marketplace Type</h2>"));
		$fields->push(new TextField("Name","Name"));
		$fields->push(new CheckboxField("Active","Active"));

		if($this->owner->ID>0){
			$slug_field = new TextField('Slug','Slug');
			$slug_field->setReadonly(true);
			$slug_field->setDisabled(true);
			$slug_field->performReadonlyTransformation();
			$fields->push($slug_field);
			$group_field = new TextField('Group','Group',$this->owner->AdminGroup()->Title);
			$group_field->setReadonly(true);
			$group_field->setDisabled(true);
			$group_field->performReadonlyTransformation();
			$fields->push($group_field);
		}
		return $fields;
	}

	public function onBeforeWrite(){
		//create group here?
		parent::onBeforeWrite();
	}
} 