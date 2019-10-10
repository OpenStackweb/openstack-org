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
            $this->processComponentsAndCategories();
            $this->processCapabilityList();

            foreach($releases as $release)
            {
                echo sprintf('processing release %s ...', $release->Name).PHP_EOL;
                $this->processApiVersionsPerRelease($release);
                $this->processProjectPerRelease($release);
            }
        });
        $delta = time() - $start;
        echo sprintf('task took %s seconds to run.',$delta).PHP_EOL;
        $this->client = null;
    }

    private function processApiVersionsPerRelease($release){
        $url_template = "http://git.openstack.org/cgit/openstack/project-navigator-data/plain/releases/%s/%s.json";

        foreach(OpenStackComponent::get() as $component){
            $url = sprintf($url_template, strtolower($release->Name),  strtolower($component->CodeName));
            echo sprintf("processing url %s ", $url).PHP_EOL;
            $response = null;
            try {
                $response = $this->client->get
                (
                    $url
                );
            } catch (Exception $ex) {
                echo 'NOT FOUND'.PHP_EOL;
                //echo $ex->getMessage() . PHP_EOL;
                //SS_Log::log($ex->getMessage(), SS_Log::WARN);
            }

            if (is_null($response)) continue;

            if ($response->getStatusCode() != 200) continue;

            if(!$release->supportsComponent($component->CodeName)){
                echo sprintf("adding component %s to release %s", $component->CodeName, $release->Name).PHP_EOL;
                $release->addOpenStackComponent($component);
            }

            $body = $response->getBody();
            if (is_null($body)) continue;
            $content = $body->getContents();
            if (empty($content)) continue;

            $api_versions = json_decode($content, true);

            if(!isset($api_versions['versions'])) continue;

            $component->SupportsVersioning = true;
            $component->write();

            foreach($api_versions['versions'] as $api_version){
                // check first if api version exists on component ...
                $status         = OpenStackApiVersion::convertStatus($api_version['status']);
                $db_api_version = $component->getVersionByLabel($api_version['id']);
                if(is_null($db_api_version)){
                    echo sprintf("Component %s - Adding Api Version %s", $component->CodeName, $api_version['id']).PHP_EOL;
                    $db_api_version                       = new OpenStackApiVersion();
                    $db_api_version->Version              = $api_version['id'];
                    $db_api_version->CreatedFromTask      = 1;
                    $db_api_version->OpenStackComponentID = $component->ID;
                }

                $db_api_version->Status  = $status;
                $db_api_version->write();

                //check if version exists on release / component ...
                $old_db_api_version = $release->supportsApiVersion($db_api_version);
                if(is_null($old_db_api_version)){
                    // associate it on release / component ...
                    echo sprintf("Release %s - Component %s - Adding api version %s - status %s", $release->Name , $component->CodeName, $api_version['id'], $status).PHP_EOL;
                    $old_db_api_version = $release->addSupportedVersion($db_api_version, $status);
                    $old_db_api_version->CreatedFromTask = 1;
                }

                $old_db_api_version->Status  = $status;
                $old_db_api_version->write();
            }
        }
    }

    private function processProjects()
    {
        $url      = "https://opendev.org/openstack/governance/raw/branch/master/reference/projects.yaml";
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

        echo 'processing file projects.yaml' . PHP_EOL;

        try {

            $projects = Spyc::YAMLLoadString($content);

            foreach($projects as $project_name => $info)
            {
                // DEBUG
                // echo sprintf('processing component %s', $project_name).PHP_EOL;

                $components = OpenStackComponent::get()
                    ->filter([ 'ProjectTeam'  => $project_name ]);

                if($components->Count() == 0){
                    // DEBUG
                    // echo 'project team missing: '.$project_name.PHP_EOL;
                    continue;
                }

                foreach($components as $component) {
                    $ptl            = isset($info['ptl']) ? $info['ptl'] : null;
                    $wiki           = isset($info['url']) ? $info['url'] : null;
                    $deliverables   = isset($info['deliverables']) ? $info['deliverables'] : null;
                    $ptl_member     = null;

                    $component->Description = (isset($info['mission'])) ? $info['mission'] : $component->Description;
                    $component->WikiUrl = $wiki;

                    if(!empty($ptl) && is_array($ptl)) {
                        $fname = null;
                        $lname = null;
                        $email = null;

                        if(isset($ptl['name'])) {
                            $ptl_names = preg_split("/\s/", $ptl['name']);
                            $fname = $ptl_names[0];
                            $lname = $ptl_names[1];
                        }

                        if(isset($ptl['email'])) {
                            $email = trim($ptl['email']);
                        }

                        if(!empty($email)) {
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

                    if(!is_null($ptl_member) && $component->LatestReleasePTLID != $ptl_member->ID) {
                        echo sprintf('setting PTL %s %s (%s) to Component %s', $ptl_member->FirstName, $ptl_member->Surname, $ptl_member->Email, $component->Name).PHP_EOL;
                        $component->LatestReleasePTLID = $ptl_member->ID;
                    }

                    if (!empty($deliverables) && is_array($deliverables) && $component->Slug) {
                        $deliverablesComponent = isset($deliverables[$component->Slug]) ? $deliverables[$component->Slug] : null;

                        if ($deliverablesComponent && isset($deliverablesComponent['tags'])) {
                            $component->Tags()->removeAll();

                            foreach ($deliverablesComponent['tags'] as $tag) {
                                if( !$tag_obj = OpenStackComponentTag::get()->filter('Name', $tag)->first() ) {
                                    $tag_obj = new OpenStackComponentTag();
                                    $tag_obj->Name = $tag;
                                    $tag_obj->write();
                                }

                                $component->addTag($tag_obj);
                            }
                        }
                    }

                    $component->write();
                }


            }
        } catch (Exception $ex) {
            echo $ex->getMessage().PHP_EOL;
            SS_Log::log($ex->getMessage(), SS_Log::ERR);
            return;
        }
    }

    private function processComponentsAndCategories()
    {
        // disable all categories
        DB::query("UPDATE `OpenStackComponentCategory` SET `Enabled` = 0 ");

        try {
            // iterate between files in repo
            $fileUrl = 'https://opendev.org/osf/openstack-map/raw/branch/master/';
            $files = ['deployment_tools.yaml','openstack_components.yaml','sdks.yaml'];

            foreach ($files as $file) {
                // now read yaml file of parent category and extract subcategories and components
                echo sprintf("processing file %s ", $file).PHP_EOL;
                $yamlResponse = $this->client->get($fileUrl.$file);

                if (is_null($yamlResponse)) continue;
                if ($yamlResponse->getStatusCode() != 200) continue;
                $body = $yamlResponse->getBody();
                if (is_null($body)) continue;
                $content = $body->getContents();
                if (empty($content)) continue;

                $categoryYaml = Spyc::YAMLLoadString($content);

                // DEBUG
                //echo 'Processing category ' . $categoryYaml['name'] . PHP_EOL;

                // create parent category per file
                $categoryName = $categoryYaml['name'];
                $category = OpenStackComponentCategory::get()->filter('Name', $categoryName)->first();
                if (!$category) {
                    $category = new OpenStackComponentCategory();
                    $category->Name = $categoryName;
                }

                $category->Enabled = 1;
                $category->write();
                $subCatOrder = 1;

                foreach($categoryYaml['tabs'] as $tab) {
                    $subcatName = $tab['name'];
                    $subcatDesc = $tab['prelude'];
                    //echo sprintf("- cat %s - %s ", $subcatName, $subcatDesc).PHP_EOL;

                    // one level categories will have a tab with same name as category, so we skip the level
                    if ($subcatName == $categoryName) {
                        $subcat = $category;
                        $subcat->Description = $subcatDesc;
                        $subcat->write();
                    } else {
                        $subcat = OpenStackComponentCategory::get()->filter('Name', $subcatName)->first();
                        if (!$subcat) {
                            $subcat = new OpenStackComponentCategory();
                            $subcat->Name = $subcatName;
                        }
                        $subcat->Enabled = 1;
                        $subcat->Description = $subcatDesc;
                        $subcat->ParentCategoryID = $category->ID;
                        $subcat->Order = $subCatOrder;
                        $subcat->write();

                        $subCatOrder++;
                    }


                    $subSubCatOrder = 1;
                    foreach($tab['categories'] as $subcategory) {
                        // DEBUG
                        //echo '-- Processing sub-category ' . $subcategory['category'] . PHP_EOL;

                        $subcatName2 = $subcategory['category'];

                        $subcat2 = OpenStackComponentCategory::get()->filter('Name', $subcatName2)->first();
                        if (!$subcat2) {
                            $subcat2 = new OpenStackComponentCategory();
                            $subcat2->Name = $subcatName2;
                        }
                        $subcat2->Enabled = 1;
                        $subcat2->ParentCategoryID = $subcat->ID;
                        $subcat2->OpenStackComponents()->removeAll();
                        $subcat2->Order = $subSubCatOrder;
                        $subcat2->write();

                        $subSubCatOrder++;

                        $compOrder = 1;
                        foreach($subcategory['components'] as $component) {
                            // DEBUG
                            //echo '--- Processing component ' . $component['name'] . PHP_EOL;
                            if(!isset($component['project-team']) || !$component['project-team']) {
                                echo 'project team missing: '.$component['project-team'].PHP_EOL;
                                continue;
                            }

                            $compSlug = $component['name'];

                            $comp = OpenStackComponent::get()->filter('Slug', $compSlug)->first();
                            if (!$comp) {
                                $comp = new OpenStackComponent();
                                $comp->Slug = $compSlug;
                            }

                            $comp->Name = (isset($component['title'])) ? $component['title'] : '';
                            $comp->CodeName = (isset($component['name'])) ? ucfirst($component['name']) : '';
                            $comp->ProjectTeam = (isset($component['project-team'])) ? $component['project-team'] : '';
                            $comp->Description = (isset($component['desc'])) ? $component['desc'] : '';
                            $comp->Since = (isset($component['since'])) ? $component['since'] : '';
                            $comp->CategoryID = $subcat2->ID;
                            $comp->Order = $compOrder;
                            $comp->YouTubeID = '';
                            $comp->VideoDescription = '';
                            $comp->VideoTitle = '';

                            $comp->CapabilityTags()->removeAll();
                            if (isset($component['capabilities'])) {
                                foreach ($component['capabilities'] as $cap) {
                                    if (isset($cap['tags'])) {
                                        foreach ($cap['tags'] as $tagName) {
                                            $tag = OpenStackComponentCapabilityTag::get()->filter('Name', $tagName)->first();
                                            if ($tag) {
                                                $comp->CapabilityTags()->add($tag);
                                            }
                                        }
                                    }
                                }
                            }

                            if (isset($component['video'])) {
                                $comp->YouTubeID = (isset($component['video']['id'])) ? $component['video']['id'] : '';
                                $comp->VideoDescription = (isset($component['video']['desc'])) ? $component['video']['desc'] : '';
                                $comp->VideoTitle = (isset($component['video']['title'])) ? $component['video']['title'] : '';
                            }

                            if (isset($component['docs-title']) && isset($component['docs-url'])) {
                                if (!$docsLink = $comp->DocsLink()) {
                                    $docsLink = new OpenStackComponentLink();
                                }
                                $docsLink->Label = $component['docs-title'];
                                $docsLink->URL = $component['docs-url'];
                                $docsLink->write();
                                $comp->DocsLinkID = $docsLink->ID;
                            } else {
                                $comp->DocsLinkID = null;
                            }

                            if (isset($component['download-title']) && isset($component['download-url'])) {
                                if (!$downloadLink = $comp->DownloadLink()) {
                                    $downloadLink = new OpenStackComponentLink();
                                }
                                $downloadLink->Label = $component['download-title'];
                                $downloadLink->URL = $component['download-url'];
                                $downloadLink->write();
                                $comp->DownloadLinkID = $downloadLink->ID;
                            } else {
                                $comp->DownloadLinkID = null;
                            }

                            // LINKS
                            $comp->Links()->removeAll();
                            if (isset($component['links'])) {
                                foreach ($component['links'] as $linkArray) {
                                    if (!is_array($linkArray)) continue;
                                    foreach ($linkArray as $label => $link) {
                                        $linkObj = OpenStackComponentLink::get()->filter(['Label' => $label, 'URL' => $link])->First();

                                        if (!$linkObj) {
                                            $linkObj = new OpenStackComponentLink();
                                            $linkObj->Label = $label;
                                            $linkObj->URL = $link;
                                            $linkObj->write();
                                        }

                                        $comp->Links()->add($linkObj);
                                    }
                                }
                            }

                            // SUPPORT TEAMS
                            $comp->SupportTeams()->removeAll();
                            if (isset($component['support-teams'])) {
                                foreach ($component['support-teams'] as $label) {

                                    $supportTeam = OpenStackComponent::get()->filter('Slug', $label)->First();
                                    if(!$supportTeam) continue;

                                    $comp->SupportTeams()->add($supportTeam);
                                }
                            }

                            // DEPENDENCIES
                            $comp->Dependencies()->removeAll();
                            if (isset($component['dependencies'])) {
                                foreach ($component['dependencies'] as $dep) {
                                    $compObj = OpenStackComponent::get()->filter('CodeName', $dep)->First();
                                    if (!$compObj) continue;
                                    $comp->Dependencies()->add($compObj);
                                }
                            }

                            // SEE ALSO - RELATED
                            $comp->RelatedComponents()->removeAll();
                            if (isset($component['see-also'])) {
                                foreach ($component['see-also'] as $related) {
                                    $compObj = OpenStackComponent::get()->filter('CodeName', $related)->First();
                                    if (!$compObj) continue;
                                    $comp->RelatedComponents()->add($compObj);
                                }
                            }

                            $comp->write();
                            $compOrder++;
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            echo 'NOT FOUND'.PHP_EOL;
            // echo $ex->getMessage() . PHP_EOL;
            // SS_Log::log($ex->getMessage(), SS_Log::WARN);
        }
    }

    private function processProjectPerRelease($release)
    {
        $url_template = "http://git.openstack.org/cgit/openstack/releases/plain/deliverables/%s/%s.yaml";

        foreach($release->OpenStackComponents() as $component){

            $custom_filename = $component->CustomTeamYAMLFileName;
            $url = empty($custom_filename) ? sprintf($url_template, strtolower($release->Name),  strtolower($component->CodeName)): $custom_filename;
            echo sprintf("processing url %s ", $url).PHP_EOL;
            $response = null;
            try {
                $response = $this->client->get
                (
                    $url
                );
            } catch (Exception $ex) {
                echo 'NOT FOUND'.PHP_EOL;
                // echo $ex->getMessage() . PHP_EOL;
                // SS_Log::log($ex->getMessage(), SS_Log::WARN);
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
                echo 'NOT FOUND'.PHP_EOL;
                // echo $ex->getMessage() . PHP_EOL;
                // SS_Log::log($ex->getMessage(), SS_Log::WARN);
            }
        }
    }

    private function processCapabilityList()
    {
        DB::query("UPDATE `OpenStackComponentCapabilityCategory` SET `Enabled` = 0 ");
        DB::query("UPDATE `OpenStackComponentCapabilityTag` SET `Enabled` = 0 ");

        try {
            $file = 'https://opendev.org/osf/openstack-map/raw/branch/master/deployment_tools_capabilities.yaml';

            echo "processing capability list ".PHP_EOL;
            $yamlResponse = $this->client->get($file);

            if (is_null($yamlResponse)) return;
            if ($yamlResponse->getStatusCode() != 200) return;
            $body = $yamlResponse->getBody();
            if (is_null($body)) return;
            $content = $body->getContents();
            if (empty($content)) return;

            $contentYaml = Spyc::YAMLLoadString($content);

            foreach($contentYaml['capabilities'] as $capCategory) {

                $catName = $capCategory['category'];
                $capCat = OpenStackComponentCapabilityCategory::get()->filter('Name', $catName)->first();
                if (!$capCat) {
                    $capCat = new OpenStackComponentCapabilityCategory();
                }

                $capCat->Name = $catName;
                $capCat->Description = $capCategory['description'];
                $capCat->Enabled = 1;
                $capCat->Tags()->removeAll();
                $capCat->write();

                foreach($capCategory['tags'] as $tag) {
                    $tagName = $tag['name'];
                    $capTag = OpenStackComponentCapabilityTag::get()->filter('Name', $tagName)->first();
                    if (!$capTag) {
                        $capTag = new OpenStackComponentCapabilityTag();
                    }

                    $capTag->Name = $tagName;
                    $capTag->Description = isset($tag['desc']) ? $tag['desc'] : '';
                    $capTag->Enabled = 1;
                    $capTag->write();

                    $capCat->Tags()->add($capTag);
                }

                $capCat->write();
            }
        } catch (Exception $ex) {
            echo 'NOT FOUND'.PHP_EOL;
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
            echo 'NOT FOUND'.PHP_EOL;
            // echo $ex->getMessage() . PHP_EOL;
            // SS_Log::log($ex->getMessage(), SS_Log::WARN);
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
            echo 'NOT FOUND'.PHP_EOL;
            // echo $ex->getMessage() . PHP_EOL;
            // SS_Log::log($ex->getMessage(), SS_Log::WARN);
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
            echo 'NOT FOUND'.PHP_EOL;
            // echo $ex->getMessage() . PHP_EOL;
            // SS_Log::log($ex->getMessage(), SS_Log::WARN);
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

            $timeline_url = sprintf($timeline_stats_url_template, strtolower($c->ProjectTeam), strtolower($release->Name));
            $company_url = sprintf($company_contrib_url_template, strtolower($c->ProjectTeam), strtolower($release->Name));
            $engineers_url = sprintf($engineers_contrib_url_template, strtolower($c->ProjectTeam), strtolower($release->Name));


            echo sprintf("processing url %s ", $timeline_url).PHP_EOL;

            try
            {
                $response = $this->client->get($timeline_url);
            }
            catch (Exception $ex)
            {
                echo 'NOT FOUND'.PHP_EOL;
                // echo $ex->getMessage() . PHP_EOL;
                // SS_Log::log($ex->getMessage(), SS_Log::WARN);
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

            echo sprintf("processing url %s ", $company_url).PHP_EOL;

            try
            {
                $response = $this->client->get($company_url);
            }
            catch (Exception $ex)
            {
                echo 'NOT FOUND'.PHP_EOL;
                // echo $ex->getMessage() . PHP_EOL;
                // SS_Log::log($ex->getMessage(), SS_Log::WARN);
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

            echo sprintf("processing url %s ", $engineers_url).PHP_EOL;

            try
            {
                $response = $this->client->get($engineers_url);
            }
            catch (Exception $ex)
            {
                echo 'NOT FOUND'.PHP_EOL;
                // echo $ex->getMessage() . PHP_EOL;
                // SS_Log::log($ex->getMessage(), SS_Log::WARN);
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