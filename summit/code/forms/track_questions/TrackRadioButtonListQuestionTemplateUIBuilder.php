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
 * Class TrackRadioButtonListQuestionTemplateUIBuilder
 */
final class TrackRadioButtonListQuestionTemplateUIBuilder extends AbstractTrackQuestionTemplateUIBuilder {

    /**
     * @param ITrackQuestionTemplate $question
     * @param ITrackAnswer $answer
     * @return FormField
     */
    public function build(ITrackQuestionTemplate $question, ITrackAnswer $answer)
    {
        $options = array();
        foreach($question->Values()->sort('Order') as $val)
        {
            $options[$val->ID] = empty($val->Label)?$val->Value:$val->Label;
        }

        $field         = new OptionsetField($question->name(), $question->label(), $options);
        $field->setFieldHolderTemplate('BootstrapFieldHolder');

        $default_value = $question->getDefaultValue();
        if(!is_null($default_value) && $default_value->ID > 0){
            $field->setValue($default_value->ID);
        }
        if($question->isReadOnly()) $field->setDisabled(true);
        if($question->isMandatory())
        {
            $field->setAttribute('data-rule-required','true');
        }
        if(!is_null($answer)){
            $field->setValue($answer->value());
        }

        $field->setTemplate('RSVPOptionSetField');

        return $field;
    }
}