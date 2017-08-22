<?php
/**
 * Copyright 2017 Openstack Foundation
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
 * Class AddMarketPlaceHelpLinksMigration
 */
final class AddMarketPlaceHelpLinksMigration extends AbstractDBMigrationTask {

	protected $title = "Add MarketPlace Help Links Migration";

	protected $description = "add default help links to all products";

	function doUp(){

        $pages = MarketPlaceDirectoryPage::get();

        $links = array(
            [
                'Label' => 'Online Docs',
                'Link' => 'http://docs.openstack.org',
                'SortOrder' => 0,
            ],
            [
                'Label' => 'Operations Guide',
                'Link' => 'http://docs.openstack.org/ops',
                'SortOrder' => 1,
            ],
            [
                'Label' => 'Security Guide',
                'Link' => 'http://docs.openstack.org/security-guide',
                'SortOrder' => 2,
            ],
            [
                'Label' => 'Getting Started',
                'Link' => 'http://www.openstack.org/software/start',
                'SortOrder' => 3,
            ]
        );


        foreach ($pages as $page) {
            foreach ($links as $link) {
                $link_do = new MarketPlaceHelpLink();
                $link_do->Label = $link['Label'];
                $link_do->Link = $link['Link'];
                $link_do->SortOrder = $link['SortOrder'];
                $link_do->write();

                $page->HelpLinks()->add($link_do);
            }

            $page->write();
        }

	}

	function doDown()	{

	}
}