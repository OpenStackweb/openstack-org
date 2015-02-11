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
class Deployment extends DataObject
{

	private static $db = array(

		// Section 2
		'Label' => 'Text',
		'IsPublic' => 'Boolean',
		'DeploymentType' => 'Text',
		'ProjectsUsed' => 'Text',
		'CurrentReleases' => 'Text',
		'DeploymentStage' => 'Text',
		'NumCloudUsers' => 'Text',
		'WorkloadsDescription' => 'Text',
		'OtherWorkloadsDescription' => 'Text',

		// Section 3
		'APIFormats' => "Text",
		'Hypervisors' => "Text",
		'OtherHypervisor' => 'Text',
		'BlockStorageDrivers' => 'Text',
		'OtherBlockStorageDriver' => 'Text',
		'NetworkDrivers' => 'Text',
		'OtherNetworkDriver' => 'Text',
		'WhyNovaNetwork' => 'Text',
		'OtherWhyNovaNetwork' => 'Text',
		'IdentityDrivers' => 'Text',
		'OtherIndentityDriver' => 'Text',
		'SupportedFeatures' => 'Text',
		'DeploymentTools' => 'Text',
		'OtherDeploymentTools' => 'Text',
		'OperatingSystems' => 'Text',
		'OtherOperatingSystems' => 'Text',
		'ComputeNodes' => 'Text',
		'ComputeCores' => 'Text',
		'ComputeInstances' => 'Text',
		'BlockStorageTotalSize' => 'Text',
		'ObjectStorageSize' => 'Text',
		'ObjectStorageNumObjects' => 'Text',
		'NetworkNumIPs' => 'Text',
		//control fields
		'SendDigest' => 'Boolean',// SendDigest = 1 SENT, SendDigest = 0 to be send
		'UpdateDate' => 'SS_Datetime',
		'SwiftGlobalDistributionFeatures' => 'Text',
		'SwiftGlobalDistributionFeaturesUsesCases' => 'Text',
		'OtherSwiftGlobalDistributionFeaturesUsesCases' => 'Text',
		'Plans2UseSwiftStoragePolicies' => 'Text',
		'OtherPlans2UseSwiftStoragePolicies' => 'Text',
		'UsedDBForOpenStackComponents' => 'Text',
		'OtherUsedDBForOpenStackComponents' => 'Text',
		'ToolsUsedForYourUsers' => 'Text',
		'OtherToolsUsedForYourUsers' => 'Text',
		'Reason2Move2Ceilometer' => 'Text',
        'CountriesPhysicalLocation' => 'Text',
        'CountriesUsersLocation' => 'Text',
        'ServicesDeploymentsWorkloads' => 'Text',
        'OtherServicesDeploymentsWorkloads' => 'Text',
        'EnterpriseDeploymentsWorkloads' => 'Text',
        'OtherEnterpriseDeploymentsWorkloads' => 'Text',
        'HorizontalWorkloadFrameworks' => 'Text',
        'OtherHorizontalWorkloadFrameworks' => 'Text',
        'UsedPackages' => 'Text',
        'CustomPackagesReason' => 'Text',
        'OtherCustomPackagesReason' => 'Text',
		'PaasTools' => 'Text',
		'OtherPaasTools' => 'Text',
		'OtherSupportedFeatures' => 'Text',
		'InteractingClouds' => 'Text',
		'OtherInteractingClouds' => 'Text',
    );

	private static $has_one = array(
		'DeploymentSurvey' => 'DeploymentSurvey',
		'Org' => 'Org'
	);

	private static $summary_fields = array(
		'Label' => 'Label',
		'Org.Name' => 'Organization'
	);

	private static $singular_name = 'Deployment';
	private static $plural_name = 'Deployments';


	protected function onBeforeWrite()
	{
		parent::onBeforeWrite();
		$this->UpdateDate = SS_Datetime::now()->Rfc2822();
	}

	function getCMSFields()
	{


		$fields = new FieldList(
			$rootTab = new TabSet("Root",
				$tabContent = new TabSet('Content',
					new Tab('Main'),
					new Tab('Details')
				))
		);

		$fields->addFieldsToTab('Root.Main',
			array(
				new TextField('Label', 'Deployment Name'),
				new OptionSetField(
					'IsPublic',
					'Would you like to keep this information confidential or allow the Foundation to share information about this deployment publicly?',
					array('1' => '<strong>Willing to share:</strong> The information on this page may be shared for this deployment',
						'0' => '<strong>Confidential:</strong> All details provided should be kept confidential to the OpenStack Foundation'),
					1
				),
				new DropdownField('OrgID', 'Organization', DataObject::get('Org', '', 'Org.Name ASC')->map("ID", "Name", "-- Please choose an Organization --")),
				new DropdownField('DeploymentType', 'Deployment Type', Deployment::$deployment_type_options),
				new CheckboxSetField('ProjectsUsed', 'Projects Used', Deployment::$projects_used_options),
				new CheckboxSetField('CurrentReleases', 'What releases are you currently using?', Deployment::$current_release_options),
				new CheckboxSetField(
					'DeploymentStage',
					'In what stage is your OpenStack deployment? (make a new deployment profile for each type of deployment)',
					Deployment::$stage_options
				),
				new TextAreaField('NumCloudUsers',
					'What\'s the size of your cloud by number of users?'),
				new CheckboxSetField(
					'WorkloadsDescription',
					'Describe the workloads or applications running in your Openstack environment. (choose any that apply)',
					ArrayUtils::AlphaSort(Deployment::$workloads_description_options, null, array('Other' => 'Other (please specify)'))),
				new TextAreaField(
					'OtherWorkloadsDescription',
					'Other workloads or applications running in your Openstack environment. (optional)'),
			));

		$fields->addFieldsToTab('Root.Details',

			array(
				new LiteralField('Break', '<p>The information below will help us better understand
        the most common configuration and component choices OpenStack deployments are using.</p>'),
				new CheckboxSetField(
					'Hypervisors',
					'If you are using OpenStack Compute, which hypervisors are you using?',
					ArrayUtils::AlphaSort(Deployment::$hypervisors_options)
				),
				new TextField('OtherHypervisor', 'Other Hypervisor'),
				new CheckboxSetField(
					'NetworkDrivers',
					'Do you use nova-network, or OpenStack Network (Neutron)? If you are using OpenStack Network (Neutron), which drivers are you using?',
					ArrayUtils::AlphaSort(Deployment::$network_driver_options)
				),
				new TextField('OtherNetworkDriver', 'Other Network Driver'),
				new CheckboxSetField(
					'WhyNovaNetwork',
					'If you are using nova-network and not OpenStack Networking (Neutron), what would allow you to migrate? (optional)',
					ArrayUtils::AlphaSort(Deployment::$why_nova_network_options, null, array('Other (please specify)' => 'Other (please specify)'))),
				new LiteralField('Break', '<br/>'),
				new TextField('OtherWhyNovaNetwork', ''),
				new CheckboxSetField(
					'BlockStorageDrivers',
					'If you are using OpenStack Block Storage, which drivers are you using?',
					ArrayUtils::AlphaSort(Deployment::$block_storage_divers_options)
				),
				new TextField('OtherBlockStorageDriver', 'Other Block Storage Driver'),
				new CheckboxSetField(
					'IdentityDrivers',
					'If you are using OpenStack Identity which OpenStack Identity drivers are you using?',
					ArrayUtils::AlphaSort(Deployment::$identity_driver_options)
				),
				new TextField('OtherIndentityDriver', 'Other/Custom Identity Driver'),
				new CheckboxSetField(
					'SupportedFeatures',
					'Which of the following compatibility APIs does/will your deployment support?',
					ArrayUtils::AlphaSort(Deployment::$deployment_features_options_new)
				),
				new CheckboxSetField(
					'DeploymentTools',
					'What tools are you using to deploy/configure your cluster?',
					ArrayUtils::AlphaSort(Deployment::$deployment_tools_options)
				),
				new TextField('OtherDeploymentTools', 'Other tools'),
				new CheckboxSetField(
					'OperatingSystems',
					'What is the main Operating System you are using to run your OpenStack cloud?',
					ArrayUtils::AlphaSort(Deployment::$operating_systems_options)
				),
				new TextField('OtherOperatingSystems', 'Other Operating System'),
				new LiteralField('Break', '<p>Please provide the following information about the
        size and scale of this OpenStack deployment. This information is optional, but will
        be kept confidential and never published in connection with your organization.</p>'),
				new LiteralField('Break', '<p><strong>If using OpenStack Compute, what’s the size of your cloud?</strong></p>'),
				new DropdownField(
					'ComputeNodes',
					'Physical compute nodes',
					Deployment::$compute_nodes_options
				),
				new DropdownField(
					'ComputeCores',
					'Processor cores',
					Deployment::$compute_cores_options
				),
				new DropdownField(
					'ComputeInstances',
					'Number of instances',
					Deployment::$compute_instances_options
				),
				new DropdownField(
					'BlockStorageTotalSize',
					'If using OpenStack Block Storage, what’s the size of your cloud by total storage in terabytes?',
					Deployment::$storage_size_options
				),
				new LiteralField('Break', '<p><strong>If using OpenStack Object Storage, what’s the size of your cloud?</strong></p>'),
				new DropdownField(
					'ObjectStorageSize',
					'Total storage in terabytes',
					Deployment::$storage_size_options
				),
				new DropdownField(
					'ObjectStorageNumObjects',
					'Total objects stored',
					Deployment::$storage_objects_options
				),
				new DropdownField(
					'SwiftGlobalDistributionFeatures',
					'Are you using Swift\'s global distribution features?',
					ArrayUtils::AlphaSort(Deployment::$swift_global_distribution_features_options, array('unspecified' => '-- Select One --'))
				),

				new DropdownField(
					'SwiftGlobalDistributionFeaturesUsesCases',
					'If yes, what is your use case?',
					ArrayUtils::AlphaSort(Deployment::$swift_global_distribution_features_uses_cases_options, array('unspecified' => '-- Select One --'))
				),
				new TextField('OtherSwiftGlobalDistributionFeaturesUsesCases', ''),

				new DropdownField(
					'Plans2UseSwiftStoragePolicies',
					'Do you have plans to use Swift\'s storage policies or erasure codes in the next year?',
					ArrayUtils::AlphaSort(Deployment::$plans_2_use_swift_storage_policies_options, array('unspecified' => '-- Select One --'))
				),
				new TextField('OtherPlans2UseSwiftStoragePolicies', ''),

				new DropdownField(
					'NetworkNumIPs',
					'If using OpenStack Network, what’s the size of your cloud by number of fixed/floating IPs?',
					Deployment::$network_ip_options
				),
				new CheckboxSetField(
					'UsedDBForOpenStackComponents',
					'What database do you use for the components of your OpenStack cloud?',
					ArrayUtils::AlphaSort(Deployment::$used_db_for_openstack_components_options, null, array('Other' => 'Other (Specify)'))
				),
				new TextField('OtherUsedDBForOpenStackComponents', ''),
				new CheckboxSetField(
					'ToolsUsedForYourUsers',
					'What tools are you using charging or show-back for your users?',
					ArrayUtils::AlphaSort(Deployment::$tools_used_for_your_users_options, null, array('Other' => 'Other (Specify)'))
				),
				new TextField('OtherToolsUsedForYourUsers', ''),
				new TextareaField('Reason2Move2Ceilometer', 'If you are not using Ceilometer, what would allow you to move to it (optional free text)?')
			)
		);
		return $fields;
	}

	function getCMSValidator()
	{
		return $this->getValidator();
	}

	function getValidator()
	{
		// Create Validators
		$validator = new RequiredFields('Label',
			'IsPublic',
			'ProjectsUsed',
			'NumCloudUsers',
			'CurrentReleases',
			'DeploymentStage',
			'DeploymentType');
		return $validator;
	}

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

	public function OrgAndLabel()
	{
		return $this->getOrg() . ' - ' . $this->Label;
	}

	public function hasUserStory()
	{
		$userStory = UserStory::get()->filter('DeploymentID', $this->ID)->first();
		return ($userStory) ? true : false;
	}

	public function getUserStory()
	{
		if ($this->hasUserStory()) {
			return UserStory::get()->filter('DeploymentID', $this->ID)->first();
		}
		return false;
	}

	/**
	 * @param int $batch_size
	 * @return mixed
	 */
	public function getNotDigestSent($batch_size)
	{
		Deployment::get()->filter('SendDigest', 0)->sort('UpdateDate', 'ASC')->limit($batch_size);
	}

	public static $deployment_type_options = array(
		'' => '-- Select One --',
		'On-Premise Private Cloud (Managed by my organization)' => 'On-Premise Private Cloud (Managed by my organization)',
		'On-Premise Private Cloud (Managed by someone else)' => 'On-Premise Private Cloud (Managed by someone else)',
		'Public Cloud' => 'Public Cloud',
		'Remote Private Cloud (Managed by my organization)' => 'Remote Private Cloud (Managed by my organization)',
        'Remote Private Cloud (Managed by someone else)' => 'Remote Private Cloud (Managed by someone else)',
		'Community Cloud' => 'Community Cloud'
	);

	public static $projects_used_options = array(
		'Bare Metal (Ironic)' => 'Bare Metal (Ironic)',
        'Block Storage (Cinder)' => 'Block Storage (Cinder)',
        'Compute (Nova)' => 'Compute (Nova)',
        'Dashboard (Horizon)' => 'Dashboard (Horizon)',
        'Data Processing (Sahara)' => 'Data Processing (Sahara)',
        'Database Service (Trove)' => 'Database Service (Trove)',
        'DNS Services (Designate)' => 'DNS Services (Designate)',
        'Identity (Keystone)' => 'Identity (Keystone)',
        'Image Service (Glance)' => 'Image Service (Glance)',
        'Key Management (Barbican)' => 'Key Management (Barbican)',
        'Networking (Neutron)' => 'Networking (Neutron)',
        'Object Storage (Swift)' => 'Object Storage (Swift)',
        'Orchestration (Heat)' => 'Orchestration (Heat)',
        'Queue Service (Zaqar)' => 'Queue Service (Zaqar)',
        'Telemetry (Ceilometer)' => 'Telemetry (Ceilometer)',
 	);

	public static $current_release_options = array(
		'Trunk / continuous deployment' => 'Trunk / continuous deployment',
        'Kilo (2015.1)' => 'Kilo (2015.1)',
        'Juno (2014.2)' => 'Juno (2014.2)',
        'Icehouse (2014.1)' => 'Icehouse (2014.1)',
        'Havana (2013.2)' => 'Havana (2013.2)',
        'Grizzly (2013.1)' => 'Grizzly (2013.1)',
        'Folsom (2012.2)' => 'Folsom (2012.2)',
        'Essex (2012.1)' => 'Essex (2012.1)',
	);

	public static $stage_options = array(
		'' => '-- Select One --',
		'Proof of Concept' => 'Proof of Concept',
		'Under development/in testing' => 'Under development/in testing',
		'Production' => 'Production'
	);

	public static $num_cloud_users_options = array(
		'Prefer not to say' => 'Prefer not to say',
		'1-100 users' => '1-100 users',
		'101-1,000 users' => '101-1,000 users',
		'1,001-5,000 users' => '1,001-5,000 users',
		'5,001-10,000 users' => '5,001-10,000 users',
		'10,001-50,000 users' => '10,001-50,000 users',
		'50,001-100,000 users' => '50,001-100,000 users',
		'More than 100,000 users' => 'More than 100,000 users'
	);

	public static $workloads_description_options = array(
		'Virtual Desktops' => 'Virtual Desktops',
		'HPC' => 'High Throughput Computing/Batch System/HPC',
		'Public Hosting' => 'Public Hosting',
		'Web Services' => 'Web Services',
		'Data Mining/Big Data/Hadoop' => 'Data Mining/Big Data/Hadoop',
		'Storage/Backup' => 'Storage/Backup',
		'QA/Test Environment' => 'QA/Test Environment',
		'Continuous integration/Automated Testing workflows' => 'Continuous integration/Automated Testing workflows',
		'Bio/Medical Applications' => 'Bio/Medical Applications',
		'Mobile Applications' => 'Mobile Applications',
		'Network Applications' => 'Network Applications',
		'Geographical Information Systems (GIS)' => 'Geographical Information Systems (GIS)',
		'File Sharing' => 'File Sharing',
		'CDN/Video Streaming' => 'CDN/Video Streaming',
		'Education/MOOC' => 'Education/MOOC',
		'Enterprise Applications' => 'Enterprise Applications',
		'Databases' => 'Databases',
		'Benchmarks/Stress Testing' => 'Benchmarks/Stress Testing',
		'Research' => 'Research',
		'Management and Monitoring Systems' => 'Management and Monitoring Systems',
		'Games/Online Games' => 'Games/Online Games',
		'Up to the user' => 'It’s up to the user',

	);

    public static $services_deployment_workloads_options = array(
        'Social Media' => 'Social Media',
        'Content Delivery / CDN - includes streaming and caching' => 'Content Delivery / CDN - includes streaming and caching',
        'Content Sharing - files of any type' => 'Content Sharing - files of any type',
        'Commerce' => 'Commerce',
        'Geographical Information Systems (GIS)' => 'Geographical Information Systems (GIS)',
        'Online Games' => 'Online Games',
        'Mobile Apps, Including Mobile Games' => 'Mobile Apps, Including Mobile Games',
        'Education / MOOC' => 'Education / MOOC',
        'Business Intelligence and Web analytics' => 'Business Intelligence and Web analytics',
        'HPC and Other High Performance and High Throughput Computing, Including Research' => 'HPC and Other High Performance and High Throughput Computing, Including Research',
        'Bio / Medical, Separate from HPC' => 'Bio / Medical, Separate from HPC',
        'Big Data Analytics / Data Mining / Hadoop, Spark, etc.' => 'Big Data Analytics / Data Mining / Hadoop, Spark, etc.',
        'Business Process - ERP, CRM, SCM, etc.' => 'Business Process - ERP, CRM, SCM, etc.',
        'Email' => 'Email',
        'Application Development' => 'Application Development',
        'Web Infrastructure' => 'Web Infrastructure',
        'Public Hosting' => 'Public Hosting',
        'It’s up to the User' => 'It’s up to the User',
        'Other' => 'Other'
    );

    public static $enterprise_deployment_workloads_options = array(
        'Business Processing: ERP, CRM, OLTP' => 'Business Processing: ERP, CRM, OLTP',
        'Decision Support: database analysis, business intelligence, and business analytics' => 'Decision Support: database analysis, business intelligence, and business analytics',
        'Application Development: programming, debug, Q&A' => 'Application Development: programming, debug, Q&A',
        'Collaborative: email and groupware' => 'Collaborative: email and groupware',
        'IT Infrastructure: file/print and support for network protocols' => 'IT Infrastructure: file/print and support for network protocols',
        'Web Infrastructure: Web-serving, proxy and caching' => 'Web Infrastructure: Web-serving, proxy and caching',
        'Industrial R&D: scientific/technical/engineering' => 'Industrial R&D: scientific/technical/engineering',
        'Other' => 'Other'
    );

    public static $horizontal_workload_framework_options = array(
        'OS: Windows Server, Linux' => 'OS: Windows Server, Linux',
        'Virtualization: Xen, KVM, Hyper-V, etc.' => 'Virtualization: Xen, KVM, Hyper-V, etc.',
        'Virtual Desktops / Remote Desktops' => 'Virtual Desktops / Remote Desktops',
        'Database: mySQL, etc.' => 'Database: mySQL, etc.',
        'Network Function Virtualization (NFV) – network applications' => 'Network Function Virtualization (NFV) – network applications',
        'Storage / Backup / Archiving' => 'Storage / Backup / Archiving',
        'Runtime and Managed Languages: Java, Python, Ruby, etc. (interpreted at runtime or JIT compiled as needed)' => 'Runtime and Managed Languages: Java, Python, Ruby, etc. (interpreted at runtime or JIT compiled as needed)',
        'Benchmarks / Stress Testing' => 'Benchmarks / Stress Testing',
        'Management and Monitoring Systems' => 'Management and Monitoring Systems',
        'QA / Test Environment / Continuous Integration / Automated Testing Workflows' => 'QA / Test Environment / Continuous Integration / Automated Testing Workflows',
        'Other' => 'Other'
    );


	public static $api_options = array(
		'XML' => 'XML',
		'JSON' => 'JSON'
	);

	public static $hypervisors_options = array(
		'Bare Metal' => 'Bare Metal',
		'Citrix XenServer' => 'Citrix XenServer',
		'Docker' => 'Docker',
		'KVM' => 'KVM',
		'LXC' => 'LXC',
		'Microsoft Hyper-V' => 'Microsoft Hyper-V',
		'OpenVZ' => 'OpenVZ',
		'PowerKVM' => 'PowerKVM',
		'QEMU' => 'QEMU',
		'VMware ESX' => 'VMware ESX',
		'Xen / XCP' => 'Xen / XCP',
		'Other Hypervisors' => 'Other Hypervisors',
	);

	public static $block_storage_divers_options = array(
		'Ceph RBD' => 'Ceph RBD',
		'Coraid' => 'Coraid',
		'Dell EqualLogic' => 'Dell EqualLogic',
		'EMC' => 'EMC',
		'GlusterFS' => 'GlusterFS',
		'HDS' => 'HDS',
		'HP 3PAR' => 'HP 3PAR',
		'HP LeftHand' => 'HP LeftHand',
		'Huawei' => 'Huawei',
		'IBM GPFS' => 'IBM GPFS',
		'IBM NAS' => 'IBM NAS',
		'IBM Storwize' => 'IBM Storwize',
		'IBM XIV / DS8000' => 'IBM XIV / DS8000',
		'LVM (default)' => 'LVM (default)',
		'Mellanox' => 'Mellanox',
		'NetApp' => 'NetApp',
		'Nexenta' => 'Nexenta',
		'NFS' => 'NFS',
		'ProphetStor' => 'ProphetStor',
		'SAN / Solaris' => 'SAN / Solaris',
		'Scality' => 'Scality',
		'Sheepdog' => 'Sheepdog',
		'SolidFire' => 'SolidFire',
		'VMWare VMDK' => 'VMWare VMDK',
		'Windows Server 2012' => 'Windows Server 2012',
		'Xenapi NFS' => 'Xenapi NFS',
		'XenAPI Storage Manager' => 'XenAPI Storage Manager',
		'Zadara' => 'Zadara',
		'Other Block Storage Driver' => 'Other Block Storage Driver',
	);

	public static $network_driver_options = array(
		'A10 Networks' => 'A10 Networks',
		'Arista' => 'Arista',
		'Big Switch' => 'Big Switch',
		'Brocade' => 'Brocade',
		'Cisco UCS / Nexus' => 'Cisco UCS / Nexus',
		'Embrane' => 'Embrane',
		'Extreme Networks' => 'Extreme Networks',
		'Hyper-V' => 'Hyper-V',
		'IBM SDN-VE' => 'IBM SDN-VE',
		'Juniper' => 'Juniper',
		'Linux Bridge' => 'Linux Bridge',
		'Mellanox' => 'Mellanox',
		'Meta Plugin' => 'Meta Plugin',
		'MidoNet' => 'MidoNet',
		'Modular Layer 2 Plugin (ML2)' => 'Modular Layer 2 Plugin (ML2)',
		'NEC OpenFlow' => 'NEC OpenFlow',
		'Nuage Networks' => 'Nuage Networks',
		'One Convergence NVSD' => 'One Convergence NVSD',
		'OpenDaylight' => 'OpenDaylight',
		'Open vSwitch' => 'Open vSwitch',
		'PLUMgrid' => 'PLUMgrid',
		'Ruijie Networks' => 'Ruijie Networks',
		'Ryu OpenFlow Controller' => 'Ryu OpenFlow Controller',
		'VMWare NSX (formerly Nicira NVP)' => 'VMWare NSX (formerly Nicira NVP)',
		'nova-network' => 'nova-network',
		'Other Network Driver' => 'Other Network Driver',
	);

	public static $identity_driver_options = array(
		'Active Directory' => 'Active Directory',
		'KVS' => 'KVS',
		'LDAP' => 'LDAP',
		'PAM' => 'PAM',
		'SQL (default)' => 'SQL (default)',
		'Templated' => 'Templated',
		'Other Identity Driver' => 'Other Identity Driver',
	);

	public static $deployment_features_options = array(
		'EC2 compatibility API' => 'EC2 compatibility API',
		'GCE compatibility API' => 'GCE compatibility API',
		'OCCI compatibility API' => 'OCCI compatibility API',
		'S3 compatibility API' => 'S3 compatibility API',
		'Other Compatibility API' => 'Other Compatibility API',
	);

	public static $deployment_tools_options = array(
		'Ansible' => 'Ansible',
        'CFEngine' => 'CFEngine',
        'Chef' => 'Chef',
        'Crowbar' => 'Crowbar',
        'DevStack' => 'DevStack',
        'Fuel' => 'Fuel',
        'Juju' => 'Juju',
        'PackStack' => 'PackStack',
        'Puppet' => 'Puppet',
        'SaltStack' => 'SaltStack',
        'Other Tool' => 'Other Tool',
	);

	public static $operating_systems_options = array(
		'CentOS' => 'CentOS',
		'Debian' => 'Debian',
		'Fedora Server' => 'Fedora Server',
		'Microsoft Windows Server' => 'Microsoft Windows Server',
        'Red Hat Enterprise Linux (RHEL)' => 'Red Hat Enterprise Linux (RHEL)',
        'SUSE Linux Enterprise Server (SLES)' => 'SUSE Linux Enterprise Server (SLES)',
        'Scientific Linux' => 'Scientific Linux',
        'Ubuntu Server' => 'Ubuntu Server',
        'openSUSE Server' => 'openSUSE Server',
	);

	public static $compute_nodes_options = array(
		'1 to 9 nodes' => '1 to 9 nodes',
		'10 to 99 nodes' => '10 to 99 nodes',
		'100 to 999 nodes' => '100 to 999 nodes',
		'1,000 to 9,999 nodes' => '1,000 to 9,999 nodes',
		'10,000 to 99,999 nodes' => '10,000 to 99,999 nodes',
		'100,000 to 999,999 nodes' => '100,000 to 999,999 nodes',
		'1 million or more nodes' => '1 million or more nodes',
	);

	public static $compute_cores_options = array(
		'1 to 9 cores' => '1 to 9 cores',
		'10 to 99 cores' => '10 to 99 cores',
		'100 to 999 cores' => '100 to 999 cores',
		'1,000 to 9,999 cores' => '1,000 to 9,999 cores',
		'10,000 to 99,999 cores' => '10,000 to 99,999 cores',
		'100,000 to 999,999 cores' => '100,000 to 999,999 cores',
		'1 million or more cores' => '1 million or more cores',
	);

	public static $compute_instances_options = array(
		'1 to 9 instances' => '1 to 9 instances',
		'10 to 99 instances' => '10 to 99 instances',
		'100 to 999 instances' => '100 to 999 instances',
		'1,000 to 9,999 instances' => '1,000 to 9,999 instances',
		'10,000 to 99,999 instances' => '10,000 to 99,999 instances',
		'100,000 to 999,999 instances' => '100,000 to 999,999 instances',
		'1 million or more instances' => '1 million or more instances',
	);

	public static $storage_size_options = array(
		'Less than 1 TB' => 'Less than 1 TB' ,
		'1 TB to 9 TB' => '1 TB to 9 TB' ,
		'10 TB to 99 TB' => '10 TB to 99 TB' ,
		'100 TB to 999 TB' => '100 TB to 999 TB' ,
		'1 PB to 9 PB' => '1 PB to 9 PB' ,
		'10 PB to 99 PB' => '10 PB to 99 PB' ,
		'100 PB to 999 PB' => '100 PB to 999 PB' ,
		'1 EB to 9 EB' => '1 EB to 9 EB' ,
		'10 EB to 99 EB' => '10 EB to 99 EB' ,
		'100 to 999 EB' => '100 to 999 EB' ,
		'1 ZB to 9 ZB' => '1 ZB to 9 ZB' ,
	);

	public static $storage_objects_options = array(
		'Fewer than 1,000 objects' => 'Fewer than 1,000 objects',
		'1,000 to 9,999 objects' => '1,000 to 9,999 objects',
		'10,000 to 99,999 objects' => '10,000 to 99,999 objects',
		'100,000 to 999,999 objects' => '100,000 to 999,999 objects',
		'1 million to 9 million objects' => '1 million to 9 million objects',
		'10 million to 999 million objects' => '10 million to 999 million objects',
		'100 million to 999 million objects' => '100 million to 999 million objects',
		'1 billion to 9 billion objects' => '1 billion to 9 billion objects',
		'10 billion to 99 billion objects' => '10 billion to 99 billion objects',
		'100 billion or more objects' => '100 billion or more objects',
	);

	public static $network_ip_options = array(
		'1 to 9 IPs' => '1 to 9 IPs',
		'10 to 99 IPs' => '10 to 99 IPs',
		'100 to 999 IPs' => '100 to 999 IPs',
		'1,000 to 9,999 IPs' => '1,000 to 9,999 IPs',
		'10,000 to 99,999 IPs' => '10,000 to 99,999 IPs',
		'100,000 or more IPs ' => '100,000 or more IPs ',
	);

	public static $why_nova_network_options = array(
		'Simplification of Neutron' => 'Simplification of Neutron',
		'Migration ease from nova-network to Neutron' => 'Migration ease from nova-network to Neutron',
		'Scalability' => 'Scalability',
		'Performance' => 'Performance',
		'Other Reason' => 'Other Reason',
	);

	public static $swift_global_distribution_features_options = array(
		'No, this Swift cluster is only in a single region' => 'No, this Swift cluster is only in a single region',
		'Yes, container sync' => 'Yes, container sync',
		'Yes, globally distributed clusters' => 'Yes, globally distributed clusters',
	);

	public static $swift_global_distribution_features_uses_cases_options = array(
		'Disaster recovery' => 'Disaster recovery',
		'Continuity of business' => 'Continuity of business',
		'Regulatory reasons' => 'Regulatory reasons',
		'Locality of access' => 'Locality of access',
		'Other' => 'Other Swift Use Case'
	);

	public static $plans_2_use_swift_storage_policies_options = array(
		'Yes' => 'Yes',
		'No' => 'No',
		'Maybe. Please explain' => 'Maybe. Please explain',
	);

	public static $used_db_for_openstack_components_options = array(
		'MariaDB' => 'MariaDB',
		'MariaDB Galera Cluster' => 'MariaDB Galera Cluster',
		'MongoDB' => 'MongoDB',
		'MySQL' => 'MySQL',
		'MySQL with DRBD' => 'MySQL with DRBD',
		'MySQL with Galera' => 'MySQL with Galera',
		'Percona Server' => 'Percona Server',
		'Percona XtraDB Cluster' => 'Percona XtraDB Cluster',
		'PostgreSQL' => 'PostgreSQL',
		'SQLite' => 'SQLite',
		'Other Database' => 'Other Database',
	);

	public static $tools_used_for_your_users_options = array(
		'Home grown tools using ceilometer' => 'Home grown tools using ceilometer',
		'Home grown tools using other OpenStack components than ceilometer' => 'Home grown tools using other OpenStack components than ceilometer',
		'Cloud Kitty' => 'Cloud Kitty',
		'None' => 'None',
		'Other' => 'Other'
	);

    public static $used_packages_options = array(
        'Unmodified packages from the OS' => 'Unmodified packages from the OS',
        'Unmodified packages from a non-OS source (e.g. vendor distribution)' => 'Unmodified packages from a non-OS source (e.g. vendor distribution)',
        'Packages you\'ve modified' => 'Packages you\'ve modified',
        'Packages you’ve built yourself' => 'Packages you’ve built yourself',
    );

    public static $custom_package_reason_options = array(
        'Need an OpenStack bug fix not in standard packages' => 'Need an OpenStack bug fix not in standard packages',
        'Need an OpenStack feature not in standard packages' => 'Need an OpenStack feature not in standard packages',
        'Standard packages have bugs' => 'Standard packages have bugs',
        'Standard packages aren\'t updated quickly enough' => 'Standard packages aren\'t updated quickly enough',
        'Other' => 'Other',
    );

	public static $paas_tools_options = array(
		'CloudFoundry' => 'CloudFoundry',
		'OpenShift' => 'OpenShift',
		'OpenStack Orchestration (Heat)' => 'OpenStack Orchestration (Heat)',
		'None' => 'None',
	);

	public static $interacting_clouds_options = array(
		'Amazon' => 'Amazon',
		'Azure' => 'Azure',
		'Google Compute Engine' => 'Google Compute Engine',
		'Other OpenStack clouds' => 'Other OpenStack clouds',
		'None' => 'None',
		'Other Clouds ' => 'Other Clouds ',
	);

	public static $cloud_users_options = array(
		'1 to 9 users' => '1 to 9 users',
		'10 to 99 users' => '10 to 99 users',
		'100 to 999 users' => '100 to 999 users',
		'1,000 to 9,999 users' => '1,000 to 9,999 users',
		'10,000 to 99,999 users' => '10,000 to 99,999 users',
		'100,000 to 999,999 users' => '100,000 to 999,999 users',
		'1,000,000 to 9,999,999 users' => '1,000,000 to 9,999,999 users',
		'10,000,000 to 999,999,999 users' => '10,000,000 to 999,999,999 users',
		'100,000,000 or more users ' => '100,000,000 or more users',
	);
	
}