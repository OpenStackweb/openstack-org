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
final class SoftwareManager implements ISoftwareManager
{

    /**
     * @var IEntitySerializer
     */
    private $serializer;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * SoftwareManager constructor.
     * @param IEntitySerializer $serializer
     * @param ITransactionManager $tx_manager
     */
    public function __construct(IEntitySerializer $serializer, ITransactionManager $tx_manager)
    {
        $this->serializer = $serializer;
        $this->tx_manager = $tx_manager;
    }

    /**
     * @param IOpenStackRelease $release
     * @param string $term
     * @param int $adoption
     * @param int $maturity
     * @param int $age
     * @param string $sort
     * @param string $sort_dir
     * @return array
     */
    public function getComponents(IOpenStackRelease $release , $term = '', $adoption = 0, $maturity = 0, $age = 0, $sort = '', $sort_dir = '')
    {
        $res1 = array();
        $res2 = array();

        $core_components     = $release->getOpenStackCoreComponents($term, $adoption, $maturity, $age);
        $optional_components = $release->getOpenStackOptionalComponents($term, $adoption, $maturity, $age, $sort, $sort_dir);

        foreach($core_components as $c)
        {
            array_push($res1, $this->serializer->serialize($c));
        }

        foreach($optional_components as $c)
        {
            array_push($res2, $this->serializer->serialize($c));
        }

        return array($res1, $res2);
    }

    /**
     * @param IOpenStackRelease $release
     * @return IOpenStackRelease
     */
    public function cloneRelease(IOpenStackRelease $release){

        return $this->tx_manager->transaction(function() use($release){
            $clone = OpenStackRelease::create();
            $index = '';
            $idx   = 0;

            do
            {
                $proposed_name = $release->Name . '-CLONE'.$index;
                ++$idx;
                $index = '-'.$idx;
            }
            while( intval(OpenStackRelease::get()->filter('Name', $proposed_name)->count()) > 0);

            $clone->Name          = $proposed_name;
            $clone->Status        = 'Future';
            $clone->write();
            //components
            foreach($release->OpenStackComponents() as $component)
            {
                $clone->OpenStackComponents()->add($component);
            }
            //supported apis
            foreach($release->SupportedApiVersions() as $api)
            {
                $new_api = OpenStackReleaseSupportedApiVersion::create();
                $new_api->OpenStackComponentID = $api->OpenStackComponentID;
                $new_api->ApiVersionID         = $api->ApiVersionID;
                $new_api->ReleaseVersion       = $api->ReleaseVersion;
                $new_api->ReleaseID            = $clone->ID;
                $new_api->write();
            }
            //sample configurations
            foreach($release->SampleConfigurationTypes() as $config_type)
            {
                $new_config_type               = OpenStackSampleConfigurationType::create();
                $new_config_type->Type         = $config_type->Type;
                $new_config_type->Order        = $config_type->Order;
                $new_config_type->IsDefault    = $config_type->IsDefault;
                $new_config_type->write();

                foreach($config_type->SampleConfigurations() as $sample)
                {
                    $new_sample = OpenStackSampleConfig::create();

                    $new_sample->Title       = $sample->Title;
                    $new_sample->Summary     = $sample->Summary;
                    $new_sample->Description = $sample->Description;
                    $new_sample->IsDefault   = $sample->IsDefault;
                    $new_sample->Order       = $sample->Order;
                    $new_sample->CuratorID   = $sample->CuratorID;
                    $new_sample->ReleaseID   = $clone->ID;
                    $new_sample->write();

                    foreach($sample->RelatedNotes() as $note)
                    {
                        $new_note = OpenStackSampleConfigRelatedNote::create();
                        $new_note->Title = $note->Title;
                        $new_note->Link  = $note->Link;
                        $new_note->Order = $note->Order;
                        $new_note->write();

                        $new_sample->RelatedNotes()->add($new_note);
                    }

                    foreach($sample->OpenStackComponents() as $sample_comp)
                    {
                        $new_sample->OpenStackComponents()->add($sample_comp, array('Order' => $sample_comp->Order));
                    }

                    $new_config_type->SampleConfigurations()->add($new_sample);
                }

                $clone->SampleConfigurationTypes()->add($new_config_type);
            }

            return $clone;
        });
    }
}