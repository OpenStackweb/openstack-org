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
 * Class RSVPLiteralContentQuestionTemplate
 */
final class RSVPLiteralContentQuestionTemplate extends RSVPQuestionTemplate {

    static $db = array(
        'Content' => 'HTMLText',
    );

    private static $defaults = array(
        'ReadOnly'  => true,
        'Mandatory' => false,
    );

    public function Type(){
        return 'Literal';
    }

    protected function validate() {
        $valid = ValidationResult::create();
        if(!$valid->valid()) return $valid;

        if(empty($this->Name)){
            return $valid->error('Name is empty!');
        }

        $res = DB::query("SELECT COUNT(Q.ID) FROM RSVPQuestionTemplate Q
                          INNER JOIN `RSVPTemplate` T ON T.ID = Q.RSVPTemplateID
                          WHERE Q.Name = '{$this->Name}' AND Q.ID <> {$this->ID} AND Q.RSVPTemplateID = $this->RSVPTemplateID")->value();

        if (intval($res) > 0) {
            return $valid->error('There is already another Question on the rsvp form with that name!');
        }

        return $valid;
    }

    public function getCMSFields() {
        $fields = new FieldList();
        $fields->add(new TextField('Name','Name (Without Spaces)'));
        $fields->add(new HtmlEditorField('Content', 'Content'));
        return $fields;
    }
}