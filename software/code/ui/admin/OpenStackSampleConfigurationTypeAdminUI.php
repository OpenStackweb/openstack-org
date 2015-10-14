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
class OpenStackSampleConfigurationTypeAdminUI extends DataExtension
{
    public function updateCMSFields(FieldList $fields)
    {

        $oldFields = $fields->toArray();
        foreach ($oldFields as $field) {
            $fields->remove($field);
        }

        $fields->push(new TextField("Type", "Type"));
        $fields->push(new CheckboxField("IsDefault", "Is Default?"));

        if($this->owner->ID > 0) {

            $config         = new GridFieldConfig_RelationEditor(100);
            $configurations = new GridField
            (
                "SampleConfigurations",
                "Sample Configurations",
                $this->owner->SampleConfigurations(),
                $config
            );
            $detailFormFields = new FieldList();
            $detailFormFields->push(new CheckBoxField(
                'IsDefault',
                'Is Default?'
            ));
            $config->getComponentByType('GridFieldDetailForm')->setFields($detailFormFields);
            $fields->push($configurations);

        }
        return $fields;
    }
}