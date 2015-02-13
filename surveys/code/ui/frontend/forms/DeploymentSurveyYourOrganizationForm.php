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

class DeploymentSurveyYourOrganizationForm extends Form {

    function __construct($controller, $name){
        $org_field = null;
        $current_user = Member::currentUser();
        $current_affiliations = $current_user->getCurrentAffiliations();
        $org_field_name = 'Organization';
        if (!$current_affiliations)
            $org_field = new TextField('Organization', 'Your Organization Name');
        else {
            if (count($current_affiliations) > 1) {
                $source = array();
                foreach ($current_affiliations as $a) {
                    $org = $a->Organization();
                    $source[$org->ID] = $org->Name;
                }
                $source['0'] = "-- New One --";
                $org_field_name = 'OrgID';
                $ddl = new DropdownField('OrgID', 'Your Organization', $source);
                $ddl->setEmptyString('-- Select Your Organization --');
                $org_field = new FieldGroup();
                $org_field->push($ddl);
                $org_field->push($txt = new TextField('Organization', ''));
                $txt->addExtraClass('new-org-name');
            } else {
                $org_field = new TextField('Organization', 'Your Organization Name', $current_user->getOrgName());
            }
        }

        $fields = new FieldList (
            $org_field,
            new DropdownField(
                'Industry',
                'Your Organization’s Primary Industry',
                ArrayUtils::AlphaSort(DeploymentSurvey::$industry_options, array('' => '-- Please Select One --'), array('Other' => 'Other Industry (please specify)') )
            ),
            new TextareaField('OtherIndustry', 'Other Industry'),
            $org_it_activity = new TextareaField('ITActivity', 'Your Organization’s Primary IT Activity'),
            new LiteralField('Break', '<hr/>'),
            new LiteralField('Break', '<p>Your Organization’s Primary Location or Headquarters</p>'),
            $country = new DropdownField(
                'PrimaryCountry',
                'Country',
                CountryCodes::$iso_3166_countryCodes
                
            ),
            new TextField('PrimaryState', 'State / Province / Region'),
            new TextField('PrimaryCity', 'City'),
            new DropdownField(
                'OrgSize',
                'Your Organization Size (All Branches, Locations, Sites)',
                DeploymentSurvey::$organization_size_options
            ),
            new CustomCheckboxSetField('OpenStackInvolvement', 'What best describes your Organization’s involvement with OpenStack?<BR>Select All That Apply', ArrayUtils::AlphaSort(DeploymentSurvey::$openstack_involvement_options))
        );

        $org_it_activity->addExtraClass('hidden');
        $country->setEmptyString('-- Select One --');

        $nextButton = new FormAction('NextStep', '  Next Step  ');

        $actions = new FieldList(
            $nextButton
        );

        $validator = new RequiredFields();

        Requirements::javascript('surveys/js/deployment_survey_yourorganization_form.js');

        parent::__construct($controller, $name, $fields, $actions, $validator);
    }

    function forTemplate(){
        return $this->renderWith(array(
            $this->class,
            'Form'
        ));
    }
} 