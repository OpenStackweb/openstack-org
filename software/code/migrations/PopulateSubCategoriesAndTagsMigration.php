<?php

/**
 * Copyright 2017 OpenStack Foundation
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

class PopulateSubCategoriesAndTagsMigration extends AbstractDBMigrationTask
{
    protected $title = "PopulateSubCategoriesAndTagsMigration";

    protected $description = "Create new heriarchy with categories and subcategories and populate component tags";

    function doUp()
    {
        global $database;

        $tags = array(
            [
                'Name' => 'assert:follows-standard-deprecation',
                'Label' => 'follows standard deprecation',
                'Type' => 'maturity',
                'Description' => 'The “assert:follows-standard-deprecation” tag asserts that the project will follow standard feature deprecation rules',
                'Link' => 'http://governance.openstack.org/reference/tags/assert_follows-standard-deprecation.html',
                'LabelTranslationKey' => 'FOLLOW_DEPRECATION',
                'DescriptionTranslationKey' => 'DEPRECATION_TAG_DESCRIPTION',
            ],
            [
                'Name' => 'supports-api-interoperability',
                'Label' => 'supports api interoperability',
                'Type' => 'maturity',
                'Description' => '',
                'Link' => '',
                'LabelTranslationKey' => '',
                'DescriptionTranslationKey' => '',
            ],
            [
                'Name' => 'assert:supports-upgrade',
                'Label' => 'supports minimal cold (offline) upgrade capabilities',
                'Type' => 'maturity',
                'Description' => 'asserts that the project will support minimal cold (offline) upgrade capabilities',
                'Link' => 'http://governance.openstack.org/reference/tags/assert_supports-upgrade.html',
                'LabelTranslationKey' => 'MINIMAL_UPGRADE',
                'DescriptionTranslationKey' => 'MINIMAL_UPGRADE_DESCRIPTION',
            ],
            [
                'Name' => 'assert:supports-rolling-upgrade',
                'Label' => 'supports minimal rolling upgrade capabilities',
                'Type' => 'maturity',
                'Description' => 'tag asserts that the project will support minimal rolling upgrade capabilities.',
                'Link' => 'http://governance.openstack.org/reference/tags/assert_supports-rolling-upgrade.html',
                'LabelTranslationKey' => 'MINIMAL_ROLLING',
                'DescriptionTranslationKey' => 'MINIMAL_ROLLING_DESCRIPTION',
            ],
            [
                'Name' => 'supports-zero-downtime-upgrade',
                'Label' => 'supports zero downtime upgrade',
                'Type' => 'maturity',
                'Description' => '',
                'Link' => '',
                'LabelTranslationKey' => '',
                'DescriptionTranslationKey' => '',
            ],
            [
                'Name' => 'vulnerability:managed',
                'Label' => 'Are vulnerability issues managed by the OpenStack security team?',
                'Type' => 'info',
                'Description' => '',
                'Link' => 'http://governance.openstack.org/reference/tags/vulnerability_managed.html',
                'LabelTranslationKey' => 'VULNERABILITY_ISSUES',
                'DescriptionTranslationKey' => '',
            ],
            [
                'Name' => 'team:diverse-affiliation',
                'Label' => 'team has achieved corporate diversity',
                'Type' => 'info',
                'Description' => 'A project with this tag has achieved a level of diversity in the affiliation of contributors that is indicative of a healthy collaborative project. This tag exists in the ‘team’ category, which as the name implies, covers information about the team itself. Another example of a tag that could exist in this category is one that conveys the size of the team that is actively contributing.',
                'Link' => 'http://governance.openstack.org/reference/tags/team_diverse-affiliation.html',
                'LabelTranslationKey' => 'USED_IN_CORPORATE',
                'DescriptionTranslationKey' => 'PROJECT_DIVERSITY',
            ],
            [
                'Name' => 'stable:follows-policy',
                'Label' => 'is maintained following the common Stable branch policy',
                'Type' => 'info',
                'Description' => '',
                'Link' => 'http://docs.openstack.org/project-team-guide/stable-branches.html',
                'LabelTranslationKey' => 'STABLE_BRANCHES',
                'DescriptionTranslationKey' => '',
            ],
            [
                'Name' => 'status:maintenance-mode',
                'Label' => 'status maintenance mode',
                'Type' => 'info',
                'Description' => '',
                'Link' => '',
                'LabelTranslationKey' => '',
                'DescriptionTranslationKey' => '',
            ]
        );

        $categories = array(
            [
                'Name' => 'OpenStack',
                'Description' => 'user-facing components that deployers may opt to deploy to add functionality to their OpenStack deployment.',
                'Order' => 1,
                'SubCategories' => [
                    ['Name' => 'Web frontend', 'Order' => 1, 'Components' => ['Horizon']],
                    ['Name' => 'API proxies', 'Order' => 2, 'Components' => ['EC2API']],
                    ['Name' => 'Workload provisioning', 'Order' => 3, 'Components' => ['Magnum','Trove','Sahara']],
                    ['Name' => 'Application lifecycle', 'Order' => 4, 'Components' => ['Murano','Freezer','Solum','Masakari']],
                    ['Name' => 'Orchestration', 'Order' => 5, 'Components' => ['Heat','Mistral','Aodh','Senlin','Zaqar','Blazar']],
                    ['Name' => 'Compute', 'Order' => 6, 'Components' => ['Nova','Ironic','Zun']],
                    ['Name' => 'Networking', 'Order' => 7, 'Components' => ['Neutron','Octavia','Designate']],
                    ['Name' => 'Storage', 'Order' => 8, 'Components' => ['Swift','Cinder','Manila']],
                    ['Name' => 'Shared services', 'Order' => 9, 'Components' => ['Keystone','Glance','Barbican','Searchlight','Karbor']]
                ]
            ],
            [
                'Name' => 'OpenStack Operations',
                'Description' => 'mainly operator-facing components that deployers may opt to deploy to facilitate managing their OpenStack deployment.',
                'Order' => 2,
                'SubCategories' => [
                    ['Name' => 'Monitoring tools', 'Order' => 1, 'Components' => ['Ceilometer','Panko','Monasca']],
                    ['Name' => 'Optimization / Policy tools', 'Order' => 2, 'Components' => ['Watcher','Vitrage','Congress','Rally','Cyborg']],
                    ['Name' => 'Billing / Business logic', 'Order' => 3, 'Components' => ['CloudKitty']],
                    ['Name' => 'Multi-region tools', 'Order' => 4, 'Components' => ['Tricircle']]
                ]
            ],
            [
                'Name' => 'OpenStack LifeCycle Management',
                'Description' => 'tools and recipes that deployers may choose to help them deploy and maintain the lifecycle of their OpenStack deployment.',
                'Order' => 3,
                'SubCategories' => [
                    ['Name' => 'Deployment/Lifecycle tools', 'Order' => 1, 'Components' => ['Kolla-Ansible','Kolla-K8s','TripleO','Bifrost']],
                    ['Name' => 'Packaging recipes', 'Order' => 2, 'Components' => ['RPM','Ansible','Puppet','Chef','Charms','Helm','OCI containers']]
                ]
            ],
            [
                'Name' => 'OpenStack User',
                'Description' => 'tools that end users may want to install to interact with an OpenStack cloud',
                'Order' => 4,
                'SubCategories' => [
                    ['Name' => 'SDK', 'Order' => 1, 'Components' => ['OpenStackClient','Shade','Python SDK']],
                ]
            ],
            [
                'Name' => 'OpenStack Adjacent Enablers',
                'Description' => 'bridges that let you reuse OpenStack components in other infra stacks',
                'Order' => 5,
                'SubCategories' => [
                    ['Name' => 'Container infrastructure', 'Order' => 1, 'Components' => ['Kuryr','Fuxi']],
                    ['Name' => 'NFV', 'Order' => 2, 'Components' => ['Tacker']],
                ]
            ]
        );

        foreach ($tags as $tag) {
            if (!$tag_obj = OpenStackComponentTag::get()->filter('Name', $tag['Name'])->first()) {
                $tag_obj = new OpenStackComponentTag();
                $tag_obj->Name = $tag['Name'];
            }

            $tag_obj->Label = $tag['Label'];
            $tag_obj->Type = $tag['Type'];
            $tag_obj->Description = $tag['Description'];
            $tag_obj->Link = $tag['Link'];
            $tag_obj->LabelTranslationKey = $tag['LabelTranslationKey'];
            $tag_obj->DescriptionTranslationKey = $tag['DescriptionTranslationKey'];
            $tag_obj->write();
        }

        // remove all current categories

        $old_categories = OpenStackComponentCategory::get();
        foreach( $old_categories as $old_cat) {
            $old_cat->delete();
        }

        // add new categories heriarchy

        foreach ($categories as $cat) {
            if (!$cat_obj = OpenStackComponentCategory::get()->filter('Name', $cat['Name'])->first()) {
                $cat_obj = new OpenStackComponentCategory();
                $cat_obj->Name = $cat['Name'];
                $cat_obj->write();
            }

            $cat_obj->Description = $cat['Description'];
            $cat_obj->Order = $cat['Order'];

            foreach ($cat['SubCategories'] as $sub_cat) {
                if (!$sub_cat_obj = OpenStackComponentCategory::get()->filter('Name', $sub_cat['Name'])->first()) {
                    $sub_cat_obj = new OpenStackComponentCategory();
                    $sub_cat_obj->Name = $sub_cat['Name'];
                    $sub_cat_obj->Order = $sub_cat['Order'];
                    $sub_cat_obj->write();
                }

                foreach ($sub_cat['Components'] as $comp) {
                    $comp_obj = OpenStackComponent::get()->where("Name = '".$comp."' OR CodeName = '".$comp."'")->first();
                    if ($comp_obj) {
                        $sub_cat_obj->OpenStackComponents()->add($comp_obj);
                    }
                }

                $sub_cat_obj->write();
                $cat_obj->SubCategories()->add($sub_cat_obj);
            }

            $cat_obj->write();
        }

    }
}