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
 * Class SangriaPageDeploymentExtension
 */
final class SangriaPageDeploymentExtension extends Extension
{

    public function onBeforeInit()
    {
        Config::inst()->update(get_class($this), 'allowed_actions', array(
            'ViewDeploymentStatistics',
            'ViewDeploymentSurveyStatistics',
            'ViewDeploymentDetails',
            'DeploymentDetails',
            'AddNewDeployment',
            'AddUserStory',
            'ViewDeploymentsPerRegion'));

        Config::inst()->update(get_class($this->owner), 'allowed_actions', array(
            'ViewDeploymentStatistics',
            'ViewDeploymentSurveyStatistics',
            'ViewDeploymentDetails',
            'DeploymentDetails',
            'AddNewDeployment',
            'AddUserStory',
            'ViewDeploymentsPerRegion'));
    }

    function DeploymentDetails()
    {
        $params = $this->owner->request->allParams();
        $deployment_id = intval(Convert::raw2sql($params["ID"]));;
        $deployment = Deployment::get()->byID($deployment_id);
        if ($deployment)
            return $this->owner->Customise($deployment)->renderWith(array('SangriaPage_DeploymentDetails', 'SangriaPage', 'SangriaPage'));
        return $this->owner->httpError(404, 'Sorry that Deployment could not be found!.');
    }

// Deployment Survey data

    public function ViewDeploymentSurveyStatistics()
    {
        SangriaPage_Controller::generateDateFilters();
        Requirements::css("themes/openstack/javascript/datetimepicker/jquery.datetimepicker.css");
        Requirements::javascript("themes/openstack/javascript/datetimepicker/jquery.datetimepicker.js");
        Requirements::css("themes/openstack/css/deployment.survey.page.css");
        Requirements::javascript("themes/openstack/javascript/deployment.survey.filters.js");
        return $this->owner->Customise(array())->renderWith(array('SangriaPage_ViewDeploymentSurveyStatistics', 'SangriaPage', 'SangriaPage'));
    }

    function DeploymentSurveysCount()
    {
        $DeploymentSurveys = DeploymentSurvey::get()->where("Title IS NOT NULL")->where(SangriaPage_Controller::$date_filter_query);
        $Count = ($DeploymentSurveys) ? $DeploymentSurveys->Count() : 0;
        return $Count;
    }

    function IndustrySummary()
    {
        $list = new ArrayList();
        $options = DeploymentSurvey::$industry_options;

        foreach ($options as $option => $label) {
            $count = DB::query("select count(*) from DeploymentSurvey where Industry like '%" . $option . "%' AND " . SangriaPage_Controller::$date_filter_query)->value();
            $do = new DataObject();
            $do->Value = $label;
            $do->Count = $count;
            $list->push($do);
        }

        return $list;
    }

    function OtherIndustry()
    {
        $list = DeploymentSurvey::get()->where("OtherIndustry IS NOT NULL AND " . SangriaPage_Controller::$date_filter_query)->sort('OtherIndustry');
        return $list;
    }

    function OrganizationSizeSummary()
    {
        $list = new ArrayList();
        $options = DeploymentSurvey::$organization_size_options;

        foreach ($options as $option => $label) {
            $count = DB::query("select count(*) from DeploymentSurvey where OrgSize like '%" . $option . "%' AND " . SangriaPage_Controller::$date_filter_query)->value();
            $do = new DataObject();
            $do->Value = $label;
            $do->Count = $count;
            $list->push($do);
        }

        return $list;
    }

    function InvolvementSummary()
    {
        $list = new ArrayList();
        $options = DeploymentSurvey::$openstack_involvement_options;

        foreach ($options as $option => $label) {
            $count = DB::query("select count(*) from DeploymentSurvey where OpenStackInvolvement like '%" . $option . "%' AND " . SangriaPage_Controller::$date_filter_query)->value();
            $do = new DataObject();
            $do->Value = $label;
            $do->Count = $count;
            $list->push($do);
        }

        return $list;
    }

    function InformationSourcesSummary()
    {
        $list = new ArrayList();
        $options = DeploymentSurvey::$information_options;

        foreach ($options as $option => $label) {
            $count = DB::query("select count(*) from DeploymentSurvey where InformationSources like '%" . $option . "%' AND " . SangriaPage_Controller::$date_filter_query)->value();
            $do = new DataObject();
            $do->Value = $label;
            $do->Count = $count;
            $list->push($do);
        }

        return $list;
    }

    function OtherInformationSources()
    {
        $list = DeploymentSurvey::get()->where("OtherInformationSources IS NOT NULL AND " . SangriaPage_Controller::$date_filter_query)->sort('OtherInformationSources');
        return $list;
    }

    function FurtherEnhancement()
    {
        $list = DeploymentSurvey::get()->where("FurtherEnhancement IS NOT NULL AND " . SangriaPage_Controller::$date_filter_query)->sort('FurtherEnhancement');
        return $list;
    }

    function FoundationUserCommitteePriorities()
    {
        $list = DeploymentSurvey::get()->where("FoundationUserCommitteePriorities IS NOT NULL AND " . SangriaPage_Controller::$date_filter_query)->sort('FurtherEnhancement');
        return $list;
    }

    function BusinessDriversSummary()
    {
        $list = new ArrayList();
        $options = DeploymentSurvey::$business_drivers_options;

        foreach ($options as $option => $label) {
            if ($option == 'Ability to innovate, compete') {
                $option = 'Ability to innovate{comma} compete';
            }
            $count = DB::query("select count(*) from DeploymentSurvey where BusinessDrivers like '%" . $option . "%' AND " . SangriaPage_Controller::$date_filter_query)->value();
            $do = new DataObject();
            $do->Value = $label;
            $do->Count = $count;
            $list->push($do);
        }

        return $list;
    }

    function OtherBusinessDrivers()
    {
        $list = DeploymentSurvey::get()->where("OtherBusinessDrivers IS NOT NULL AND " . SangriaPage_Controller::$date_filter_query)->sort("OtherBusinessDrivers");
        return $list;
    }

    function WhatDoYouLikeMost()
    {
        $list = DeploymentSurvey::get()->where("WhatDoYouLikeMost IS NOT NULL AND " . SangriaPage_Controller::$date_filter_query)->sort("WhatDoYouLikeMost");
        return $list;
    }

    function NumCloudUsersSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("NumCloudUsers",
            Deployment::$num_cloud_users_options);
    }


// Deployment Survey data

    function ViewDeploymentStatistics()
    {
        SangriaPage_Controller::generateDateFilters();
        Requirements::css("themes/openstack/javascript/datetimepicker/jquery.datetimepicker.css");
        Requirements::javascript("themes/openstack/javascript/datetimepicker/jquery.datetimepicker.js");
        Requirements::css("themes/openstack/css/deployment.survey.page.css");
        Requirements::javascript("themes/openstack/javascript/deployment.survey.filters.js");
        return $this->owner->Customise(array())->renderWith(array('SangriaPage_ViewDeploymentStatistics', 'SangriaPage', 'SangriaPage'));
    }

    function DeploymentsCount()
    {
        $filterWhereClause = SangriaPage_Controller::generateFilterWhereClause();
        $Deployments = Deployment::get()->where(" 1=1 " . $filterWhereClause . ' AND ' . SangriaPage_Controller::$date_filter_query);
        return $Deployments->count();
    }

    function IsPublicSummary()
    {
        $options = array(0 => "No", 1 => "Yes");
        return SangriaPage_Controller::generateSelectListSummary("IsPublic", $options, true);
    }

    function DeploymentTypeSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("DeploymentType", Deployment::$deployment_type_options, true);
    }

    function ProjectsUsedSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("ProjectsUsed", Deployment::$projects_used_options, true);
    }

    function CurrentReleasesSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("CurrentReleases", Deployment::$current_release_options, true);
    }

    function APIFormatsSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("APIFormats", Deployment::$api_options, true);
    }

    function DeploymentStageSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("DeploymentStage", Deployment::$stage_options, true);
    }

    function HypervisorsSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("Hypervisors", Deployment::$hypervisors_options, true);
    }

    function IdentityDriversSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("IdentityDrivers", Deployment::$identity_driver_options, true);
    }

    function SupportedFeaturesSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("SupportedFeatures", Deployment::$deployment_features_options, true);
    }

    function NetworkDriversSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("NetworkDrivers", Deployment::$network_driver_options, true);
    }

    function NetworkNumIPsSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("NetworkNumIPs", Deployment::$network_ip_options, true);
    }

    function BlockStorageDriversSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("BlockStorageDrivers", Deployment::$block_storage_divers_options, true);
    }

    function ComputeNodesSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("ComputeNodes", Deployment::$compute_nodes_options, true);
    }

    function ComputeCoresSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("ComputeCores", Deployment::$compute_cores_options, true);
    }

    function ComputeInstancesSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("ComputeInstances", Deployment::$compute_instances_options, true);
    }

    function BlockStorageTotalSizeSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("BlockStorageTotalSize", Deployment::$storage_size_options, true);
    }

    function ObjectStorageSizeSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("ObjectStorageSize", Deployment::$storage_size_options, true);
    }

    function ObjectStorageNumObjectsSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("ObjectStorageNumObjects", Deployment::$stoage_objects_options, true);
    }

// Deployment Details

    function ViewDeploymentDetails()
    {
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js");
        Requirements::javascript(Director::protocol() . "ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/additional-methods.min.js");
        Requirements::css(THIRDPARTY_DIR . '/jquery-ui-themes/smoothness/jquery-ui.css');
        Requirements::javascript(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');
        Requirements::javascript("themes/openstack/javascript/jquery.ui.datepicker.validation.package-1.0.1/jquery.ui.datepicker.validation.js");
        Requirements::javascript("themes/openstack/javascript/jquery.validate.custom.methods.js");
        Requirements::javascript("themes/openstack/javascript/sangria/view.deployment.details.js");
        return $this->owner->getViewer('ViewDeploymentDetails')->process($this->owner);
    }

    function Deployments()
    {

        $sort = $this->owner->request->getVar('sort');
        $sort_dir = $this->owner->getSortDir('deployments');
        $date_from = Convert::raw2sql(trim($this->owner->request->getVar('date-from')));
        $date_to = Convert::raw2sql(trim($this->owner->request->getVar('date-to')));
        $free_text = Convert::raw2sql(trim($this->owner->request->getVar('free-text')));

        $sort_query = '';
        if (!empty($sort)) {
            switch (strtolower(trim($sort))) {
                case 'date': {
                    $sort_query = "UpdateDate";
                    $sort_dir = strtoupper($sort_dir);
                }
                    break;
                default: {
                    $sort_query = "ID";
                    $sort_dir = 'DESC';
                }
                    break;
            }
        }

        $where_query = "IsPublic = 1";
        $res = Deployment::get();
        if (!empty($date_from) && !empty($date_to)) {
            $start = new \DateTime($date_from);
            $start->setTime(00, 00, 00);
            $end = new \DateTime($date_to);
            $end->setTime(23, 59, 59);
            $where_query .= " AND ( UpdateDate >= '{$start->format('Y-m-d H:i:s')}' AND UpdateDate <= '{$end->format('Y-m-d H:i:s')}')";
        }

        if (!empty($free_text)) {
            $where_query .= " AND ( Org.Name LIKE '%{$free_text}%' OR Label LIKE '%{$free_text}%' ) ";
            $res = $res->innerJoin('Org', 'Org.ID = Deployment.OrgID');
        }

        $res = $res->where($where_query);
        if (!empty($sort_query) && !empty($sort_dir)) {
            $res->sort($sort_query, $sort_dir);
        }
        return $res;
    }


    function DeploymentsSurvey()
    {

        $sqlQuery = new SQLQuery();
        $sqlQuery->setSelect(array('DeploymentSurvey.*'));
        $sqlQuery->setFrom(array("DeploymentSurvey, Deployment, Org"));
        $sqlQuery->setWhere(array("Deployment.DeploymentSurveyID = DeploymentSurvey.ID
                            AND Deployment.IsPublic = 1
                            AND Org.ID = DeploymentSurvey.OrgID
                            AND DeploymentSurvey.Title IS NOT NULL
                            "));
        $sqlQuery->setOrderBy('Org.Name');

        $result = $sqlQuery->execute();

        $arrayList = new ArrayList();

        foreach ($result as $rowArray) {
            // concept: new Product($rowArray)
            $arrayList->push(new $rowArray['ClassName']($rowArray));
        }

        return $arrayList;
    }

// Add User Story from Deployment
    function AddUserStory()
    {

        if (isset($_GET['ID']) && is_numeric($_GET['ID'])) {
            $ID = $_GET['ID'];
        } else {
            die();
        }

        $parent = UserStoryHolder::get()->first();
        if (!$parent) {
            $this->owner->setMessage('Error', 'could not add an user story bc there is not any available parent page(UserStoryHolder).');
            Controller::curr()->redirectBack();
        }
        $userStory = new UserStory;
        $userStory->Title = $_GET['label'];
        $userStory->DeploymentID = $ID;
        $userStory->UserStoriesIndustryID = $_GET['industry'];
        $userStory->CompanyName = $_GET['org'];
        $userStory->CaseStudyTitle = $_GET['org'];
        $userStory->ShowInAdmin = 1;
        $userStory->setParent($parent); // Should set the ID once the Holder is created...
        $userStory->write();
        $userStory->publish("Live", "Stage");

        $this->owner->setMessage('Success', '<b>' . $userStory->Title . '</b> added as User Story.');

        Controller::curr()->redirectBack();
    }

    function AddNewDeployment()
    {

        $survey = DataObject::get_one('DeploymentSurvey', 'ID = ' . $_POST['survey']);

        $deployment = new Deployment;
        $deployment->Label = $_POST['label'];
        $deployment->DeploymentType = $_POST['type'];
        $deployment->CountryCode = $_POST['country'];
        $deployment->DeploymentSurveyID = $_POST['survey'];
        if ($survey) {
            $deployment->OrgID = $survey->OrgID;
        } else {
            $deployment->OrgID = 0;
        }
        $deployment->IsPublic = 1;
        $deployment->write();

        $this->owner->setMessage('Success', '<b>' . $_POST['label'] . '</b> added as a new Deployment.');

        Controller::curr()->redirectBack();
    }

    function WorkloadsSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("WorkloadsDescription",
            Deployment::$workloads_description_options);
    }

    function DeploymentToolsSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("DeploymentTools",
            Deployment::$deployment_tools_options);
    }

    function OperatingSystemSummary()
    {
        return SangriaPage_Controller::generateSelectListSummary("OperatingSystems",
            Deployment::$operating_systems_options);
    }

    function WhyNovaNetwork()
    {
        $filterWhereClause = SangriaPage_Controller::generateFilterWhereClause();

        $list = DataObject::get("Deployment", "WhyNovaNetwork IS NOT NULL" . $filterWhereClause, "WhyNovaNetwork");

        return $list;
    }

    function ViewDeploymentsPerRegion(){

        $continent = intval(Convert::raw2sql(Controller::curr()->request->getVar('continent')));
        $country = Convert::raw2sql(Controller::curr()->request->getVar('country'));
        Requirements::javascript(Director::protocol()."maps.googleapis.com/maps/api/js?sensor=false");
        Requirements::javascript("marketplace/code/ui/admin/js/utils.js");
        Requirements::javascript("marketplace/code/ui/frontend/js/markerclusterer.js");
        Requirements::javascript("marketplace/code/ui/frontend/js/oms.min.js");
        Requirements::javascript("marketplace/code/ui/frontend/js/infobubble-compiled.js");
        Requirements::javascript("marketplace/code/ui/frontend/js/google.maps.jquery.js");

        if(!empty($country)){
            $continent = DB::query("SELECT ContinentID from Continent_Countries where CountryCode = '{$country}';")->value();
            $count     = DB::query("SELECT COUNT(*) FROM Deployment D INNER JOIN DeploymentSurvey DS ON DS.ID = D.DeploymentSurveyID WHERE DS.PrimaryCountry = '{$country}';")->value();
            $result = array(
                'country'      => $country ,
                'country_name' => CountryCodes::$iso_3166_countryCodes[$country],
                'continent'    => $continent,
                'count'        => $count
            );
            Requirements::javascript('themes/openstack/javascript/sangria/sangria.page.viewdeploymentscountry.js');
            return $this->owner->getViewer('ViewDeploymentsPerCountry')->process($this->owner->customise($result));
        }
        if(!empty($continent)){
            $continent_name = DB::query("SELECT Name from Continent where ID = {$continent}")->value();
            $result = array(
                'continent' => $continent ,
                'continent_name' => $continent_name
            );
            Requirements::javascript('themes/openstack/javascript/sangria/sangria.page.viewdeploymentscontinent.js');
            return $this->owner->getViewer('ViewDeploymentsPerContinent')->process($this->owner->customise($result));
        }
        Requirements::javascript('themes/openstack/javascript/sangria/sangria.page.viewdeploymentsregion.js');
        return $this->owner->getViewer('ViewDeploymentsPerRegion')->process($this->owner);
    }


    function LoadJsonCountriesCoordinates(){

        $doc = new DOMDocument;

        // We don't want to bother with white spaces
        $doc->preserveWhiteSpace = false;
        $dir = dirname(__FILE__);
        $doc->Load($dir.'/data/countries.xml');

        $xpath = new DOMXPath($doc);

        // We starts from the root element
        $query = "//country";

        $entries = $xpath->query($query);

        $json_data = 'var countries_data = [];';
        foreach ($entries as $entry) {
            $code   = $entry->attributes->item(2)->nodeValue;
            $lat_lng = $entry->attributes->item(14)->nodeValue;
            $lat_lng = @explode(',', $lat_lng);
            if(count($lat_lng) != 2)
                continue;
            $lat = $lat_lng[0];
            $lng = $lat_lng[1];
            if(empty($lat) || empty($lng)) continue;
            $link = $this->owner->Link('ViewDeploymentsPerRegion').'?country='.$code;
            $json_data .= "countries_data[\"". $code."\"] = { lat: ".$lat." , lng :".$lng.", url: '".$link."'};";
        }

        return $json_data;
    }

    function DeploymentsPerContinentCountry($continent_id){

        $list = new ArrayList();
        $countries = DB::query("SELECT COUNT(D.ID) AS Qty, DS.PrimaryCountry FROM Deployment D
INNER JOIN DeploymentSurvey DS ON DS.ID = D.DeploymentSurveyID
WHERE PrimaryCountry
IN (SELECT CountryCode from Continent_Countries where ContinentID = {$continent_id}) group BY PrimaryCountry;");
        foreach($countries as $country) {
                $count = $country['Qty'];
                $country = $country['PrimaryCountry'];
                $do = new DataObject();
                $do->count = $count;
                $do->country = $country;
                $do->country_name = CountryCodes::$iso_3166_countryCodes[$country];
                $list->push($do);
        }
        return $list;
    }

    function DeploymentsPerContinent(){
        $list = new ArrayList();
        $records = DB::query('SELECT COUNT(D.ID) AS DeploymentsQty, C.ID AS ContinentID, C.Name AS Continent FROM Deployment D
INNER JOIN DeploymentSurvey DS ON DS.ID = D.DeploymentSurveyID
INNER JOIN Continent_Countries CC ON CC.CountryCode = DS.PrimaryCountry
INNER JOIN Continent C ON C.ID = CC.ContinentID
GROUP BY C.Name, C.ID;');
        foreach($records as $record){
            $count            = $record['DeploymentsQty'];
            $continent        = $record['Continent'];
            $continent_id     = $record['ContinentID'];
            $do = new DataObject();
            $do->count        = $count;
            $do->continent    = $continent;
            $do->continent_id = $continent_id;
            $list->push($do);
        }
        return $list;
    }

    function DeploymentsPerCountry($country){
        $list = new ArrayList();
        $deployments = DB::query("SELECT D.* from Deployment D INNER JOIN DeploymentSurvey DS ON DS.ID = D.DeploymentSurveyID WHERE DS.PrimaryCountry = '{$country}' ; ");
        foreach($deployments as $deployment) {
            // concept: new Deployment($deployment)
            $list->push(new $deployment['ClassName']($deployment));
        }
        return $list;
    }

    function CountriesWithDeployments($continent_id){
        $list = new ArrayList();
        $countries = DB::query("SELECT  CC.CountryCode, COUNT(CC.CountryCode) AS Qty from Continent_Countries CC INNER JOIN DeploymentSurvey DS ON DS.PrimaryCountry = CC.CountryCode
INNER JOIN  Deployment D ON DS.ID = D.DeploymentSurveyID
WHERE CC.ContinentID =  {$continent_id} GROUP BY CC.CountryCode; ");
        foreach($countries as $country) {
            // concept: new Deployment($deployment)
            $do = new DataObject();
            $do->country        = $country['CountryCode'];
            $do->country_name   = CountryCodes::$iso_3166_countryCodes[$do->country];
            $do->count          = $country['Qty'];
            $list->push($do);
        }
        return $list;
    }

    function DeploymentCount()    {
        return DB::query("
SELECT COUNT(*) from Deployment D
INNER JOIN DeploymentSurvey DS ON DS.ID = D.DeploymentSurveyID
INNER JOIN Continent_Countries CC ON CC.CountryCode = DS.PrimaryCountry")->value();
    }
}