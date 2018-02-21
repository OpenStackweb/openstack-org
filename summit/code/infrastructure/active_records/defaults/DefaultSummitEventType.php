<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class DefaultSummitEventType
 */
class DefaultSummitEventType extends DataObject
{
    use Colorable;

    private static $db = array
    (
        'Type'                 => 'Text',
        'Color'                => 'Text',
        'BlackoutTimes'        => 'Boolean',
        'UseSponsors'          => 'Boolean',
        'AreSponsorsMandatory' => 'Boolean',
        'AllowsAttachment'     => 'Boolean',
        'IsPrivate'            => 'Boolean',
    );

    private static $has_many = [];

    private static $defaults = [
        'UseSponsors'          => 0,
        'AreSponsorsMandatory' => 0,
        'AllowsAttachment'     => 0,
        'BlackoutTimes'        => 0,
        'IsPrivate'            => 0,
        'Color'                => 'f0f0ee',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Type'                    => 'Type',
        'Color'                   => 'Color',
        'ClassName'               => 'Class Name',
    ];

    public function getCMSFields() {
        $fields = new FieldList();
        $fields->add($type_txt = new TextField('Type','Type'));
        $fields->add(new ColorField("Color","Color"));
        $fields->add(new CheckboxField("BlackoutTimes","Blackout Times"));
        $fields->add(new CheckboxField("UseSponsors","Should use Sponsors?"));
        $fields->add(new CheckboxField("AreSponsorsMandatory","Are Sponsors Mandatory?"));
        $fields->add(new CheckboxField("AllowsAttachment","Allows Attachment?"));
        return $fields;
    }

    /**
     * @return SummitEventType
     */
    protected function buildType(){
        return new SummitEventType();
    }

    /**
     * @param int $summit_id
     * @return SummitEventType
     */
    public function buildEventType($summit_id){
        $event_type                       = $this->buildType();
        $event_type->SummitID             = $summit_id;
        $event_type->IsDefault            = 1;
        $event_type->Type                 = $this->Type;
        $event_type->Color                = $this->Color;
        $event_type->BlackoutTimes        = $this->BlackoutTimes;
        $event_type->UseSponsors          = $this->UseSponsors;
        $event_type->AreSponsorsMandatory = $this->AreSponsorsMandatory;
        $event_type->AllowsAttachment     = $this->AllowsAttachment;
        $event_type->IsPrivate            = $this->IsPrivate;
        return $event_type;
    }
}