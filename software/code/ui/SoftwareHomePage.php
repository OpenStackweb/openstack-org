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

    static $db = array
    (
        'IntroTitle' => 'Text',
        'IntroText'  => 'HTMLText',
        'IntroTitle2' => 'Text',
        'IntroText2'  => 'HTMLText',
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('Content');
        $fields->addFieldsToTab('Root.Main',new TextField('IntroTitle', 'Intro Title'));
        $fields->addFieldsToTab('Root.Main',new HtmlEditorField('IntroText', 'Intro Text'));
        $fields->addFieldsToTab('Root.Main',new TextField('IntroTitle2', 'Intro Title 2'));
        $fields->addFieldsToTab('Root.Main',new HtmlEditorField('IntroText2', 'Intro Text 2'));
        return $fields;
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
    );

    static $url_handlers = array
    (
        'GET all-projects'                         => 'allComponents',
        'GET releases/$RELEASE_ID/components/$ID!' => 'getComponent',
        'GET releases/$RELEASE_ID/components'      => 'getComponentsbyRelease',
    );


    public function init()
    {
        parent::init();
        Requirements::css("themes/openstack/bower_assets/webui-popover/dist/jquery.webui-popover.min.css");
        Requirements::css("software/css/software.css");
        Requirements::javascript("themes/openstack/bower_assets/webui-popover/dist/jquery.webui-popover.min.js");
        Requirements::javascript("software/js/software.js");
    }

    public function index()
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
                        'Use' => 'Object Storage'
                    )
                )->sort('Order','ASC'),
                'ComputeCoreServices' => $release->OpenStackComponents()->filter
                (
                    array
                    (
                        'IsCoreService' => true,
                        'Use' => 'Compute'
                    )
                )->sort('Order','ASC'),
                'NoneCoreServices' => $release->OpenStackComponents()->filter
                (
                    array
                    (
                        'IsCoreService' => true,
                        'Use' => 'None'
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
        Requirements::css("themes/openstack/bower_assets/seiyria-bootstrap-slider/dist/css/bootstrap-slider.min.css");
        Requirements::javascript("themes/openstack/bower_assets/seiyria-bootstrap-slider/dist/bootstrap-slider.min.js");
        return $this->render();
    }

    public function getComponent(SS_HTTPRequest $request)
    {
        Requirements::css("themes/openstack/bower_assets/jqplot-bower/dist/jquery.jqplot.min.css");
        //jqplot and plugins ...
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/jquery.jqplot.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.canvasAxisTickRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.dateAxisRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.cursor.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.categoryAxisRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.canvasTextRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.canvasOverlay.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.enhancedLegendRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.json2.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.logAxisRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.pointLabels.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.trendline.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.barRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.bubbleRenderer.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.canvasAxisLabelRenderer.min.js");
        Requirements::javascript("themes/openstack/bower_assets/jqplot-bower/dist/plugins/jqplot.highlighter.min.js");

        Requirements::javascript('software/js/openstack-component-details.js');

        $release_id   = Convert::raw2sql($request->param('RELEASE_ID'));
        $component_id = Convert::raw2sql($request->param('ID'));

        $release      = OpenStackRelease::get()->filter('Name',ucfirst($release_id))->first();
        if(is_null($release)) return $this->httpError(404);

        $component = $release->supportsComponent(ucfirst($component_id));
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

        return $this->render
        (
            array
            (
                'MostActiveIndividualContributors' => $engineers_contrib,
                'MostActiveCompanyContributors'    => $companies_contrib,
                'Release'                          => $release,
                'Component'                        => $component,
                'Releases'                         => $component->Releases()->where("Status <>'Deprecated' AND Name <> 'Trunk' ")->sort('ReleaseDate', 'DESC')
            )
        );
    }

    public function getReleases()
    {
        $releases = OpenStackRelease::get()->filter
        (
            array
            (
                'HasStatistics' => true,
            )
        )->sort('ReleaseDate','DESC');

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
                'HasStatistics' => true,
            )
        )->sort('ReleaseDate','DESC')->first();
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

    public function getMaxAllowedMaturityPoints()
    {
        return MAX_ALLOWED_MATURITY_POINTS;
    }
}