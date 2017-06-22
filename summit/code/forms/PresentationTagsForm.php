<?php

/**
 * Class PresentationTagsForm
 */
final class PresentationTagsForm extends BootstrapForm
{
    /**
     * @var IPresentation
     */
    private $presentation;

    /**
     * PresentationTagsForm constructor.
     * @param Controller $controller
     * @param string $name
     * @param FieldList $actions
     * @param IPresentation $presentation
     */
    public function __construct($controller, $name, $actions, $presentation) {
        $this->presentation = $presentation;

        $this->setTemplate('PresentationTagsForm');
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::javascript('summit/javascript/presentation-form-save-actions.js');

        parent::__construct(
            $controller,
            $name,
            $this->getFormFields(),
            $actions
         );
    }

    protected function getFormFields() {
        $fields    = FieldList::create();
        $tag_field = new TagManagerField('Tags', 'Tags');
        $tag_field->setCategory($this->presentation->Category());
        $fields->add($tag_field);
        $fields->add($continue_field = new HiddenField('Continue','',1));
        $continue_field->addExtraClass('continue_field');

        return $fields;
    }

    public function forTemplate() {
        parent::forTemplate();

        $return = $this->renderWith(['PresentationTagsForm']);

        // Now that we're rendered, clear message
        $this->clearMessage();

        return $return;
    }

}
