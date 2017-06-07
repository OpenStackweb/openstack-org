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
 * Class OpenStackImplementationAssembler
 */
final class OpenStackImplementationAssembler
{

    /**
     * @param IOpenStackImplementation $implementation
     * @return array
     */
    public static function convertOpenStackImplementationToArray(IOpenStackImplementation $implementation)
    {
        $res = RegionalSupportedCompanyServiceAssembler::convertRegionalSupportedCompanyServiceToArray($implementation);

        //capabilities
        $capabilities = array();
        foreach ($implementation->getCapabilities() as $capability) {
            $capability = OpenStackImplementationAssembler::convertCapabilityToArray($capability);
            if(!is_null($capability))
            array_push($capabilities, $capability);
        }
        $res['capabilities'] = $capabilities;
        //hypervisors
        $hypervisors = array();
        foreach ($implementation->getHyperVisors() as $hypervisor) {
            array_push($hypervisors, $hypervisor->getIdentifier());
        }
        $res['hypervisors'] = $hypervisors;
        //os guests
        $guest_os = array();
        foreach ($implementation->getGuests() as $guest) {
            array_push($guest_os, $guest->getIdentifier());
        }
        $res['guest_os'] = $guest_os;
        //draft
        if ($res) {
            return $res;
        }
    }

    /**
     * @param IOpenStackImplementationApiCoverage $capability
     * @return array
     */
    public static function convertCapabilityToArray(IOpenStackImplementationApiCoverage $capability)
    {
        $res = array();
        $res['id'] = $capability->getIdentifier();
        $release_api_version = $capability->getReleaseSupportedApiVersion();
        if($release_api_version->ID === 0 ) return null;

        $res['component_id'] = $release_api_version->getOpenStackComponent()->getIdentifier();
        $res['supports_versioning'] = $release_api_version->getOpenStackComponent()->getSupportsVersioning();
        $res['release_id'] = $release_api_version->getRelease()->getIdentifier();
        $res['version_id'] = $release_api_version->getApiVersion()->getIdentifier();
        $res['version_name'] = $release_api_version->getApiVersion()->getVersion();
        $res['coverage'] = $capability->getCoveragePercent();

        return $res;
    }

} 