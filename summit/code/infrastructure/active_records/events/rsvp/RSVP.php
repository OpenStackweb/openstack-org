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
 * Class RSVP
 */
class RSVP extends DataObject implements IRSVP
{

    static $db = array
    (
        'BeenEmailed' => 'Boolean',
    );

    static $indexes = array();

    static $has_one = array
    (
        'SubmittedBy'  => 'Member',
        'Event'        => 'SummitEvent',
    );

    static $many_many = array();

    static $has_many = array(
        'Answers' => 'RSVPAnswer',
    );

    private static $defaults = array(
        'BeenEmailed' => false,
    );


    private static $searchable_fields = array();

    private static $summary_fields = array
    (
        'ID'                      => 'ID',
        'Created'                 => 'Created',
        'SubmittedBy.Email'       => 'SubmittedBy',
        'Event.RSVPTemplate.Name' => 'Name',
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    /**
     * @return IRSVPAnswer[]
     */
    public function getAnswers()
    {
        $query = new QueryObject(new RSVPAnswer);
        $query->addAlias(QueryAlias::create('Answer'));

        return new ArrayList(AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Answers',
            $query)->toArray());
    }

    /**
     * @param IRSVPAnswer $new_answer
     * @return void
     */
    public function addAnswer(IRSVPAnswer $new_answer)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Answers')->add($new_answer);
    }
    /**
     * @return IRSVPTemplate
     */
    public function template()
    {
        return $this->Event()->RSVPTemplate();
    }

    /**
     * @return ICommunityMember
     */
    public function submittedBy()
    {
        return AssociationFactory::getInstance()->getMany2OneAssociation($this, 'SubmittedBy')->getTarget();
    }

    /**
     * @return bool
     */
    public function isEmailSent()
    {
        return $this->getField('BeenEmailed');
    }

    /**
     * @param IMessageSenderService $service
     * @throws EntityValidationException
     */
    public function sentEmail(IMessageSenderService $service)
    {
        if ($this->BeenEmailed) {
            throw new EntityValidationException(array(array('message' => 'RSVP Email Already sent !')));
        }

        $this->BeenEmailed = true;
        $service->send($this);
    }

    /**
     * @param IRSVPQuestionTemplate $question
     * @return IRSVPAnswer
     */
    public function findAnswerByQuestion(IRSVPQuestionTemplate $question)
    {
        foreach ($this->getAnswers() as $answer) {
            if($answer->question()->getIdentifier() === $question->getIdentifier()) {
                if (!is_null($answer)) {
                    return $answer;
                }
            }
        }

        return null;
    }

    protected function onBeforeDelete()
    {
        parent::onBeforeDelete();
        foreach($this->Answers() as $answer){
            $answer->delete();
        }
    }

    public function getCMSFields()
    {
        $fields = new FieldList(
            $rootTab = new TabSet("Root", $tabMain = new Tab('Main'))
        );

        //$fields->addFieldsToTab('Root.Main', new HiddenField('TemplateID', 'TemplateID'));
        //$fields->addFieldsToTab('Root.Main', new ReadonlyField('SubmitteddByEmail','SubmittedBy', $this->SubmittedBy()->Email));

        return $fields;
    }
}