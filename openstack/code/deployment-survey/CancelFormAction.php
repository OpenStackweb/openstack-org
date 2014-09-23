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
	
	function Field($properties = array()) {

		$properties = array_merge(
			$properties,
			array(
				'id' => $this->id(),
				'name' => $this->action,
				'class' => 'action cancel roundedButton ' . ($this->extraClass() ? $this->extraClass() : ''),
				'name' => $this->action,
				'href' => $this->getLink()
			)
		);

		if($this->isReadonly()) {
			$properties['disabled'] = 'disabled';
			$properties['class'] = $properties['class'] . ' disabled';
		}

		return FormField::create_tag('a', $properties, 	$this->buttonContent ? $this->buttonContent : $this->Title());

	}
}