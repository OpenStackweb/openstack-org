<?php
 
/**
 * Action that takes the user back to a given link rather than submitting
 * the form.
 *
 * @package cancelformaction
 */
class CancelFormAction extends FormAction {

	/**
	 * @var string
	 */
	private $link;
	
	function __construct($link = "", $title = "", $form = null, $extraData = null, $extraClass = 'roundedButton') {
		if(!$title) $title = _t('CancelFormAction.CANCEL', 'Cancel');
		
		$this->setLink($link);
	
		parent::__construct('CancelFormAction', $title, $form, $extraData, $extraClass);
	}
	
	function setLink($link) {
		$this->link = $link;
	}
	
	function getLink() {
		return $this->link;
	}
	
	function Field() {
		$attributes = array(
			'class' => 'action cancel ' . ($this->extraClass() ? $this->extraClass() : ''),
			'id' => $this->id(),
			'name' => $this->action,
			'tabindex' => $this->getTabIndex(),
			'href' => $this->getLink()
		);
		
		if($this->isReadonly()) {
			$attributes['disabled'] = 'disabled';
			$attributes['class'] = $attributes['class'] . ' disabled';
		}
		
		return $this->createTag(
			'a', 
			$attributes, 
			$this->buttonContent ? $this->buttonContent : $this->Title()
		);
	}
}