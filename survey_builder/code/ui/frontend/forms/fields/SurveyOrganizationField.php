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

class SurveyOrganizationField extends CompositeField {


    /**
     * @var ISingleValueTemplateQuestion
     */
    private $question;

    public function setRequired(){
        $this->required = true;
    }

    public function isRequired(){
        return  $this->required;
    }

    public function Question(){
        return $this->question;
    }

    public function __construct(ISingleValueTemplateQuestion $question, $value = null){

        $children = new FieldList();

        $this->question = $question;

        $current_user         = Member::currentUser();
        $current_affiliations = $current_user->getCurrentAffiliations();

        if (!$current_affiliations) {
            $children->add($txt = new TextField($question->name(), $question->label()));
            $txt->addExtraClass('input-organization-name');
        }
        else {
            if (intval($current_affiliations->count()) > 1) {
                $source = array();
                foreach ($current_affiliations as $a) {
                    $org = $a->Organization();
                    $source[$org->ID] = $org->Name;
                }
                $source['0'] = "-- New One --";
                $children->add( $ddl = new DropdownField($question->name() .'ID', $question->label(), $source));
                $ddl->setEmptyString('-- Select Your Organization --');
                $ddl->addExtraClass('select-organization-name');
                if(!is_null($value)){
                    $org = Org::get()->filter('Name', $value)->first();
                    if($org) $ddl->setValue($org->ID);
                }
                $children->add($txt = new TextField($question->name(), ''));
                $txt->addExtraClass('input-organization-name');
            } else {
                $children->add( $txt = new TextField($question->name(), $question->label(), $current_user->getOrgName()));
                $txt->addExtraClass('input-organization-name');
            }
        }

        parent::__construct($children);

        $control_css_class = strtolower($this->question->name().'-composite');
        $this->addExtraClass($control_css_class);

        Requirements::javascript('survey_builder/js/survey.organization.field.js');

        Requirements::customScript("
        jQuery(document).ready(function($) {
            $('.'+'{$control_css_class}').survey_organization_field();
        });");
    }
}