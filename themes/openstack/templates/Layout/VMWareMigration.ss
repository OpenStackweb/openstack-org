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
                            <a class="vmwmigration download-btn" alt="Join the OpenInfra Foundation" type="button" href="https://openinfra.dev/join/members" target="_blank">
                             Join Us&nbsp;&nbsp;<i class="fa fa-arrow-circle-right"></i>
                        </a>&nbsp;&nbsp;<a class="vmwmigration download-btn" alt="VMware Migration to OpenStack White Paper" type="button" href="">
                             Read the Whitepaper&nbsp;<i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div> 
    </div><!-- /.intro-header -->


    <!-- Overview -->
        <section class="vmwmigration-grayback vmwmigration-video-wall">
            <div class="container">
                <div class="row ">
                    <div class="col-sm-12">
                        <div class="intro-message"> 
                            <p>&nbsp;</p>
                            <h2>"OpenStack allows us to avoid vendor lock-in and allows us to customize our infrastructure to meet our specific needs. We can integrate various open-source tools and platforms, which is something we couldn't do with VMware. Additionally, OpenStack's community-driven development model means we can contribute back and benefit from innovations made by others."</h2>
                            <h4>- Tad Van Fleet, GEICO Distinguished Architect 
                            </h4>
                            <p>&nbsp;</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="vmwmigration-white page-intro">
            <div class="container">
                <div class="row ">
                    <div class="col-sm-12">
                        <div class="intro-message"> 
                            <h2>OpenStack to VMware Feature Comparison</h2>
                            <p>There is not complete feature parity between VMware, a proprietary virtualization platform, and OpenStack, an open source project composed of services that can replicate specific functionality. A comparable or enhanced virtualization platform can be implemented by combining OpenStack’s existing suite of projects with services provided by the global ecosystem of OpenStack vendors.</p>
                            <p>We'd like to thank our friends at <a href="https://cloudification.io/" target="_blank">Cloudification</a> and the OpenStack VMware Migration Working Group for providing the content for this comparison chart. If you're interested in joining the Working Group, please <a href="mailto:bizdev@openinfra.dev">email us</a>.</p>
                        </div>
                        <table class="vmwmigration-table">
                            <thead>
                                <tr>
                                    <th>VMware</th>
                                    <th class="th-openstack">OpenStack</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <h3><b>VMware vSphere</b> (Hypervisor ESXi)</h3>
                                        <h4>Key features:</h4>
                                        <p>(subject to subscription type and extra costs)</p>
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
                                        <h3><b>OpenStack</b> (Hypervisor KVM)</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Web console - <b>yes</b></li>
                                            <li>Live migration - <b>yes</b></li>
                                            <li>Volume migration - <b>yes</b></li>
                                            <li>Control plane HA - <b>yes</b></li>
                                            <li>Backup-Integrations - <b>yes</b></li>
                                            <li>VMHA - <b>yes(1)</b></li>
                                            <li>Hot plug and extend (Networks/Volumes) - <b>yes</b></li>
                                            <li>Site-to-site VM migration - <b>yes(2)</b></li>
                                        </ul>
                                        <p><b>1</b> OpenStack provides VMHA functionality with <a href="https://docs.openstack.org/masakari/latest/">Masakari</a><br>
                                        <b>2</b> When OpenStack control plane stretched across DCs</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3><b>VMware NSX</b> (VCF + VMware Firewall)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Switching (Layer 2 networks over Layer 3)
                                                <ul>
                                                    <li>Within data center</li>
                                                    <li>Across data centers</li>
                                                </ul>
                                            </li>
                                            <li>Routing
                                                <ul>
                                                    <li>Distributed routing</li>
                                                    <li>Active-active failover with physical routers</li>
                                                    <li>Static routing</li>
                                                    <li>Dynamic routing</li>
                                                    <li>IPv6 support</li>
                                                </ul>
                                            </li>
                                            <li>Virtual routing and forwarding (VRF)
                                                <ul>
                                                    <li>Tenant isolation</li>
                                                    <li>Separate routing tables</li>
                                                    <li>NAT</li>
                                                    <li>EDGE Firewall</li>
                                                </ul>
                                            </li>
                                            <li>Quality of service control (QoS)</li>
                                            <li>Security Groups</li>
                                            <li>NSX gateway (L2 Gateway)</li>
                                            <li>DPU-based acceleration</li>
                                            <li>Federation and Multi-cloud networking</li>
                                        </ul>
                                        <p>(consistent networking and security across DCs,<br>private/public cloud boundaries)</p>
                                    </td>
                                    <td>
                                        <h3><b>OpenStack Neutron</b></h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Switching
                                                <ul>
                                                    <li>Within data center - <b>yes</b></li>
                                                    <li>Across data centers - <b>yes(1)</b></li>
                                                </ul>
                                            </li>
                                            <li>Routing
                                                <ul>
                                                    <li>Distributed routing - <b>yes</b></li>
                                                    <li>Active-active failover with physical routers - <b>yes</b></li>
                                                    <li>Static routing - <b>yes</b></li>
                                                    <li>Dynamic routing - <b>yes</b></li>
                                                    <li>IPv6 support - <b>yes</b></li>
                                                </ul>
                                            </li>
                                            <li>Virtual routing and forwarding (VRF)
                                                <ul>
                                                    <li>Tenant isolation - <b>yes</b></li>
                                                    <li>Separate routing tables - <b>yes</b></li>
                                                    <li>NAT - <b>yes</b></li>
                                                    <li>EDGE Firewall - <b>no(2)</b></li>
                                                </ul>
                                            </li>
                                            <li>QoS - <b>yes</b></li>
                                            <li>Security Groups - <b>yes</b></li>
                                            <li>L2 Gateway - <b>yes</b></li>
                                            <li>DPU-based acceleration - <b>yes</b></li>
                                            <li>Federation and Multi-cloud networking - <b>yes(3)</b></li>
                                        </ul>
                                        <p><b>1</b> if OpenStack control plane stretched across DCs<br>
                                        <b>2</b> OpenStack has <a href="https://docs.openstack.org/neutron/latest/admin/fwaas.html">FWaaS extension</a><br>
                                        <b>3</b> Partially with <a href="https://docs.openstack.org/networking-bgpvpn/latest/">BGP VPN interconnection extension</a></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3><b>VMware NSX Advanced Load Balancer</b><br>(<a href="https://www.vmware.com/content/dam/digitalmarketing/vmware/en/pdf/products/nsx/vmware-nsx-advanced-load-balancer-data-sheet.pdf">by Avi Networks</a>)</h3>
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
                                        <h3><b>OpenStack Octavia</b></h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>L4-L7 load balancing - <b>yes</b></li>
                                            <li>Container ingress gateway - <b>yes</b></li>
                                            <li>HA architecture - <b>yes</b> (stand-by)</li>
                                            <li>Global server load balancing (GSLB) - <b>no</b></li>
                                            <li>Web application firewall (WAF) - <b>no</b></li>
                                            <li>Real-time application analytics - <b>no</b></li>
                                            <li>Multi-cloud load balancing - <b>no</b></li>
                                            <li>Application performance monitoring - <b>no</b></li>
                                        </ul>
                                        <p>AVI networks support integrations with <a href="https://avinetworks.com/docs/latest/openstack-support-matrix/">previous versions of OpenStack</a></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3><b>VMware vSAN</b> (<a href="https://core.vmware.com/blog/introduction-vsan-express-storage-architecture">Express Storage Architecture</a>)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Distributed Architecture:
                                                <ul>
                                                    <li>Hyperconverged, integrates with vSphere</li>
                                                    <li>Based on local storage in ESXi hosts</li>
                                                    <li>Eliminates the need for external storage arrays</li>
                                                    <li>Cluster size: min 2 hosts, max: 64 hosts</li>
                                                    <li>Uses fast disks for caching and efficient placement</li>
                                                </ul>
                                            </li>
                                            <li>Data redundancy:
                                                <ul>
                                                    <li>Distributed RAID, caching, and read/write optimizations</li>
                                                    <li>Provides fault tolerance at the storage policy level</li>
                                                </ul>
                                            </li>
                                            <li>Scalability:
                                                <ul>
                                                    <li>Scalable with additional ESXi hosts to the cluster</li>
                                                    <li>Linear scalability of storage capacity and performance resources</li>
                                                </ul>
                                            </li>
                                            <li>Network speed: 25Gb/100Gb</li>
                                            <li>File protocols: SMB, NFSv3, NFSv4.1</li>
                                            <li>S3-Compatible Object Storage</li>
                                            <li>Native snapshots</li>
                                        </ul>
                                        <p>Multi-site cluster: <a href="https://core.vmware.com/resource/vsan-stretched-cluster-guide">vSAN Stretched Cluster</a></p>
                                    </td>
                                    <td>
                                        <h3><b>OpenStack Cinder + Manila</b> (based on <a href="https://ceph.io/en/" target="_blank" rel="noopener">Ceph</a>)</h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Distributed Architecture - <b>yes</b>
                                                <ul>
                                                    <li>Distributed architecture with a cluster of storage nodes running OSDs</li>
                                                    <li>Supports object, block, and file storage interfaces</li>
                                                    <li>Cluster size: min 6 hosts, max: 1000+ hosts</li>
                                                    <li>Does not require disks for caching</li>
                                                </ul>
                                            </li>
                                            <li>Data redundancy - <b>yes</b>
                                                <ul>
                                                    <li>Data redundancy through replication (3 copies) and erasure coding</li>
                                                    <li>Replicates data across multiple OSDs or uses erasure coding for fault tolerance</li>
                                                </ul>
                                            </li>
                                            <li>Scalability - <b>yes</b>
                                                <ul>
                                                    <li>Highly scalable, can scale out to tens of PBs of data</li>
                                                    <li>Allows adding or removing storage nodes dynamically without disruption</li>
                                                </ul>
                                            </li>
                                            <li>Network speed: 10Gb/25Gb/100Gb - <b>yes</b></li>
                                            <li>File protocols: CephFS, NFS via <a href="https://wiki.openstack.org/wiki/Manila">Manila</a> - <b>yes</b></li>
                                            <li>S3-Compatible Object Storage - <b>yes</b></li>
                                            <li>Native snapshots - <b>yes</b></li>
                                            <li>Multi-site: <a href="https://docs.ceph.com/en/latest/rbd/rbd-mirroring/">Ceph RBD Mirroring</a> - <b>yes(1)</b></li>
                                        </ul>
                                        <p><b>1</b> RBD mirroring affects performance due to journaling</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3><b>VMware vCenter</b> (VCF, VVF and vSphere STD)</h3>
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
                                        <h3><b>OpenStack + Prometheus, MaaS, ArgoCD</b></h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Centralized control-plane - <b>yes(1)</b></li>
                                            <li>Web client and APIs - <b>yes</b></li>
                                            <li>Inventory search - <b>yes</b></li>
                                            <li>Alerts and notifications - <b>yes(2)</b></li>
                                            <li>Dynamic resource allocation - <b>yes(3)</b></li>
                                            <li>Multi-tenant management - <b>yes</b></li>
                                        </ul>
                                        <p><b>1</b> When OpenStack control plane stretched across DCs<br>
                                        <b>2</b> Based on Prometheus + Alertmanager with integrations<br>
                                        <b>3</b> Provided by <a href="https://docs.openstack.org/watcher/latest/">OpenStack Watcher</a></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3><b>VMware Cloud Director</b> (Cloud Management Platform)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Multi-site control</li>
                                            <li>Cloud-native approach<br>(Containers and VMs in the same environment)</li>
                                            <li>Automation</li>
                                            <li>Policy-driven Approach for Cloud management</li>
                                            <li>Global Hybrid Cloud Management</li>
                                            <li>Cloud Migration</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3><b>OpenStack + Kubernetes</b></h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Multi-site control - <b>yes(1)</b></li>
                                            <li>Cloud-native approach - <b>yes(2)</b></li>
                                            <li>Automation - <b>yes(3)</b></li>
                                            <li>Policy-driven Approach - <b>yes</b></li>
                                            <li>Global Hybrid Cloud Management - <b>no</b></li>
                                            <li>Cloud Migration - <b>yes(4)</b></li>
                                        </ul>
                                        <p><b>1</b> When OpenStack control plane stretched across DCs (Alternatively using <a href="https://www.manageiq.org/" target="_blank" rel="noopener">ManageIQ</a>)<br>
                                        <b>2</b> Requires Managed Kubernetes service installation<br>
                                        <b>3</b> Via Terraform, Heat or SDK<br>
                                        <b>4</b> Using third-party migration service</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3><b>VMware Aria</b> Operations for Logs (vRealize Log Insight)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Collect logs in files</li>
                                            <li>Send logs to centralized system</li>
                                            <li>Provide interface to search and analyze logs</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3><b>Elasticsearch + Logstash + Kibana</b></h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Collect logs in files - <b>yes</b></li>
                                            <li>Send logs to centralized system - <b>yes</b></li>
                                            <li>Interface for search and analysis - <b>yes</b></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3><b>VMware Aria</b> Automation</h3>
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
                                        <h3><b>OpenStack + ArgoCD</b></h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Multi-cloud environments management - <b>yes(1)</b></li>
                                            <li>DevOps for infrastructure - <b>yes</b></li>
                                            <li>Infrastructure as code and Kubernetes automation - <b>yes</b></li>
                                            <li>Network automation - <b>yes</b></li>
                                            <li>SecOps for infrastructure - <b>yes</b></li>
                                            <li>Ansible + GitOps approach - <b>yes</b></li>
                                        </ul>
                                        <p><b>1</b> Via GitOps approach based on ArgoCD</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3><b>VMware Aria</b> Operations for Networks</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Networking
                                                <ul>
                                                    <li>End-to-end troubleshooting traffic and path</li>
                                                    <li>Network assurance and verification</li>
                                                    <li>Overlay and underlay network troubleshooting</li>
                                                </ul>
                                            </li>
                                            <li>Applications
                                                <ul>
                                                    <li>Application discovery and plan for migration</li>
                                                    <li>Measure application latency and performance</li>
                                                    <li>Finding network bottlenecks for application</li>
                                                    <li>Analyze traffic</li>
                                                </ul>
                                            </li>
                                            <li>Security
                                                <ul>
                                                    <li>Troubleshoot security</li>
                                                    <li>FW policies and network segmentation recommendations</li>
                                                    <li>Dependencies map to reduce risk during migrations</li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3><b>OpenStack Neutron + Hubble + SkyDive</b></h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Networking
                                                <ul>
                                                    <li>End-to-end troubleshooting traffic and path - <b>yes(1)</b></li>
                                                    <li>Network assurance and verification - <b>yes</b></li>
                                                    <li>Overlay and underlay network troubleshooting - <b>yes(1)</b></li>
                                                </ul>
                                            </li>
                                            <li>Applications
                                                <ul>
                                                    <li>Application discovery and plan for migration - <b>no</b></li>
                                                    <li>Measure application latency and performance - <b>no</b></li>
                                                    <li>Finding network bottlenecks for application - <b>no</b></li>
                                                    <li>Analyze traffic - <b>yes</b></li>
                                                </ul>
                                            </li>
                                            <li>Security
                                                <ul>
                                                    <li>Troubleshoot security - <b>no</b></li>
                                                    <li>FW policies and network segmentation recommendations - <b>yes</b></li>
                                                    <li>Dependencies map to reduce risk during migrations - <b>no</b></li>
                                                </ul>
                                            </li>
                                        </ul>
                                        <p><b>1</b> Underlay network with Cilium <a href="https://github.com/cilium/hubble" target="_blank" rel="noopener">Hubble</a>, overlay (cloud) networks with <a href="https://github.com/skydive-project/skydive" target="_blank" rel="noopener">SkyDive</a></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3><b>VMware Tanzu</b> (Container Orchestration)</h3>
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
                                        <h3><b>OpenStack + Gardener</b></h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Kubernetes cluster management - <b>yes(1)</b></li>
                                            <li>Multi-cloud - <b>no</b></li>
                                            <li>Application catalog - <b>yes(2)</b></li>
                                            <li>Service Mesh - <b>yes</b></li>
                                            <li>Observability - <b>yes</b></li>
                                        </ul>
                                        <p><b>1</b> Using either <a href="https://docs.openstack.org/magnum/latest/" target="_blank" rel="noopener">Magnum</a> or <a href="https://gardener.cloud/" target="_blank" rel="noopener">Gardener</a><br>
                                        <b>2</b> Any Helm3 - based application</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3><b>VMware Horizon</b> (Virtual Desktop Infrastructure)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>Remote desktops</li>
                                            <li>Hybrid cloud management</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3><b>OpenStack + OpenUDS</b></h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>Remote desktops - <b>yes</b></li>
                                            <li>Hybrid cloud management - <b>no</b></li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h3><b>VMware SQL</b> (Database as Service)</h3>
                                        <h4>Key features:</h4>
                                        <ul>
                                            <li>PostgreSQL support</li>
                                            <li>MySQL support</li>
                                        </ul>
                                    </td>
                                    <td>
                                        <h3><b>OpenStack Trove</b></h3>
                                        <h4>Comparable features:</h4>
                                        <ul>
                                            <li>PostgreSQL support - <b>yes</b></li>
                                            <li>MySQL support - <b>yes</b></li>
                                            <li>MongoDB support - <b>yes</b></li>
                                            <li>Redis support - <b>yes</b></li>
                                            <li>Cassandra support - <b>yes</b></li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="vmwmigration-table-source">
                            Source: <a class="source" href="https://cloudification.io/vmware-alternative/">Cloudification</a>
                        </p>
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
                <div class="row info-block">
                    <div class="col-sm-12">
                        <p>The OpenInfra Member: VMware Migration working group formed to collaboratively address the market opportunity for organizations to re-define their virtualization strategy. Participants represent the global ecosystem of OpenStack experts who support the OpenInfra Foundation. New participants are welcome to join, share VMware migration experience, and build more OpenStack awareness as a virtualization alternative.
                        </p>
                    </div>
                </div>
                <div class="row five-columns row01">
                    <div class="col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/BSystems.jpg" alt="B1 Systems">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/binariocloud-lg.png" alt="Binario Cloud logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/Binero-lg.png" alt="Binero logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://www.openstack.org/companies/57/logos_resampled/ScaleWidthWzIwN10/Canonical-s.png" alt="Canonical logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/695/logos/_resampled/ScaleWidthWzIwN10/cleura-lg1.jpg" alt="Cleura logo">
                    </div>
                </div>

                <div class="row five-columns row02">
                    <div class="col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/cloudheat-lg.jpg" alt="Cloud & Heat logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/cloudbase-lg2.jpg" alt="Cloudbase Solutions logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/cloudification-blue-logo-small.png" alt="Cloudification logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://www.openstack.org/companies/582/logos_resampled/ScaleWidthWzIwN10/fairbanks-lg1.png" alt="Fairbanks logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="{https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/huawei-new-lg.jpg" alt="Huawei logo">
                    </div>
                </div>

                <div class="row five-columns row03">
                    <div class="col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2">
                    <img class="ecosystem-logo" src="https://www.openstack.org/companies/1331/logos_resampled/ScaleWidthWzIwN10/hydolix-lg.png" alt="Hydrolix logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/mirantis-lg.png" alt="Mirantis logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/okestro-lg3.png" alt="Okestro logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://www.openstack.org/companies/1277/logos_resampled/ScaleWidthWzIwN10/planethoster-lg.png" alt="PlanetHoster logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://www.openstack.org/companies/1/logos_resampled/ScaleWidthWzIwN10/rackspace-lg2.png" alt="Rackspace logo">
                    </div>
                </div>

                <div class="row five-columns row04">
                    <div class="col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/RedHat-lg.png" alt="Red Hat logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://www.openstack.org/companies/489/logos_resampled/ScaleWidthWzIwN10/sardina-lg2.png" alt="Sardina Systems logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/storware-lg.png" alt="Storware logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/ULTIMUM-TECHNOLOGIES-320x132.png" alt="Ultimum Technologieslogo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/vexxhost-lg2.jpg" alt="Vexxhost logo">
                    </div>
                </div>

                <div class="row five-columns row05">
                    <div class="col-md-5th-1 col-sm-4">&nbsp;
                    </div>
                    <div class="col-md-5th-1 col-sm-4 col-md-offset-0 col-sm-offset-2">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/virtuozzo-lg-new.jpg" alt="Virtuozzo logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/vyos-lg.png" alt="VyOS logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">
                    <img class="ecosystem-logo" src="https://object-storage-ca-ymq-1.vexxhost.net/swift/v1/6e4619c416ff4bd19e1c087f27a43eea/www-assets-prod/companies/main_logo/_resampled/ScaleWidthWzIwN10/zconverter-lg.png" alt="ZConverter logo">
                    </div>
                    <div class="col-md-5th-1 col-sm-4">&nbsp;
                    </div>
                </div>
                <div class="row info-block">
                        <p><a class="vmwmigration red-button"  href="https://openinfra.dev/join/members" target="_blank">Join the OpenInfra Foundation Today <i class="icon-arrow-right"></i></a></p>
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
                                This is an opportunity for open source solutions that follow the "4 opens" - open source, open development, open governance, and open design. Relying on single vendor open source projects puts companies at risk similar to proprietary software. Projects like OpenStack, with its 14-year history and multi-vendor support, are seeing a resurgence due to this uncertainty.
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
                                The primary challenges are terminology and education. VMware has a very simple UX and many companies have been invested in VMware solutions for a long time. The OpenInfra Foundation is working to educate the market around feature parity and address potential gaps between OpenStack and VMware.
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
