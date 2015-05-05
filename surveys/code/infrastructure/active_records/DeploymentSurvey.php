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
class DeploymentSurvey extends DataObject
{

	static $db = array(
		'Title' => 'Text',
		'Industry' => 'Text',
		'OtherIndustry' => 'Text',
		'PrimaryCity' => 'Text',
		'PrimaryState' => 'Text',
		'PrimaryCountry' => 'Text',
		'OrgSize' => 'Text',
		'OpenStackInvolvement' => 'Text',
		'InformationSources' => 'Text',
		'OtherInformationSources' => 'Text',
		'FurtherEnhancement' => 'Text',
		'FoundationUserCommitteePriorities' => 'Text',
		'BusinessDrivers' => 'Text',
		'OtherBusinessDrivers' => 'Text',
		'WhatDoYouLikeMost' => 'Text',
		'UserGroupMember' => 'Boolean',
		'UserGroupName' => 'Text',
		'CurrentStep' => 'Text',
		'HighestStepAllowed' => 'Text',
		'BeenEmailed' => 'Boolean',
		'OkToContact' => 'Boolean',
		// New Deployment Survey Daily Digest
		'SendDigest' => 'Boolean', // SendDigest = 1 SENT, SendDigest = 0 to be send
		'UpdateDate' => 'SS_Datetime',
		'FirstName' => 'Text',
		'Surname' => 'Text',
		'Email' => 'Text',
		'OpenStackRecommendRate' => 'Text',
		'OpenStackRecommendation' => 'Text',
        'OpenStackActivity' => 'Text',
        'OpenStackRelationship' => 'Text',
        'ITActivity' => 'Text',
		'InterestedUsingContainerTechnology' => 'Boolean',
		'ContainerRelatedTechnologies' => 'Text',
    );

	static $has_one = array(
		'Member' => 'Member',
		'Org' => 'Org'
	);

	static $has_many = array(
		'Deployments' => 'Deployment',
		'AppDevSurveys' => 'AppDevSurvey'
	);

	static $summary_fields = array(
		'Created' => 'Created Date',
		'Member.FirstName' => 'Member First Name',
		'Member.Surname' => 'Member Surname',
		'Member.Email' => 'Email',
		'Org.Name' => 'Organization'
	);

	static $searchable_fields = array(
		'Created' => 'ExactMatchFilter',
		'Org.Name' => 'PartialMatchFilter',
		'Member.FirstName'=> 'PartialMatchFilter',
		'Member.Surname'=> 'PartialMatchFilter',
		'Member.Email'=> 'PartialMatchFilter',
	);

	static $defaults = array(
		"CurrentStep" => 'AboutYou',
		'HighestStepAllowed' => 'AboutYou',
		'OkToContact' => 'True'
	);

	static $singular_name = 'Deployment Survey';
	static $plural_name = 'Deployment Surveys';

	public static $steps = array(
		'Login', 'AboutYou','YourOrganization', 'YourThoughts',  'AppDevSurvey', 'Deployments', 'DeploymentDetails', 'MoreDeploymentDetails', 'ThankYou'
	);


	protected function onBeforeWrite()
	{
		parent::onBeforeWrite();
		$this->UpdateDate = SS_Datetime::now()->Rfc2822();
	}

	function hasAppDevSurveys(){
		return $this->AppDevSurveys()->count() > 0;
	}

	function getCMSFields()
	{
		$fields = new FieldList();
		$fields->push(new TabSet("Root"));

		$CountryCodes = CountryCodes::$iso_3166_countryCodes;

		$fields->addFieldsToTab('Root.About You',
			array(
				$first_name_field 		= new ReadonlyField('FirstName', 'First name / Given name'),
				$last_name_field  		= new ReadonlyField('Surname', 'Last name / Family name'),
				$os_activity            = new CustomCheckboxSetField('OpenStackActivity', 'Which of the following do you yourself personally do?<BR>Select All That Apply', DeploymentSurveyOptions::$activities_options),
				$os_relationship        = new TextAreaField('OpenStackRelationship', 'Please describe your relationship with OpenStack'),
				$email_field            = new ReadonlyField('Member.Email', 'Your Email', $this->Member()->Email),
				$ok_2_contact           = new CheckboxField('OkToContact', 'The OpenStack Foundation and User Committee may communicate with me in the future about my usage.')
			));

		$fields->addFieldsToTab('Root.YourOrganization', array(
			new ReadonlyField('Org.Name', 'Organization', $this->Org()->Name),
			new DropdownField(
				'Industry',
				'Your Organization’s Primary Industry',
				ArrayUtils::AlphaSort(DeploymentSurveyOptions::$industry_options, array('' => '-- Please Select One --'), array('Other' => 'Other Industry (please specify)') )
			),
			new TextareaField('OtherIndustry', 'Other Industry'),
			$org_it_activity = new TextField('ITActivity', 'Your Organization’s Primary IT Activity'),
			new LiteralField('Break', '<p>Your Organization’s Primary Location or Headquarters</p>'),
			$ddl_country = new DropdownField(
				'PrimaryCountry',
				'Country',
				$CountryCodes
			),
			new TextField('PrimaryState', 'State / Province / Region'),
			new TextField('PrimaryCity', 'City'),
			new DropdownField(
				'OrgSize',
				'Your Organization Size (All Branches, Locations, Sites)',
                DeploymentSurveyOptions::$organization_size_options
			),
			new CheckboxSetField('OpenStackInvolvement', 'What best describes your Organization’s involvement with OpenStack?', ArrayUtils::AlphaSort(DeploymentSurveyOptions::$openstack_involvement_options))
		));

		$ddl_country->setEmptyString('-- Select One --');

		$fields->addFieldsToTab('Root.Your Thoughts', array(
			new CustomCheckboxSetField(
				'BusinessDrivers',
				'What are your top business drivers for using OpenStack?<BR>Please rank up to 5.<BR>1 = top business driver, 2 = next, 3 = third, and so on<BR>Select At Least One',
				ArrayUtils::AlphaSort(DeploymentSurveyOptions::$business_drivers_options,null, array('Other' => 'Something else not listed here (please specify)'))),
			new TextAreaField('OtherBusinessDrivers', ''),
			new CustomCheckboxSetField('InformationSources', 'Where do end up finding information about using OpenStack, after using search engines and talking to your colleagues?<BR>Select All That Apply', ArrayUtils::AlphaSort(DeploymentSurveyOptions::$information_options, null, array('Other' => 'Other Sources (please specify)'))),
			new TextAreaField('OtherInformationSources', ''),
			$ddl_rate = new DropdownField(
				'OpenStackRecommendRate',
				'How likely are you to recommend OpenStack to a friend or colleague? (0=Least Likely, 10=Most Likely)',
                DeploymentSurveyOptions::$openstack_recommendation_rate_options),
			new LiteralField('Break', '<hr/>'),
			new LiteralField('Break', '<p>We would love to hear how OpenStack and the OpenStack Foundation can better meet your needs. These free-form questions are optional, but will provide valuable insights.</p>'),
			new LiteralField('Break', '<p>Your responses are anonymous, and each of these text fields is independent, so we cannot “See previous answer”. We would really appreciate a separate answer to each question.</p>'),
			new TextAreaField('WhatDoYouLikeMost', 'What do you like most about OpenStack, besides “free” and “open”?'),
			new TextAreaField('FurtherEnhancement', 'What areas of OpenStack require further enhancement? '),
			new TextAreaField('FoundationUserCommitteePriorities', 'What should be the priorities for the Foundation and User Committee during the coming year?'),
			new CheckboxField('InterestedUsingContainerTechnology','Are you interested in using container technology with OpenStack?'),
			new CustomCheckboxSetField('ContainerRelatedTechnologies','Which of the following container related technologies are you interested in using?<BR>Please select all that apply', DeploymentSurveyOptions::$container_related_technologies)
		));

		$ddl_rate->setEmptyString('Neutral');

		$app_config = new GridFieldConfig_RecordEditor();

		$apps = new GridField("AppDevSurveys", "AppDevSurveys", $this->AppDevSurveys(), $app_config);
		$fields->addFieldsToTab('Root.Application Development', array(
			$apps
		));

		$deployments_config = new GridFieldConfig_RecordEditor();

		$deployments = new GridField("Deployments", "Deployments", $this->Deployments(), $deployments_config);

		$fields->addFieldsToTab('Root.Deployments', array(
			$deployments
		));

		$first_name_field->setReadonly(true);
		$last_name_field->setReadonly(true);
		$email_field->setReadonly(true);


		return $fields;
		//return parent::getCMSFields();
	}

	public function DisplayOrg()
	{
		return Member::currentUser()->getOrgName();
	}

	/**
	 * @param int $batch_size
	 * @return mixed
	 */
	public static function getNotDigestSent($batch_size)
	{
		return DeploymentSurvey::get()->filter(array('SendDigest' => 0))->where("\"Title\" IS NULL ")->sort('Created')->limit($batch_size);
	}


	public function copyFrom(DeploymentSurvey $oldSurvey){
		// copy properties

        foreach(DeploymentSurvey::$db as $field => $type){

            if(in_array($field, DeploymentSurveyMigrationOptions::$blank_fields)) continue;
            $new_value = '';
            if(array_key_exists($field, DeploymentSurveyMigrationOptions::$migration_fields)){
                $new_value = $oldSurvey->getField($field);
                if(empty($new_value)) continue;
                $table     = DeploymentSurveyMigrationOptions::$migration_fields[$field];
                foreach($table as $old => $new){
                    $new_value = str_replace( $old, $new, $new_value);
                }
            }
            else {
                $new_value = $oldSurvey->getField($field);
            }
            $this->setField($field, $new_value);

        }

		$this->setField('OrgID',$oldSurvey->getField('OrgID'));
		$this->setField('MemberID',$oldSurvey->getField('MemberID'));

		foreach($oldSurvey->Deployments() as $oldDeployment){
			$newDeployment = new Deployment();
			$newDeployment->copyFrom($oldDeployment);
			$newDeployment->write();
			$this->Deployments()->add($newDeployment);
		}

		foreach($oldSurvey->AppDevSurveys() as $oldAppDev){
			$newAppDev = new AppDevSurvey();
			$newAppDev->copyFrom($oldAppDev);
			$newAppDev->write();
			$this->AppDevSurveys()->add($newAppDev);
		}
	}

	public function getSurveyType(){
		$start_date = new DateTime(SURVEY_START_DATE);
		$created    = new DateTime($this->Created);
		if($created >= $start_date)
			return SurveyType::MARCH_2015;
		else
			return SurveyType::OLD;
	}

	public function canEdit($member = null) {
		return $this->getSurveyType() == SurveyType::MARCH_2015;
	}

	public function canDelete($member = null) {
		return $this->getSurveyType() == SurveyType::MARCH_2015;
	}

	public function getOrgName(){
		return intval($this->OrgID) > 0 ?  $this->Org()->Name : $this->Member()->getOrgName();
	}
}


class DeploymentSurveyController extends Page_Controller
{
}
