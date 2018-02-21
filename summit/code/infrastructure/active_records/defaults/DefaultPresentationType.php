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
 * Class DefaultPresentationType
 */
final class DefaultPresentationType extends DefaultSummitEventType
{
    private static $db = [

        'MaxSpeakers'            => 'Int',
        'MinSpeakers'            => 'Int',
        'MaxModerators'          => 'Int',
        'MinModerators'          => 'Int',
        'UseSpeakers'            => 'Boolean',
        'AreSpeakersMandatory'   => 'Boolean',
        'UseModerator'           => 'Boolean',
        'IsModeratorMandatory'   => 'Boolean',
        'ModeratorLabel'         => 'VarChar(255)',
        'ShouldBeAvailableOnCFP' => 'Boolean',
    ];

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('AllowsAttachment');

        $fields->add(new CheckboxField("ShouldBeAvailableOnCFP","Should be available on CFP ?"));

        $fields->add(new CheckboxField("UseSpeakers","Should use Speakers?"));
        $fields->add(new CheckboxField("AreSpeakersMandatory","Are Speakers Mandatory?"));
        $fields->add(new TextField("MinSpeakers","Min Speakers"));
        $fields->add(new TextField("MaxSpeakers","Max Speakers"));

        $fields->add(new CheckboxField("UseModerator","Should use Moderator?"));
        $fields->add(new CheckboxField("IsModeratorMandatory","Is Moderator Mandatory?"));
        $fields->add(new TextField('ModeratorLabel', 'Moderator Label'));
        $fields->add(new TextField("MinModerators","Min Moderators"));
        $fields->add(new TextField("MaxModerators","Max Moderators"));

        return $fields;
    }

    /**
     * @return PresentationType
     */
    protected function buildType(){
        return new PresentationType();
    }

    /**
     * @param int $summit_id
     * @return SummitEventType
     */
    public function buildEventType($summit_id){
        $event_type                         = parent::buildEventType($summit_id);
        $event_type->MaxSpeakers            = $this->MaxSpeakers;
        $event_type->MinSpeakers            = $this->MinSpeakers;
        $event_type->MaxModerators          = $this->MaxModerators;
        $event_type->MinModerators          = $this->MinModerators;
        $event_type->UseSpeakers            = $this->UseSpeakers;
        $event_type->AreSpeakersMandatory   = $this->AreSpeakersMandatory;
        $event_type->UseModerator           = $this->UseModerator;
        $event_type->IsModeratorMandatory   = $this->IsModeratorMandatory;
        $event_type->IsModeratorMandatory   = $this->IsModeratorMandatory;
        $event_type->ModeratorLabel         = $this->ModeratorLabel;
        $event_type->ShouldBeAvailableOnCFP = $this->ShouldBeAvailableOnCFP;

        return $event_type;
    }

}