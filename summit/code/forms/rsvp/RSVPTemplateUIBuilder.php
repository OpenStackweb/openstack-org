<?php
/**
 * Copyright 2015 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class RSVPTemplateUIBuilder
 */
class RSVPTemplateUIBuilder implements IRSVPUIBuilder
{
    /**
     * @param IRSVPTemplate $template
     * @param IRSVP $rsvp
     * @param ISummitEvent $event_id
     * @param string $form_name
     * @return Form
     */
    public function build(IRSVPTemplate $template, IRSVP $rsvp, ISummitEvent $event, $form_name ='RSVPForm')
    {
        Requirements::javascript('summit/javascript/summitapp-rsvpform.js');

        $fields = new FieldList();

        foreach ($template->getQuestions() as $q) {

            $type          = $q->Type();
            $builder_class = $type.'UIBuilder';

            // @IRSVPQuestionTemplateUIBuilder
            $builder = Injector::inst()->create($builder_class);
            $answer = ($rsvp) ? $rsvp->findAnswerByQuestion($q) : null;
            $field   = $builder->build($rsvp, $q, $answer);
            $fields->add($field);
        }

        $validator = null;

        if ($rsvp)
        $fields->add(new HiddenField('rsvp_id', 'rsvp_id', $rsvp->getIdentifier()));
        $fields->add(new HiddenField('event_id', 'event_id', $event->getIdentifier()));
        $fields->add(new HiddenField('summit_id', 'summit_id', $event->Summit()->getIdentifier()));

        $fields->add(new LiteralField('hr','<hr>'));

        $actions   = new FieldList
        (
            FormAction::create('submit_rsvp')->setTitle('Send RSVP')->addExtraClass('rsvp_submit')
        );

        $form =  new BootstrapForm(Controller::curr(), $form_name, $fields, $actions, $validator);
        $form->setAttribute('class','rsvp_form');
        return $form;
    }
}