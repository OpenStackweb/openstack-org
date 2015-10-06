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
use \GuzzleHttp\Exception\ClientException as HttpException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

class IngestOpenStackComponentsDataCronTask extends CronTask
{
    /**
     * @return void
     */
    public function run()
    {
        $releases = OpenStackRelease::get()->where(" Status = 'SecuritySupported' OR Status = 'Current' ");

        foreach($releases as $release)
        {
            $this->getProductionUseStatus($release);
            $this->getInstallationGuideStatus($release);
        }

        $this->processProjects();
    }

    private function processProjects()
    {
        $url = "https://raw.githubusercontent.com/openstack/governance/master/reference/projects.yaml";
        $client   = new HttpClient;

        try
        {
            $response = $client->get
            (
                $url
            );
        }
        catch(HttpException $ex)
        {
            return;
        }

        if(is_null($response)) return;
        if($response->getStatusCode() != 200) return;
        $body =  $response->getBody();
        if(is_null($body)) return;
        $content = $body->getContents();
        if(empty($content)) return;

        $yaml = new Parser();

        try {
            $projects = $yaml->parse($content);
            foreach($projects as $project_name => $info)
            {
                $component    = OpenStackComponent::get()->filter('CodeName',ucfirst($project_name))->first();
                if(is_null($component)) continue;
                $ptl          = isset($info['ptl']) ? $info['ptl'] : null;
                $wiki         = isset($info['url']) ? $info['url'] : null;
                $tags         = isset($info['tags']) ? $info['tags'] : array();
                $ptl_member   = null;
                if(!empty($ptl))
                {
                    $ptl_names = preg_split("/\s/", $ptl);
                    $fname = $ptl_names[0];
                    $lname = $ptl_names[1];
                    $ptl_member  = Member::get()->filter
                    (
                        array
                        (
                            'FirstName' => $fname,
                            'Surname'   => $lname,
                        )
                    )->first();
                }

                $team_diverse_affiliation = false;
                $is_service               = false;
                $has_stable_branches      = false;
                $tc_approved_release      = false;
                $release_milestones       = false;
                $release_intermediary     = false;
                $release_independent      = false;
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
                    if($tag === "release:has-stable-branches" )
                        $has_stable_branches = true;
                    if($tag === "release:cycle-with-milestones" )
                        $release_milestones = true;
                    if($tag === "release:cycle-with-intermediary" )
                        $release_intermediary = true;
                    if($tag === "release:independent" )
                        $release_independent = true;
                    if($tag === "tc-approved-release" )
                        $tc_approved_release = true;
                }
                if(!$is_service) continue;
                $component->HasStableBranches = $has_stable_branches;
                $component->WikiUrl = $wiki;
                $component->TCApprovedRelease = $tc_approved_release;
                $component->ReleaseMileStones = $release_milestones;
                $component->ReleaseCycleWithIntermediary = $release_intermediary;
                $component->ReleaseIndependent = $release_independent;
                $component->HasTeamDiversity = $team_diverse_affiliation;
                if(!is_null($ptl_member))
                    $component->LatestReleasePTLID = $ptl_member->ID;
                $component->write();
            }
        } catch (ParseException $e) {
            return;
        }
    }

    private function getInstallationGuideStatus(OpenStackRelease $release)
    {
        $template_url = 'https://raw.githubusercontent.com/stackforge/ops-tags-team/master/%s/ops-docs-install-guide.json';
        $client   = new HttpClient;

        try
        {
            $response = $client->get
            (
                sprintf($template_url, strtolower($release->Name))
            );
        }
        catch(HttpException $ex)
        {
            return;
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
            $error = json_last_error();
            return;
        }
        $cs = $release->getManyManyComponents('OpenStackComponents');
        foreach($installation_guide_status_json as $component_name => $status)
        {
            preg_match("/\((\w*)\)/", $component_name, $output_array);
            if(count($output_array) !== 2) continue;
            $code_name = $output_array[1];
            $component = $release->supportsComponent($code_name);
            if(is_null($component)) continue;
            $status = $status['status'];
            if($status !== 'available') continue;
            $cs->add($component, array('HasInstallationGuide'=> true));
        }
    }

    private function getProductionUseStatus(OpenStackRelease $release)
    {

        $template_url = 'https://raw.githubusercontent.com/stackforge/ops-tags-team/master/%s/ops-production-use.json';
        $client   = new HttpClient;

        try
        {
            $response = $client->get
            (
                sprintf($template_url, strtolower($release->Name))
            );
        }
        catch(HttpException $ex)
        {
            return;
        }

        if(is_null($response)) return;
        if($response->getStatusCode() != 200) return;
        $body =  $response->getBody();
        if(is_null($body)) return;
        $content = $body->getContents();
        if(empty($content)) return;
        $production_use_status_json = json_decode($content, true);
        $cs = $release->getManyManyComponents('OpenStackComponents');

        foreach($production_use_status_json as $component_name => $status)
        {
            preg_match("/\((\w*)\)/", $component_name, $output_array);
            if(count($output_array) !== 2) continue;
            $code_name = $output_array[1];
            $component = $release->supportsComponent($code_name);
            if(is_null($component)) continue;
            $status = $status['status'];
            $percentage =preg_split("/\%/", $status);
            if(count($percentage) !== 2) continue;
            $percentage  = intval($percentage[0]);

            $cs->add($component, array('Adoption'=>$percentage));
        }
    }
}