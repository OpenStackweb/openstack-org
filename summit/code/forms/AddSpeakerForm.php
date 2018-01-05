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
        $max_moderators_reached = $this->presentation->maxModeratorsReached();
        $speaker_type           = (!$max_moderators_reached) ? 'Moderator' : 'Speaker';

        $fields = FieldList::create(
            LiteralField::create('SpeakerNote',
                '<p class="at-least-one">Each '.$presentation_type.' needs at least one speaker. You cannot submit your '.$presentation_type.' without a speaker. If you are speaking AND you are the '.$presentation_type.' owner, you still must add yourself as a speaker.</p>'),
            OptionsetField::create('SpeakerType', '', array(
                'Me'   => 'Add yourself as a '.$speaker_type.' to this '.$presentation_type,
                'Else' => 'Add someone else'
            ))->setValue('Me'),
            LiteralField::create('LegalMe', sprintf('
                <div id="legal-me" style="display: none;">
                 <label>
                    '.$speaker_type.'s agree that OpenStack Foundation may record and publish their talks presented during the %s OpenStack Summit. If you submit a proposal on behalf of a speaker, you represent to OpenStack Foundation that you have the authority to submit the proposal on the speaker’s behalf and agree to the recording and publication of their presentation.
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
                    '.$speaker_type.'s agree that OpenStack Foundation may record and publish their talks presented during the %s OpenStack Summit. If you submit a proposal on behalf of a speaker, you represent to OpenStack Foundation that you have the authority to submit the proposal on the speaker’s behalf and agree to the recording and publication of their presentation.
                </label>
                </div>', $this->summit->Title)
            )
        );

        if (Member::currentUser()->IsSpeaker($this->presentation)
            || $this->presentation->ModeratorID == Member::currentUser()->getSpeakerProfile()->ID) {
            $fields->replaceField('SpeakerType', HiddenField::create('SpeakerType', '', 'Else'));
            $fields->field('EmailAddress')
                ->setTitle('Enter the first name, last name or email address of your '.$speaker_type.' (*)')
                ->setDisplayLogicCriteria(null);
        }

        if ($this->presentation->Speakers()->exists() && $max_moderators_reached) {
            if (!$max_speakers_reached) {
                $fields->insertBefore(
                    LiteralField::create('MoreSpeakers', '<h3 class="more-speakers">Any more speakers to add?</h3>'),
                    'SpeakerNote'
                );
                $fields->removeField('SpeakerNote');
            } else {
                $fields->insertBefore(
                    LiteralField::create('LimitSpeakers', '<h3 class="limit-speakers">You have reached the maximum of speakers.</h3>'),
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
        $max_moderators_reached = $this->presentation->maxModeratorsReached();
        $speaker_type           = (!$max_moderators_reached) ? 'Moderator' : 'Speaker';
        $actions = array();

        if ($this->presentation->Speakers()->exists() && $max_moderators_reached) {
            if (!$max_speakers_reached) {
                $actions[] = FormAction::create('doAddSpeaker', '<i class="fa fa-plus fa-start"></i> Add another speaker');
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
