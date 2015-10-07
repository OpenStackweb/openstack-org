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

}

/**
 * Class SoftwareHomePage_Controller
 */
class SoftwareHomePage_Controller extends Page_Controller
{

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
        return $this->customise
        (
            array
            (
                'StorageCoreServices' => OpenStackComponent::get()->filter
                (
                    array
                    (
                        'IsCoreService' => true,
                        'Use' => 'Object Storage'
                    )
                ),
                'ComputeCoreServices' => OpenStackComponent::get()->filter
                (
                    array
                    (
                        'IsCoreService' => true,
                        'Use' => 'Compute'
                    )
                ),
                'NoneCoreServices' => OpenStackComponent::get()->filter
                (
                    array
                    (
                        'IsCoreService' => true,
                        'Use' => 'None'
                    )
                ),
                'OptionalServices' => OpenStackComponent::get()->filter
                (
                    array
                    (
                        'IsCoreService' => false,
                    )
                ),
            )
        )->renderWith(array('SoftwareHomePage'));
    }

    public function allComponents(SS_HTTPRequest $request)
    {
        return $this->render();
    }

    public function getComponent(SS_HTTPRequest $request)
    {
        $release_id   = intval($request->param('RELEASE_ID'));
        $component_id = intval($request->param('ID'));

        $release      = OpenStackRelease::get()->byID($release_id);
        if(is_null($release)) return $this->httpError(404);

        $component = $release->getComponentById($component_id);
        if(is_null($component)) return $this->httpError(404);

        return $this->render
        (
            array
            (
                'Release'   => $release,
                'Component' => $component
            )
        );
    }

    public function getReleases()
    {
        $releases = OpenStackRelease::get()->filter
        (
            array
            (
                'Status' => 'Current',
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
        return  OpenStackRelease::get()->sort('ReleaseDate','DESC')->first();
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