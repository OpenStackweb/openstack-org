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

	public static $industry_options = array(
		'Academic / Research' => 'Academic / Research / Education',
		'Consumer Goods' => 'Consumer Goods',
		'Energy' => 'Energy',
		'Film/Media' => 'Film / Media / Entertainment',
		'Finance' => 'Finance & Investment',
		'Government / Defense' => 'Government / Defense',
		'Healthcare' => 'Healthcare',
		'Information Technology' => 'Information Technology',
		'Insurance' => 'Insurance',
		'Manufacturing/Industrial' => 'Manufacturing / Industrial',
		'Retail' => 'Retail',
		'Telecommunications' => 'Telecommunications',
	);

	public static $organization_size_options = array(
		'1 to 9 employees' => '1 to 9 employees',
        '10 to 99 employees' => '10 to 99 employees',
        '100 to 999 employees' => '100 to 999 employees',
        '1,000 to 9,999 employees' => '1,000 to 9,999 employees',
        '10,000 to 99,999 employees' => '10,000 to 99,999 employees',
        '100,000 employees or more' => '100,000 employees or more',
        'Don’t know / not sure' => 'Don’t know / not sure',
 	);

	public static $openstack_recommendation_rate_options = array(
		'0' => '0',
		'1' => '1',
		'2' => '2',
		'3' => '3',
		'4' => '4',
		'5' => '5',
		'6' => '6',
		'7' => '7',
		'8' => '8',
		'9' => '9',
		'10' => '10',
	);

	public static $openstack_involvement_options = array(
		'Service Provider' => 'OpenStack cloud service provider - provides public or hosted private cloud services for other organizations',
		'Ecosystem Vendor' => 'Ecosystem vendor - provides software or solutions that enable others to build or run OpenStack clouds',
		'Cloud operator' => 'Private cloud operator - Runs an OpenStack private cloud for your own organization',
		'Cloud Consumer' => 'Consumer of an OpenStack cloud - has API or dashboard credentials for one or more OpenStack resource pools, including an Application Developer'
	);

	public static $information_options = array(
		'Ask OpenStack (ask.openstack.org)' => 'Ask OpenStack (ask.openstack.org)',
		'Blogs' => 'Blogs',
		'docs.openstack.org' => 'docs.openstack.org',
		'IRC' => 'IRC',
		'Local user group' => 'Local user group',
        'OpenStack Mailing List' => 'OpenStack Mailing List',
        'OpenStack Operators Mailing List' => 'OpenStack Operators Mailing List',
        'OpenStack Dev Mailing List' => 'OpenStack Dev Mailing List',
        'The OpenStack Operations Guide' => 'The OpenStack Operations Guide',
        'Online Forums' => 'Online Forums',
        'OpenStack Planet (planet.openstack.org)' => 'OpenStack Planet (planet.openstack.org)',
        'Read the source code' => 'Read the source code',
        'Superuser' => 'Superuser.openstack.org',
        'Vendor documentation' => 'Vendor documentation',
  	);

	public static $business_drivers_options = array(
		'Save money over alternative infrastructure choices' => 'Save money over alternative infrastructure choices',
        'Increase operational efficiency' => 'Increase operational efficiency',
        'Accelerate my organization\’s ability to innovate and compete by deploying applications faster' => 'Accelerate my organization’s ability to innovate and compete by deploying applications faster',
        'Avoid vendor lock-in with an open platform and ecosystem including flexibility of underlying technology choices' => 'Avoid vendor lock-in with an open platform and ecosystem, including flexibility of underlying technology choices',
        'Attract top technical talent by participating in an active global technology community' => 'Attract top technical talent by participating in an active, global technology community',
        'Achieve security and/or privacy goals with control of platform' => 'Achieve security and/or privacy goals with control of platform',
        'Standardize on the same open platform and APIs that power a global network of of public and private clouds' => 'Standardize on the same open platform and APIs that power a global network of of public and private clouds',
		'Other' => 'Something else not listed here',
   );

    public static $activities_options = array(
        'Write code that is upstreamed into OpenStack' => 'Write code that is upstreamed into OpenStack' ,
        'Manage people who write code that is upstreamed into OpenStack' => 'Manage people who write code that is upstreamed into OpenStack',
        'Write applications that run on OpenStack' => 'Write applications that run on OpenStack',
        'Manage people who write applications that run on OpenStack' => 'Manage people who write applications that run on OpenStack',
        'Install / administer / deploy OpenStack' => 'Install / administer / deploy OpenStack',
        'Install / administer / deploy applications that run on OpenStack' => 'Install / administer / deploy applications that run on OpenStack',
        'Manage people who install / administer / deploy OpenStack' => 'Manage people who install / administer / deploy OpenStack',
        'Manage people who install / administer / deploy applications that run on OpenStack' => 'Manage people who install / administer / deploy applications that run on OpenStack',
        'None of these' => 'None of these',
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
				$os_activity            = new CustomCheckboxSetField('OpenStackActivity', 'Which of the following do you yourself personally do?<BR>Select All That Apply', DeploymentSurvey::$activities_options),
				$os_relationship        = new TextAreaField('OpenStackRelationship', 'Please describe your relationship with OpenStack'),
				$email_field            = new ReadonlyField('Member.Email', 'Your Email', $this->Member()->Email),
				$ok_2_contact           = new CheckboxField('OkToContact', 'The OpenStack Foundation and User Committee may communicate with me in the future about my usage.')
			));

		$fields->addFieldsToTab('Root.YourOrganization', array(
			new ReadonlyField('Org.Name', 'Organization', $this->Org()->Name),
			new DropdownField(
				'Industry',
				'Your Organization’s Primary Industry',
				ArrayUtils::AlphaSort(DeploymentSurvey::$industry_options, array('' => '-- Please Select One --'), array('Other' => 'Other Industry (please specify)') )
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
				DeploymentSurvey::$organization_size_options
			),
			new CheckboxSetField('OpenStackInvolvement', 'What best describes your Organization’s involvement with OpenStack?', ArrayUtils::AlphaSort(DeploymentSurvey::$openstack_involvement_options))
		));

		$ddl_country->setEmptyString('-- Select One --');

		$fields->addFieldsToTab('Root.Your Thoughts', array(
			new CustomCheckboxSetField(
				'BusinessDrivers',
				'What are your top business drivers for using OpenStack?<BR>Please rank up to 5.<BR>1 = top business driver, 2 = next, 3 = third, and so on<BR>Select At Least One',
				ArrayUtils::AlphaSort(DeploymentSurvey::$business_drivers_options,null, array('Other' => 'Something else not listed here (please specify)'))),
			new TextAreaField('OtherBusinessDrivers', ''),
			new CustomCheckboxSetField('InformationSources', 'Where do end up finding information about using OpenStack, after using search engines and talking to your colleagues?<BR>Select All That Apply', ArrayUtils::AlphaSort(DeploymentSurvey::$information_options, null, array('Other' => 'Other Sources (please specify)'))),
			new TextAreaField('OtherInformationSources', ''),
			$ddl_rate = new DropdownField(
				'OpenStackRecommendRate',
				'How likely are you to recommend OpenStack to a friend or colleague? (0=Least Likely, 10=Most Likely)',
				DeploymentSurvey::$openstack_recommendation_rate_options),
			new LiteralField('Break', '<hr/>'),
			new LiteralField('Break', '<p>We would love to hear how OpenStack and the OpenStack Foundation can better meet your needs. These free-form questions are optional, but will provide valuable insights.</p>'),
			new LiteralField('Break', '<p>Your responses are anonymous, and each of these text fields is independent, so we cannot “See previous answer”. We would really appreciate a separate answer to each question.</p>'),
			new TextAreaField('WhatDoYouLikeMost', 'What do you like most about OpenStack, besides “free” and “open”?'),
			new TextAreaField('FurtherEnhancement', 'What areas of OpenStack require further enhancement? '),
			new TextAreaField('FoundationUserCommitteePriorities', 'What should be the priorities for the Foundation and User Committee during the coming year?')
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
	public function getNotDigestSent($batch_size)
	{
		return DeploymentSurvey::get()->filter(array('SendDigest' => 0))->where("\"Title\" IS NULL ")->sort('Created')->limit($batch_size);
	}


	public function copyFrom(DeploymentSurvey $oldSurvey){
		// copy properties

		foreach(DeploymentSurvey::$db as $field => $type){
			$value = $oldSurvey->getField($field);
			$this->setField($field, $value);
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
}


class DeploymentSurveyController extends Page_Controller
{
}
