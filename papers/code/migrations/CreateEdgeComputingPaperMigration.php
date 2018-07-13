<?php
/**
 * Copyright 2018 OpenStack Foundation
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
 * Class CreateEdgeComputingPaperMigration
 */
final class CreateEdgeComputingPaperMigration extends AbstractDBMigrationTask
{
    protected $title = "CreateEdgeComputingPaperMigration";

    protected $description = "CreateEdgeComputingPaperMigration";

    /**
     * @param string $content
     * @return string
     */
    static function cleanContent($content){
        $content = trim($content);

        $_arr = preg_split("/[\r\n]+/",$content,-1,PREG_SPLIT_NO_EMPTY);

        $arr= [];
        foreach($_arr as $line){
            // trim these line
            array_push($arr, trim($line));
        }

        return join(PHP_EOL, $arr);
    }

    function doUp()
    {
        $paper = new Paper();
        $paper->Title = "Cloud Edge Computing: Beyond the Data Center";
        $paper->Subtitle = "Beyond the Data Center";
        $paper->write();

        // intro section
        $section1 = new PaperSection();
        $section1->Title = "INTRODUCTION";
        $section1->Order = 1;
        $section1->PaperID = $paper->ID;
        $section1->write();

        $section_1_contents = [
            '<p>For over a decade, centralized cloud computing has been considered a standard IT delivery platform. Though cloud computing is ubiquitous, emerging requirements and workloads are beginning to expose its limitations. With its strong data center centric view, where compute and storage resources are relatively plentiful and centralized, little or no thought was ever given to the optimization of the supporting hypervisor and management platform footprint. Few cloud developers seriously considered the requirements needed to support resource-constrained nodes reachable only over unreliable or bandwidth-limited network connections, or thought about the needs of applications that demand very high bandwidth, low latency, or widespread compute capacity across many sites.</p>',
            '<p>New applications, services, and workloads increasingly demand a different kind of architecture, one that’s built to directly support a distributed infrastructure. New requirements for availability and cloud capability at remote sites are needed to support both today’s requirements (retail data analytics, network services) and tomorrow’s innovations (smart cities, AR/VR). The maturity, robustness, flexibility, and simplicity of cloud now needs to be extended across multiple sites and networks in order to cope with evolving demands.</p>',
            '<p>Container technologies like <a href="https://www.docker.com/open-source-0" target="_blank">Docker</a> and <a href="https://kubernetes.io" target="_blank">Kubernetes</a> are becoming the leading standards for building containerized applications. They help free organizations from complexity that limits development agility. Containers, container infrastructure, and container deployment technologies have proven themselves to be very powerful abstractions that can be applied to a number of different use cases. Using something like Kubernetes, an organization can deliver a cloud that solely uses containers for application delivery.</p>',
            '<p>Recently companies have begun to apply the simplified administration and flexibility of cloud computing architectures to distributed infrastructures that span across multiple sites and networks. Organizations have an emerging need to take cloud capabilities across WAN networks and into increasingly smaller deployments out at the network edge. Though this approach is in its early days, it is becoming clear that many emerging use cases and scenarios would benefit from distributed architectures.</p>',
            '<p>In this paper, we explore this emerging need. It has been called many names: distributed cloud, fog computing, 4th generation data centers, but for the purposes of this document, we will stick with a common, easily understood term—cloud edge computing. </p>',
            '<p>In this document, we aim to accomplish several important tasks:</p>',
            '<ol>
<li>
<p>Cultivate a conversation around cloud edge computing, including some basic definitions, stimulating interest and engagement from the open source community.</p>
</li>
<li>
<p>Guide both the broader open source and OpenStack communities in developing tools and standards needed for broad adoption.</p>
</li>
<li>
<p>Explore how current tools, standards and architectures may need to change to accommodate this distributed cloud model.</p>
</li>
</ol>',
            '<p>There is much work to be done to achieve our goals, and we welcome and encourage the entire open source community to join in both the effort and the opportunity of creating or adapting tools to meet the new requirements of cloud edge computing.</p>'
        ];

        $idx = 1;
        foreach($section_1_contents as $content){
            $c = new PaperParagraph();
            $c->Content = self::cleanContent($content);
            $c->Order = $idx;
            $c->SectionID = $section1->ID;
            $c->write();
            $idx++;
        }

        // WHAT IS CLOUD EDGE COMPUTING?
        $section2 = new PaperSection();
        $section2->Title = "WHAT IS CLOUD EDGE COMPUTING?";
        $section2->Order = 2;
        $section2->PaperID = $paper->ID;
        $section2->write();

        $section_2_contents = [
            '<p>It is worth highlighting that many overlapping and sometimes conflicting definitions of edge computing exist—edge computing means many things to many people. But for our purposes, the most mature view of edge computing is that it is offering application developers and service providers cloud computing capabilities, as well as an IT service environment at the edge of a network.</p>',
            '<p>The aim is to deliver compute, storage, and bandwidth much closer to data inputs and/or end users. An edge computing environment is characterized by potentially high latency among all the sites and low and unreliable bandwidth—alongside distinctive service delivery and application functionality possibilities that cannot be met with a pool of centralized cloud resources in distant data centers. By moving some or all of the processing functions closer to the end user or data collection point, cloud edge computing can mitigate the effects of widely distributed sites by minimizing the effect of latency on the applications.</p>',
            '<p>Edge computing first emerged by virtualizing network services over WAN networks, taking a step away from the data center. The initial use cases were driven by a desire to leverage a platform that delivered the flexibility and simple tools that cloud computing users have become accustomed to.</p>',
            '<p>As new edge computing capabilities emerge, we see a changing paradigm for computing—one that is no longer necessarily bound by the need to build centralized data centers. Instead, for certain applications, cloud edge computing is taking the lessons of virtualization and cloud computing and creating the capability to have potentially thousands of massively distributed nodes that can be applied to diverse use cases, such as industrial IoT or even far-flung monitoring networks for tracking real time water resource usage over thousands, or millions, of locations.</p>',
            '<p>Many proprietary and open source edge computing capabilities already exist without relying on distributed cloud—some vendors refer to this as “device edge.” Components of this approach include elements such as IoT gateways or NFV appliances. But increasingly, applications need the versatility of cloud at the edge, although the tools and architectures needed to build distributed edge infrastructures are still in their infancy. Our view is that the market will continue to demand better capabilities for cloud edge computing.</p>',
            '<p>Edge computing capabilities include, but are not limited to:</p>',
            '<ol>
<li>
<p>A consistent operating paradigm across diverse infrastructures.</p>
</li>
<li>
<p>The ability to perform in a massively distributed (think thousands of global locations) environment.</p>
</li>
<li>
<p>The need to deliver network services to customers located at globally distributed remote locations.</p>
</li>
<li>
<p>Application integration, orchestration and service delivery requirements.</p>
</li>
<li>
<p>Hardware limitations and cost constraints.</p>
</li>
<li>
<p>Limited or intermittent network connections.</p>
</li>
<li>
<p>Methods to address applications with strict low latency requirements (AR/VR, voice, and so forth).</p>
</li>
<li>
<p>Geofencing and requirements for keeping sensitive private data local.</p>
</li>
</ol>',
        ];

        $idx = 1;
        foreach($section_2_contents as $content){
            $c = new PaperParagraph();
            $c->Content = self::cleanContent($content);
            $c->Order = $idx;
            $c->SectionID = $section2->ID;
            $c->write();
            $idx++;
        }

        // A DEEPER EXPLORATION OF EDGE COMPUTING CONSIDERATIONS
        $section3 = new PaperSection();
        $section3->Title = "A DEEPER EXPLORATION OF EDGE COMPUTING CONSIDERATIONS";
        $section3->Order = 1;
        $section3->ParentSectionID = $section2->ID;
        $section3->PaperID = $paper->ID;
        $section3->write();

        $section_3_contents = [
            '<p>The "edge" in edge computing refers to the outskirts of an administrative domain, as close as possible to discrete data sources or end users. This concept applies to telecom networks, to large enterprises with distributed points of presence such as retail, or to other applications, in particular in the context of IoT.</p>',
            '<p class="text-center"><img src="https://www.openstack.org/assets/papers/edge-computing/1-edge-fog-diagram7.svg" alt="Table 1" ></p>',
            '<p>One of the characteristics of edge computing is that the application is strongly associated with the edge location. For telecoms, “the edge” would refer to a point close to the end user but controlled by the provider, potentially having some elements of workloads running on end user devices. For large enterprises, “the edge” is the point where the application, service or workload is used (e.g. a retail store or a factory). For the purposes of this definition, the edge is not an end device with extremely limited capacity for supporting even a minimal cloud architecture, such as an IoT or sensor device. This is an important consideration, because many discussions of edge computing do not make that distinction.</p>',
            '<p>Edge computing is similar to data center computing in that:</p>',
            '<ul>
<li>
<p>It includes compute, storage and networking resources.</p>
</li>
<li>
<p>Its resources may be shared by many users and many applications.</p>
</li>
<li>
<p>It benefits from virtualization and abstraction of the resource pool.</p>
</li>
<li>
<p>It benefits from the ability to leverage commodity hardware.</p>
</li>
<li>
<p>It uses APIs to support interoperability.</p>
</li>
</ul>',
             '<p>Edge computing differs from computing in large data centers in that:</p>',
             '<ul>
<li>
<p>Edge sites are as close as possible to end users. They improve the experience over high latency and unreliable connections.</p>
</li>
<li>
<p>May require specialized hardware, such as GPU/FPGA platforms for AR/VR functionality.</p>
</li>
<li>
<p>Edge can scale to large numbers of sites, distributed in distinct locations.</p>
</li>
<li>
<p>An edge site’s location and the identity of the access links it terminates are significant. An application that needs to run close to its users, needs to be in the right part of the edge. It is common for the application location to matter in edge computing.</p>
</li>
<li>
<p>The entire pool of sites can be considered to be dynamic. Because of their physical separation, edge sites will, in some cases, be connected to each other and the core with WAN connections. Edge sites will join and leave the pool of infrastructure over time.</p>
</li>
<li>
<p>Edge sites are remote and potentially unmanned, and therefore must be administered remotely. Tools need to support intermittent network access to the site.</p>
</li>
<li>
<p>Edge supports large differences in site size and scale, from data center scale down to a single device.</p>
</li>
<li>
<p>Edge sites may be resource constrained; adding capacity to an existing site is restricted due to space or power requirements.</p>
</li>
<li>
<p>Multi-tenancy on a massive scale is required for some of the use cases. </p>
</li>
<li>
<p>Isolation of edge computing from data center clouds may be required to ensure that compromises in the “external cloud” domain cannot impact services.</p>
</li>
</ul>',
                    '<p>The concept of edge computing must cover both the edge site (e.g. the compute, network and storage infrastructure), but also the applications (workloads) that run on it. It is worth noting that any applications in an edge computing environment could potentially leverage any or all of the capabilities provided by a cloud—compute, block storage, object storage, virtual networking, bare metal, or containers.</p>',
                    '<p>The essential features that define and separate edge computing from cloud computing are:</p>',
                    '<ul>
<li>
<p>The ability to support a dynamic pool of multiple potentially widely distributed sites,</p>
</li>
<li>
<p>Potentially unreliable network connections, and</p>
</li>
<li>
<p>the likelihood of difficult-to-resolve resource constraints at sites across the network.</p>
</li>
</ul>',
        ];

        $idx = 1;
        foreach($section_3_contents as $content){
            $c = new PaperParagraph();
            $c->Content = self::cleanContent($content);
            $c->Order = $idx;
            $c->SectionID = $section3->ID;
            $c->write();
            $idx++;
        }

        // EXPLORING CHARACTERISTICS
        $section4 = new PaperSection();
        $section4->Title = "EXPLORING CHARACTERISTICS";
        $section4->Order = 3;
        $section4->PaperID = $paper->ID;
        $section4->write();

        $section_4_contents = [
            '<p>So what do we know so far about edge computing characteristics, use cases, and scenarios? </p>',
            '<p>The defining need that drives cloud edge computing is the need for service delivery to be closer to users or end-point data sources. Edge computing environments will work in conjunction with core capacity, but aim to deliver an improved end user experience without putting unreasonable demands on connectivity to the core. Improvements result from:</p>',
            '<ol>
<li>
<p>
<span>Reducing latency:</span>The latency to the end user could be lower than it would be if the compute was farther away—making, for instance, responsive remote desktops possible, or successful AR, or better gaming.
</p>
</li>
<li>
<p>
<span>Mitigating bandwidth limits:</span>The ability to move workloads closer to the end users or data collection points reduces the effect of limited bandwidth at a site. This is especially useful if the service on the edge node reduces the need to transmit large amounts of data to the core for processing, as is often the case with IoT and NFV workloads. Data reduction and local processing can be translated into both more responsive applications and reduces the cost of transporting terabytes of data over long distances.   
</p>
</li>
</ol>',
            '<p>But there are trade-offs. To deliver edge computing, it is necessary to vastly increase the number of deployments. This institutes a significant challenge to widespread edge deployments. If managing a single cloud takes a team of ten, how can an organization cope with hundreds or even thousands of small clouds? Some requirements include:</p>',
            '<ol>
<li>
<p>
Standardization and infrastructure consistency are needed. Each location has to be similar; a known quantity.
 </p>
</li>
<li>
<p>
Manageability needs to be automated; deployment, replacement and any recoverable failures should be simple and straightforward.
</p>
</li>
<li>
<p>
Simple, cost-effective plans need to be laid for when hardware fails.  
</p>
</li>
<li>
<p>
Locally fault-tolerant designs might be important, particularly in environments that are remote or unreachable—zero touch infrastructure is desirable. This is a question that balances the cost of buying and running redundant hardware against the cost of outages and emergency repairs. Considerations include:
<ol>
<li><p>Do these locations need to be self-sufficient?</p></li>
<li><p>If a location has a failure, no one is going to be on-site to fix it, and local spares are unlikely.</p></li>
<li><p>Does it need to tolerate failures? And if it does, how long is it going to be before someone will be available  to repair it—two hours, a week, a month? </p></li>
</ol>
</p></li>
<li><p>Maintainability needs to be straightforward—untrained technicians perform manual repairs and replacements, while a skilled remote administrator re-installs or maintains software.</p></li>
<li><p>Physical designs may need a complete rethink. Most edge computing environments won’t be ideal—limited power, dirt, humidity and vibration have to be considered.</p></li>
</ol>',
        ];

        $idx = 1;
        foreach($section_4_contents as $content){
            $c = new PaperParagraph();
            $c->Content = self::cleanContent($content);
            $c->Order = $idx;
            $c->SectionID = $section4->ID;
            $c->write();
            $idx++;
        }

        // USE CASES
        $section5 = new PaperSection();
        $section5->Title = "USE CASES";
        $section5->Order = 1;
        $section5->PaperID = $paper->ID;
        $section5->ParentSectionID = $section4->ID;
        $section5->write();

        $section_5_contents = [
            '<p>There are probably dozens of ways to characterize use cases and this paper is too short to provide an exhaustive list. But here are some examples to help clarify thinking and highlight opportunities for collaboration.</p>',
            '<p>Four major categories of workload requirements that benefit from a distributed architecture are analytics, compliance, security, and NFV.</p>',
            '<p><h4>DATA COLLECTION AND ANALYTICS</h4></p>',
            '<p>IoT, where data is often collected from a large network of microsites, is an example of an application that benefits from the edge computing model. Sending masses of data over often limited network connections to an analytics engine located in a centralized data center is counterproductive; it may not be responsive enough, could contribute to excessive latency, and wastes precious bandwidth. Since edge devices can also produce terabytes of data, taking the analytics closer to the source of the data on the edge can be more cost-effective by analyzing data near the source and only sending small batches of condensed information back to the centralized systems. There is a tradeoff here—balancing the cost of transporting data to the core against losing some information.</p>',
            '<p><h4>SECURITY</h4></p>',
            '<p>Unfortunately, as edge devices proliferate––including mobile handsets and IoT sensors––new attack vectors are emerging that take advantage of the proliferation of endpoints. Edge computing offers the ability to move security elements closer to the originating source of attack, enables higher performance security applications, and increases the number of layers that help defend the core against breaches and risk.</p>',
            '<p><h4>COMPLIANCE REQUIREMENTS</h4></p>',
            '<p>Compliance covers a broad range of requirements, ranging from geofencing, data sovereignty, and copyright enforcement. Restricting access to data based on geography and political boundaries, limiting data streams depending on copyright limitations, and storing data in places with specific regulations are all achievable and enforceable with edge computing infrastructure.</p>',
            '<p><h4>NETWORK FUNCTION VIRTUALIZATION (NFV)</h4></p>',
            '<p>Network Function Virtualization (NFV) is at its heart the quintessential edge computing application because it provides infrastructure functionality. Telecom operators are looking to transform their service delivery models by running virtual network functions as part of, or layered on top of, an edge computing infrastructure. To maximize efficiency and minimize cost/complexity, running NFV on edge computing infrastructure makes sense.</p>',
            '<p><h4>REAL-TIME</h4></p>',
            '<p>Real-time applications, such as AR/VR, connected cars, telemedicine, tactile internet Industry 4.0 and smart cities, are unable to tolerate more than a few milliseconds of latency and can be extremely  sensitive to jitter, or latency variation. As an example, connected cars will require low latency and high bandwidth, and depend on computation and content caching near the user, making edge capacity a necessity. In many scenarios, particularly where closed-loop automation is used to maintain high availability, response times in tens of milliseconds are needed, and cannot be met without edge computing infrastructure.</p>',
            '<p><h4>IMMERSIVE</h4></p>',
            '<p>Edge computing expands bandwidth capabilities, unlocking the potential of new immersive applications. Some of these include AR/VR, 4K video, and 360° imaging for verticals like healthcare. Caching and optimizing content at the edge is already becoming a necessity since protocols like TCP don’t respond well to sudden changes in radio network traffic. Edge computing infrastructure, tied into real-time access to radio/network information can reduce stalls and delays in video by up to 20% during peak viewing hours, and can also vary the video feed bitrate based on radio conditions.</p>',
            '<p><h4>NETWORK EFFICIENCY</h4></p>',
            '<p>Many applications are not sensitive to latency and do not require large amounts of nearby compute or storage capacity, so they could theoretically run in a centralized cloud, but the bandwidth requirements and/or compute requirements may still make edge computing a more efficient approach. Some of these workloads are common today, including video surveillance and IoT gateways, while others, including facial recognition and vehicle number plate recognition, are emerging capabilities. With many of these, the edge computing infrastructure not only reduces bandwidth requirements, but can also provide a platform for functions that enable the value of the application—for example, video surveillance motion detection and threat recognition. In many of these applications, 90% of the data is routine and irrelevant, so sending it to a centralized cloud is prohibitively expensive and wasteful of often scarce network bandwidth. It makes more sense to sort the data at the edge for anomalies and changes, and only report on the actionable data.</p>',
            '<p><h4>SELF-CONTAINED AND AUTONOMOUS SITE OPERATIONS</h4></p>',
            '<p>Many environments, even today, have limited, unreliable or unpredictable connectivity. These could include transportation (planes, buses, ships), mining operations (oil rigs, pipelines, mines), power infrastructure (wind farms, solar power plants), and even environments that should typically have good connectivity, like stores. Edge computing neatly supports such environments by allowing sites to remain semi-autonomous and functional when needed or when the network connectivity is not available. The best example of this approach is the need for retail locations to maintain their point of sales (POS) systems, even when there is temporarily no network connectivity.</p>',
            '<p><h4>PRIVACY</h4></p>',
            '<p>Enterprises may have needs for edge computing capacity depending on workloads, connectivity limits and privacy. For example, medical applications that need to anonymize personal health information (PHI) before sending it to the cloud could do this utilizing edge computing infrastructure.</p>',
            '<p>Another way to look at requirements that would benefit from cloud edge computing is by the type of company that would deploy them. Operator applications are workloads put on edge computing infrastructure that is built and managed by operators—telecommunications companies, for example. Third-party applications are built by organizations to run on existing edge infrastructure, in order to leverage others’ edge computing infrastructure. It is worth noting that any applications could leverage any or all of the capabilities provided by a cloud—compute, block storage, object storage, virtual networking, bare metal, or containers.</p>'
        ];

        $idx = 1;
        foreach($section_5_contents as $content){
            $c = new PaperParagraph();
            $c->Content = self::cleanContent($content);
            $c->Order = $idx;
            $c->SectionID = $section5->ID;
            $c->write();
            $idx++;
        }

        // SCENARIOS

        $section6 = new PaperSection();
        $section6->Title = "SCENARIOS";
        $section6->Order = 2;
        $section6->PaperID = $paper->ID;
        $section6->ParentSectionID = $section4->ID;
        $section6->write();

        $section_6_contents = [
            '<p>The basic characteristic of the edge computing paradigm is that the infrastructure is located closer to the end user, that the scale of site distribution is high and that the edge nodes are connected by WAN network connections. Examining a few scenarios in additional depth helps us evaluate current capabilities that map to the use case, as well as highlighting weaknesses and opportunities for improvement.</p>',
            '<p>1. Retail/finance/remote location “cloud in a box”: Edge computing infrastructure that supports a suite of applications customized to the specific company or industry vertical. Often used by the enterprise, edge computing infrastructure, ultimately coupled together into distributed infrastructure, to reduce the hardware footprint, standardize deployments at many sites, deliver greater flexibility to replace applications located at the edge (and to have the same application running uniformly in all nodes irrespective of HW), boost resiliency, and address concerns about intermittent WAN connections. Caching content or providing compute, storage, and networking for self-contained applications are obvious uses for edge computing in settings with limited connectivity.</p>',
            '<p>2. Mobile connectivity: Mobile/wireless networks are likely to be a common environmental element for cloud edge computing, as mobile networks will remain characterized by limited and unpredictable bandwidth, at least until 5G becomes widely available. Applications such as augmented reality for remote repair and telemedicine, IoT devices for capturing utility (water, gas, electric, facilities management) data, inventory, supply chain and transportation solutions, smart cities, smart roads and remote security applications will all rely on the mobile network to greater or lesser degrees. They will all benefit from edge computing’s ability to move workloads closer to the end user.</p>',
            '<p class="text-center"><img src="https://www.openstack.org/assets/papers/edge-computing/2-edge-diagram-2.svg" alt="Table 2" ></p>',
            '<p>3. Network-as-a-Service (NaaS): Coming from the need to deliver an identical network service application experience in radically different environments, the NaaS use case requires both a small footprint of its distributed platform at the edges, and strong centralized management tools that cross over unreliable or limited WAN network connections in support of the services out on the edge. The main characteristics of this scenario are: small hardware footprint, moving (changing network connections) and constantly changing workloads, hybrid locations of data and applications. This is one of the cases that needs infrastructure to support micro nodes—small doses of compute in non-traditional packages (not all 19in rack in a cooled data center). NaaS will require support for thousands or tens of thousands of nodes at the edge and must support mesh and/or hierarchical architectures as well as on demand sites that might spin up as they are needed and shutdown when they are done. APIs and GUIs will have to change to reflect that large numbers of compute nodes will have different locations instead of being present in the same data center.</p>',
            '<p class="text-center"><img src="https://www.openstack.org/assets/papers/edge-computing/3-edge-network-transport-diagram2.svg" alt="Table 3"></p>',
            '<p>4. Universal Customer Premises Equipment (uCPE): This scenario, already being deployed today, demands support for appliance-sized hardware footprints and is characterized by limited network connections with generally stable workloads requiring high availability. It also requires a method of supporting hybrid locations of data and applications across hundreds or thousands of nodes and scaling existing uCPE deployments will be an emerging requirement.</p>',
            '<p>This is particularly applicable to NFV applications where different sites might need a different set of service chained applications, or sites with a different set of required applications that still need to work in concert. Mesh or hierarchical architectures would need to be supported with localized capacity and the need to store and forward data processing due to intermittent network connections. Self-healing and self-administration combined with the ability to remotely administer the node are musts.</p>',
            '<p>5. Satellite enabled communication (SATCOM): This scenario is characterized by numerous capable terminal devices, often distributed to the most remote and harsh conditions. At the same time, it makes sense to utilize these distributed platforms for hosting services, especially considering the extremely high latency, limited bandwidth and the cost of over-the-satellite communications. Specific examples of such use cases might include vessels (from fishing boats to tanker ships), aircrafts, oil rigs, mining operations or military grade infrastructure.</p>',
        ];

        $idx = 1;
        foreach($section_6_contents as $content){
            $c = new PaperParagraph();
            $c->Content = self::cleanContent($content);
            $c->Order = $idx;
            $c->SectionID = $section6->ID;
            $c->write();
            $idx++;
        }

        // CHALLENGES

        $section7 = new PaperSection();
        $section7->Title = "CHALLENGES";
        $section7->Order = 3;
        $section7->PaperID = $paper->ID;
        $section7->ParentSectionID = $section4->ID;
        $section7->write();

        $section_7_contents = [
            '<p>Though there are plenty of examples of edge deployments already in progress around the world, widespread adoption will require new ways of thinking to solve emerging and already existing challenges and limitations.</p>',
            '<p>We have established that the edge computing platform has to be, by design, much more fault tolerant and robust than a traditional data center centric cloud, both in terms of the hardware as well as the platform services that support the application lifecycle. We cannot assume that such edge use cases will have the maintenance and support facilities that standard data center infrastructure does. Zero touch provisioning, automation, and autonomous orchestration in all infrastructure and platform stacks are crucial requirements in these scenarios.</p>',
            '<p>But there are other challenges that need to be taken under consideration.</p>',
            '<p>For one, edge resource management systems should deliver a set of high-level mechanisms whose assembly results in a system capable of operating and using a geo-distributed IaaS infrastructure relying on WAN interconnects. In other words, the challenge is to revise (and extend when needed) IaaS core services in order to deal with aforementioned edge specifics—network disconnections/bandwidth, limited capacities in terms of compute and storage, unmanned deployments, and so forth.</p>',
            '<p>Some foreseeable needs include:</p>',
            '<ul>
<li>
<p>
A virtual-machine/container/bare-metal manager in charge of managing machine/container lifecycle (configuration, scheduling, deployment, suspend/resume, and shutdown).
</p>
</li>
<li>
<p>
An image manager in charge of template files (a.k.a. virtual-machine/container images).
</p>
</li>
<li>
<p>
A network manager in charge of providing connectivity to the infrastructure: virtual networks and external access for users.
</p>
</li>
<li>
<p>
A storage manager, providing storage services to edge applications.
</p>
</li>
<li>
<p>
Administrative tools, providing user interfaces to operate and use the dispersed infrastructure.
</p>
</li>
</ul>
</p>',
'<p>These needs are relatively obvious and could likely be met by leveraging and adapting existing projects. But other needs for edge computing are more challenging. These include, but are not limited to:</p>',
'<p>
<ul>
<li>
<p>
Addressing storage latency over WAN connections.
</p>
</li>
<li>
<p>
Reinforced security at the edge—monitoring the physical and application integrity of each site, with the ability to autonomously enable corrective actions when necessary.
</p>
</li>
<li>
<p>
Monitoring resource utilization across all nodes simultaneously.
</p>
</li>
<li>
<p>
Orchestration tools that manage and coordinate many edge sites and workloads, potentially leading toward a peering control plane or "self-organizing edge."
</p>
</li>
<li>
<p>
Orchestration of a federation of edge platforms (or cloud-of-clouds) has to be explored and introduced to the IaaS core services.
<ul>
<li>
<p>
Automated edge commission/decommission operations, including initial software deployment and upgrades of the resource management system’s components.
</p>
</li>
<li>
<p>
Automated data and workload relocations—load balancing across geographically distributed hardware.
</p>
</li>
</ul>
</p>
</li>
<li>
<p>
Some form of synchronization of abstract state propagation should be needed at the "core" of the infrastructure to cope with discontinuous network links.
</p>
</li>
<li>
<p>
New ways to deal with network partitioning issues due to limited connectivity—coping with short disconnections and long disconnections alike.
</p>
</li>
<li>
<p>
Tools to manage edge application life cycles, including: 
<ul>
<li>
<p>
The definition of advanced placement constraints in order to cope with latency requirements of application components.
</p>
</li>
<li>
<p>
The provisioning/scheduling of applications in order to satisfy placement requirements (initial placement).
</p>
</li>
<li>
<p>
Data and workload relocations according to internal/external events (mobility use-cases, failures, performance considerations, and so forth).
</p>
</li>
<li>
<p>
Integration location awareness: Not all edge deployments will require the same application at the same moment. Location and demand awareness are a likely need.
</p>
</li>
</ul>
</p>
</li>
<li>
<p>
Discrete hardware with limited resources and limited ability to expand at the remote site needs to be taken into consideration when designing both the overall architecture at the macro level and the administrative tools. The concept of being able to grab remote resources on demand from other sites, either neighbors over a mesh network or from core elements in a hierarchical network, means that fluctuations in local demand can be met without inefficiency in hardware deployments.
</p>
</li>
</ul>',
        ];

        $idx = 1;
        foreach($section_7_contents as $content){
            $c = new PaperParagraph();
            $c->Content = self::cleanContent($content);
            $c->Order = $idx;
            $c->SectionID = $section7->ID;
            $c->write();
            $idx++;
        }

        // CONCLUSION AND CALL TO ACTION
        $section8 = new IndexSection();
        $section8->Title = "CONCLUSION AND CALL TO ACTION";
        $section8->Order = 4;
        $section8->PaperID = $paper->ID;
        $section8->write();

        $section_8_contents = [
            '<p>Edge computing is not and should not be limited to just the components and architectures of OpenStack, but there are some reasons that OpenStack is particularly attractive as a platform for cloud edge computing. The Edge Computing Group is asking the open source community to begin exploring these challenges and possibilities. We recognize that there is work to be done to achieve our goals of creating the tools to meet these new requirements. We welcome and encourage the entire open source community to join in the opportunity to define and develop cloud edge computing. You can find more information about the group activities on the <a href="https://www.openstack.org/edge-computing/">OpenStack Edge Computing</a> web page.</p>',
            '<p>Visit <a href="https://www.openstack.org">www.openstack.org</a> to get started with OpenStack or click on these resources for more information:</p>',
        ];

        $idx = 1;
        foreach($section_8_contents as $content){
            $c = new PaperParagraph();
            $c->Content = self::cleanContent($content);
            $c->Order = $idx;
            $c->SectionID = $section8->ID;
            $c->write();
            $idx++;
        }

        $section_8__idx_items = [
            'OSF Edge Computing web page' => '<p>A central place to find pointers to videos of previous events, articles and further content on edge computing.</p>',
            'OSF Edge Computing mailing list' => '<p>A forum for discussions concerning edge and edge computing (not exclusively to OpenStack) and to receive information about ongoing activities and calls for action.</p>',
            'OpenStack Open Infrastructure Summit' => '<p>A four-day conference for IT business leaders, cloud operators and developers covering the open infrastructure landscape..Look for edge-related tracks and sessions at upcoming Summits.</p>',
            'IRC' => '<p>Fog/Edge/Massively Distributed Clouds (FEMDC) SIG IRC meetings (online) are held every two weeks (odd weeks), 1500 UTC on Wednesday, on the IRC channel #openstack-meeting.</p>',
            'OSF events' => '<p>Global schedule of events including the popular OpenStack Summits and regional OpenStack Days.</p>',
            'OpenStack Marketplace' => '<p> One-stop resource to the skilled global ecosystem for distributions, drivers, training, services and more.</p>',
            'Complete OpenStack documentation' => '<p> Index to all documentation, for every role and step in planning and operating an OpenStack cloud.</p>',
            'Welcome to the community!' => '<p>Join mailing lists and IRC channels, find jobs and events, access the source code and more.</p>',
            'User groups' => '<p>Find a user group near you, attend meetups and hackathons—or organize one!</p>',
        ];

        $section_8_links =
        [
            'https://www.openstack.org/edge-computing/',
            'http://lists.openstack.org/cgi-bin/mailman/listinfo/edge-computing',
            'https://www.openstack.org/summit/berlin-2018/',
            'https://wiki.openstack.org/wiki/IRC',
            'https://www.openstack.org/community/events/',
            'https://www.openstack.org/marketplace/',
            'https://docs.openstack.org/queens/',
            'https://www.openstack.org/community/',
            'https://groups.openstack.org/',
        ];

        $idx = 0;
        foreach($section_8__idx_items as $title => $content){
            $c = new IndexItem();
            $c->Title = $title;
            $c->Link = $section_8_links[$idx];
            $c->Content = self::cleanContent($content);
            $c->Order = $idx + 1;
            $c->SectionID = $section8->ID;
            $c->write();
            $idx++;
        }

        // AUTHORS

        $section8 = new PaperSection();
        $section8->Title = "AUTHORS";
        $section8->Order = 5;
        $section8->PaperID = $paper->ID;
        $section8->write();

        $section_8_contents = [
            '<h4 class="subtitle">CONTRIBUTORS</h4>
<ul>
<li>
<p>Beth Cohen, Distinguished Member of Technical Staff, Verizon</p>
</li>
<li>
<p>Gnanavelkandan Kathirvel, Director – Cloud Strategy & Architecture, AT&T and Board of Directors, OpenStack Foundation (OSF)</p>
</li>
<li>
<p>Gregory Katsaros, Senior System Architect, Inmarsat</p>
</li>
<li>
<p>Adrien Lebre, Ass. Prof., IMT Atlantique/Inria/LS2N, France</p>
</li>
<li>
<p>Andrew Mitry, Sr. Distinguished Engineer, Walmart Labs</p>
</li>
<li>
<p>Christopher Price, President, Ericsson Software Technology</p>
</li>
<li>
<p>Paul-André Raymond, SVP Technology, B.Yond</p>Brian E. Whitaker, Founder, Zettabyte Content
</li>
<li>
<p>Alex Reznik, Enterprise Architect, HPE and Chair, ETSI ISG MEC</p>
</li>
<li>
<p>Pasi Vaananen, Systems Architect, NFV, Red Hat</p>
</li>
<li>
<p>Ildiko Vansca, Ecosystem Technical Lead, OpenStack Foundation (OSF)</p>
</li>
<li>
<p>Ian Wells, Distinguished Engineer, Cisco</p>
</li>
</ul>',
'<h4 class="subtitle">TECHNICAL WRITER</h4>
<ul>
<li>
<p>Brian E. Whitaker, Founder, Zettabyte Content</p>
</li>
</ul>'
        ];

        $idx = 1;
        foreach($section_8_contents as $content){
            $c = new PaperParagraph();
            $c->Content = self::cleanContent($content);
            $c->Order = $idx;
            $c->SectionID = $section8->ID;
            $c->write();
            $idx++;
        }
    }

}