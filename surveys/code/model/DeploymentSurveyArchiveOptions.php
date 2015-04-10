<?php
/**
 * Copyright 2015 Openstack Foundation
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

final class DeploymentSurveyArchiveOptions {

    public static $industry_options = array(
        'Academic / Research' => 'Academic / Research / Education',
        'Consumer Goods' => 'Consumer Goods',
        'Energy' => 'Energy',
        'Film/Media' => 'Film / Media / Entertainment',
        'Finance' => 'Finance & Investment',
        'Government / Defense' => 'Government / Defense',
        'Healthcare' => 'Healthcare',
        'Information Technology' => 'Information Technology',
        'Insurance' => 'Insurance',
        'Manufacturing/Industrial' => 'Manufacturing / Industrial',
        'Retail' => 'Retail',
        'Telecommunications' => 'Telecommunications',
        'Transportation/Shipping' => 'Transportation / Shipping',
    );

    public static $organization_size_options = array(
        '1-20 employees' => '1-20 employees',
        '21-100 employees' => '21-100 employees',
        '101 to 500 employees' => '101 to 500 employees',
        '501 to 1,000 employees' => '501 to 1,000 employees',
        '1,001 to 5,000 employees' => '1,001 to 5,000 employees',
        '5,001 to 10,000 employees' => '5,001 to 10,000 employees',
        'More than 10,000 employees' => 'More than 10,000 employees'
    );

    public static $openstack_recommendation_rate_options = array(
        '0' => '0',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        '8' => '8',
        '9' => '9',
        '10' => '10',
    );

    public static $openstack_involvement_options = array(
        'Service Provider' => 'OpenStack cloud service provider - provides public or hosted private cloud services for other organizations',
        'Ecosystem Vendor' => 'Ecosystem vendor - provides software or solutions that enable others to build or run OpenStack clouds',
        'Cloud operator' => 'Private cloud operator - runs an OpenStack private cloud for their own organization',
        'Cloud Consumer' => 'Consumer of an OpenStack cloud - has API or dashboard credentials for one or more OpenStack resource pools, including an <strong>Application Developer<strong>'
    );

    public static $information_options = array(
        'Ask OpenStack (ask.openstack.org)' => 'Ask OpenStack (ask.openstack.org)',
        'Blogs' => 'Blogs',
        'docs.openstack.org' => 'docs.openstack.org',
        'IRC' => 'IRC',
        'OpenStack Mailing List' => 'OpenStack Mailing List',
        'OpenStack Dev Mailing List' => 'OpenStack Dev Mailing List',
        'The OpenStack Operations Guide' => 'The OpenStack Operations Guide',
        'Other Online Forums' => 'Online Forums',
        'OpenStack Planet' => 'OpenStack Planet (planet.openstack.org)',
        'Source Code' => 'Read the source code',
        'Local user group' => 'Local user group',
        'OpenStack Operators Mailing List' => 'OpenStack Operators Mailing List',
        'Superuser' => 'Superuser',
        'Vendor documentation' => 'Vendor documentation',
    );

    public static $business_drivers_options = array(
        'Cost savings' => 'Cost savings',
        'Operational efficiency' => 'Operational efficiency',
        'Time to market' => 'Time to market, ability to deploy applications faster',
        'Avoiding vendor lock-in' => 'Avoiding vendor lock-in',
        'Ability to innovate, compete' => 'Ability to innovate, compete',
        'Flexibility of underlying technology choices' => 'Flexibility of underlying technology choices',
        'Attracting talent' => 'Building a technology environment that attracts top technical talent',
        'Open technology' => 'Adopting an open technology platform',
        'Control' => 'Control of platform to achieve security and privacy goals',
    );

}