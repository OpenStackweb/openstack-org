<?php

/**
 * Copyright 2014 Openstack Foundation
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
class AppDevSurvey extends DataObject
{

    static $db = array(
        // Section 2
        'Toolkits' => 'Text',
        'OtherToolkits' => 'Text',
        'ProgrammingLanguages' => 'Text',
        'OtherProgrammingLanguages' => 'Text',
        'APIFormats' => 'Text',
        'DevelopmentEnvironments' => 'Text',
        'OtherDevelopmentEnvironments' => 'Text',
        'OperatingSystems' => 'Text',
        'OtherOperatingSystems' => 'Text',
        'ConfigTools' => 'Text',
        'OtherConfigTools' => 'Text',
        'StateOfOpenStack' => 'Text',
        'DocsPriority' => 'Text',
        'InteractionWithOtherClouds' => 'Text',
        'OtherAPIFormats' => 'Text',
        'GuestOperatingSystems' => 'Text',
        'OtherGuestOperatingSystems' => 'Text',
        'StruggleDevelopmentDeploying' => 'Text',
        'OtherDocsPriority' => 'Text',
    );

    static $has_one = array(
        'DeploymentSurvey' => 'DeploymentSurvey',
        'Member' => 'Member'
    );

    static $singular_name = 'App Development Survey';
    static $plural_name = 'App Development Surveys';

    static $summary_fields = array(
        'Member.FirstName' => 'Member First Name',
        'Member.Surname' => 'Member Surname',
        'Member.Email' => 'Email',
    );

    static $searchable_fields = array(
        'Member.FirstName' => 'PartialMatchFilter',
        'Member.Surname' => 'PartialMatchFilter',
        'Member.Email' => 'PartialMatchFilter',
    );

    public function getCountry()
    {
        return $this->DeploymentSurvey()->PrimaryCountry;
    }

    public function getIndustry()
    {
        return $this->DeploymentSurvey()->Industry;
    }

    public function getMember()
    {
        return $this->DeploymentSurvey()->Member();
    }

    public function getOrg()
    {
        return $this->Org()->Name;
    }

    function getCMSFields()
    {
        $fields = new FieldList(
            $rootTab = new TabSet("Root")
        );

        $fields->addFieldsToTab('Root.Main', array(
            new LiteralField('Break', ColumnFormatter::$left_column_start),
            new CustomCheckboxSetField(
                'Toolkits',
                'What toolkits do you use or plan to use to interact with the OpenStack API?<BR>Select All That Apply',
                ArrayUtils::AlphaSort(AppDevSurveyOptions::$toolkits_options, null, array('Other' => 'Other Toolkits (please specify)'))),
            $t1 = new TextareaField('OtherToolkits', ''),

            new LiteralField('Break', ColumnFormatter::$right_column_start),
            new CustomCheckboxSetField('ProgrammingLanguages',
                'If you wrote your own code for interacting with the OpenStack API, what programming language did you write it in?',
                ArrayUtils::AlphaSort(AppDevSurveyOptions::$languages_options, null, array('Other' => 'Other (please specify)'))),
            $t2 = new TextareaField('OtherProgrammingLanguages', ''),

            new LiteralField('Break', ColumnFormatter::$end_columns),

            new LiteralField('Break', ColumnFormatter::$left_column_start),
            new CustomCheckboxSetField('APIFormats',
                'If you wrote your own code for interacting with the OpenStack API, what wire format are you using?<BR>Select All That Apply',
                ArrayUtils::AlphaSort(AppDevSurveyOptions::$api_format_options, null, array('Other' => 'Other Wire Format (please specify)'))),
            $t3 = new TextareaField('OtherAPIFormats', ''),
            new CustomCheckboxSetField(
                'OperatingSystems',
                'What operating systems are you using or plan on using to develop your applications?<BR>Select All That Apply',
                ArrayUtils::AlphaSort(AppDevSurveyOptions::$opsys_options, null, array('Other' => 'Other Development OS (please specify)'))),
            $t4 = new TextareaField('OtherOperatingSystems', ''),
            new CustomCheckboxSetField(
                'GuestOperatingSystems',
                'What guest operating systems are you using or plan on using to deploy your applications to customers?<BR>Select All That Apply', ArrayUtils::AlphaSort(AppDevSurveyOptions::$opsys_options, null, array('Other' => 'Other Development OS (please specify)'))),
            $t5 = new TextareaField('OtherGuestOperatingSystems', ''),
            new LiteralField('Break', '<hr/>'),
            new LiteralField('Break', '<p>Please share your thoughts with us on the state of applications on OpenStack</p>'),
            new TextAreaField('StruggleDevelopmentDeploying', 'What do you struggle with when developing or deploying applications on OpenStack?'),
            $docs = new DropdownField(
                'DocsPriority',
                'What is your top priority in evaluating API and SDK docs?', AppDevSurveyOptions::$docs_priority_options),
            $t6 = new TextareaField('OtherDocsPriority', '')
        ));

        $docs->setEmptyString('-- Select One --');

        return $fields;
    }

    public function copyFrom(AppDevSurvey $oldAppDev)
    {
        foreach (AppDevSurvey::$db as $field => $type) {
            $value = $oldAppDev->getField($field);
            $this->setField($field, $value);
        }

        foreach(AppDevSurvey::$db as $field => $type){
            if(in_array($field, AppDevSurveyMigrationOptions::$blank_fields)) continue;
            $new_value = '';
            if(array_key_exists($field, AppDevSurveyMigrationOptions::$migration_fields)){
                $new_value = $oldAppDev->getField($field);
                if(empty($new_value)) continue;
                $table     = AppDevSurveyMigrationOptions::$migration_fields[$field];
                $new_value = $oldAppDev->getField($field);
                foreach($table as $old => $new){
                    $new_value = str_replace( $old, $new, $new_value);
                }
            }
            else {
                $$new_value = $oldAppDev->getField($field);
            }
            $this->setField($field, $new_value);

        }

        $this->setField('DeploymentSurveyID', $oldAppDev->getField('DeploymentSurveyID'));
        $this->setField('MemberID', $oldAppDev->getField('MemberID'));
    }

    public function getSurveyType()
    {
        $start_date = new DateTime(SURVEY_START_DATE);
        $created = new DateTime($this->Created);
        if ($created >= $start_date)
            return SurveyType::MARCH_2015;
        else
            return SurveyType::OLD;
    }
}
