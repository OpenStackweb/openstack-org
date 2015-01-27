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
		'Title' => 'Title',
		'Member.FirstName' => 'Member First Name',
		'Member.Surname' => 'Member Surname',
		'Org.Name' => 'Organization'
	);

	static $searchable_fields = array(
		'Title' => 'PartialMatchFilter',
		'Org.Name' => 'PartialMatchFilter',
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
		'Cloud operator' => 'Private cloud operator - runs an OpenStack private cloud for their own organization',
		'Cloud Consumer' => 'Consumer of an OpenStack cloud - has API or dashboard credentials for one or more OpenStack resource pools, including an <strong>Application Developer<strong>'
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
        'Superuser' => 'Superuser',
        'Vendor documentation' => 'Vendor documentation',
  	);

	public static $business_drivers_options = array(
		'Save money over alternative infrastructure choices' => 'Save money over alternative infrastructure choices',
        'Increase operational efficiency' => 'Increase operational efficiency',
        'Accelerate my organization’s ability to innovate and compete by deploying applications faster' => 'Accelerate my organization’s ability to innovate and compete by deploying applications faster',
        'Avoid vendor lock-in with an open platform and ecosystem, including flexibility of underlying technology choices' => 'Avoid vendor lock-in with an open platform and ecosystem, including flexibility of underlying technology choices',
        'Attract top technical talent by participating in an active, global technology community' => 'Attract top technical talent by participating in an active, global technology community',
        'Achieve security and/or privacy goals with control of platform' => 'Achieve security and/or privacy goals with control of platform',
        'Standardize on the same open platform and APIs that power a global network of of public and private clouds' => 'Standardize on the same open platform and APIs that power a global network of of public and private clouds',
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

	function getCMSFields()
	{

		$fields = new FieldList(array(new TabSet("Root")));

		$CountryCodes = CountryCodes::$iso_3166_countryCodes;

		$member = $this->Member();
		$fields->addFieldsToTab('Root.Main',
			array(
				$user_name_txt = new ReadonlyField('UserName', 'User', $member->getFullName()),

				new DropdownField('OrgID',
					'Organization',
					Org::get()->sort('Org.Name', 'ASC')->map("ID", "Name", "-- Please choose an Organization --")),
				new ReadonlyField('FirstName', 'First Name'),
				new ReadonlyField('Surname', 'Last Name'),

				new DropdownField(
					'Industry',
					'Industry',
					ArrayUtils::AlphaSort(DeploymentSurvey::$industry_options, array('unspecified' => '-- Please Select One --'), array('Other' => 'Other (please specify)'))
				),
				new TextField('OtherIndustry', 'Other Industry'),
				new DropdownField(
					'OrgSize',
					'Organization Size',
					DeploymentSurvey::$organization_size_options
				),
				new LiteralField('Break', '<p>Where is the primary location or headquarters of your organization?</p>'),
				new TextField('PrimaryCity', 'City'),
				new TextField('PrimaryState', 'State/Province'),
				new DropdownField(
					'PrimaryCountry',
					'Country',
					$CountryCodes
				),
				new TextField('Title', 'Your Job Title'),
				new CheckboxSetField('OpenStackInvolvement', 'What best describes your involvement with OpenStack?', ArrayUtils::AlphaSort(DeploymentSurvey::$openstack_involvement_options)),
				new CheckboxSetField('InformationSources', 'Where do you go for information about using OpenStack?', ArrayUtils::AlphaSort(DeploymentSurvey::$information_options, null, array('Other' => 'Other (please specify)'))),
				new TextField('OtherInformationSources', 'Other information sources'),
				new CheckboxField('OkToContact', 'The OpenStack Foundation and User Committee can communicate with me in the future about my usage'),
				new LiteralField('Break', '<p>We would love to hear how OpenStack and the OpenStack Foundation can better meet your needs. These free-form questions are optional, but will provide valuable insights.</p>'),
				new TextAreaField('FurtherEnhancement', 'What areas of OpenStack software require further enhancement? (optional)'),
				new TextAreaField('FoundationUserCommitteePriorities', 'What should be the priorities for the Foundation and User Committee during the coming year? (optional)'),
				new TextAreaField('WhatDoYouLikeMost', 'What do you like most about OpenStack? (optional)'),
				new CheckboxSetField(
					'BusinessDrivers',
					'What are your business drivers for using OpenStack? (optional)',
					ArrayUtils::AlphaSort(DeploymentSurvey::$business_drivers_options, null, array('Other' => 'Other (please specify)'))),
				new TextField('OtherBusinessDrivers', 'Other business drivers'),
				new DropdownField(
					'OpenStackRecommendRate',
					'How likely is it that you would recommend OpenStack to a friend or colleague? (10 being the best)',
					DeploymentSurvey::$openstack_recommendation_rate_options
				),
				new TextareaField('OpenStackRecommendation', 'Are there any additional comments you would pass as part of this recommendation?')
			));
		$user_name_txt->setReadonly(true);
		return $fields;
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
}


class DeploymentSurveyController extends Page_Controller
{
}
