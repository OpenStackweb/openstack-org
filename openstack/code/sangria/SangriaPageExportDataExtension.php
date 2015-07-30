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

/**
 * Class SangriaPageExportDataExtension
 */
final class SangriaPageExportDataExtension extends Extension
{

    public function __construct() {
        parent::__construct();
    }

    public function onBeforeInit()
    {
        Config::inst()->update(get_class($this), 'allowed_actions', array(
            'ExportDataUsersByRole',
            'exportCLAUsers',
            'exportConditionrs',
            'exportGerritUsers',
            'ExportDataGerritUsers',
            'ExportDataCompanyData',
            'ExportSurveyResults',
            'ExportAppDevSurveyResults',
            'exportFoundationMembers',
            'exportCompanyData',
            'exportDupUsers',
            'exportMarketplaceAdmins',
            'ExportAppDevSurveyResultsFlat',
            'ExportSurveyResultsFlat',
            'ExportSpeakersData'
        ));

        Config::inst()->update(get_class($this->owner), 'allowed_actions', array(
            'ExportDataUsersByRole',
            'exportCLAUsers',
            'exportConditionrs',
            'exportGerritUsers',
            'ExportDataGerritUsers',
            'ExportDataCompanyData',
            'ExportSurveyResults',
            'ExportAppDevSurveyResults',
            'exportFoundationMembers',
            'exportCompanyData',
            'exportDupUsers',
            'exportMarketplaceAdmins',
            'ExportAppDevSurveyResultsFlat',
            'ExportSurveyResultsFlat',
            'ExportSpeakersData'
        ));
    }

    public function onAfterInit() {
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');
        Requirements::javascript("themes/openstack/javascript/jquery.validate.custom.methods.js");
        Requirements::javascript('themes/openstack/javascript/sangria/sangria.page.export.data.js');
    }

    function ExportDataUsersByRole()
    {
        $this->Title = 'Export Users By Role';
        return $this->owner->getViewer('ExportUsersByRole')->process($this->owner);
    }

    function exportCLAUsers()
    {
        $params = $this->owner->getRequest()->getVars();
        $fields = $params['fields'];
        $ext = $params['ext'];

        if (!isset($params['fields']) || empty($params['fields']))
            return $this->owner->httpError('412', 'missing required param fields');
        if (!isset($params['ext']) || empty($params['ext']))
            return $this->owner->httpError('412', 'missing required param ext');
        if (!count($fields)) {
            return $this->httpError('412', 'missing required param fields');
        }

        $query = new SQLQuery();
        $query->setFrom('Member');
        $query->addLeftJoin('Group_Members', 'Group_Members.MemberID = Member.ID');
        $query->addLeftJoin('Group', 'Group.ID = Group_Members.GroupID');
        $query->addWhere('Member.GerritID IS NOT NULL');
        $fields['Groups'] = "GROUP_CONCAT(Group.Code, ' | ')";
        $query->setSelect($fields);
        $query->addGroupBy('Member.ID');
        $query->addOrderBy(array('Member.Surname','Member.FirstName'));

        $result = $query->execute();
        $filename = "MembersByRole-" . date('Ymd') . "." . $ext;
        $delimiter = ($ext == 'xls') ? "\t" : "," ;
        return CSVExporter::getInstance()->export($filename, $result, $delimiter);
    }

    function ExportDataGerritUsers()
    {
        $this->Title = 'Export Gerrit Users';
        return $this->owner->getViewer('ExportGerritUsers')->process($this->owner);
    }

    function ExportDataCompanyData()
    {
        $this->Title = 'Export Company Data';
        return $this->owner->getViewer('ExportCompanyData')->process($this->owner);
    }

    function ExportSpeakersData()
    {
        $this->Title = 'Export Speakers Data';
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $summits = Summit::get();

            return $this->owner->getViewer('ExportSpeakersData')->process($this->owner->Customise(array("Summits" => $summits)));
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $summits = Summit::get();
            $selectedSummits = array();
            foreach($summits as $summit) {
                if (isset($_POST["summit_".$summit->ID])) {
                    $selectedSummits[] = $summit->ID;
                }
            }

            $onlyApprovedSpeakers = isset($_POST["onlyApprovedSpeakers"]);
            $affiliation = $_POST["affiliation"];

            $speakersExportQuerySpecification = new SpeakersExportQuerySpecification($selectedSummits, $onlyApprovedSpeakers, $affiliation);
            $speakersExportQuery = new SpeakersExportQuery();
            $res = $speakersExportQuery->handle($speakersExportQuerySpecification);


            $ext = $_POST['ext'];
            $filename = "PresentationSpeakers_" . date('Ymd') . "." . $ext;
            $delimiter = ",";

            return CSVExporter::getInstance()->export($filename, $res->getResult()[0], $delimiter);
        }
    }

    function GetSpeakersData($sort=SangriaPageExportDataExtension::SpeakersSortSummit) {

    }

    function exportConditionrs()
    {

        $params = $this->owner->getRequest()->getVars();
        if (!isset($params['fields']) || empty($params['fields']))
            return $this->owner->httpError('412', 'missing required param fields');

        if (!isset($params['ext']) || empty($params['ext']))
            return $this->owner->httpError('412', 'missing required param ext');

        $fields = $params['fields'];
        $ext = $params['ext'];

        $sanitized_fields = array();

        if (!count($fields)) {
            return $this->httpError('412', 'missing required param fields');
        }

        $allowed_fields = array('ID' => 'ID', 'FirstName' => 'FirstName', 'SurName' => 'SurName', 'Email' => 'Email');

        for ($i = 0; $i < count($fields); $i++) {
            if (!array_key_exists($fields[$i], $allowed_fields))
                return $this->httpError('412', 'invalid field');
            array_push($sanitized_fields, 'M.' . $fields[$i]);
        }

        $sanitized_fields = implode(',', $sanitized_fields);

        $sql = <<< SQL
		SELECT {$sanitized_fields}
		, GROUP_CONCAT(G.Code, ' | ') AS Groups
		FROM Member M
		LEFT JOIN Group_Members GM on GM.MemberID = M.ID
		LEFT JOIN `Group` G  on G.ID = GM.GroupID
		WHERE GerritID IS NOT NULL
		GROUP BY M.ID
		ORDER BY M.SurName, M.FirstName;
SQL;

        $result = DB::query($sql);
        $data = array();
        array_push($fields, 'Groups');
        foreach ($result as $row) {
            $member = array();
            foreach ($fields as $field) {
                $member[$field] = $row[$field];
            }
            array_push($data, $member);
        }

        $filename = "CLAMembers_" . date('Ymd') . "." . $ext;
        $delimiter = ($ext == 'xls') ? "\t" : "," ;

        return CSVExporter::getInstance()->export($filename, $data, $delimiter);
    }

    function exportGerritUsers()
    {
        $params = $this->owner->getRequest()->getVars();
        if (!isset($params['status']) || empty($params['status']))
            return $this->owner->httpError('412', 'missing required param status');

        if (!isset($params['ext']) || empty($params['ext']))
            return $this->owner->httpError('412', 'missing required param ext');

        $status = $params['status'];
        $ext = $params['ext'];

        $sanitized_filters = array();
        $allowed_filter_values = array('foundation-members' => 'foundation-members', 'community-members' => 'community-members');
        for ($i = 0; $i < count($status); $i++) {
            if (!array_key_exists($status[$i], $allowed_filter_values))
                return $this->httpError('412', 'invalid filter value');
            array_push($sanitized_filters, $status[$i]);
        }

        $sanitized_filters = implode("','", $sanitized_filters);
        $sql = <<<SQL

		SELECT M.FirstName,
		   M.Surname,
	       M.Email,
		   COALESCE(NULLIF(M.SecondEmail , ''), 'N/A') AS Secondary_Email ,
	       M.GerritID,
	       COALESCE(NULLIF(M.LastCodeCommit, ''), 'N/A') AS LastCodeCommitDate,
		   g.Code as Member_Status,
		   CASE g.Code WHEN 'foundation-members' THEN (SELECT LA.Created FROM LegalAgreement LA WHERE LA.MemberID =  M.ID and LA.LegalDocumentPageID = 422 LIMIT 1) ELSE 'N/A'END AS FoundationMemberJoinDate,
		   CASE g.Code WHEN 'foundation-members' THEN 'N/A' ELSE ( SELECT ActionDate FROM FoundationMemberRevocationNotification WHERE RecipientID = M.ID AND Action = 'Revoked' LIMIT 1) END AS DateMemberStatusChanged ,
		   GROUP_CONCAT(O.Name, ' | ') AS Company_Affiliations
		FROM Member M
		LEFT JOIN Affiliation A on A.MemberID = M.ID
		LEFT JOIN Org O on O.ID = A.OrganizationID
		INNER JOIN Group_Members gm on gm.MemberID = M.ID
		INNER JOIN `Group` g on g.ID = gm.GroupID and ( g.Code = 'foundation-members' or g.Code = 'community-members')
		WHERE GerritID IS NOT NULL AND g.Code IN ('{$sanitized_filters}')
		GROUP BY M.ID;
SQL;

        $res = DB::query($sql);
        $fields = array('FirstName', 'Surname', 'Email', 'Secondary_Email', 'GerritID', 'LastCodeCommitDate', 'Member_Status', 'FoundationMemberJoinDate', 'DateMemberStatusChanged', 'Company_Affiliations');
        $data = array();

        foreach ($res as $row) {
            $member = array();
            foreach ($fields as $field) {
                $member[$field] = $row[$field];
            }
            array_push($data, $member);
        }

        $filename = "Gerrit_Users_" . date('Ymd') . "." . $ext;
        $delimiter = ($ext == 'xls') ? "\t" : "," ;

        return CSVExporter::getInstance()->export($filename, $data, $delimiter);
    }

    public function Groups()
    {
        $sql = <<<SQL
		SELECT G.Code,G.Title,G.ClassName FROM `Group` G ORDER BY G.Title;
SQL;
        $result = DB::query($sql);

        // let Silverstripe work the magic

        $groups = new ArrayList();

        foreach ($result as $rowArray) {
            // $res: new Product($rowArray)
            $groups->push(new $rowArray['ClassName']($rowArray));
        }

        return $groups;
    }

    public function MarketplaceTypes()
    {
        $sql = <<<SQL
		SELECT Name,Slug,AdminGroupID FROM MarketPlaceType WHERE Active = 1 ORDER BY AdminGroupID;
SQL;
        $result = DB::query($sql);

        // let Silverstripe work the magic

        $types = new ArrayList();

        foreach ($result as $rowArray) {
            $types->push($rowArray);
        }

        return $types;
    }

    // Export CSV of all Deployment Surveys and Associated Deployments

    private function ExportSurveyResultsData(){
        SangriaPage_Controller::generateDateFilters('s');
        $range = Controller::curr()->getRequest()->getVar('Range');
        $range_filter = '';
        if (!empty($range)) {
            $range_filter = ($range == SurveyType::MARCH_2015) ? "AND s.Created >= '" . SURVEY_START_DATE . "'" : "AND s.Created < '" . SURVEY_START_DATE . "'";
        }
        $surveyQuery = "SELECT
				s.ID as SurveyID,
				s.Created as SurveyCreated,
                s.UpdateDate as SurveyEdited,
                o.Name as OrgName,
                o.ID as OrgID ,
                d.ID as DeploymentID,
                d.Created as DeploymentCreated,
                d.UpdateDate as DeploymentEdited,
				m.FirstName,
                m.Surname,
                m.Email,
                s.Title,
                s.Industry,
                s.OtherIndustry,
                s.PrimaryCity,
                s.PrimaryState,
                s.PrimaryCountry,
                s.OrgSize,
                s.OpenStackInvolvement,
                s.InformationSources,
                s.OtherInformationSources,
                s.FurtherEnhancement,
                s.FoundationUserCommitteePriorities,
                s.UserGroupMember,
                s.UserGroupName,
                s.OkToContact,
                s.BusinessDrivers,
                s.OtherBusinessDrivers,
                s.WhatDoYouLikeMost,
                s.OpenStackRecommendRate as NetPromoter,
                s.OpenStackRecommendation,
                s.OpenStackActivity,
                s.OpenStackRelationship,
                s.ITActivity,
                s.InterestedUsingContainerTechnology,
                s.ContainerRelatedTechnologies,
                d.Label,
                d.IsPublic,
                d.DeploymentType,
                d.ProjectsUsed,
                d.CurrentReleases,
                d.DeploymentStage,
				d.NumCloudUsers,
				d.APIFormats,
				d.Hypervisors,
				d.OtherHypervisor,
                d.BlockStorageDrivers,
                d.OtherBlockStorageDriver,
                d.NetworkDrivers,
                d.OtherNetworkDriver,
				d.IdentityDrivers,
				d.OtherIndentityDriver,
                d.SupportedFeatures,
                d.ComputeNodes,
                d.ComputeCores,
                d.ComputeInstances,
                d.BlockStorageTotalSize,
                d.ObjectStorageSize,
                d.ObjectStorageNumObjects,
                d.NetworkNumIPs,
                d.WorkloadsDescription,
                d.OtherWorkloadsDescription,
                d.WhyNovaNetwork,
                d.OtherWhyNovaNetwork,
                d.DeploymentTools,
                d.OtherDeploymentTools,
                d.OperatingSystems as DeploymentOperatingSystems,
                d.OtherOperatingSystems DeploymentOtherOperatingSystems,
                d.SwiftGlobalDistributionFeatures,
                d.SwiftGlobalDistributionFeaturesUsesCases,
                d.OtherSwiftGlobalDistributionFeaturesUsesCases,
                d.Plans2UseSwiftStoragePolicies,
                d.OtherPlans2UseSwiftStoragePolicies,
                d.UsedDBForOpenStackComponents,
                d.OtherUsedDBForOpenStackComponents,
                d.ToolsUsedForYourUsers,
                d.OtherToolsUsedForYourUsers,
                d.Reason2Move2Ceilometer,
                d.CountriesPhysicalLocation,
				d.CountriesUsersLocation,
				d.ServicesDeploymentsWorkloads,
				d.OtherServicesDeploymentsWorkloads,
				d.EnterpriseDeploymentsWorkloads,
				d.OtherEnterpriseDeploymentsWorkloads,
				d.HorizontalWorkloadFrameworks,
				d.OtherHorizontalWorkloadFrameworks,
				d.UsedPackages,
				d.CustomPackagesReason,
				d.OtherCustomPackagesReason,
				d.PaasTools,
				d.OtherPaasTools,
				d.OtherSupportedFeatures,
				d.InteractingClouds,
				d.OtherInteractingClouds
            from DeploymentSurvey s
                left outer join Member m on (s.MemberID = m.ID)
                left outer join Deployment d on (d.DeploymentSurveyID = s.ID)
                left outer join Org o on (s.OrgID = o.ID)
            where " . SangriaPage_Controller::$date_filter_query . $range_filter . " order by s.ID;";

        $res = DB::query($surveyQuery);

        return $res;
    }

    function ExportSurveyResults()
    {
        $fileDate = date('Ymdhis');

        $res = $this->ExportSurveyResultsData();

        $fields = array(
            'SurveyID',
            'SurveyCreated',
            'SurveyEdited',
            'OrgName',
            'OrgID',
            'DeploymentID',
            'DeploymentCreated',
            'DeploymentEdited',
            'FirstName',
            'Surname',
            'Email',
            'Title',
            'Industry',
            'OtherIndustry',
            'PrimaryCity',
            'PrimaryState',
            'PrimaryCountry',
            'OrgSize',
            'OpenStackInvolvement',
            'InformationSources',
            'OtherInformationSources',
            'FurtherEnhancement',
            'FoundationUserCommitteePriorities',
            'UserGroupMember',
            'UserGroupName',
            'OkToContact',
            'BusinessDrivers',
            'OtherBusinessDrivers',
            'WhatDoYouLikeMost',
            'NetPromoter',
            'OpenStackRecommendation',
            'OpenStackActivity',
            'OpenStackRelationship',
            'ITActivity',
            'InterestedUsingContainerTechnology',
            'ContainerRelatedTechnologies',
            'Label',
            'IsPublic',
            'DeploymentType',
            'ProjectsUsed',
            'CurrentReleases',
            'DeploymentStage',
            'NumCloudUsers',
            'APIFormats',
            'Hypervisors',
            'OtherHypervisor',
            'BlockStorageDrivers',
            'OtherBlockStorageDriver',
            'NetworkDrivers',
            'OtherNetworkDriver',
            'IdentityDrivers',
            'OtherIndentityDriver',
            'SupportedFeatures',
            'ComputeNodes',
            'ComputeCores',
            'ComputeInstances',
            'BlockStorageTotalSize',
            'ObjectStorageSize',
            'ObjectStorageNumObjects',
            'NetworkNumIPs',
            'WorkloadsDescription',
            'OtherWorkloadsDescription',
            'WhyNovaNetwork',
            'OtherWhyNovaNetwork',
            'DeploymentTools',
            'OtherDeploymentTools',
            'DeploymentOperatingSystems',
            'DeploymentOtherOperatingSystems',
            'SwiftGlobalDistributionFeatures',
            'SwiftGlobalDistributionFeaturesUsesCases',
            'OtherSwiftGlobalDistributionFeaturesUsesCases',
            'Plans2UseSwiftStoragePolicies',
            'OtherPlans2UseSwiftStoragePolicies',
            'UsedDBForOpenStackComponents',
            'OtherUsedDBForOpenStackComponents',
            'ToolsUsedForYourUsers',
            'OtherToolsUsedForYourUsers',
            'Reason2Move2Ceilometer',
            'CountriesPhysicalLocation',
            'CountriesUsersLocation',
            'ServicesDeploymentsWorkloads',
            'OtherServicesDeploymentsWorkloads',
            'EnterpriseDeploymentsWorkloads',
            'OtherEnterpriseDeploymentsWorkloads',
            'HorizontalWorkloadFrameworks',
            'OtherHorizontalWorkloadFrameworks',
            'UsedPackages',
            'CustomPackagesReason',
            'OtherCustomPackagesReason',
            'PaasTools',
            'OtherPaasTools',
            'OtherSupportedFeatures',
            'InteractingClouds',
            'OtherInteractingClouds'
        );
        $data = array();

        foreach ($res as $row) {
            $member = array();
            foreach ($fields as $field) {
                $member[$field] = $row[$field];
            }
            array_push($data, $member);
        }

        $filename = "Survey_" . $fileDate . ".csv";

        return CSVExporter::getInstance()->export($filename, $data, ',');
    }

    function ExportSurveyResultsFlat()
    {
        $fileDate = date('Ymdhis');

        $res = $this->ExportSurveyResultsData();

        $range = Controller::curr()->getRequest()->getVar('Range');

        $fields = array(
            'SurveyID',
            'SurveyCreated',
            'SurveyEdited',
            'OrgName',
            'OrgID',
            'DeploymentID',
            'DeploymentCreated',
            'DeploymentEdited',
            'FirstName',
            'Surname',
            'Email',
            'Title',
            'Industry',
            'OtherIndustry',
            'PrimaryCity',
            'PrimaryState',
            'PrimaryCountry',
            'OrgSize',
            'OpenStackInvolvement',
            'InformationSources',
            'OtherInformationSources',
            'FurtherEnhancement',
            'FoundationUserCommitteePriorities',
            'OkToContact',
            'BusinessDrivers',
            'OtherBusinessDrivers',
            'WhatDoYouLikeMost',
            'NetPromoter',
            'OpenStackActivity',
            'OpenStackRelationship',
            'ITActivity',
            'InterestedUsingContainerTechnology',
            'ContainerRelatedTechnologies',
            'Label',
            'IsPublic',
            'DeploymentType',
            'ProjectsUsed',
            'CurrentReleases',
            'DeploymentStage',
            'NumCloudUsers',
            'Hypervisors',
            'OtherHypervisor',
            'BlockStorageDrivers',
            'OtherBlockStorageDriver',
            'NetworkDrivers',
            'OtherNetworkDriver',
            'IdentityDrivers',
            'OtherIndentityDriver',
            'ComputeNodes',
            'ComputeCores',
            'ComputeInstances',
            'BlockStorageTotalSize',
            'ObjectStorageSize',
            'ObjectStorageNumObjects',
            'NetworkNumIPs',
            'WhyNovaNetwork',
            'OtherWhyNovaNetwork',
            'DeploymentTools',
            'OtherDeploymentTools',
            'DeploymentOperatingSystems',
            'DeploymentOtherOperatingSystems',
            'SwiftGlobalDistributionFeatures',
            'SwiftGlobalDistributionFeaturesUsesCases',
            'OtherSwiftGlobalDistributionFeaturesUsesCases',
            'Plans2UseSwiftStoragePolicies',
            'OtherPlans2UseSwiftStoragePolicies',
            'UsedDBForOpenStackComponents',
            'OtherUsedDBForOpenStackComponents',
            'ToolsUsedForYourUsers',
            'OtherToolsUsedForYourUsers',
            'Reason2Move2Ceilometer',
            'CountriesPhysicalLocation',
            'CountriesUsersLocation',
            'ServicesDeploymentsWorkloads',
            'OtherServicesDeploymentsWorkloads',
            'EnterpriseDeploymentsWorkloads',
            'OtherEnterpriseDeploymentsWorkloads',
            'HorizontalWorkloadFrameworks',
            'OtherHorizontalWorkloadFrameworks',
            'UsedPackages',
            'CustomPackagesReason',
            'OtherCustomPackagesReason',
            'PaasTools',
            'OtherPaasTools',
            'InteractingClouds',
            'OtherInteractingClouds',
            'SupportedFeatures',
            'OtherSupportedFeatures',
        );


        $flat_fields_V2 = array(
            //survey
            'Industry' => DeploymentSurveyOptions::$industry_options,
            'OpenStackInvolvement' => DeploymentSurveyOptions::$openstack_involvement_options,
            'BusinessDrivers' => DeploymentSurveyOptions::$business_drivers_options,
            'InformationSources' => DeploymentSurveyOptions::$information_options,
            'ContainerRelatedTechnologies' => DeploymentSurveyOptions::$container_related_technologies,
            //app dev survey
            'Toolkits' => AppDevSurveyOptions::$toolkits_options,
            'ProgrammingLanguages' => AppDevSurveyOptions::$languages_options,
            'APIFormats' => AppDevSurveyOptions::$api_format_options,
            'GuestOperatingSystems' => AppDevSurveyOptions::$opsys_options,
            //deployment
            'ProjectsUsed' => DeploymentOptions::$projects_used_options,
            'CurrentReleases' => DeploymentOptions::$current_release_options,
            'ServicesDeploymentsWorkloads' => DeploymentOptions::$services_deployment_workloads_options,
            'EnterpriseDeploymentsWorkloads' => DeploymentOptions::$enterprise_deployment_workloads_options,
            'HorizontalWorkloadFrameworks' => DeploymentOptions::$horizontal_workload_framework_options,
            'UsedPackages' => DeploymentOptions::$used_packages_options,
            'CustomPackagesReason' => DeploymentOptions::$custom_package_reason_options,
            'DeploymentTools' => DeploymentOptions::$deployment_tools_options,
            'PaasTools' => DeploymentOptions::$paas_tools_options,
            'Hypervisors' => DeploymentOptions::$hypervisors_options,
            'SupportedFeatures' => DeploymentOptions::$deployment_features_options,
            'UsedDBForOpenStackComponents' => DeploymentOptions::$used_db_for_openstack_components_options,
            'NetworkDrivers' => DeploymentOptions::$network_driver_options,
            'IdentityDrivers' => DeploymentOptions::$identity_driver_options,
            'BlockStorageDrivers' => DeploymentOptions::$block_storage_divers_options,
            'InteractingClouds' => DeploymentOptions::$interacting_clouds_options,
            'WhyNovaNetwork' => DeploymentOptions::$why_nova_network_options,
            'OpenStackActivity' => DeploymentSurveyOptions::$activities_options,
            'DeploymentOperatingSystems' => DeploymentOptions::$operating_systems_options,
        );

        $flat_fields_V1 = array(
            //survey
            'Industry' => DeploymentSurveyArchiveOptions::$industry_options,
            'OpenStackInvolvement' => DeploymentSurveyArchiveOptions::$openstack_involvement_options,
            'BusinessDrivers' => DeploymentSurveyArchiveOptions::$business_drivers_options,
            'InformationSources' => DeploymentSurveyArchiveOptions::$information_options,
            'ContainerRelatedTechnologies' => DeploymentSurveyOptions::$container_related_technologies,
            //app dev survey
            'Toolkits' => AppDevSurveyArchiveOptions::$toolkits_options,
            'ProgrammingLanguages' => AppDevSurveyArchiveOptions::$languages_options,
            'APIFormats' => AppDevSurveyArchiveOptions::$api_format_options,
            'DeploymentOperatingSystems' => AppDevSurveyArchiveOptions::$opsys_options,
            'GuestOperatingSystems' => AppDevSurveyArchiveOptions::$opsys_options,
            //deployment
            'ProjectsUsed' => DeploymentArchiveOptions::$projects_used_options,
            'CurrentReleases' => DeploymentArchiveOptions::$current_release_options,
            'ServicesDeploymentsWorkloads' => DeploymentOptions::$services_deployment_workloads_options,
            'EnterpriseDeploymentsWorkloads' => DeploymentOptions::$enterprise_deployment_workloads_options,
            'HorizontalWorkloadFrameworks' => DeploymentOptions::$horizontal_workload_framework_options,
            'UsedPackages' => DeploymentOptions::$used_packages_options,
            'CustomPackagesReason' => DeploymentOptions::$custom_package_reason_options,
            'DeploymentTools' => DeploymentArchiveOptions::$deployment_tools_options,
            'PaasTools' => DeploymentOptions::$paas_tools_options,
            'Hypervisors' => DeploymentArchiveOptions::$hypervisors_options,
            'SupportedFeatures' => DeploymentArchiveOptions::$deployment_features_options,
            'UsedDBForOpenStackComponents' => DeploymentArchiveOptions::$used_db_for_openstack_components_options,
            'NetworkDrivers' => DeploymentArchiveOptions::$network_driver_options,
            'IdentityDrivers' => DeploymentArchiveOptions::$identity_driver_options,
            'BlockStorageDrivers' => DeploymentArchiveOptions::$block_storage_divers_options,
            'InteractingClouds' => DeploymentOptions::$interacting_clouds_options,
            'WhyNovaNetwork' => DeploymentArchiveOptions::$why_nova_network_options,
            'OpenStackActivity' => DeploymentSurveyOptions::$activities_options,
        );

        $flat_fields = ($range == SurveyType::MARCH_2015)?$flat_fields_V2:$flat_fields_V1;

        $file_data = array();

        foreach ($res as $row) {
            $line = array();
            foreach ($fields as $field) {
                if (isset($flat_fields[$field])) {
                    $options = $flat_fields[$field];
                    $values  = $row[$field];
                    foreach ($options as $k => $v) {
                        if($field === 'BusinessDrivers'){
                            $business_drivers = (empty($values))? array():explode(',',$values);
                            $business_drivers = (count($business_drivers) > 0) ? array_combine($business_drivers,$business_drivers): array();
                            $index = false;
                            if(isset($business_drivers[$k])){
                                $index = array_search($k,array_keys($business_drivers));
                            }

                            $line[$field . ' - ' . $k] = $index === false ? '0': ($index+1);
                        }
                        else
                            $line[$field . ' - ' . $k] = strpos($values, $k) !== false ?  '1' : '0';
                    }
                } else {
                    $line[$field] = $row[$field];
                }
            }
            array_push($file_data, $line);
        }

        $version  = $range == SurveyType::MARCH_2015 ? 'v2':'v1';
        $filename = "Survey_Flat_" . $version. '_' . $fileDate . ".csv";

        return CSVExporter::getInstance()->export($filename, $file_data, ',');
    }

    // Export CSV of all App Dev Surveys

    private function ExportAppDevSurveyData()
    {
        SangriaPage_Controller::generateDateFilters('s');
        $range = Controller::curr()->getRequest()->getVar('Range');
        $range_filter = '';
        if (!empty($range)) {
            $range_filter = ($range == SurveyType::MARCH_2015) ? "AND s.Created >= '" . SURVEY_START_DATE . "'" : "AND s.Created < '" . SURVEY_START_DATE . "'";
        }

        $surveyQuery = "select s.ID as SurveyID, s.Created as SurveyCreated,
                s.LastEdited as SurveyEdited, o.Name as OrgName, o.ID as OrgID,  a.ID as AppSurveyID,
                a.Created as AppSurveyCreated, a.LastEdited as AppSurveyEdited, m.FirstName,
                m.Surname, m.Email, s.Title, s.Industry, s.OtherIndustry, s.PrimaryCity,
                s.PrimaryState, s.PrimaryCountry, s.OrgSize, s.OpenStackInvolvement,
                s.InformationSources, s.OtherInformationSources, s.FurtherEnhancement,
                s.FoundationUserCommitteePriorities, s.UserGroupMember, s.UserGroupName,
                s.OkToContact, s.BusinessDrivers, s.OtherBusinessDrivers, s.WhatDoYouLikeMost,
                a.Toolkits, a.OtherToolkits, a.ProgrammingLanguages, a.OtherProgrammingLanguages,
                a.APIFormats, a.DevelopmentEnvironments, a.OtherDevelopmentEnvironments,
                a.OperatingSystems as ApplicationDevelopmentOperatingSystems, a.OtherOperatingSystems as ApplicationDevelopmentOtherOperatingSystems , a.ConfigTools, a.OtherConfigTools,
                a.StateOfOpenStack, a.DocsPriority, a.InteractionWithOtherClouds, a.OtherAPIFormats, a.GuestOperatingSystems,
                a.OtherGuestOperatingSystems, a.StruggleDevelopmentDeploying, a.OtherDocsPriority
            from DeploymentSurvey s
                right join AppDevSurvey a on (a.DeploymentSurveyID = s.ID)
                left outer join Member m on (a.MemberID = m.ID)
                left outer join Org o on (s.OrgID = o.ID)
            where " . SangriaPage_Controller::$date_filter_query . $range_filter . "
            order by s.ID;";

        return DB::query($surveyQuery);
    }

    function ExportAppDevSurveyResults()
    {
        $fileDate = date('Ymdhis');
        $res = $this->ExportAppDevSurveyData();
        $fields = array('SurveyID', 'SurveyCreated', 'SurveyEdited', 'OrgName', 'OrgID', 'AppSurveyID', 'AppSurveyCreated', 'AppSurveyEdited', 'FirstName',
            'Surname', 'Email', 'Title', 'Industry', 'OtherIndustry', 'PrimaryCity', 'PrimaryState', 'PrimaryCountry', 'OrgSize', 'OpenStackInvolvement', 'InformationSources',
            'OtherInformationSources', 'FurtherEnhancement', 'FoundationUserCommitteePriorities', 'UserGroupMember', 'UserGroupName', 'OkToContact', 'BusinessDrivers',
            'OtherBusinessDrivers', 'WhatDoYouLikeMost', 'Toolkits', 'OtherToolkits', 'ProgrammingLanguages', 'OtherProgrammingLanguages', 'APIFormats', 'DevelopmentEnvironments', 'OtherDevelopmentEnvironments',
            'ApplicationDevelopmentOperatingSystems', 'ApplicationDevelopmentOtherOperatingSystems', 'ConfigTools', 'OtherConfigTools', 'StateOfOpenStack', 'DocsPriority', 'InteractionWithOtherClouds', 'OtherAPIFormats', 'GuestOperatingSystems', 'OtherGuestOperatingSystems', 'StruggleDevelopmentDeploying', 'OtherDocsPriority');

        $data = array();

        foreach ($res as $row) {
            $member = array();
            foreach ($fields as $field) {
                $member[$field] = $row[$field];
            }
            array_push($data, $member);
        }

        $filename = "App_Dev_Surveys_" . $fileDate . ".csv";

        return CSVExporter::getInstance()->export($filename, $data, ',');
    }

    function ExportAppDevSurveyResultsFlat()
    {

        $fileDate = date('Ymdhis');

        $res = $this->ExportAppDevSurveyData();

        $range = Controller::curr()->getRequest()->getVar('Range');

        $flat_fields_V2 = array(
            //survey
            'Industry' => DeploymentSurveyOptions::$industry_options,
            'OpenStackInvolvement' => DeploymentSurveyOptions::$openstack_involvement_options,
            'BusinessDrivers' => DeploymentSurveyOptions::$business_drivers_options,
            'InformationSources' => DeploymentSurveyOptions::$information_options,
            'ContainerRelatedTechnologies' => DeploymentSurveyOptions::$container_related_technologies,
            //app dev survey
            'Toolkits' => AppDevSurveyOptions::$toolkits_options,
            'ProgrammingLanguages' => AppDevSurveyOptions::$languages_options,
            'APIFormats' => AppDevSurveyOptions::$api_format_options,
            'ApplicationDevelopmentOperatingSystems' => AppDevSurveyOptions::$opsys_options,
            'GuestOperatingSystems' => AppDevSurveyOptions::$opsys_options,
            //deployment
            'ProjectsUsed' => DeploymentOptions::$projects_used_options,
            'CurrentReleases' => DeploymentOptions::$current_release_options,
            'ServicesDeploymentsWorkloads' => DeploymentOptions::$services_deployment_workloads_options,
            'EnterpriseDeploymentsWorkloads' => DeploymentOptions::$enterprise_deployment_workloads_options,
            'HorizontalWorkloadFrameworks' => DeploymentOptions::$horizontal_workload_framework_options,
            'UsedPackages' => DeploymentOptions::$used_packages_options,
            'CustomPackagesReason' => DeploymentOptions::$custom_package_reason_options,
            'DeploymentTools' => DeploymentOptions::$deployment_tools_options,
            'PaasTools' => DeploymentOptions::$paas_tools_options,
            'Hypervisors' => DeploymentOptions::$hypervisors_options,
            'SupportedFeatures' => DeploymentOptions::$deployment_features_options,
            'UsedDBForOpenStackComponents' => DeploymentOptions::$used_db_for_openstack_components_options,
            'NetworkDrivers' => DeploymentOptions::$network_driver_options,
            'IdentityDrivers' => DeploymentOptions::$identity_driver_options,
            'BlockStorageDrivers' => DeploymentOptions::$block_storage_divers_options,
            'InteractingClouds' => DeploymentOptions::$interacting_clouds_options,
            'WhyNovaNetwork' => DeploymentOptions::$why_nova_network_options,
            'OpenStackActivity' => DeploymentSurveyOptions::$activities_options,
        );

        $flat_fields_V1 = array(
            //survey
            'Industry' => DeploymentSurveyArchiveOptions::$industry_options,
            'OpenStackInvolvement' => DeploymentSurveyArchiveOptions::$openstack_involvement_options,
            'BusinessDrivers' => DeploymentSurveyArchiveOptions::$business_drivers_options,
            'InformationSources' => DeploymentSurveyArchiveOptions::$information_options,
            'ContainerRelatedTechnologies' => DeploymentSurveyOptions::$container_related_technologies,
            //app dev survey
            'Toolkits' => AppDevSurveyArchiveOptions::$toolkits_options,
            'ProgrammingLanguages' => AppDevSurveyArchiveOptions::$languages_options,
            'APIFormats' => AppDevSurveyArchiveOptions::$api_format_options,
            'ApplicationDevelopmentOperatingSystems' => AppDevSurveyArchiveOptions::$opsys_options,
            'GuestOperatingSystems' => AppDevSurveyArchiveOptions::$opsys_options,
            //deployment
            'ProjectsUsed' => DeploymentArchiveOptions::$projects_used_options,
            'CurrentReleases' => DeploymentArchiveOptions::$current_release_options,
            'ServicesDeploymentsWorkloads' => DeploymentOptions::$services_deployment_workloads_options,
            'EnterpriseDeploymentsWorkloads' => DeploymentOptions::$enterprise_deployment_workloads_options,
            'HorizontalWorkloadFrameworks' => DeploymentOptions::$horizontal_workload_framework_options,
            'UsedPackages' => DeploymentOptions::$used_packages_options,
            'CustomPackagesReason' => DeploymentOptions::$custom_package_reason_options,
            'DeploymentTools' => DeploymentArchiveOptions::$deployment_tools_options,
            'PaasTools' => DeploymentOptions::$paas_tools_options,
            'Hypervisors' => DeploymentArchiveOptions::$hypervisors_options,
            'SupportedFeatures' => DeploymentArchiveOptions::$deployment_features_options,
            'UsedDBForOpenStackComponents' => DeploymentArchiveOptions::$used_db_for_openstack_components_options,
            'NetworkDrivers' => DeploymentArchiveOptions::$network_driver_options,
            'IdentityDrivers' => DeploymentArchiveOptions::$identity_driver_options,
            'BlockStorageDrivers' => DeploymentArchiveOptions::$block_storage_divers_options,
            'InteractingClouds' => DeploymentOptions::$interacting_clouds_options,
            'WhyNovaNetwork' => DeploymentArchiveOptions::$why_nova_network_options,
            'OpenStackActivity' => DeploymentSurveyOptions::$activities_options,
        );

        $flat_fields = ($range == SurveyType::MARCH_2015)?$flat_fields_V2:$flat_fields_V1;

        $fields = array('SurveyID', 'SurveyCreated', 'SurveyEdited', 'OrgName', 'OrgID', 'AppSurveyID', 'AppSurveyCreated', 'AppSurveyEdited', 'FirstName',
            'Surname', 'Email', 'Title', 'Industry', 'OtherIndustry', 'PrimaryCity', 'PrimaryState', 'PrimaryCountry', 'OrgSize', 'OpenStackInvolvement', 'InformationSources',
            'OtherInformationSources', 'FurtherEnhancement', 'FoundationUserCommitteePriorities', 'OkToContact', 'BusinessDrivers',
            'OtherBusinessDrivers', 'WhatDoYouLikeMost', 'Toolkits', 'OtherToolkits', 'ProgrammingLanguages', 'OtherProgrammingLanguages', 'DevelopmentEnvironments',
            'OtherDevelopmentEnvironments', 'ApplicationDevelopmentOperatingSystems', 'ApplicationDevelopmentOtherOperatingSystems', 'ConfigTools', 'OtherConfigTools', 'StateOfOpenStack', 'DocsPriority', 'InteractionWithOtherClouds',
            'GuestOperatingSystems', 'OtherGuestOperatingSystems', 'StruggleDevelopmentDeploying', 'OtherDocsPriority', 'APIFormats', 'OtherAPIFormats');


        $file_data = array();

        foreach ($res as $row) {
            $line = array();
            foreach ($fields as $field) {
                if (isset($flat_fields[$field])) {
                    $options = $flat_fields[$field];
                    $values  = $row[$field];
                    foreach ($options as $k => $v) {
                        if($field === 'BusinessDrivers'){
                            $business_drivers = (empty($values))? array():explode(',',$values);
                            $business_drivers = (count($business_drivers) > 0) ? array_combine($business_drivers,$business_drivers): array();
                            $index = false;
                            if(isset($business_drivers[$k])){
                                $index = array_search($k,array_keys($business_drivers));
                            }

                            $line[$field . ' - ' . $k] = $index === false ? '0': ($index+1);
                        }
                        else
                            $line[$field . ' - ' . $k] = strpos($values, $k) !== false ?  '1' : '0';
                    }
                } else {
                    $line[$field] = $row[$field];
                }
            }
            array_push($file_data, $line);
        }

        $version  = $range == SurveyType::MARCH_2015 ? 'v2':'v1';
        $filename = "App_Dev_Surveys_Flat_" .$version.'_'. $fileDate . ".csv";

        return CSVExporter::getInstance()->export($filename, $file_data, ',');
    }

    function exportFoundationMembers()
    {
        $params = $this->owner->getRequest()->getVars();
        if (!isset($params['fields']) || empty($params['fields']))
            return $this->owner->httpError('412', 'missing required param fields');

        if (!isset($params['ext']) || empty($params['ext']))
            return $this->owner->httpError('412', 'missing required param ext');

        $fields = $params['fields'];
        $ext = $params['ext'];

        $sanitized_fields = array();

        if (!count($fields)) {
            return $this->owner->httpError('412', 'missing required param fields');
        }

        $allowed_fields = array('ID' => 'ID', 'FirstName' => 'FirstName', 'SurName' => 'SurName', 'Email' => 'Email');

        for ($i = 0; $i < count($fields); $i++) {
            if (!array_key_exists($fields[$i], $allowed_fields))
                return $this->httpError('412', 'invalid field');
            array_push($sanitized_fields, 'Member.' . $fields[$i]);
        }

        $query = new SQLQuery();

        $query->setFrom('Member');
        $query->setSelect($sanitized_fields);
        $query->addInnerJoin('Group_Members', 'Group_Members.MemberID = Member.ID');
        $query->addInnerJoin('Group', "Group.ID = Group_Members.GroupID AND Group.Code='foundation-members'");
        $query->setOrderBy('SurName,FirstName');

        $result = $query->execute();

        $data = array();

        foreach ($result as $row) {
            $member = array();
            foreach ($fields as $field) {
                $member[$field] = $row[$field];
            }
            array_push($data, $member);
        }

        $filename = "Foundation_Members_" . date('Ymd') . "." . $ext;

        return CSVExporter::getInstance()->export($filename, $data, ',');
    }

    function exportCompanyData  ()
    {
        $params = $this->owner->getRequest()->getVars();

        if (!isset($params['report_name']) || empty($params['report_name']) || !count($params['report_name']))
            return $this->owner->httpError('412', 'missing required param report_name');

        if (!isset($params['extension']) || empty($params['extension']))
            return $this->owner->httpError('412', 'missing required param extension');

        $report_name = (isset($params['report_name'])) ? $params['report_name'] : '';
        $fields = (isset($params['fields'])) ? $params['fields'] : array();
        $ext = $params['extension'];

        $query = new SQLQuery();

        if($report_name) {
            switch($report_name) {
                case 'sponsorship_type' :
                    $query->setFrom('Company');
                    $query->addLeftJoin('SummitSponsorPage_Companies', 'SummitSponsorPage_Companies.CompanyID = Company.ID');
                    $query->addLeftJoin('Summit', 'Summit.ID = SummitSponsorPage_Companies.SummitID');
                    $query->addWhere('Summit.Active','1');
                    $fields = array_merge($fields,array('Sponsorship'=>'SummitSponsorPage_Companies.SponsorshipType','Summit ID'=>'Summit.ID'));

                    $query->setSelect($fields);
                    $query->addOrderBy('SummitSponsorPage_Companies.SponsorshipType');

                    $filename = "Sponsorship_Levels_" . date('Ymd') . "." . $ext;
                    break;
                case 'member_level' :
                    $query->setFrom('Company');
                    array_push($fields, 'Company.MemberLevel');
                    $query->setSelect($fields);

                    $filename = "Foundation_Levels_" . date('Ymd') . "." . $ext;
                    break;
                case 'users_roles' :
                    $query->setFrom('Company');
                    $query->addInnerJoin('Company_Administrators', 'Company_Administrators.CompanyID = Company.ID');
                    $query->addLeftJoin('Member', 'Member.ID = Company_Administrators.MemberID');
                    $query->addLeftJoin('Group', 'Group.ID = Company_Administrators.GroupID');
                    array_push($fields, 'Group.Title');
                    $query->setSelect($fields);
                    $query->addOrderBy('Company.Name');

                    $filename = "User_Roles_" . date('Ymd') . "." . $ext;
                    break;
                case 'affiliates' :
                    $query->setFrom('Org');
                    $query->addLeftJoin('Affiliation', 'Affiliation.OrganizationID = Org.ID');
                    $query->addLeftJoin('Member', 'Member.ID = Affiliation.MemberID');
                    $fields = array_merge($fields,array('Is Current'=>'Affiliation.Current','Job Title'=>'Affiliation.JobTitle'));
                    $query->setSelect($fields);
                    $query->addOrderBy('Org.Name');

                    $filename = "Employees_Affiliates_" . date('Ymd') . "." . $ext;
                    break;
                case 'deployments' :
                    $query->setFrom('Org');
                    $query->addInnerJoin('Deployment', 'Deployment.OrgID = Org.ID');
                    $custom_fields = array('Creation'=>'Deployment.Created','Edited'=>'Deployment.LastEdited',
                                           'Label'=>'Deployment.Label','Is Public'=>'Deployment.IsPublic');
                    $fields = array_merge($fields,$custom_fields);
                    $query->setSelect($fields);
                    $query->selectField("CONCAT('http://openstack.org/sangria/DeploymentDetails/',Deployment.ID)","Link");
                    $query->addOrderBy('Org.Name');

                    $filename = "Deployments_" . date('Ymd') . "." . $ext;
                    break;
                case 'deployment_surveys' :
                    $query->setFrom('Org');
                    $query->addLeftJoin('DeploymentSurvey', 'DeploymentSurvey.OrgID = Org.ID');
                    $query->addLeftJoin('Member', 'DeploymentSurvey.MemberID = Member.ID');
                    $custom_fields = array('Creation'=>'DeploymentSurvey.Created','Edited'=>'DeploymentSurvey.LastEdited',
                                        'Title'=>'DeploymentSurvey.Title','City'=>'DeploymentSurvey.PrimaryCity',
                                        'State'=>'DeploymentSurvey.PrimaryState','Country'=>'DeploymentSurvey.PrimaryCountry',
                                        'Org Size'=>'DeploymentSurvey.OrgSize','Is Group Member'=>'DeploymentSurvey.UserGroupMember',
                                        'Group Name'=>'DeploymentSurvey.UserGroupName','Ok to Contact'=>'DeploymentSurvey.OkToContact');

                    //insert custom fields after org fields
                    $pos = -1;
                    foreach ($fields as $field) {
                        $pos++;
                        if (strpos($field,'Org') !== false) continue;
                        else {
                            array_splice($fields,$pos,0,$custom_fields);
                            break;
                        }
                    }

                    $query->setSelect($fields);
                    $query->selectField("CONCAT('http://openstack.org/sangria/SurveyDetails/',DeploymentSurvey.ID)","Link");

                    $filename = "Deployment_Surveys" . date('Ymd') . "." . $ext;
                    break;
                case 'speakers' :
                    $query->setFrom('PresentationSpeaker');
                    $query->addLeftJoin('Affiliation', 'Affiliation.MemberID = PresentationSpeaker.MemberID');
                    $query->addLeftJoin('Org', 'Affiliation.OrganizationID = Org.ID');
                    $query->addLeftJoin('Summit', 'Summit.ID = PresentationSpeaker.SummitID');
                    $custom_fields = array('Speaker Name'=>'PresentationSpeaker.FirstName',
                                        'Speaker Surname'=>'PresentationSpeaker.LastName','Summit'=>'Summit.Name');
                    $fields = array_merge($fields,$custom_fields);
                    $query->setSelect($fields);

                    $filename = "Speakers_" . date('Ymd') . "." . $ext;
                    break;
            }
        }

        //die($query->sql());
        $result = $query->execute();
        $delimiter = ($ext == 'xls') ? "\t" : "," ;

        return CSVExporter::getInstance()->export($filename, $result, $delimiter);
    }

    public function exportDupUsers()
    {

        $fileDate = date('Ymdhis');

        SangriaPage_Controller::generateDateFilters('s');

        $sql = <<< SQL
select FirstName, Surname, count(FirstName) AS Qty , group_concat(Email SEPARATOR '|') AS Emails,group_concat(ID SEPARATOR '|') AS MemberIds
from Member
group by FirstName, Surname
having count(FirstName) > 1
order by FirstName, Surname;
SQL;

        $res = DB::query($sql);

        $fields = array('FirstName', 'Surname', 'Qty', 'Emails', 'MemberIds');
        $data = array();

        foreach ($res as $row) {
            $member = array();
            foreach ($fields as $field) {
                $member[$field] = $row[$field];
            }
            array_push($data, $member);
        }

        $filename = "Duplicate_Users_" . $fileDate . ".csv";

        return CSVExporter::getInstance()->export($filename, $data, ',');
    }

    public function exportMarketplaceAdmins()
    {

        $params = $this->owner->getRequest()->getVars();
        if (!isset($params['marketplace_type']) || empty($params['marketplace_type']))
            return $this->owner->httpError('412', 'missing required param marketplace type');

        $marketplace_type = $params['marketplace_type'];

        $filters_string = implode("','", $marketplace_type);

        $fileDate = date('Ymdhis');

        SangriaPage_Controller::generateDateFilters('s');

        $sql = <<< SQL
SELECT M.FirstName, M.Surname, M.Email, C.Name AS Company, GROUP_CONCAT(MT.Name ORDER BY MT.Name ASC SEPARATOR ' - ') AS Marketplace
FROM Member AS M
INNER JOIN ( SELECT MemberID, CompanyID, GroupID FROM Company_Administrators WHERE Company_Administrators.GroupID IN ('{$filters_string}') ) AS CA ON CA.MemberID = M.ID
INNER JOIN Company AS C ON C.ID = CA.CompanyID
INNER JOIN MarketPlaceType AS MT ON MT.AdminGroupID = CA.GroupID
GROUP BY M.FirstName, M.Surname, M.Email, C.Name
ORDER BY M.Email, C.Name ;
SQL;

        $res = DB::query($sql);

        $fields = array('FirstName', 'Surname', 'Email', 'Company', 'Marketplace');
        $data = array();

        foreach ($res as $row) {
            $member = array();
            foreach ($fields as $field) {
                $member[$field] = str_replace(',', ' ', $row[$field]); //commas tabs cell in excel
            }
            array_push($data, $member);
        }

        $filename = "Marketplace_Admins_" . $fileDate . ".csv";

        return CSVExporter::getInstance()->export($filename, $data, ',');
    }
}