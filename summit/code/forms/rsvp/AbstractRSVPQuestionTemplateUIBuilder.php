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
 * Class AbstractRSVPQuestionTemplateUIBuilder
 */
abstract class AbstractRSVPQuestionTemplateUIBuilder
    implements IRSVPQuestionTemplateUIBuilder {

    /**
     * @param IRSVPQuestionTemplate $question
     * @param FormField $field
     * @param IRSVP|null $rsvp
     * @return FormField
     */
    protected function buildDependantRules(IRSVPQuestionTemplate $question, FormField $field, ?IRSVP $rsvp){
        //depends : check visibility
        $depends = $question->getDependsOn();

        foreach ($depends as $d) {
            switch ($d->ClassName) {
                case 'RSVPCheckBoxQuestionTemplate':
                    $field->displayIf($d->name())->isChecked();
                    break;
                case 'RSVPCheckBoxListQuestionTemplate':
                    $field->displayIf($d->name())->hasCheckedOption($d->ValueID);
                    break;
                default:
                    $field->displayIf($d->name())->isEqualTo($d->ValueID);
                    break;
            }
        }

        return $field;
    }
}
