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

class DeploymentMigrationOptions {

    public static $blank_fields = array('DeploymentType', 'ComputeNodes', 'ComputeCores', 'ComputeInstances', 'BlockStorageTotalSize', 'ObjectStorageSize', 'ObjectStorageNumObjects', 'NetworkNumIPs', 'WhyNovaNetwork');

    public static $migration_fields = array(
        'BlockStorageDrivers' => array(
            'Storwize'=>'IBM Storwize',
            'XIV'=>'IBM XIV/DS8000',
            'LVM'=>'LVM (default)',
            'Windows'=>'Windows Server 2012',
            'Xenapi'=>'Xenapi NFS',
            'SAN/Solaris'=>'SAN / Solaris',
        ),
        'SupportedFeatures' => array(
            'Dashboard'=>'',
            'Object storage'=>'',
            'Live migration'=>'',
            'Snapshotting to new images'=>'',
        ),
        'OperatingSystems' =>  array(
            'Fedora'=>'Fedora Server',
            'openSUSE'=>'openSUSE Server',
            'Red Hat Enterprise Linux'=>'Red Hat Enterprise Linux (RHEL)',
            'SUSE Linux Enterprise'=>'SUSE Linux Enterprise Server (SLES)',
            'Ubuntu'=>'Ubuntu Server',
            'Windows'=>'Microsoft Windows Server',
            'Fedora'=>'Fedora Server',
            ),
        'SwiftGlobalDistributionFeaturesUsesCases' => array(
            'Disaster recovery, continuity of business, or regulatory reasons'=>'Disaster recovery,Continuity of business,Regulatory reasons',
        ),
    );
}