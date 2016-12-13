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
use GuzzleHttp\Client as HttpClient;

/**
 * Class IngestOpenStackComponentsDataCronTask
 */
final class IngestOpenStackComponentsDataCronTask extends CronTask
{
    /**
     * @var ITransactionManager
     */
    private $tx_manager;
    /**
     * @var HttpClient
     */
    private $client;
    /**
     * IngestOpenStackComponentsDataCronTask constructor.
     * @param ITransactionManager $tx_manager
     */
    public function __construct(ITransactionManager $tx_manager)
    {   parent::__construct();
        $this->tx_manager = $tx_manager;

        $this->client     = new HttpClient([
            'defaults' => [
                'timeout'         => 60,
                'allow_redirects' => false,
                'verify'          => true
            ]
        ]);
    }

    /**
     * @return void
     */
    public function run()
    {
        $start = time();
        $this->tx_manager->transaction(function(){
            $releases = OpenStackRelease::get()->where(" Name <> 'Trunk' ")->sort('ReleaseDate', 'DESC');
            DB::query('DELETE FROM OpenStackComponentReleaseCaveat;');
            $this->processProjects();
            foreach($releases as $release)
            {
                echo sprintf('processing release %s ...', $release->Name).PHP_EOL;
                $this->ProcessProjectPerRelease($release);
                $this->getProductionUseStatus($release);
                $this->getInstallationGuideStatus($release);
                $this->getSDKSupport($release);
                $this->getQualityOfPackages($release);
                $this->calculateMaturityPoints($release);
                $this->getStackAnalytics($release);
                $this->ProcessProjectPerRelease($release);
            }
        });
        $delta = time() - $start;
        echo sprintf('task took %s seconds to run.',$delta).PHP_EOL;
        $this->client = null;
    }

    private function processProjects()
    {
        $url      = "https://raw.githubusercontent.com/openstack/governance/master/reference/projects.yaml";
        $response = null;
        try
        {
            $response = $this->client->get
            (
                $url
            );
        }
        catch (Exception $ex)
        {
            echo $ex->getMessage().PHP_EOL;
            SS_Log::log($ex->getMessage(), SS_Log::WARN);
        }

        if(is_null($response)) return;

        if($response->getStatusCode() != 200) return;
        $body =  $response->getBody();
        if(is_null($body)) return;
        $content = $body->getContents();
        if(empty($content)) return;

        try {

            $projects = Spyc::YAMLLoadString($content);

            foreach($projects as $project_name => $info)
            {
                $component    = OpenStackComponent::get()->filter('CodeName', ucfirst($project_name))->first();
                if(is_null($component)){
                    $component    = OpenStackComponent::get()->filter('Name', ucfirst($project_name))->first();
                }
                echo sprintf('processing component %s', $project_name).PHP_EOL;
                if(is_null($component)){
                    echo sprintf('component %s not found!', $project_name).PHP_EOL;
                    continue;
                }

                $ptl          = isset($info['ptl']) ? $info['ptl']   : null;
                $wiki         = isset($info['url']) ? $info['url']   : null;
                $tags         = isset($info['tags']) ? $info['tags'] : [];
                $ptl_member   = null;

                if(!empty($ptl))
                {
                    if(is_array($ptl) && isset($ptl['name']))
                    {
                        $ptl_names = preg_split("/\s/", $ptl['name']);
                        $fname = $ptl_names[0];
                        $lname = $ptl_names[1];
                    }
                    else
                    {
                        $ptl_names = preg_split("/\s/", $ptl);
                        $fname = $ptl_names[0];
                        $lname = $ptl_names[1];
                    }
                    $email = isset($ptl['email']) ? trim($ptl['email']) : null;
                    echo sprintf('PTL %s %s (%s)', $fname, $lname, $email).PHP_EOL;
                    if(!empty($email)){
                        $ptl_member = Member::get()->filter
                        (
                            array
                            (
                                'Email' => $email,
                            )
                        )->first();
                    }

                    if(is_null($ptl_member)) {
                        $ptl_member = Member::get()->filter
                        (
                            array
                            (
                                'FirstName' => $fname,
                                'Surname'   => $lname,
                            )
                        )->first();
                    }
                }

                $team_diverse_affiliation     = false;
                $is_service                   = false;
                $has_stable_branches          = false;
                $tc_approved_release          = false;
                $starter_kit                  = false;
                $vulnerability_managed        = false;
                $follows_standard_deprecation = false;
                $supports_upgrade             = false;
                $supports_rolling_upgrade     = false;

                foreach($tags as $tag)
                {
                    if($tag === "team:diverse-affiliation" )
                        $team_diverse_affiliation = true;
                }

                $deliverables = isset($info['deliverables']) ? $info['deliverables'] : array();
                $service_info = isset($deliverables[$project_name]) ? $deliverables[$project_name] : array();
                $service_tags = isset($service_info['tags']) ? $service_info['tags'] : array();

                foreach($service_tags as $tag)
                {
                    if($tag === "type:service" )
                        $is_service = true;
                    if($tag === "stable:follows-policy" )
                        $has_stable_branches = true;
                    if($tag === "tc-approved-release" )
                        $tc_approved_release = true;
                    if($tag === "starter-kit:compute" )
                        $starter_kit = true;
                    if($tag === "vulnerability:managed" )
                        $vulnerability_managed = true;
                    if($tag === "assert:follows-standard-deprecation" )
                        $follows_standard_deprecation = true;
                    if($tag === "assert:supports-upgrade" )
                        $supports_upgrade = true;
                    if($tag === "assert:supports-rolling-upgrade" )
                        $supports_rolling_upgrade = true;
                }

                $component->HasStableBranches            = $has_stable_branches;
                $component->WikiUrl                      = $wiki;
                $component->TCApprovedRelease            = $tc_approved_release;
                $component->HasTeamDiversity             = $team_diverse_affiliation;
                $component->IncludedComputeStarterKit    = $starter_kit;
                $component->VulnerabilityManaged         = $vulnerability_managed;
                $component->FollowsStandardDeprecation   = $follows_standard_deprecation;
                $component->SupportsUpgrade              = $supports_upgrade;
                $component->SupportsRollingUpgrade       = $supports_rolling_upgrade;

                if(!is_null($ptl_member)) {
                    echo sprintf('setting PTL %s %s (%s) to Component %s', $ptl_member->FirstName, $ptl_member->Surname, $ptl_member->Email, $component->Name).PHP_EOL;
                    $component->LatestReleasePTLID = $ptl_member->ID;
                }

                $component->write();
            }
        } catch (Exception $ex) {
            echo $ex->getMessage().PHP_EOL;
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return;
        }
    }

    private function ProcessProjectPerRelease($release)
    {
        $url_template = "http://git.openstack.org/cgit/openstack/releases/plain/deliverables/%s/%s.yaml";

        foreach($release->OpenStackComponents() as $component){
            $url      = sprintf($url_template, strtolower($release->Name), strtolower($component->CodeName));
            echo sprintf("processing url %s ", $url).PHP_EOL;
            $response = null;
            try {
                $response = $this->client->get
                (
                    $url
                );
            } catch (Exception $ex) {
                echo $ex->getMessage() . PHP_EOL;
                SS_Log::log($ex->getMessage(), SS_Log::WARN);
            }

            if (is_null($response)) continue;

            if ($response->getStatusCode() != 200) continue;
            $body = $response->getBody();
            if (is_null($body)) continue;
            $content = $body->getContents();
            if (empty($content)) continue;

            try {

                $component_yaml               = Spyc::YAMLLoadString($content);
                $release_milestones           = false;
                $release_intermediary         = false;
                $release_independent          = false;
                $release_trailing             = false;
                $release_notes                = isset($component_yaml['release-notes']) ? $component_yaml['release-notes'] : null;
                if(is_array($release_notes)){
                    $release_notes = implode(", ", $release_notes);
                }
                if(!isset($component_yaml['release-model'])) continue;
                switch($component_yaml['release-model']){
                    case 'cycle-with-milestones':
                        $release_milestones = true;
                    break;
                    case 'cycle-with-intermediary':
                        $release_intermediary = true;
                    break;
                    case 'cycle-trailing':
                         $release_trailing = true;
                        break;
                    case 'independant':
                        $release_independent = true;
                    break;
                }

                $release->OpenStackComponents()->add($component , [
                    'ReleaseMileStones'            => $release_milestones,
                    'ReleaseCycleWithIntermediary' => $release_intermediary,
                    'ReleaseIndependent'           => $release_independent,
                    'ReleaseTrailing'              => $release_trailing,
                    'ReleasesNotes'                => $release_notes
                ]);

            } catch (Exception $ex) {
                echo $ex->getMessage() . PHP_EOL;
                SS_Log::log($ex->getMessage(), SS_Log::WARN);
            }
        }
    }

    private function getInstallationGuideStatus(OpenStackRelease $release)
    {

        $template_url = '%s/%s/ops-docs-install-guide.json';
        $response     = null;
        try
        {
            $response = $this->client->get
            (
                sprintf($template_url, OpsTagsTeamRepositoryUrl, strtolower($release->Name))
            );
        }
        catch (Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::WARN);
        }

        if(is_null($response)) return;
        if($response->getStatusCode() != 200) return;
        $body =  $response->getBody();
        if(is_null($body)) return;
        $content = $body->getContents();
        if(empty($content)) return;
        $installation_guide_status_json = json_decode($content, true);
        if(is_null($installation_guide_status_json))
        {
            return;
        }
        $cs = $release->getManyManyComponents('OpenStackComponents');
        foreach($installation_guide_status_json as $component_name => $entry)
        {
            preg_match("/\((\w*)\)/", $component_name, $output_array);
            if(count($output_array) !== 2) continue;
            $code_name = $output_array[1];
            $component = $release->supportsComponent($code_name);
            if(is_null($component)) continue;
            $status = $entry['status'];
            if($status !== 'available') continue;
            $data = array('HasInstallationGuide' => true);
            if(isset($entry['caveats'])) {
                $caveats = $entry['caveats'];
                foreach($caveats as $caveat)
                {
                    $c               = new OpenStackComponentReleaseCaveat();
                    $c->ReleaseID   = $release->ID;
                    $c->ComponentID = $component->ID;
                    $c->Status      = isset($caveat['status'])?$caveat['status']:'';
                    $c->Label       = isset($caveat['label'])?$caveat['label'] : '';
                    $c->Description = isset($caveat['description'])?$caveat['description']:'';
                    $c->Type        = 'InstallationGuide';
                    $c->write();
                }
            }
            $cs->add($component, $data);
        }
    }

    private function getProductionUseStatus(OpenStackRelease $release)
    {

        $template_url = '%s/%s/ops-production-use.json';
        $response     = null;
        try
        {
            $response = $this->client->get
            (
                sprintf($template_url, OpsTagsTeamRepositoryUrl, strtolower($release->Name))
            );
        }
        catch (Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::WARN);
        }

        if(is_null($response)) return;
        if($response->getStatusCode() != 200) return;
        $body =  $response->getBody();
        if(is_null($body)) return;
        $content = $body->getContents();
        if(empty($content)) return;
        $production_use_status_json = json_decode($content, true);
        if(is_null($production_use_status_json))
        {
            return;
        }
        $cs = $release->getManyManyComponents('OpenStackComponents');

        foreach($production_use_status_json as $component_name => $entry)
        {
            preg_match("/\((\w*)\)/", $component_name, $output_array);
            if(count($output_array) !== 2) continue;
            $code_name = $output_array[1];
            $component = $release->supportsComponent($code_name);
            if(is_null($component)) continue;
            $status = $entry['status'];
            $percentage =preg_split("/\%/", $status);
            if(count($percentage) !== 2) continue;
            $percentage  = intval($percentage[0]);

            if(isset($entry['caveats'])) {
                $caveats = $entry['caveats'];
                foreach($caveats as $caveat)
                {
                    $c               = new OpenStackComponentReleaseCaveat();
                    $c->ReleaseID   = $release->ID;
                    $c->ComponentID = $component->ID;
                    $c->Status      = isset($caveat['status'])?$caveat['status']:'';
                    $c->Label       = isset($caveat['label'])?$caveat['label'] : '';
                    $c->Description = isset($caveat['description'])?$caveat['description']:'';
                    $c->Type        = 'ProductionUse';
                    $c->write();
                }
            }

            $cs->add($component, array( 'Adoption' => $percentage ));
        }
        $release->HasStatistics = true;
        $release->write();
    }

    private function calculateMaturityPoints(OpenStackRelease $release)
    {
        $components = $release->OpenStackComponents();

        foreach($components as $c)
        {
            $points = 0;
            if($c->Adoption > 75)
            {
                $points += 1;
            }
            if($c->HasInstallationGuide)
            {
                $points += 1;
            }
            if($c->HasTeamDiversity)
            {
                $points += 1;
            }
            if($c->HasStableBranches)
            {
                $points += 1;
            }
            if(intval($c->SDKSupport) > 7)
            {
                $points += 1;
            }
            if($c->FollowsStandardDeprecation)
            {
                $points += 1;
            }
            if($c->SupportsUpgrade)
            {
                $points += 1;
            }
            if($c->SupportsRollingUpgrade)
            {
                $points += 1;
            }
            $components->add($c, array('MaturityPoints' => $points));
        }
    }

    private function getSDKSupport(OpenStackRelease $release)
    {
        $components    = $release->OpenStackComponents();
        $template_url  = '%s/%s/ops-sdk-support.json';
        $response      = null;
        try
        {
            $response = $this->client->get
            (
                sprintf($template_url, OpsTagsTeamRepositoryUrl, strtolower($release->Name))
            );
        }
        catch (Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::WARN);
        }

        if(is_null($response)) return;
        if($response->getStatusCode() != 200) return;
        $body =  $response->getBody();
        if(is_null($body)) return;
        $content = $body->getContents();
        if(empty($content)) return;
        $sdk_support = json_decode($content, true);
        if(is_null($sdk_support)) return;

        foreach($sdk_support as $component_name => $entry)
        {
            preg_match("/\((\w*)\)/", $component_name, $output_array);
            if(count($output_array) !== 2) continue;
            $code_name = $output_array[1];
            $component = $release->supportsComponent($code_name);
            if(is_null($component)) continue;
            $status = isset($entry['status']) ? intval($entry['status']) : 0;

            $data = array('SDKSupport' => $status);

            if(isset($entry['caveats'])) {
                $caveats = $entry['caveats'];
                foreach($caveats as $caveat)
                {
                    $c               = new OpenStackComponentReleaseCaveat();
                    $c->ReleaseID   = $release->ID;
                    $c->ComponentID = $component->ID;
                    $c->Status      = isset($caveat['status'])?$caveat['status']:'';
                    $c->Label       = isset($caveat['label'])?$caveat['label'] : '';
                    $c->Description = isset($caveat['description'])?$caveat['description']:'';
                    $c->Type        = 'SDKSupport';
                    $c->write();
                }
            }

            $components->add($component, $data);
        }
    }

    private function getQualityOfPackages(OpenStackRelease $release)
    {
        $components    = $release->OpenStackComponents();
        $template_url  = '%s/%s/ops-packaged.json';
        $response      = null;
        try
        {
            $response = $this->client->get
            (
                sprintf($template_url, OpsTagsTeamRepositoryUrl, strtolower($release->Name))
            );
        }
        catch (Exception $ex)
        {
            SS_Log::log($ex->getMessage(), SS_Log::WARN);
        }

        if(is_null($response)) return;
        if($response->getStatusCode() != 200) return;
        $body =  $response->getBody();
        if(is_null($body)) return;
        $content = $body->getContents();
        if(empty($content)) return;
        $package_q = json_decode($content, true);
        if(is_null($package_q)) return;

        foreach($package_q as $component_name => $entry)
        {
            preg_match("/\((\w*)\)/", $component_name, $output_array);
            if(count($output_array) !== 2) continue;
            $code_name = $output_array[1];
            $component = $release->supportsComponent($code_name);
            if(is_null($component)) continue;

            $data = array('QualityOfPackages' => $entry['status']);

            if(isset($entry['caveats'])) {
                $caveats = $entry['caveats'];
                foreach($caveats as $caveat)
                {
                    $c               = new OpenStackComponentReleaseCaveat();
                    $c->ReleaseID   = $release->ID;
                    $c->ComponentID = $component->ID;
                    $c->Status      = isset($caveat['status'])?$caveat['status']:'';
                    $c->Label       = isset($caveat['label'])?$caveat['label'] : '';
                    $c->Description = isset($caveat['description'])?$caveat['description']:'';
                    $c->Type        = 'QualityOfPackages';
                    $c->write();
                }
            }

            $components->add($component, $data);
        }
    }

    private function getStackAnalytics(OpenStackRelease $release)
    {
        $timeline_stats_url_template    = "http://stackalytics.com/api/1.0/stats/timeline?module=%s-group&release=%s";
        $company_contrib_url_template   = "http://stackalytics.com/api/1.0/stats/companies?module=%s-group&release=%s";
        $engineers_contrib_url_template = "http://stackalytics.com/api/1.0/stats/engineers?module=%s-group&release=%s";

        $components    = $release->OpenStackComponents();


        foreach($components as $c)
        {
            $timeline_json          = null;
            $company_contrib_json   = null;
            $engineers_contrib_json = null;
            $response               = null;

            try
            {
                $response = $this->client->get
                (
                    sprintf($timeline_stats_url_template, strtolower($c->CodeName), strtolower($release->Name))
                );
            }
            catch (Exception $ex)
            {
                SS_Log::log($ex->getMessage(), SS_Log::WARN);
            }

            if (!is_null($response) && $response->getStatusCode() === 200) {
                $body = $response->getBody();
                if (!is_null($body)) {
                    $content = $body->getContents();
                    if (!empty($content)) {
                        $timeline_json = $content;
                    }
                }

            }

            try
            {
                $response = $this->client->get
                (
                    sprintf($company_contrib_url_template, strtolower($c->CodeName), strtolower($release->Name))
                );
            }
            catch (Exception $ex)
            {
                SS_Log::log($ex->getMessage(), SS_Log::WARN);
            }

            if (!is_null($response) && $response->getStatusCode() === 200) {
                $body = $response->getBody();
                if (!is_null($body)) {
                    $content = $body->getContents();
                    if (!empty($content)) {
                        $company_contrib_json = $content;
                    }
                }

            }

            try
            {
                $response = $this->client->get
                (
                    sprintf($engineers_contrib_url_template, strtolower($c->CodeName), strtolower($release->Name))
                );
            }
            catch (Exception $ex)
            {
                SS_Log::log($ex->getMessage(), SS_Log::WARN);
            }

            if (!is_null($response) && $response->getStatusCode() === 200)
            {
                $body = $response->getBody();
                if (!is_null($body)) {
                    $content = $body->getContents();
                    if (!empty($content)) {
                        $engineers_contrib_json = $content;
                    }
                }

            }

            $components->add($c,
                array
                (
                    'ContributionsJson'                      => $timeline_json,
                    'MostActiveContributorsByCompanyJson'    => $company_contrib_json,
                    'MostActiveContributorsByIndividualJson' => $engineers_contrib_json,
                )
            );

        }
    }
}