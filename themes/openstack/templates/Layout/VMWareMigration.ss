</div>

    <!-- Begin Page Content -->

    <!-- Hero Intro -->
    <div class="intro-header vmwmigration">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="hero-message"> 
                        <h1>Migration to OpenStack</h1>
                        <p>Broadcom’s acquisition of VMware and subsequent licensing changes have incentivized organizations around the world to re-evaluate their virtualization strategy. OpenStack, the open source standard for cloud infrastructure, has emerged as a leading alternative. Over 80% of OpenInfra members have already talked to customers about migrating workloads from VMware to OpenStack. </p>
                        <p>
                        OpenStack’s flexibility and open development enables organizations to: 
                        </p>
                        <ul>
                            <li>Implement a cost effective virtualization strategy by avoiding vendor lock-in</li>
                            <li>Modernize their infrastructure with a cloud native strategy</li>
                            <li>Customize their stack through increased integration options</li>
                            <li>Rely on a global ecosystem and diverse, active open source contributors</li>
                            <li>Maintain complete ownership of their infrastructure</li>
                        </ul>
                            <a class="vmwmigration download-btn" alt="Join the OpenInfra Foundation" type="button" href="https://openinfra.dev/join">
                             Join Us&nbsp;&nbsp;<i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div> 
    </div><!-- /.intro-header -->

    <!-- Overview -->
    <div class="overview vmwmigration">
        <section class="vmwmigration-white page-intro">
            <div class="container">
                <div class="row ">
                    <div class="col-sm-12">
                        <div class="intro-message"> 
                            <h2>OpenStack to VMware Feature Comparison</h2>
                            <p>There is not complete feature parity between VMware, a proprietary virtualization platform, and OpenStack, an open source project composed of services that can replicate specific functionality. A comparable or enhanced virtualization platform can be implemented by combining OpenStack’s existing suite of projects with services provided by the global ecosystem of OpenStack vendors.</p>
                        </div>
                        <table class="vmwmigration-table">
                            <thead>
                                <tr>
                                    <th>VMware</th>
                                    <th>OpenStack</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <h3>VMware vSphere (Hypervisor ESXi)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Web console</li>
                                            <li>VM Live migration (vMotion)</li>
                                            <li>Volume migration (Storage vMotion)</li>
                                            <li>Clustering/HA for Control plane</li>
                                            <li>Backup Integrations</li>
                                            <li>Auto-rescheduling for VMs (VMHA)</li>
                                            <li>Hot plug and extend (Net devices/volumes)</li>
                                            <li>Site-to-site VM migration</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3>OpenStack (Hypervisor KVM)</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Web console - <strong>yes</strong></li>
                                            <li>Live migration - <strong>yes</strong></li>
                                            <li>Volume migration - <strong>yes</strong></li>
                                            <li>Control plane HA - <strong>yes</strong></li>
                                            <li>Backup-Integrations - <strong>yes</strong></li>
                                            <li>VMHA - <strong>yes(1)</strong></li>
                                            <li>Hot plug and extend (Networks/Volumes) - <strong>yes</strong></li>
                                            <li>Site-to-site VM migration - <strong>yes(2)</strong></li>
                                        </ul>
                                        <p><strong>1</strong> OpenStack provides VMHA functionality with Masakari<br>
                                        <strong>2</strong> When OpenStack control plane stretched across DCs</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3>VMware NSX (VCF + VMware Firewall)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Switching (Layer 2 networks over Layer 3)</li>
                                            <li>Routing (Distributed, Active-active failover, Static, Dynamic, IPv6)</li>
                                            <li>Virtual routing and forwarding (VRF)</li>
                                            <li>Quality of service control (QoS)</li>
                                            <li>Security Groups</li>
                                            <li>NSX gateway (L2 Gateway)</li>
                                            <li>DPU-based acceleration</li>
                                            <li>Federation and Multi-cloud networking</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3>OpenStack Neutron</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Switching - <strong>yes</strong></li>
                                            <li>Routing - <strong>yes</strong></li>
                                            <li>Virtual routing and forwarding (VRF) - <strong>yes</strong></li>
                                            <li>QoS - <strong>yes</strong></li>
                                            <li>Security Groups - <strong>yes</strong></li>
                                            <li>L2 Gateway - <strong>yes</strong></li>
                                            <li>DPU-based acceleration - <strong>yes</strong></li>
                                            <li>Federation and Multi-cloud networking - <strong>yes(3)</strong></li>
                                        </ul>
                                        <p><strong>3</strong> Partially with BGP VPN interconnection extension</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3>VMware NSX Advanced Load Balancer (by Avi Networks)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>L4-L7 load balancing</li>
                                            <li>Container ingress gateway</li>
                                            <li>HA architecture</li>
                                            <li>Global server load balancing (GSLB)</li>
                                            <li>Web application firewall (WAF)</li>
                                            <li>Real-time application analytics</li>
                                            <li>Multi-cloud load balancing</li>
                                            <li>Application performance monitoring</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3>OpenStack Octavia</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>L4-L7 load balancing - <strong>yes</strong></li>
                                            <li>Container ingress gateway - <strong>yes</strong></li>
                                            <li>HA architecture - <strong>yes</strong> (stand-by)</li>
                                            <li>Global server load balancing (GSLB) - <strong>no</strong></li>
                                            <li>Web application firewall (WAF) - <strong>no</strong></li>
                                            <li>Real-time application analytics - <strong>no</strong></li>
                                            <li>Multi-cloud load balancing - <strong>no</strong></li>
                                            <li>Application performance monitoring - <strong>no</strong></li>
                                        </ul>
                                        <p>AVI networks support integrations with previous versions of OpenStack</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3>VMware vSAN (Express Storage Architecture)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Distributed Architecture</li>
                                            <li>Data redundancy</li>
                                            <li>Scalability</li>
                                            <li>Network speed: 25Gb/100Gb</li>
                                            <li>File protocols: SMB, NFSv3, NFSv4.1</li>
                                            <li>S3-Compatible Object Storage</li>
                                            <li>Native snapshots</li>
                                        </ul>
                                        <p>Multi-site cluster: vSAN Stretched Cluster</p>
                                    </td>
                                    <td>
                                        <h3>OpenStack Cinder + Manila (based on Ceph)</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Distributed Architecture - <strong>yes</strong></li>
                                            <li>Data redundancy - <strong>yes</strong></li>
                                            <li>Scalability - <strong>yes</strong></li>
                                            <li>Network speed: 10Gb/25Gb/100Gb - <strong>yes</strong></li>
                                            <li>File protocols: CephFS, NFS via Manila - <strong>yes</strong></li>
                                            <li>S3-Compatible Object Storage - <strong>yes</strong></li>
                                            <li>Native snapshots - <strong>yes</strong></li>
                                            <li>Multi-site: Ceph RBD Mirroring - <strong>yes(4)</strong></li>
                                        </ul>
                                        <p><strong>4</strong> RBD mirroring affects performance due to journaling</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3>VMware vCenter (VCF, VVF and vSphere STD)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Centralized Control and Visibility</li>
                                            <li>Web client and APIs</li>
                                            <li>Inventory search</li>
                                            <li>Alerts and notifications</li>
                                            <li>Dynamic resource allocation</li>
                                            <li>Multi-tenant management</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3>OpenStack + Prometheus, MaaS, ArgoCD</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Centralized control-plane - <strong>yes(5)</strong></li>
                                            <li>Web client and APIs - <strong>yes</strong></li>
                                            <li>Inventory search - <strong>yes</strong></li>
                                            <li>Alerts and notifications - <strong>yes(6)</strong></li>
                                            <li>Dynamic resource allocation - <strong>yes(7)</strong></li>
                                            <li>Multi-tenant management - <strong>yes</strong></li>
                                        </ul>
                                        <p><strong>5</strong> When OpenStack control plane stretched across DCs<br>
                                        <strong>6</strong> Based on Prometheus + Alertmanager with integrations<br>
                                        <strong>7</strong> Provided by OpenStack Watcher</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3>VMware Cloud Director (Cloud Management Platform)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Multi-site control</li>
                                            <li>Cloud-native approach</li>
                                            <li>Automation</li>
                                            <li>Policy-driven Approach for Cloud management</li>
                                            <li>Global Hybrid Cloud Management</li>
                                            <li>Cloud Migration</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3>OpenStack + Kubernetes</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Multi-site control - <strong>yes(8)</strong></li>
                                            <li>Cloud-native approach - <strong>yes(9)</strong></li>
                                            <li>Automation - <strong>yes(10)</strong></li>
                                            <li>Policy-driven Approach - <strong>yes</strong></li>
                                            <li>Global Hybrid Cloud Management - <strong>no</strong></li>
                                            <li>Cloud Migration - <strong>yes(11)</strong></li>
                                        </ul>
                                        <p><strong>8</strong> When OpenStack control plane stretched across DCs (Alternatively using ManageIQ)<br>
                                        <strong>9</strong> Requires Managed Kubernetes service installation<br>
                                        <strong>10</strong> Via Terraform, Heat or SDK<br>
                                        <strong>11</strong> Using third-party migration service</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3>VMware Aria Operations for Logs (vRealize Log Insight)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Collect logs in files</li>
                                            <li>Send logs to centralized system</li>
                                            <li>Provide interface to search and analyze logs</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3>Elasticsearch + Logstash + Kibana</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Collect logs in files - <strong>yes</strong></li>
                                            <li>Send logs to centralized system - <strong>yes</strong></li>
                                            <li>Interface for search and analysis - <strong>yes</strong></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3>VMware Aria Automation</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Multi-cloud environments management</li>
                                            <li>DevOps for infrastructure</li>
                                            <li>Infrastructure as code and Kubernetes automation</li>
                                            <li>Network automation</li>
                                            <li>SecOps for infrastructure</li>
                                            <li>SaltStack</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3>OpenStack + ArgoCD</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Multi-cloud environments management - <strong>yes(12)</strong></li>
                                            <li>DevOps for infrastructure - <strong>yes</strong></li>
                                            <li>Infrastructure as code and Kubernetes automation - <strong>yes</strong></li>
                                            <li>Network automation - <strong>yes</strong></li>
                                            <li>SecOps for infrastructure - <strong>yes</strong></li>
                                            <li>Ansible + GitOps approach - <strong>yes</strong></li>
                                        </ul>
                                        <p><strong>12</strong> Via GitOps approach based on ArgoCD</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3>VMware Aria Operations for Networks</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Networking</li>
                                            <li>Applications</li>
                                            <li>Security</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3>OpenStack Neutron + Hubble + SkyDive</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Networking - <strong>yes(13)</strong></li>
                                            <li>Applications - Partial</li>
                                            <li>Security - Partial</li>
                                        </ul>
                                        <p><strong>13</strong> Underlay network with Cilium Hubble, overlay (cloud) networks with SkyDive</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3>VMware Tanzu (Container Orchestration)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Kubernetes cluster management</li>
                                            <li>Multi-cloud</li>
                                            <li>Application catalog</li>
                                            <li>Service Mesh</li>
                                            <li>Observability</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3>OpenStack + Gardener</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Kubernetes cluster management - <strong>yes(14)</strong></li>
                                            <li>Multi-cloud - <strong>no</strong></li>
                                            <li>Application catalog - <strong>yes(15)</strong></li>
                                            <li>Service Mesh - <strong>yes</strong></li>
                                            <li>Observability - <strong>yes</strong></li>
                                        </ul>
                                        <p><strong>14</strong> Using either Magnum or Gardener<br>
                                        <strong>15</strong> Any Helm3 - based application</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3>VMware Horizon (Virtual Desktop Infrastructure)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Remote desktops</li>
                                            <li>Hybrid cloud management</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3>OpenStack + OpenUDS</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Remote desktops - <strong>yes</strong></li>
                                            <li>Hybrid cloud management - <strong>no</strong></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3>VMware SQL (Database as Service)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>PostgreSQL support</li>
                                            <li>MySQL support</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3>OpenStack Trove</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>PostgreSQL support - <strong>yes</strong></li>
                                            <li>MySQL support - <strong>yes</strong></li>
                                            <li>MongoDB support - <strong>yes</strong></li>
                                            <li>Redis support - <strong>yes</strong></li>
                                            <li>Cassandra support - <strong>yes</strong></li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- .container -->
        </section>
    </div>
    <div class="resources vmwmigration">
        <section class="vmwmigration vmwmigration-ecosystem">
            <div class="container">
                <div class="row info-block">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-8">
                        <h2>OpenInfra Foundation Members Supporting Migration from VMware to OpenStack</h2>
                    </div>
                </div>
                <div class="row five-columns row01">
                    <div class="col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/vexxhost-logo.png")}" alt="Vexxhost logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/sardina-logo.png")}" alt="Sardina logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/sentinella-logo.png")}" alt="Sentinella logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/Btech-logo.png")}" alt="Btech logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/china-mobile-logo.png")}" alt="China Mobile logo">
                    </div>
	            </div>

                <div class="row five-columns row02">
                    <div class="col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/devstack-lg-logo.png")}" alt="DevStack logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/tencent-logo.png")}" alt="Tencent logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/verneglobal-logo.png")}" alt="Verne Global logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("assets/bare-metal-logo-program/verizon-media.png")}" alt="Verzion Media logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/ovh-logo.png")}" alt="OVH logo">
                    </div>
	            </div>

                <div class="row five-columns row03">
                    <div class="col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/chinatelecom-logo.png")}" alt="China Telecom logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/stc-logo.png")}" alt="STC logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/opencloud-logo.png")}" alt="OpenCloud logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/avaya-logo.png")}" alt="Avaya logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/debian-logo.png")}" alt="Debian logo">
                    </div>
	            </div>

                <div class="row five-columns row04">
                    <div class="col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/zte-logo.png")}" alt="ZTE logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/suse-logo.png")}" alt="SUSE logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/H3C-logo.png")}" alt="H3C logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/chinaunicom-logo.png")}" alt="China Unicom logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/StackHPC-logo.png")}" alt="Stack HPC logo">
                    </div>
	            </div>

                <div class="row five-columns row05">
                    <div class="col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/ZConverter-logo.png")}" alt="ZConverter logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/EasyStack-logo.png")}" alt="Easy Stack logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/tfcloud-logo.png")}" alt="tfcloud logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/fiberhome-logo.png")}" alt="fiberhome logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/leboncoin-logo.png")}" alt="leboncoin logo">
                    </div>
	            </div>

                <div class="row five-columns row06">
                    <div class="col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/Platform9-logo.png")}" alt="Platform9 logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/mirantis-logo.png")}" alt="mirantis logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("images/baremetal/ecosystem/99cloud-sm-logo.png")}" alt="99cloud logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("assets/vmwmigration/red-hat-sm.png")}" alt="RedHat logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("assets/Uploads/0003supportedblack-orangehex.png")}" alt="Ubuntu logo">
                    </div>
	            </div>
                <div class="row five-columns row06">
                    <div class="col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("assets/vmwmigration/dell-technologies.jpg")}" alt="Dell logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{$Top.CloudUrl("assets/companies/main_logo/_resampled/ScaleWidthWzIwN10/inspur-lg.jpg")}" alt="Inspur">
                    </div>
                </div>
                <div class="row info-block">
                    <div class="col-sm-12">
                        <p>The OpenInfra Member: VMware Migration working group formed to collaboratively address the market opportunity for organizations to re-define their virtualization strategy. Participants represent the global ecosystem of OpenStack experts who support the OpenInfra Foundation. New participants are welcome to join, share VMware migration experience, and build more OpenStack awareness as a virtualization alternative.
                        </p>
                        <p><a class="vmwmigration red-button" href="https://openinfra.dev/join" target="_blank">Join the OpenInfra Foundation Today <i class="icon-arrow-right"></i></a></p>
                    </div>
                </div>
            </div> <!-- .container -->
        </section>


        <!-- Videos-->
        <section class="vmwmigration-grayback vmwmigration-video-wall">
            <div class="container">
                <div class="row info-block">
                    <h2>FAQs</h2>
                    <div class="vmwmigration-faqs">
                        <dl>
                            <dt>How have the licensing changes resulting from Broadcom's acquisition of VMware impacted users?</dt>
                            <dd>
                                <ul>
                                    <li>They shook the confidence of their users, putting a 45% market share at risk</li>
                                    <li>Broadcom switched strategies to target/support enterprise solutions at the expense of small to medium sized Managed Service Providers</li>
                                    <li>Prices were raised for renewing contracts, in some cases as high as 500%</li>
                                    <li>Companies with extremely large footprints and perpetual licenses were affected</li>
                                    <li>VMware's strong partner program was dismantled virtually overnight, affecting smaller MSPs</li>
                                    <li>Pricing structure changed, requiring purchase of the entire VMware Cloud Foundation suite</li>
                                </ul>
                            </dd>

                            <dt>Is this a danger or an opportunity for open source software solutions?</dt>
                            <dd>
                                <p>This is an opportunity for open source solutions that follow the "4 opens" - open source, open development, open governance, and open design. Relying on single vendor open source projects puts companies at risk similar to proprietary software. Projects like OpenStack, with its 14-year history and multi-vendor support, are seeing a resurgence due to this uncertainty.</p>
                            </dd>

                            <dt>Why should VMware users consider migrating to OpenStack over other solutions? Who is that best for?</dt>
                            <dd>
                                <ul>
                                    <li>Viable for organizations running 3 or more compute nodes</li>
                                    <li>Avoids vendor lock-in due to its open source nature</li>
                                    <li>No licensing fees or required service contracts</li>
                                    <li>Seamless integration with various technologies, both proprietary and open source</li>
                                    <li>Robust scalability, enabling resources to be scaled on demand</li>
                                </ul>
                            </dd>

                            <dt>How large does my team need to be to implement OpenStack for my organization?</dt>
                            <dd>
                                <ul>
                                    <li>Work with an OpenStack provider or consultant to help set up and retrain your existing VMware team</li>
                                    <li>A team as small as 2-3 OpenStack engineers can achieve a cost-effective cloud native virtualization solution</li>
                                </ul>
                            </dd>

                            <dt>What are some of the challenges in migrating from VMware to OpenStack?</dt>
                            <dd>
                                <p>The primary challenges are terminology and education. VMware has a very simple UX and many companies have been invested in VMware solutions for a long time. The OpenInfra Foundation is working to educate the market around feature parity and address potential gaps between OpenStack and VMware.</p>
                            </dd>

                            <dt>What migration tools are available to migrate from VMware to OpenStack?</dt>
                            <dd>
                                <ul>
                                    <li>migratekit: A complete open source solution</li>
                                    <li>Coriolis by Cloudbase Solutions: A blend of proprietary and open source technologies</li>
                                    <li>ZConverter: An any-to-any cloud migration tool</li>
                                    <li>Hystax</li>
                                    <li>Mirantis Migration Service</li>
                                </ul>
                            </dd>

                            <dt>Are you seeing an increase in interest in OpenStack since the VMware relicensing announcement?</dt>
                            <dd>
                                <ul>
                                    <li>80% of OpenInfra Members have received requests to migrate users from VMware to OpenStack</li>
                                    <li>Over 60% of members have already completed a successful migration</li>
                                    <li>Companies like Rackspace and Mirantis are seeing opportunities to gain market share using OpenStack</li>
                                    <li>The OpenInfra Foundation is fielding questions from both MSPs and VMware customers</li>
                                    <li>Partner organizations are working with companies like GEICO to move workloads from VMware to OpenStack</li>
                                    <li>This is expected to have a long-term impact, as many companies recently renewed their VMware licenses and are still evaluating their options</li>
                                </ul>
                            </dd>
                        </dl>
                        
                    </div>
                </div>
            </div> <!-- .container -->
        </section>
    </div>


    <!-- End Page Content -->
