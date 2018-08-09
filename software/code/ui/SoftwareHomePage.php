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
class SoftwareHomePage extends Page
{
    static $create_table_options = array('MySQLDatabase' => 'ENGINE=InnoDB');

    private static $db = array
    (
        'IntroTitle'  => 'Text',
        'IntroText'   => 'HTMLText',
        'IntroTitle2' => 'Text',
        'IntroText2'  => 'HTMLText',
    );

    private static $has_many = array
    (
        'SubMenuPages' => 'SoftwareHomePageSubMenuItem'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('Content');
        $fields->addFieldsToTab('Root.Main',new TextField('IntroTitle', 'Intro Title'));
        $fields->addFieldsToTab('Root.Main',new HtmlEditorField('IntroText', 'Intro Text'));
        $fields->addFieldsToTab('Root.Main',new TextField('IntroTitle2', 'Intro Title 2'));
        $fields->addFieldsToTab('Root.Main',new HtmlEditorField('IntroText2', 'Intro Text 2'));

        $config   = new GridFieldConfig_RecordEditor(100);
        $sub_menu = new GridField("SubMenuPages", "SubMenu Pages", $this->SubMenuPages(), $config);
        $config->addComponent(new GridFieldSortableRows('Order'));
        $fields->push($sub_menu);

        return $fields;
    }

    public function getAllowedPagesForMenu()
    {
        return Page::get()
            ->filter('ParentID', (int)$this->ID)
            ->exclude('ID', (int)$this->ID);
    }
}

/**
 * Class SoftwareHomePage_Controller
 */
class SoftwareHomePage_Controller extends Page_Controller
{

    const MaxContributionsEntries = 10;

    /**
     * @var ISoftwareManager
     */
    private $manager;

    /**
     * @return ISoftwareManager
     */
    public function getSoftwareManager()
    {
        return $this->manager;
    }

    /**
     * @param ISoftwareManager $manager
     */
    public function setSoftwareManager(ISoftwareManager $manager)
    {
        $this->manager = $manager;
    }


    private static $allowed_actions = array
    (
        'allComponents',
        'getComponent',
        'getComponentsbyRelease',
        'getSampleConfigurations',
    );

    static $url_handlers = array
    (
        'GET project-navigator/$CATEGORY!'         => 'allComponents',
        'GET sample-configs'                       => 'getSampleConfigurations',
        'GET releases/$RELEASE_ID/components/$ID!' => 'getComponent',
        'GET releases/$RELEASE_ID/components'      => 'getComponentsbyRelease',
    );

    public function init()
    {
        parent::init();
		Requirements::add_i18n_javascript('software/lang');
        Requirements::css("node_modules/webui-popover/dist/jquery.webui-popover.min.css");
        Requirements::css("software/css/software.css");
        Requirements::javascript("node_modules/webui-popover/dist/jquery.webui-popover.min.js");
        Requirements::javascript("software/js/software.js");
    }

    public function index(SS_HTTPRequest $request)
    {
        $release = $this->getDefaultRelease();
        if(is_null($release)) return 'Default Release not set!';

        return $this->customise
        (
            array
            (   'Release' => $release,
                'StorageCoreServices' => $release->OpenStackComponents()->filter
                (
                    array
                    (
                        'IsCoreService' => true,
                        'Category.Name' => 'Object Storage'
                    )
                )->sort('Order','ASC'),
                'ComputeCoreServices' => $release->OpenStackComponents()->filter
                (
                    array
                    (
                        'IsCoreService' => true,
                        'Category.Name' => 'Compute'
                    )
                )->sort('Order','ASC'),
                'NoneCoreServices' => $release->OpenStackComponents()->filter
                (
                    array
                    (
                        'IsCoreService' => true,
                        'Category.Name' => 'None'
                    )
                )->sort('Order','ASC'),
                'OptionalServices' => $release->OpenStackComponents()->filter
                (
                    array
                    (
                        'IsCoreService' => false,
                    )
                )->sort('Order','ASC'),
            )
        )->renderWith(array('SoftwareHomePage'));
    }

    public function allComponents(SS_HTTPRequest $request)
    {
        $categorySlug   = Convert::raw2sql($request->param('CATEGORY'));
        $category       = OpenStackComponentCategory::get()->filter('Slug', $categorySlug)->first();

        if(is_null($category)) return $this->httpError(404);

        $categoryId     = $category->ID;

        $depth = 1;
        while($category->SubCategories()->count()) {
            $depth++;
            $category = $category->SubCategories()->first();
        }

        Requirements::css("themes/openstack/javascript/seiyria-bootstrap-slider/dist/css/bootstrap-slider.min.css");
        Requirements::javascript("themes/openstack/javascript/seiyria-bootstrap-slider/dist/bootstrap-slider.min.js");

        return $this->render(array
            (
                'CategoryId'    => $categoryId,
                'CategoryDepth' => $depth
            )
        );
    }

    public function getComponent(SS_HTTPRequest $request)
    {

        JQPlotDependencies::renderRequirements();

        Requirements::javascript('software/js/openstack-component-details.js');

        $release_id   = Convert::raw2sql($request->param('RELEASE_ID'));
        $component_id = Convert::raw2sql($request->param('ID'));

        $release      = OpenStackRelease::get()->filter('Name',ucfirst($release_id))->first();
        if(is_null($release)) return $this->httpError(404);
        $component    = OpenStackComponent::get()->filter('Slug', $component_id)->first();
        if(is_null($component)) return $this->httpError(404);

        $component = $release->supportsComponent($component->CodeName);
        if(is_null($component)) return $this->httpError(404);

        // individual contributors

        $engineers_contrib = new ArrayList();
        $json              = $component->MostActiveContributorsByIndividualJson;

        if(!empty($json))
        {
            $data = json_decode($json, true);
            if(!is_null($data))
            {
                $stats = $data['stats'];
                $i = 0;
                foreach($stats as $entry)
                {
                    $engineers_contrib->add(new ArrayData( array( 'Name' => $entry['name'])));
                    ++$i;
                    if($i === self::MaxContributionsEntries) break;
                }
            }
        }

        $companies_contrib = new ArrayList();
        $json              = $component->MostActiveContributorsByCompanyJson;

        if(!empty($json))
        {
            $data = json_decode($json, true);
            if(!is_null($data))
            {
                $stats = $data['stats'];
                $i = 0;
                foreach($stats as $entry)
                {
                    $companies_contrib->add(new ArrayData( array( 'Name' => $entry['name'])));
                    ++$i;
                    if($i === self::MaxContributionsEntries) break;
                }
            }
        }

        $json = $component->ContributionsJson;

        if(!empty($json))
        {
            Requirements::customScript(" timeline_data = {$json};  renderTimeline();");
        }

        $has_maturity_indicators = ($component->Adoption > 75)
            || ($component->SDKSupport > 7)
            || ($component->HasInstallationGuide);

        $has_release_desc = $component->ReleaseMileStones
            || $component->ReleaseCycleWithIntermediary
            || $component->ReleaseTrailing
            || $component->ReleaseIndependent;


        $has_additional_info = (count($component->getInfoTags()) > 0) || $has_release_desc;

        return $this->render
        (
            array
            (
                'MostActiveIndividualContributors' => $engineers_contrib,
                'MostActiveCompanyContributors'    => $companies_contrib,
                'Release'                          => $release,
                'Component'                        => $component,
                'Releases'                         => $component->Releases()->where("Status <>'Deprecated' AND Name <> 'Trunk' ")->sort('ReleaseDate', 'DESC'),
                'HasMaturityIndicators'            => $has_maturity_indicators,
                'HasReleaseDesc'                   => $has_release_desc,
                'HasAdditionalInfo'                => $has_additional_info
            )
        );
    }

    public function getReleases()
    {
        $releases = OpenStackRelease::get()->sort('ReleaseDate','DESC');

        $res1 = array();

        foreach($releases as $r)
        {
            array_push($res1, array
            (
                'id'   => $r->ID,
                'name' => $r->Name,
            ));
        }

        return json_encode($res1);
    }

    /**
     * @return IOpenStackRelease
     */
    public function getDefaultRelease()
    {
        return  OpenStackRelease::get()->filter
        (
            array
            (
                'Status' => 'Current',
            )
        )->sort('ReleaseDate','DESC')->first();
    }

    public function getCurrentRelease()
    {
        $request      = $this->request;
        $release_id   = Convert::raw2sql($request->param('RELEASE_ID'));
        return OpenStackRelease::get()->filter('Name',ucfirst($release_id))->first();
    }


    public function getDefaultComponents()
    {
        list($core_components, $optional_components) = $this->manager->getComponents($this->getDefaultRelease());
        return json_encode
        (
            array
            (
                'core_components'     => $core_components,
                'optional_components' => $optional_components
            )
        );
    }

    public function getComponentsByCategoryJSON($categoryId)
    {
        $components = $this->manager->getComponentsGroupedByCategoryAndSubcategory($this->getDefaultRelease());

        return (isset($components[$categoryId])) ? json_encode($components[$categoryId]) : '';
    }

    public function getComponentCategories()
    {
        $categories = $this->manager->getComponentsGroupedByCategory($this->getDefaultRelease());
        $cat_list = new ArrayList();

        foreach ($categories as $category => $components) {
            $arr = array_filter(preg_split('/[,\s]+/', $category));
            if ($arr) {
                $cat_id = strtolower($arr[0]);
                $component_count = count($components);
                $cat_list->push(new ArrayData(array('Name' => $category, 'Id' => $cat_id, 'Count' => $component_count)));
            }
        }

        return $cat_list;
    }

    public function getSampleConfigurations()
    {
        $release = $this->getDefaultRelease();
        if(is_null($release)) return 'Default Release not set!';

        return $this->render
        (
            array
            (
                'Release' => $release,
            )
        );
    }

    public function getMaxAllowedMaturityPoints()
    {
        return MAX_ALLOWED_MATURITY_POINTS;
    }

    public function HasAvailableSampleConfigTypes()
    {
        $release = $this->getDefaultRelease();
        if(is_null($release)) return false;
        return intval($release->SampleConfigurationTypes()->count()) > 0;
    }

    public function getParentComponentCategories() {
        return OpenStackComponentCategory::get()->filter('ParentCategoryID', 0)->sort('Order');
    }
}