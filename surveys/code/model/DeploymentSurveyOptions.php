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

final class DeploymentSurveyOptions {

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
    );

    public static $organization_size_options = array(
        '1 to 9 employees' => '1 to 9 employees',
        '10 to 99 employees' => '10 to 99 employees',
        '100 to 999 employees' => '100 to 999 employees',
        '1,000 to 9,999 employees' => '1,000 to 9,999 employees',
        '10,000 to 99,999 employees' => '10,000 to 99,999 employees',
        '100,000 employees or more' => '100,000 employees or more',
        'Don’t know / not sure' => 'Don’t know / not sure',
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
        'Cloud operator' => 'Private cloud operator - Runs an OpenStack private cloud for your own organization',
        'Cloud Consumer' => 'Consumer of an OpenStack cloud - has API or dashboard credentials for one or more OpenStack resource pools, including an Application Developer'
    );

    public static $information_options = array(
        'Ask OpenStack (ask.openstack.org)' => 'Ask OpenStack (ask.openstack.org)',
        'Blogs' => 'Blogs',
        'docs.openstack.org' => 'docs.openstack.org',
        'IRC' => 'IRC',
        'Local user group' => 'Local user group',
        'OpenStack Mailing List' => 'OpenStack Mailing List',
        'OpenStack Operators Mailing List' => 'OpenStack Operators Mailing List',
        'OpenStack Dev Mailing List' => 'OpenStack Dev Mailing List',
        'The OpenStack Operations Guide' => 'The OpenStack Operations Guide',
        'Online Forums' => 'Online Forums',
        'OpenStack Planet (planet.openstack.org)' => 'OpenStack Planet (planet.openstack.org)',
        'Read the source code' => 'Read the source code',
        'Superuser' => 'Superuser.openstack.org',
        'Vendor documentation' => 'Vendor documentation',
    );

    public static $business_drivers_options = array(
        'Save money over alternative infrastructure choices' => 'Save money over alternative infrastructure choices',
        'Increase operational efficiency' => 'Increase operational efficiency',
        'Accelerate my organization\’s ability to innovate and compete by deploying applications faster' => 'Accelerate my organization’s ability to innovate and compete by deploying applications faster',
        'Avoid vendor lock-in with an open platform and ecosystem including flexibility of underlying technology choices' => 'Avoid vendor lock-in with an open platform and ecosystem, including flexibility of underlying technology choices',
        'Attract top technical talent by participating in an active global technology community' => 'Attract top technical talent by participating in an active, global technology community',
        'Achieve security and/or privacy goals with control of platform' => 'Achieve security and/or privacy goals with control of platform',
        'Standardize on the same open platform and APIs that power a global network of of public and private clouds' => 'Standardize on the same open platform and APIs that power a global network of of public and private clouds',
        'Other' => 'Something else not listed here',
    );

    public static $activities_options = array(
        'Write code that is upstreamed into OpenStack' => 'Write code that is upstreamed into OpenStack' ,
        'Manage people who write code that is upstreamed into OpenStack' => 'Manage people who write code that is upstreamed into OpenStack',
        'Write applications that run on OpenStack' => 'Write applications that run on OpenStack',
        'Manage people who write applications that run on OpenStack' => 'Manage people who write applications that run on OpenStack',
        'Install / administer / deploy OpenStack' => 'Install / administer / deploy OpenStack',
        'Install / administer / deploy applications that run on OpenStack' => 'Install / administer / deploy applications that run on OpenStack',
        'Manage people who install / administer / deploy OpenStack' => 'Manage people who install / administer / deploy OpenStack',
        'Manage people who install / administer / deploy applications that run on OpenStack' => 'Manage people who install / administer / deploy applications that run on OpenStack',
        'None of these' => 'None of these',
    );

    public static $container_related_technologies = array(
        'Docker' => 'Docker',
        'Rocket' => 'Rocket',
        'LXC' => 'LXC',
        'LXD' => 'LXD',
        'OpenVZ' => 'OpenVZ',
        'Warden' => 'Warden',
        'Kubernetes' => 'Kubernetes',
        'Mesos' => 'Mesos',
    );

}