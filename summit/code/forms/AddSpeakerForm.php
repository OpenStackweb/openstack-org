<?php
/**
 * Class AddSpeakerForm
 */
final class AddSpeakerForm extends BootstrapForm
{
    /**
     * @var IPresentation
     */
    private $presentation;

    /**
     * @var ISummit
     */
    private $summit;

    /**
     * PAddSpeakerForm constructor.
     * @param Controller $controller
     * @param string $name
     * @param IPresentation $presentation
     * @param ISummit $summit
     */
    public function __construct($controller, $name, $presentation, $summit) {
        $this->presentation = $presentation;
        $this->summit = $summit;

        $this->setTemplate('AddSpeakerForm');
        JQueryUIDependencies::renderRequirements(JQueryUIDependencies::SmoothnessTheme);
        JQueryValidateDependencies::renderRequirements(true, false);
        Requirements::javascript('summit/javascript/AddSpeakerForm.js');
        Requirements::javascript('summit/javascript/presentation-form-save-actions.js');

        parent::__construct(
            $controller,
            $name,
            $this->getFormFields(),
            $this->getFormActions($controller),
            RequiredFields::create()
         );
    }

    protected function getFormFields() {
        $presentation_type = $this->presentation->getTypeName();
        $role              = $this->presentation->getNextSpeakerRoleToAdd();
        $min_qty           = $this->presentation->getMinQtyPerRole($role);
        $formatter         = new NumberFormatter("en", NumberFormatter::SPELLOUT);

        $fields = FieldList::create(
            LiteralField::create('SpeakerNote',
                '<p class="at-least-one">Each '.$presentation_type.' needs at least '.$formatter->format($min_qty).' '.$role.
                '. You cannot submit your '.$presentation_type.' without a '.$role.
                '. If you are speaking AND you are the '.$presentation_type.' owner, you still must add yourself as a '.$role.'.</p>'),
            OptionsetField::create('SpeakerType', '', array(
                'Me'   => 'Add yourself as a '.$role.' to this '.$presentation_type,
                'Else' => 'Add someone else'
            ))->setValue('Me'),
            LiteralField::create('LegalMe', sprintf('
                <div id="legal-me" style="display: none;">
                 <label>
                    '.ucfirst($role).'s agree that OpenStack Foundation may record and publish their talks presented '.
                    'during the %s Open Infrastructure Summit. If you submit a proposal on behalf of a '.$role.
                    ', you represent to OpenStack Foundation that you have the authority to submit the proposal on the '.
                    $role.'’s behalf and agree to the recording and publication of their presentation.
                </label>
                </div>', $this->summit->Title)),
            TextField::create('EmailAddress',
                "To add another person as a ".$role.", you will need their first name, last name or email address. (*)")
                ->displayIf('SpeakerType')
                ->isEqualTo('Else')
                ->end(),
            HiddenField::create('SpeakerId','SpeakerId'),
            HiddenField::create('MemberId','MemberId'),
            LiteralField::create('LegalOther', sprintf('
                <div id="legal-other" style="display: none;">
                 <label>
                    Speakers and moderators agree that OpenStack Foundation may record and publish their talks presented'.
                    'during the %s Open Infrastructure Summit. If you submit a proposal on behalf of a '.$role.
                    ', you represent to OpenStack Foundation that you have the authority to submit the proposal on the '.
                    $role.'’s behalf and agree to the recording and publication of their presentation.
                </label>
                </div>', $this->summit->Title)
            )
        );

        if (Member::currentUser()->isSpeakerOn($this->presentation)
            || $this->presentation->ModeratorID == Member::currentUser()->getSpeakerProfile()->ID) {
            $fields->replaceField('SpeakerType', HiddenField::create('SpeakerType', '', 'Else'));
            $fields->replaceField('EmailAddress', TextField::create
            (
                'EmailAddress',
                'Enter the first name, last name or email address of your '.$role.' (*)')
            );
        }

        $removeSpeakerNoteField  = false;
        $removeEmailAddressField = false;
        $removeSpeakerTypeField  = false;

        foreach ($this->presentation->getSpeakersAllowedRoles() as $role) {
            if(!$this->presentation->hasSpeakerInRole($role)) continue;
            if($this->presentation->maxSpeakerReachedPerRole($role)){
                $fields->insertBefore(
                    LiteralField::create
                    (
                        'LimitSpeakers'.$role,
                        '<h3 class="limit-speakers">You have reached the maximum of '.$role .'s.</h3>'
                    ),
                    'SpeakerNote'
                );
                $removeSpeakerNoteField  = true;
                $removeEmailAddressField = true;
                $removeSpeakerTypeField  = true;
            }
            else // can add more ...
            {
                $fields->insertBefore(
                    LiteralField::create('MoreSpeakers', '<h3 class="more-speakers">Any more '.$role.' to add?</h3>'),
                    'SpeakerNote'
                );
                $removeSpeakerNoteField  = true;
                $removeEmailAddressField = false;
                $removeSpeakerTypeField  = false;
            }
        }

        if($removeSpeakerNoteField)
            $fields->removeField('SpeakerNote');
        if($removeEmailAddressField)
            $fields->removeField('EmailAddress');
        if($removeSpeakerTypeField)
            $fields->removeField('SpeakerType');

        $fields->add($continue_field = new HiddenField('Continue','',1));
        $continue_field->addExtraClass('continue_field');

        return $fields;
    }

    protected function getFormActions($controller) {

        $current_role      = $this->presentation->getNextSpeakerRoleToAdd();
        $actions           = [];
        $is_role_mandatory = $this->presentation->isSpeakerRoleMandatory($current_role);
        $reached_minimun   = $this->presentation->minSpeakerReachedPerRole($current_role);
        $exists_speakers   = $this->presentation->existsSpeakersPerRole($current_role);

        if(empty($current_role))
            return new FieldList([
                $controller->createSaveActions('doFinishSpeaker', 3)
            ]);

        if($is_role_mandatory && !$reached_minimun){
            $action_text = 'Add '.(($current_role == IPresentationSpeaker::RoleSpeaker) ? 'first ' : 'a ').$current_role;
            $actions[] = $exists_speakers ?
                FormAction::create('doAdd'.ucfirst($current_role), '<i class="fa fa-plus fa-start"></i> Add another '.$current_role):
                FormAction::create('doAdd'.ucfirst($current_role), '<i class="fa fa-plus fa-start"></i> '.$action_text);
        }
        else{
            foreach ($this->presentation->getSpeakersAllowedRoles() as $role) {
                if(!$this->presentation->maxSpeakerReachedPerRole($role))
                    $actions[] = FormAction::create('doAdd'.ucfirst($role), '<i class="fa fa-plus fa-start"></i> Add another ' . $role);
            }

            $default_actions = $controller->createSaveActions('doFinishSpeaker', 3);
            $actions = array_merge($actions, $default_actions);
        }

        return new FieldList($actions);
    }

    public function forTemplate() {
        parent::forTemplate();

        $return = $this->renderWith(['AddSpeakerForm']);

        // Now that we're rendered, clear message
        $this->clearMessage();

        return $return;
    }

}
