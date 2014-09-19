<?php

class AppDevSurvey extends DataObject {

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
		'DocsPriority' => 'Text'
	);

	static $has_one = array(
		'DeploymentSurvey' => 'DeploymentSurvey',
		'Member' => 'Member'
	);

	static $singular_name = 'App Development Survey';
	static $plural_name = 'App Development Surveys';

	public function getCountry() {
        return $this->DeploymentSurvey()->PrimaryCountry;
    }

	public function getIndustry() {
        return $this->DeploymentSurvey()->Industry;
    }

	public function getMember() {
        return $this->DeploymentSurvey()->Member();
    }

	public function getOrg() {
        return $this->Org()->Name;
    }

	public static $toolkits_options = array (
		'Deltacloud' => 'Deltacloud (HTTP API)',
		'FOG' => 'FOG (Ruby)',
		'jclouds' => 'jclouds (Java)',
		'OpenStack.net' => 'OpenStack.net (C#)',
		'OpenStack clients' => 'OpenStack clients (Python)',
		'php-opencloud' => 'php-opencloud (PHP)',
		'pkgcloud' => 'pkgcloud (Node.js)',
		'None' => 'None/Wrote my own',
		'Other' => 'Other (please specify)'
	);

	public static $languages_options = array (
		'C/C++' => 'C/C++',
		'C#' => 'C#',
		'Java' => 'Java',
		'Node.js' => 'Node.js',
		'Perl' => 'Perl',
		'PHP' => 'PHP',
		'Python' => 'Python',
		'Ruby' => 'Ruby',
		'Other' => 'Other (please specify)'
	);

	public static $api_format_options = array (
		'JSON' => 'JSON',
		'XML' => 'XML'
	);

	public static $opsys_options = array (
		'Linux' => 'Linux',
		'Mac OS X' => 'Mac OS X',
		'Windows' => 'Windows',
		'Other' => 'Other (please specify)'
	);

	public static $ide_options = array (
		'Eclipse' => 'Eclipse or Eclipse-based IDE',
		'IntelliJ' => 'IntelliJ IDEA or IDEA-based IDE',
		'Sublime' => 'Sublime',
		'Vim' => 'Vim',
		'Visual Studio' => 'Visual Studio',
		'Other' => 'Other (please specify)'
	);

	public static $config_tool_options = array (
		'Ansible' => 'Ansible',
		'Chef' => 'Chef',
		'Cloud Foundry' => 'Cloud Foundry and/or BOSH',
		'Docker' => 'Docker',
		'Heat' => 'OpenStack Orchestration (Heat)',
		'Puppet' => 'Puppet',
		'SaltStack' => 'SaltStack',
		'OpenShift' => 'OpenShift',
		'Other' => 'Other (please specify)'
	);

}