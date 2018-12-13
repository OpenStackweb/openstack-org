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
        $presentation_type      = $this->presentation->getTypeName();
        $max_speakers_reached   = $this->presentation->maxSpeakersReached();
        $use_speakers           = $this->presentation->Type()->UseSpeakers;
        $speakers_exists        = $this->presentation->Speakers()->exists();
        $max_moderators_reached = $this->presentation->maxModeratorsReached();
        $use_moderator          = $this->presentation->Type()->UseModerator;
        $moderator_exists       = $this->presentation->Moderator()->exists();
        $speaker_type           = ($use_moderator && (!$max_moderators_reached || !$use_speakers)) ? 'moderator' : 'speaker';

        $fields = FieldList::create(
            LiteralField::create('SpeakerNote',
                '<p class="at-least-one">Each '.$presentation_type.' needs at least one '.$speaker_type.'. You cannot submit your '.$presentation_type.' without a '.$speaker_type.'. If you are speaking AND you are the '.$presentation_type.' owner, you still must add yourself as a '.$speaker_type.'.</p>'),
            OptionsetField::create('SpeakerType', '', array(
                'Me'   => 'Add yourself as a '.$speaker_type.' to this '.$presentation_type,
                'Else' => 'Add someone else'
            ))->setValue('Me'),
            LiteralField::create('LegalMe', sprintf('
                <div id="legal-me" style="display: none;">
                 <label>
                    '.ucfirst($speaker_type).'s agree that OpenStack Foundation may record and publish their talks presented during the %s Open Infrastructure Summit. If you submit a proposal on behalf of a '.$speaker_type.', you represent to OpenStack Foundation that you have the authority to submit the proposal on the '.$speaker_type.'’s behalf and agree to the recording and publication of their presentation.
                </label>
                </div>', $this->summit->Title)),
            TextField::create('EmailAddress',
                "To add another person as a ".$speaker_type.", you will need their first name, last name or email address. (*)")
                ->displayIf('SpeakerType')
                ->isEqualTo('Else')
                ->end(),
            HiddenField::create('SpeakerId','SpeakerId'),
            HiddenField::create('MemberId','MemberId'),
            LiteralField::create('LegalOther', sprintf('
                <div id="legal-other" style="display: none;">
                 <label>
                    Speakers and moderators agree that OpenStack Foundation may record and publish their talks presented during the %s Open Infrastructure Summit. If you submit a proposal on behalf of a '.$speaker_type.', you represent to OpenStack Foundation that you have the authority to submit the proposal on the '.$speaker_type.'’s behalf and agree to the recording and publication of their presentation.
                </label>
                </div>', $this->summit->Title)
            )
        );

        if (Member::currentUser()->isSpeakerOn($this->presentation)
            || $this->presentation->ModeratorID == Member::currentUser()->getSpeakerProfile()->ID) {
            $fields->replaceField('SpeakerType', HiddenField::create('SpeakerType', '', 'Else'));
            $fields->replaceField('EmailAddress', TextField::create('EmailAddress', 'Enter the first name, last name or email address of your '.$speaker_type.' (*)'));
        }

        if ( (!$use_speakers || $speakers_exists) && (!$use_moderator || $moderator_exists) ) {
            if ( ($use_speakers && !$max_speakers_reached) || ($use_moderator && !$max_moderators_reached) ) {
                $fields->insertBefore(
                    LiteralField::create('MoreSpeakers', '<h3 class="more-speakers">Any more '.$speaker_type.' to add?</h3>'),
                    'SpeakerNote'
                );
                $fields->removeField('SpeakerNote');
            } else {
                $fields->insertBefore(
                    LiteralField::create('LimitSpeakers', '<h3 class="limit-speakers">You have reached the maximum of '.$speaker_type.'s.</h3>'),
                    'SpeakerNote'
                );
                $fields->removeField('SpeakerNote');
                $fields->removeField('EmailAddress');
                $fields->removeField('SpeakerType');
            }
        }

        $fields->add($continue_field = new HiddenField('Continue','',1));
        $continue_field->addExtraClass('continue_field');

        return $fields;
    }

    protected function getFormActions($controller) {
        $max_speakers_reached   = $this->presentation->maxSpeakersReached();
        $use_speakers           = $this->presentation->Type()->UseSpeakers;
        $speakers_exists        = $this->presentation->Speakers()->exists();
        $max_moderators_reached = $this->presentation->maxModeratorsReached();
        $use_moderator          = $this->presentation->Type()->UseModerator;
        $moderator_exists       = $this->presentation->Moderator()->exists();
        $speaker_type           = ($use_moderator && (!$max_moderators_reached || !$use_speakers)) ? 'moderator' : 'speaker';
        $actions = array();

        if ( (!$use_speakers || $speakers_exists) && (!$use_moderator || $moderator_exists) ) {
            if (($use_speakers && !$max_speakers_reached) || ($use_moderator && !$max_moderators_reached)) {
                $actions[] = FormAction::create('doAddSpeaker', '<i class="fa fa-plus fa-start"></i> Add another '.$speaker_type);
            }

            $default_actions = $controller->createSaveActions('doFinishSpeaker', 3);
            $actions = array_merge($actions,$default_actions);

        } else {
            $action_text = 'Add '.(($speaker_type == 'speaker') ? 'first ' : 'a ').$speaker_type;
            $actions[] = FormAction::create('doAddSpeaker', '<i class="fa fa-plus fa-start"></i> '.$action_text);
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
