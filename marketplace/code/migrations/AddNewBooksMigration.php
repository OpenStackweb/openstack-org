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
 * Class AddNewBooksMigration
 */
final class AddNewBooksMigration extends AbstractDBMigrationTask {

	protected $title = "Add New Books to Market place Migration";

	protected $description = "Add books to show on marketplace";

	function doUp(){

        // get company
        $company = Company::get()->filter('Name', 'Packt')->first();
        if (!$company) {
            echo 'Company Packt is missing.';
            return;
        }

        // add books
        $books = array(
            [
                'title' => 'OpenStack Administration with Ansible',
                'link'  => 'https://www.packtpub.com/virtualization-and-cloud/openstack-administration-ansible',
                'description' => '',
                'company_id' => $company->ID,
                'authors' => [
                    ['first_name' => 'Walter', 'last_name' => 'Bentley']
                ]
            ],
            [
                'title' => 'Troubleshooting OpenStack',
                'link'  => 'https://www.packtpub.com/virtualization-and-cloud/troubleshooting-openstack',
                'description' => '',
                'company_id' => $company->ID,
                'authors' => [
                    ['first_name' => 'Tony', 'last_name' => 'Campbell']
                ]
            ],
            [
                'title' => 'OpenStack Trove Essentials',
                'link'  => 'https://www.packtpub.com/virtualization-and-cloud/openstack-trove-essentials',
                'description' => '',
                'company_id' => $company->ID,
                'authors' => [
                    ['first_name' => 'Alok', 'last_name' => 'Shrivastwa'],
                    ['first_name' => 'Sunil', 'last_name' => 'Sarat']
                ]
            ],
            [
                'title' => 'OpenStack Networking Essentials',
                'link'  => 'https://www.packtpub.com/virtualization-and-cloud/openstack-networking-essentials',
                'description' => '',
                'company_id' => $company->ID,
                'authors' => [
                    ['first_name' => 'James', 'last_name' => 'Denton']
                ]
            ],
            [
                'title' => 'OpenStack Sahara Essentials',
                'link'  => 'https://www.packtpub.com/virtualization-and-cloud/openstack-sahara-essentials',
                'description' => '',
                'company_id' => $company->ID,
                'authors' => [
                    ['first_name' => 'Omar', 'last_name' => 'Khedher']
                ]
            ],
            [
                'title' => 'OpenStack Essentials - Second Edition',
                'link'  => 'https://www.packtpub.com/virtualization-and-cloud/openstack-essentials-second-edition',
                'description' => '',
                'company_id' => $company->ID,
                'authors' => [
                    ['first_name' => 'Dan', 'last_name' => 'Radez']
                ]
            ],
            [
                'title' => 'Software-Defined Networking (SDN) with OpenStack',
                'link'  => 'https://www.packtpub.com/virtualization-and-cloud/software-defined-networking-sdn-openstack',
                'description' => '',
                'company_id' => $company->ID,
                'authors' => [
                    ['first_name' => 'Sriram', 'last_name' => 'Subramanian'],
                    ['first_name' => 'Sreenivas', 'last_name' => 'Voruganti']
                ]
            ],
            [
                'title' => 'OpenStack: Building a Cloud Environment (Course)',
                'link'  => 'https://www.packtpub.com/virtualization-and-cloud/openstack-building-cloud-environment',
                'description' => '',
                'company_id' => $company->ID,
                'authors' => [
                    ['first_name' => 'Alok', 'last_name' => 'Shrivastwa'],
                    ['first_name' => 'Sunil', 'last_name' => 'Sarat'],
                    ['first_name' => 'Kevin', 'last_name' => 'Jackson'],
                    ['first_name' => 'Egle', 'last_name' => 'Sigler'],
                    ['first_name' => 'Tony', 'last_name' => 'Campbell'],
                    ['first_name' => 'Cody', 'last_name' => 'Bunch']
                ]
            ],
        );

        $manager = new BookManager (
            new SapphireMarketPlaceTypeRepository,
            new BookFactory,
            new MarketplaceFactory,
            new ValidatorFactory,
            SapphireTransactionManager::getInstance()
        );

        try {
            foreach ($books as $book) {
                $manager->addBook($book);
            }
        } catch (Exception $ex) {
            echo "Book already in DB.";
        }

	}

	function doDown()	{

	}
}