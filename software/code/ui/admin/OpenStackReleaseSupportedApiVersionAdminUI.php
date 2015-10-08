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
final class OpenStackReleaseSupportedApiVersionAdminUI
    extends DataExtension
{

    /**
     * @param FieldList $fields
     * @return FieldList|void
     */

    private $versions;

    public function updateCMSFields(FieldList $fields)
    {

        Requirements::javascript('marketplace/code/ui/admin/js/openstack.release.supported.api.version.admin.ui.js');
        $fields->removeByName('ApiVersionID');

        $versions = OpenStackApiVersion::get()->map('ID', 'Version');
        $ddl = new DropdownField('ApiVersionID', 'API Version', $versions);
        $ddl->addExtraClass('ddl-api-version-id');
        $ddl->setEmptyString('--Select An API Version --');
        $fields->insertAfter($ddl, 'OpenStackComponentID');


        $versions = array();
        foreach (OpenStackComponent::get()->filter('SupportsVersioning', true) as $component) {
            foreach ($component->getVersions() as $version) {
                if (!array_key_exists(intval($component->getIdentifier()), $versions)) {
                    $versions[intval($component->getIdentifier())] = array();
                }
                array_push($versions[intval($component->getIdentifier())],
                    array('value' => intval($version->getIdentifier()), 'text' => $version->getVersion()));
            }
        }

        $json_data = json_encode($versions);
        $script = <<<JS
		<script>
		var versions = {$json_data};
		</script>
JS;

        $fields->add(new LiteralField('js_data', $script));
        $fields->removeByName('OpenStackComponentID');
        //kludge; get parent id from url....
        $url = preg_split('/\//', $_REQUEST['url']);
        $release_id = (int)$url[8];
        $ddl = new DropdownField('OpenStackComponentID', 'OpenStack Component',
            OpenStackComponent::get()->filter('SupportsVersioning',
                true)->innerJoin('OpenStackRelease_OpenStackComponents',
                "OpenStackRelease_OpenStackComponents.OpenStackComponentID = OpenStackComponent.ID AND OpenStackReleaseID = {$release_id} ")->map('ID',
                'Name'));
        $ddl->setEmptyString('--Select A OS Component--');
        $ddl->addExtraClass('ddl-os-component-id');
        $fields->insertBefore($ddl, 'ApiVersionID');

        $fields->insertAfter(new TextField("ReleaseVersion", "Release Version"), 'ReleaseID');
        $fields->insertAfter(new LiteralField('ReleaseVersionTitle' ,'<p>You could get this data from <a href="http://docs.openstack.org/releases" target="_blank">http://docs.openstack.org/releases</a></p>'),'ReleaseVersion' );
        return $fields;
    }


    public function onBeforeWrite()
    {
        //create group here?
        parent::onBeforeWrite();
    }

    function getCMSValidator()
    {
        return $this->getValidator();
    }

    function getValidator()
    {
        $validator = new RequiredFields(array('OpenStackComponentID', 'ApiVersionID'));

        return $validator;
    }
}