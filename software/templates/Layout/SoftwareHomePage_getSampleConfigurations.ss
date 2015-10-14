<% include SoftwareHomePage_MainNavMenu Active=3 %>

<div class="software-main-wrapper">
    <!-- Projects Subnav -->
    <div class="container">

        <div class="outer-project-subnav">
            <div class="sample-config-slider-control left">
                <i id="config-left" class="fa fa-caret-left"></i>
            </div>
            <div class="sample-configs-slider">
                <ul class="sample-configs-subnav">
                    <% loop Release.SampleConfigurationTypes.Sort(Order, ASC) %>
                        <li  <% if IsDefault %>class="active"<% end_if %>><a href="#" data-id="{$ID}">$Type</a></li>
                    <% end_loop %>
                </ul>
            </div>
            <div class="sample-config-slider-control right">
                <i id="config-right" class="fa fa-caret-right"></i>
            </div>
        </div>

    </div>

    <div class="container inner-software">

        <!-- Begin Page Content -->
        <div class="sample-configs-tip closed-config-tip">
            <div class="close-tip"><i class="fa fa-times"></i></div>
            <h5><i class="fa fa-question-circle"></i>What are sample configurations?</h5>
            <p>
                Think of these as curated playlists of OpenStack configurations. These sample configurations are put together by OpenStack operators, developers, consultants, and more. Each configuration will give you a good idea of which core and optional projects can be used for the Big Data environments.
            </p>
        </div>
        <div class="row">
            <div class="col-sm-12 sample-configs-wrapper">
                <div class="open-sample-config-tip show">
                    <i class="fa fa-question-circle"></i>
                </div>
                <div class="sample-config-choices">
                    <ul>
                        <li>
                            <a class="active" href="#">Configuration #1</a>
                        </li>
                        <li>
                            <a href="#">Configuration #2</a>
                        </li>
                    </ul>
                </div>
                <h3>Big Data: Configuration #1</h3>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illo culpa voluptatem quas sequi laboriosam vitae fugiat tempora doloribus, atque qui delectus saepe neque ipsum earum assumenda quo reprehenderit ratione nulla.
                </p>
                <p>
                    <strong>Curated by:</strong> <a href="#">Wes Jossey</a> - Head of Operations, TapJoy
                </p>
                <p>
                    <a class="more-about-config" href="#">More about this configuration [+]</a>
                </p>
                <div class="more-sample-config">
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad eos voluptas eveniet sint officia iure voluptate ea inventore facilis ducimus alias, quibusdam harum quia nam error voluptates adipisci fugit veniam!
                    </p>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad eos voluptas eveniet sint officia iure voluptate ea inventore facilis ducimus alias, quibusdam harum quia nam error voluptates adipisci fugit veniam!
                    </p>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad eos voluptas eveniet sint officia iure voluptate ea inventore facilis ducimus alias, quibusdam harum quia nam error voluptates adipisci fugit veniam!
                    </p>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad eos voluptas eveniet sint officia iure voluptate ea inventore facilis ducimus alias, quibusdam harum quia nam error voluptates adipisci fugit veniam!
                    </p>
                </div>
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p class="service-section-title"><strong>Core Services</strong> included in this configuration (3 of 4)</p>
            </div>
        </div>
        <div class="row">
            <!-- Nova -->
            <div class="col-md-4 col-sm-6">
                <div class="core-services-single-full">
                    <div class="core-top">
                        <div class="core-title">
                            Nova
                        </div>
                        <div class="core-service">
                            Compute Service
                        </div>
                        <div class="core-service-icon">
                            <i class="fa fa-cogs"></i>
                        </div>
                    </div>
                    <div class="core-mid">
                        <p>
                            Nova, also known as OpenStack Compute, is the software that controls your Infrastructure as as Service (IaaS) cloud computing platform.
                        </p>
                    </div>
                    <div class="core-stats-wrapper">
                        <div class="row">
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    97%
                                </div>
                                <div class="core-stat-title">
                                    Adoption
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    4 of 5
                                </div>
                                <div class="core-stat-title">
                                    Diversity
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    4 yr
                                </div>
                                <div class="core-stat-title">
                                    Age
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="core-bottom">
                        <a class="core-service-btn" href="/software/details/">More Details</a>
                    </div>
                </div>
            </div>
            <!-- Glance -->
            <div class="col-md-4 col-sm-6">
                <div class="core-services-single-full">
                    <div class="core-top">
                        <div class="core-title">
                            Glance
                        </div>
                        <div class="core-service">
                            Image Service
                        </div>
                        <div class="core-service-icon">
                            <i class="fa fa-cloud-upload"></i>
                        </div>
                    </div>
                    <div class="core-mid">
                        <p>
                            The Glance project provides a service where users can upload and discover data assets that are meant to be used with other services.
                        </p>
                    </div>
                    <div class="core-stats-wrapper">
                        <div class="row">
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    81%
                                </div>
                                <div class="core-stat-title">
                                    Adoption
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    4 of 5
                                </div>
                                <div class="core-stat-title">
                                    Diversity
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    4 yr
                                </div>
                                <div class="core-stat-title">
                                    Age
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="core-bottom">
                        <a class="core-service-btn" href="/software/details/">More Details</a>
                    </div>
                </div>
            </div>
            <!-- Cinder -->
            <div class="col-md-4 col-sm-6">
                <div class="core-services-single-full">
                    <div class="core-top">
                        <div class="core-title">
                            Cinder
                        </div>
                        <div class="core-service">
                            Storage Service
                        </div>
                        <div class="core-service-icon">
                            <i class="fa fa-folder-open"></i>
                        </div>
                    </div>
                    <div class="core-mid">
                        <p>
                            Cinder is a Block Storage service that is designed to allow the use of a LVM to end users that can be consumed by Nova.
                        </p>
                    </div>
                    <div class="core-stats-wrapper">
                        <div class="row">
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    92%
                                </div>
                                <div class="core-stat-title">
                                    Adoption
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    4 of 5
                                </div>
                                <div class="core-stat-title">
                                    Diversity
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    4 yr
                                </div>
                                <div class="core-stat-title">
                                    Age
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="core-bottom">
                        <a class="core-service-btn" href="/software/details/">More Details</a>
                    </div>
                </div>
            </div>
            <!-- Neutron -->
            <div class="col-md-4 col-sm-6">
                <div class="core-services-single-full">
                    <div class="core-top">
                        <div class="core-title">
                            Neutron
                        </div>
                        <div class="core-service">
                            Networking
                        </div>
                        <div class="core-service-icon">
                            <i class="fa fa-exchange"></i>
                        </div>
                    </div>
                    <div class="core-mid">
                        <p>
                            OpenStack Networking is a pluggable, scalable and API-driven system for managing networks and IP addresses.
                        </p>
                    </div>
                    <div class="core-stats-wrapper">
                        <div class="row">
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    99%
                                </div>
                                <div class="core-stat-title">
                                    Adoption
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    4 of 5
                                </div>
                                <div class="core-stat-title">
                                    Diversity
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    4 yr
                                </div>
                                <div class="core-stat-title">
                                    Age
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="core-bottom">
                        <a class="core-service-btn" href="/software/details/">More Details</a>
                    </div>
                </div>
            </div>
            <!-- Swift -->
            <div class="col-md-4 col-sm-6">
                <div class="core-services-single-full">
                    <div class="core-top">
                        <div class="core-title">
                            Swift
                        </div>
                        <div class="core-service">
                            Object Storage
                        </div>
                        <div class="core-service-icon">
                            <i class="fa fa-object-group"></i>
                        </div>
                    </div>
                    <div class="core-mid">
                        <p>
                            Swift provides a fully distributed, API-accessible storage platform that can be integrated directly into applications or used for backup, archiving and data retention.
                        </p>
                    </div>
                    <div class="core-stats-wrapper">
                        <div class="row">
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    90%
                                </div>
                                <div class="core-stat-title">
                                    Adoption
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    4 of 5
                                </div>
                                <div class="core-stat-title">
                                    Diversity
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    4 yr
                                </div>
                                <div class="core-stat-title">
                                    Age
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="core-bottom">
                        <a class="core-service-btn" href="/software/details/">More Details</a>
                    </div>
                </div>
            </div>
            <!-- Keystone -->
            <div class="col-md-4 col-sm-6">
                <div class="core-services-single-full core-off">
                    <div class="core-top">
                        <div class="core-title">
                            Keystone
                        </div>
                        <div class="core-service">
                            Identity Service
                        </div>
                        <div class="core-service-icon">
                            <i class="fa fa-key"></i>
                        </div>
                    </div>
                    <div class="core-mid">
                        <p>
                            Keystone is the identity service used by OpenStack for authentication (authN) and 4 of 5-level authorization (authZ).
                        </p>
                    </div>
                    <div class="core-stats-wrapper">
                        <div class="row">
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    89%
                                </div>
                                <div class="core-stat-title">
                                    Adoption
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    4 of 5
                                </div>
                                <div class="core-stat-title">
                                    Diversity
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-4">
                                <div class="core-stat-graphic">
                                    4 yr
                                </div>
                                <div class="core-stat-title">
                                    Age
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="core-bottom">
                        <a class="core-service-btn" href="/software/details/">More Details</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p class="service-section-title"><strong>Optional Services</strong> included in this configuration (4 of 10)</p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive optional-services-table">
                    <div class="sample-optional-hidden">
                    </div><table class="table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Service</th>
                        <th>Adoption <a href="#"><i class="fa fa-sort"></i></a></th>
                        <th>Diversity <a href="#"><i class="fa fa-sort"></i></a></th>
                        <th>Age <a href="#"><i class="fa fa-sort"></i></a></th>
                        <th>Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    <% loop $Release.DefaultSampleConfigurationType.DefaultSampleConfiguration.getOptionalComponents %>
                        <tr>
                            <td>$CodeName</td>
                            <td>$Name</td>
                            <td><div class="service-stat-pill green">$Adoption %</div></td>
                            <td><div class="service-stat-pill orange">Mid</div></td>
                            <td><div class="service-stat-pill red">$getAge Yrs</div></td>
                            <td><a href="/software/details/">More Details</a></td>
                            </tr>
                        <% end_loop %>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        <!-- End Page Content -->
    </div>
</div>