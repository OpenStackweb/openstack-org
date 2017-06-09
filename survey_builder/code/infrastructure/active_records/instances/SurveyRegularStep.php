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
 * Class SurveyRegularStep
 */
class SurveyRegularStep
    extends SurveyStep
    implements ISurveyRegularStep
{

    static $has_many = [
        'Answers' => 'SurveyAnswer',
    ];

    /**
     * @return ISurveyAnswer[]
     */
    public function getAnswers()
    {
        return AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Answers')->toArray();
    }

    /**
     * @param ISurveyAnswer $new_answer
     * @return void
     */
    public function addAnswer(ISurveyAnswer $new_answer)
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Answers')->add($new_answer);
    }

    /**
     * @param int $answer_template_id
     * @return ISurveyAnswer
     */
    public function getAnswerByTemplateId($answer_template_id)
    {
        foreach ($this->getAnswers() as $a) {
            if($a->question()->getIdentifier() === $answer_template_id)
                return $a;
        }
        return null;
    }

    /**
     * @param string $name
     * @return ISurveyAnswer
     */
    public function getAnswerByName($name)
    {
        foreach ($this->getAnswers() as $a) {
            if($a->question()->name() === $name)
                return $a;
        }
        return null;
    }

    /**
     * @return void
     */
    public function clearAnswers()
    {
        AssociationFactory::getInstance()->getOne2ManyAssociation($this, 'Answers')->removeAll();
    }

    /**
     * @return void
     */
    public function clear()
    {
        $this->clearAnswers();
    }

    protected function onBeforeDelete() {
        parent::onBeforeDelete();
        foreach($this->Answers() as $answer){
            $answer->delete();
        }
    }

    /**
     * @return array
     */
    public function getCurrentAnswersSnapshotState()
    {
        $snapshot = [];
        foreach ($this->Answers() as $answer) {
            $snapshot[$answer->Question()->Name] = $answer->Value;
        }
        return $snapshot;
    }
}