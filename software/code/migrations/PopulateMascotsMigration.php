<?php

/**
 * Copyright 2016 OpenStack Foundation
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
class PopulateMascotsMigration extends AbstractDBMigrationTask
{
    protected $title = "PopulateMascotsMigration";

    protected $description = "PopulateMascotsMigration";

    function doUp()
    {
        global $database;

        $mascots = array (
            'Barbican' => 'Porcupine',
            'Chef OpenStack' => 'Kangaroo',
            'Cinder' => 'Horse',
            'CloudKitty' => 'Cat (maneki-neko)',
            'Community App Catalog' => 'Quokka',
            'Congress' => 'Raven',
            'Designate' => 'Crocodile',
            'Documentation' => 'Fox',
            'Dragonflow' => 'Seahorse',
            'EC2-API' => 'Toucan',
            'Freezer' => 'Polar Bear',
            'Glance' => 'Chipmunk',
            'Heat' => 'Flame/fire',
            'Horizon' => 'Shiba inu (dog)',
            'I18n' => 'Parrot',
            'Infrastructure' => 'Ant',
            'Interop' => 'Onion',
            'Ironic' => 'Bear',
            'Karbor' => 'Beaver',
            'Keystone' => 'Turtle',
            'Kolla' => 'Koala',
            'Kuryr' => 'Platypus',
            'Magnum' => 'Shark',
            'Manila' => 'Zorilla',
            'Mistral' => 'Dandelion',
            'Monasca' => 'Monitor lizard',
            'Murano' => 'Muraena eel (moray)',
            'Neutron' => 'Spider and its web',
            'Nova' => 'Supernova',
            'OpenStack Charms' => 'Orangutan',
            'OpenStack-Ansible' => 'Cape buffalo',
            'Octavia' => 'Peacock',
            'Oslo' => 'Moose',
            'Packaging-deb' => 'Lemur',
            'Packaging-rpm' => 'Donkey',
            'Puppet OpenStack' => 'Wolf',
            'Quality Assurance' => 'Little brown bat',
            'Rally' => 'White ermine',
            'RefStack' => 'Bee and its honeycomb',
            'Release Management' => 'Border collie',
            'Requirements' => 'Waterfall',
            'Sahara' => 'Elephant',
            'Searchlight' => 'Firefly',
            'Security' => 'Pangolin (scaly anteater)',
            'Senlin' => 'Forest',
            'Stable Branch Management' => 'Scarab beetle',
            'Storlets' => 'Storklet',
            'Tacker' => 'Giant squid',
            'Swift' => 'Swift',
            'Ceilometer' => 'Meerkat',
            'TripleO' => 'Owl',
            'Tricircle' => 'Panda',
            'Trove' => 'Stingray',
            'UX' => 'Octopus',
            'Vitrage' => 'Giraffe',
            'Watcher' => 'Jellyfish',
            'Winstackers' => 'Hawk',
            'Zaqar' => 'Carrier pigeon',
            'Zun' => 'Dolphin',
        );

        foreach ($mascots as $codename => $mascot) {
            if (!Mascot::get()->filter('Name', $mascot)->exists()) {
                $new_mascot = new Mascot();
                $new_mascot->Name = $mascot;
                $new_mascot->CodeName = $codename;
                $new_mascot_id = $new_mascot->write();

                $component = OpenStackComponent::get()->filter('CodeName', $codename)->first();
                if ($component) {
                    $component->MascotID = $new_mascot_id;
                    $component->write();
                }

            }
        }
    }

    function doDown()
    {

    }
}