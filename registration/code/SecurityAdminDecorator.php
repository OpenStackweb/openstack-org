<?php

/**
 * Class GridFieldBecomeMemberAction
 */
final class GridFieldBecomeMemberAction implements GridField_ColumnProvider, GridField_ActionProvider {

	public function augmentColumns($gridField, &$columns) {
		if(!in_array('Actions', $columns)) {
			$columns[] = 'Actions';
		}
	}

	public function getColumnAttributes($gridField, $record, $columnName) {
		return array('class' => 'col-buttons');
	}

	public function getColumnMetadata($gridField, $columnName) {
		if($columnName == 'Actions') {
			return array('title' => '');
		}
	}

	public function getColumnsHandled($gridField) {
		return array('Actions');
	}

	public function getColumnContent($gridField, $record, $columnName) {
		if(!$record->canEdit()) return;
		$member = $gridField->getList()->byID($record->ID);
		$allowed = !$member->isFoundationMember();

		$title = $allowed ? "Make this user a Foundation Member":"Foundation Member";
		$icon  = $allowed ? 'chain--exclamation':'chain-unchain';

		$field = GridField_FormAction::create($gridField,  'becomefoundationmember'.$record->ID, false, "becomefoundationmember",
			array('RecordID' => $record->ID))
			->setAttribute('title',$title)
			->setAttribute('data-icon',$icon)
			->setDescription($title);
		$field->setDisabled(!$allowed);

		return $field->Field();
	}

	public function getActions($gridField) {
		return array('becomefoundationmember');
	}

	public function handleAction(GridField $gridField, $actionName, $arguments, $data) {
		if($actionName == 'becomefoundationmember') {
			$member = $gridField->getList()->byID($arguments['RecordID']);
			$allowed = !$member->isFoundationMember();
			$msg  = 'This user is already a Foundation Member!';
			if($allowed){
				$member->upgradeToFoundationMember();
				$msg = 'User is now a Foundation Member';
			}
			Controller::curr()->getResponse()->setStatusCode(200,$msg);
		}
	}
}

/**
 * Class SecurityAdminDecorator
 */
final class SecurityAdminDecorator extends Extension {

    public function updateEditForm(Form &$form){
	    $root_tab = $form->Fields()->fieldByName('Root');
	    $tabs = $root_tab->Tabs();
	    $users_tab = $tabs->fieldByName('Users');
	    $members =  $users_tab->Fields()->FieldByName('Members');
	    if(!is_null($members))
	        $members->getConfig()->addComponent(new GridFieldBecomeMemberAction());
	    return $form;
    }
}
