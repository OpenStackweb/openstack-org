</div>

<div class="intro-header text-center">
    <h1>Leveraging Containers and OpenStack</h1>
    <h2>A Comprehensive Review</h2>
</div>





<section class="containers-page ">
    <div class="navigation stick-top">
        <div class="container">
            <div class="group">
                <button id="btnPrv" class="btn" disabled>
                    <i class="fa fa-caret-left"></i>
                </button>
                <button id="btnNxt" class="btn">
                    <i class="fa fa-caret-right"></i>
                </button>
            </div>

            <div class="dropdown" role="tablist" id="chapters">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="true"> Introduction
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="ddl-intro" data-target="introduction">Introduction</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-target="high-level">I. A High Level View of Containers in OpenStack</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-target="integration-points">II. OpenStack Container Integration Points</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-target="case-studies">III. Case Studies</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-target="conclusion">IV. Conclusion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-target="proyect-index">V. Open Source Project Index</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-target="authors">VI. Authors</a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
    <div class="container content">
        <div class="row section" id="introduction">
            <div class="col-lg-12">
                <h3 class="text-center title">Introduction</h3>
                <p>Imagine that you are tasked to build an entire private cloud infrastructure from the ground up. You have
                    a limited budget, a small but dedicated team, and are asked to pull off a miracle.
                </p>
                <p>A few years ago, you’d build an infrastructure with applications running in virtual machines, with some
                    bare-metal machines for legacy applications. As infrastructure has evolved, virtual machines enabled
                    greater levels of efficiency and agility, but virtual machines alone don’t completely meet the needs
                    of an agile approach to application deployment. They continue to serve as a foundation for running
                    many applications, but increasingly, developers are looking toward the emerging trend of containers
                    for leading-edge application development and deployment because containers offer increased levels
                    of agility and efficiency.</p>
                <p>Container technologies like Docker and Kubernetes are becoming the leading standards for building containerized
                    applications. They help free organizations from complexity that limits development agility. Containers,
                    container infrastructure, and container deployment technologies have proven themselves to be very
                    powerful abstractions that can be applied to a number of different use cases. Using something like
                    Kubernetes, an organization can deliver a cloud that solely uses containers for application delivery.</p>
                <p>But a leading-edge private cloud isn’t just about containers, and containers aren’t appropriate for all
                    workloads and use cases. Today, most private cloud infrastructures need to encompass bare-metal machines
                    for managing infrastructure, virtual machines for legacy applications, and containers for newer applications.
                    The ability to support, manage and orchestrate all three approaches is the key to operational efficiency.</p>
                <p>OpenStack is currently the best available option for building private clouds, with the ability to manage
                    networking, storage and compute infrastructure, with support for virtual machines, bare-metal, and
                    containers from one control plane. While Kubernetes is arguably the most popular container orchestrator
                    and has changed application delivery, it depends on the availability of solid cloud infrastructure,
                    and OpenStack offers the most comprehensive open source infrastructure for hosting applications.
                    OpenStack’s multi-tenant cloud infrastructure is a natural fit for Kubernetes, with several integration
                    points, deployment solutions, and ability to federate across multiple clouds.
                </p>
                <p class="text-center">
                    <img src="themes/openstack/images/containers2/diagram.svg" alt="Table 1" class="hover-shadow clickable-image">
                </p>
            </div>
        </div>
        <div class="row section" id="high-level">
            <div class="col-lg-12">
                <h3 class="text-center title">I. A High Level View of Containers in OpenStack</h3>
                <p>There are three primary scenarios where containers and OpenStack intersect.</p>
                <p>The first scenario, called infrastructure containers, allows operators to leverage containers in a way
                    that improves cloud infrastructure deployment, management, and operation. In this scenario, containers
                    are set up on a bare-metal infrastructure, and are allowed privileged access to host resources, which
                    allows them to take direct advantage of compute, networking, and storage resources that container
                    runtimes are typically trying to hide from users. Containers isolate the often complex set of dependencies
                    that each application depends on, while allows the infrastructure applications to directly manage
                    and manipulate the underlying resources. When the time comes to upgrade an service, it can be handled
                    without changes in dependencies disrupting co-located services. </p>
                <p>Modern versions of OpenStack have embraced this infrastructure container model, and it’s now normal to
                    manage an entire lifecycle of an OpenStack deployment with a combination of orchestration tooling
                    and containerized services. Infrastructure containers enable operators to use container orchestration
                    technologies to solve many issues, particularly around rapidly iterating/upgrading existing software
                    including OpenStack. Running OpenStack within containers helps operators to solve Day 2 challenges,
                    including adding new components for services, upgrading versions of software quickly, and rapidly
                    rolling updates across machines and data centers. This approach brings the agility of containers
                    to the problem of OpenStack deployment and upgrades.</p>
                <p>The second scenario is concerned with hosting containerized application frameworks on cloud infrastructure.
                    These can include Container Orchestration Engines (COEs) like Docker Swarm and Kubernetes, or lighter-weight
                    container-focused services and serverless APIs. Whether on bare-metal or virtual machines, the OpenStack
                    community has worked to ensure that it’s possible to deliver containerized applications on a secure,
                    tenant-isolated cloud host. This scenario is facilitated by drivers that allow projects like Kubernetes
                    to directly take advantage of OpenStack APIs for storage, load-balancing, and identity. It also includes
                    APIs for provisioning managed Kubernetes clusters and application containers on demand. With these
                    capabilities, development teams can write new containerized applications and quickly provision Kubernetes
                    clusters on OpenStack clouds. It’s a complete application lifecycle solution that gives them the
                    resources needed to develop, test, and debug their code, with robust automation to deploy their applications
                    into production.</p>
                <p>In the final scenario, we consider the interactions between independent OpenStack and COE deployments,
                    and in this paper particularly Kubernetes clusters. Consistency and interoperability of APIs across
                    both OpenStack and Kubernetes clusters is the primary source of success for this scenario. For example,
                    it’s possible for Kubernetes to directly attach to </p>
                <p>OpenStack Cinder hosted volumes, use OpenStack Keystone as an authorization and authentication backed,
                    or connect to OpenStack Neutron as a network overlay with OpenStack Kuryr. Conversely, it’s possible
                    for an OpenStack cloud to share the same network overlay as a Kubernetes cluster with Neutron drivers
                    for projects like Calico. The third scenario is less focused on how a cloud service is hosted (be
                    it Kubernetes or OpenStack), and more on how independent services interact.
                </p>
            </div>
        </div>

        <div class="row section" id="integration-points">
            <div class="col-lg-12">
                <h3 class="text-center title">II. OpenStack Container Integration Points</h3>
                <h4 class="subtitle">Deploying OpenStack Infrastructure on Containers</h4>
                <p>As noted in the introduction, the deployment and management of OpenStack has changed significantly with
                    the rise of containers, because containers unlock new approaches to managing infrastructure code.
                    Previous management strategies required either the creation and maintenance of heavyweight golden
                    machine images, or using brittle state-maintaining configuration-management systems. Each approach
                    comes with complexities and restrictions. Adding to the degree of difficulty is the management of
                    a collection of services that all require their own dependencies that change from release-to-release.
                    Without some form of application isolation, solving for the dependencies becomes a difficult if not
                    impossible problem.</p>
                <p>Infrastructure containers enable new OpenStack deployment projects to strike a balance between the two,
                    while elegantly solving the dependency problem. Using lightweight, independent, self-contained, and
                    typically stateless application containers, a cloud operator gains tremendous flexibility when deploying
                    a complex control plane. Combined with a container runtime and an orchestration engine, infrastructure
                    containers make it possible to quickly deploy, maintain, and upgrade complex and highly available
                    infrastructure.
                </p>
                <p>In building an OpenStack cluster, there are several dimensions for choosing deployment technologies.
                    An operator has options for LXC or Docker for their base containers, the ability to use a wide variety
                    of pre-built or custom-built application containers, and either traditional configuration-management
                    systems for orchestration or a more modern approach like Kubernetes. Table 1 summarizes the existing
                    OpenStack deployment projects and their underlying technologies.</p>
                <p class="text-center">
                    <img src="themes/openstack/images/containers2/table.svg" alt="Table 1" >
                </p>
                <p>Underlying each of these deployment systems are different approaches to building a set of containers
                    for the OpenStack code and supporting services. The OpenStack Ansible (OSA) and Kolla projects provide
                    their own project-hosted build systems, while Loci focuses on building project application containers,
                    without a specific orchestration system in mind. At a high level, the differences are:</p>
                <ol>
                    <li>
                        <p>OSA is unique in that is relies on lower-level LXC containers, and has a custom build system
                            for creating LXC application containers.</p>
                    </li>
                    <li>
                        <p>The Kolla build system produces Docker containers, one for each service, along with supporting
                            containers for initializing and managing an OpenStack deployment. Kolla containers are highly
                            configurable, with a choice of base operating system, source or package installations, and
                            a template engine for even further customization.</p>
                    </li>
                    <li>
                        <p>The final option for building OpenStack application containers is Loci. Loci also builds Docker
                            containers, and delivers one container for each project. Loci is focused on producing compact
                            containers, with the expectation that they will be further customized by the orchestration
                            tooling.
                        </p>
                    </li>
                </ol>
                <h5 class="highlight">Bare-Metal Infrastructure - OpenStack and Solving the Bootstrap Problem</h5>
                <p>At the foundation of every cloud, there exists a data center of bare-metal servers that host the infrastructure
                    services. Even “serverless computing” is running software on a cloud on hardware in some data center.
                    The problem of how to bootstrap hardware infrastructure is a critical problem that OpenStack software
                    is uniquely qualified to address in a cloud-like way.</p>
                <p>OpenStack Ironic provides bare-metal as a service. As a standalone service it can discover bare-metal
                    nodes, catalog them in a management database, and manage the entire server lifecycle including enrolling,
                    provisioning, maintenance, and decommissioning. When used as a driver to OpenStack Nova and combined
                    with the full suite of OpenStack services, it delivers a powerful, cloud-like service for managing
                    your entire bare-metal infrastructure.</p>
                <p>This raises the question: How does one bootstrap OpenStack services to manage bare-metal infrastructure?
                    One typical solution is to use the same container-based installation tools as described in the previous
                    sections to create a seed installation. This seed, often called an ‘undercloud’, can be used to entirely
                    automate the management of a bare-metal cluster as if it were a virtualized cloud.</p>
                <p>This opens up an opportunity to not just run OpenStack virtualization on a bare-metal cloud, but to also
                    run bare-metal Kubernetes-only installations that can take full advantage of the identity, storage,
                    networking, and other cloud APIs available through OpenStack services.</p>
                <h4 class="subtitle">Delivering Container-Based Applications on OpenStack</h4>
                <p>Both infrastructure containers and bare-metal infrastructure are important, but when most people think
                    of containers, they’re thinking of application containers. The isolation, encapsulation, and ease
                    of maintenance offered by containers makes them an ideal solution for delivering applications. However,
                    containers still need a host platform to serve them from, whether bare-metal, public cloud, or private
                    cloud.
                </p>
                <p>Kubernetes is a platform for delivering applications, and works best with cloud-APIs that can automate
                    the delivery of critical infrastructure such as permanent storage, load-balancers, networks, and
                    dynamic allocation of compute nodes. OpenStack delivers cloud infrastructure, whether as an on-prem
                    private cloud, or through any of the available public or managed OpenStack clouds.</p>
                <p>OpenStack was one of the first upstream cloud providers for Kubernetes, with an active team of developers
                    maintaining the "Kubernetes/Cloud Provider OpenStack" plugin. This plugin allows Kubernetes to take
                    advantage of Cinder block storage, Neutron and Octavia Load Balancers, and direct management of compute
                    resources with Nova. Using the provider is as simple as deploying the driver to your Kubernetes installation,
                    setting a flag to load the driver, and providing your local user cloud credentials.</p>
                <p>There are a number of solutions for installing Kubernetes and other application frameworks on top of
                    OpenStack. One of the easiest ways to deliver container frameworks is to use Magnum, an OpenStack
                    project that provides a simple API to deploy fully managed clusters backed by a choice of several
                    application platforms, including Kubernetes. It’s an example of a Kubernetes deployment system that
                    relies on OpenStack APIs and cloud provider plugin. For example, right now it’s being used to manage
                    over 200 independent and federated Kubernetes installations on CERN’s OpenStack on-site cloud, as
                    well as on partner clouds. If you don’t have the Magnum API available to you in your preferred OpenStack
                    cloud, you can use any other Kubernetes installation tools such as the kubeadm, Kubernetes Anywhere,
                    or Kubespray, to install and manage your Kubernetes cluster on OpenStack. Because each uses standard
                    Kubernetes, it’s easy to enable the cloud provider interface to take advantage of storage and load
                    balancing.
                </p>
                <p>In a Kubernetes cluster, a node can be a virtual server provided by OpenStack Nova, or a virtual node
                    implemented by third-party software such as Virtual Kubelet. A virtual node doesn't consume compute
                    resource upfront and allows users to provision pods and containers on demand. Zun, another OpenStack
                    Project, can be used to implement a virtual Kubernetes node. OpenStack Zun offers a lighter-weight
                    container service API for managing individual containers without the need for managing servers or
                    clusters. Direct integration with Neutron and Cinder are used to provide networking and volumes for
                    individual containers.</p>
                <p>Zun, another OpenStack project, offers a lighter-weight container service API for managing individual
                    containers without the need for managing servers or clusters. Direct integration with Neutron and
                    Cinder are used to provide networking and volumes for individual containers. Zun is being used in
                    production by ZTE Corporation.</p>
                <h4 class="subtitle">Side-by-Side OpenStack and Kubernetes Integrations</h4>
                <p>One of the primary benefits of choosing open source platforms is in the stability of interfaces across
                    standard deployments of those platforms. Both the OpenStack Foundation and the CNCF maintain interoperability
                    standards for OpenStack clouds and Kubernetes clusters, guaranteeing that libraries, applications,
                    and drivers will work across all platforms regardless of where they are deployed. This creates opportunities
                    for side-by-side integrations, allowing both OpenStack and Kubernetes to take advantage of the resources
                    provided by the other.</p>
                <p>The OpenStack Special Interest Group (SIG-OpenStack) in the Kubernetes community maintains the Cloud
                    Provider OpenStack plugin. In addition to cloud provider interface for running Kubernetes on OpenStack,
                    it also maintains several drivers that allows Kubernetes to take advantage of individual OpenStack
                    services. These drivers include:</p>
                <ul>
                    <li>
                        <p>Two standalone Cinder drivers. A Flex Volume driver uses an exec-based model to interface with
                            drivers, and a Container Storage Interface (CSI) driver which uses a standard interface for
                            container orchestration systems to expose arbitrary storage systems to their container workloads.
                            With support for over 70 storage drivers, these drivers make it possible to interface a wealth
                            of battle tested proprietary and open source storage devices through a single Cinder API.</p>
                    </li>
                    <li>
                        <p>A webhook-based authentication and authorization interface to Keystone. Each mode, authentication
                            and authorization, can be configured independently of one another. It is still a work in
                            progress, but already brings support for a soft-multi-tenancy that backs Kubernetes RBAC
                            with OpenStack Keystone.</p>
                    </li>
                </ul>
                <p>Both OpenStack and Kubernetes support highly dynamic networking models that are backed by a variety of
                    drivers. Because of these standard network interfaces, it’s easy to build standalone OpenStack and
                    Kubernetes clusters with strong network integrations. Within OpenStack, the Kuryr project produces
                    a Common Network Interface (CNI) driver that delivers Neutron networking to Docker and Kubernetes.
                    On the flip side, there projects like Calico offer Neutron drivers, providing direct access to popular
                    Kubernetes network overlays through standard Neutron APIs.</p>
                <h5 class="highlight">Kata Containers - Secure Applications through Virtualization</h5>
                <p>Kata Containers is a novel implementation of a lightweight virtual machine that seamlessly integrates
                    within the container ecosystem. Kata Containers are as light and fast as containers and integrate
                    with the container management layers -- including popular orchestration tools such as Docker and
                    Kubernetes (k8s) -- while also delivering the security advantages of VMs.</p>
                <p>The industry shift to containers presents unique challenges in securing user workloads within multi-tenant
                    environments with a mix of both trusted and untrusted workloads. Kata Containers uses hardware-backed
                    isolation as the boundary for each container or collection of containers in a pod. This approach
                    addresses the security concerns of a shared kernel in a traditional container architecture.</p>
                <p>Kata Containers is an excellent fit for both on-demand, event-based deployments such as, continuous integration/continuous
                    delivery, as well as longer running web server applications. Kata also enables an easier transition
                    to containers from traditional virtualized environments with support for legacy guest kernels and
                    device pass through capabilities. Kata Containers delivers enhanced security, scalability and higher
                    resource utilization, while at the same time leading to an overall simplified stack.</p>
                <p class="text-center">
                    <img src="themes/openstack/images/containers2/kata-diagram.svg" alt="Intro" class="hover-shadow clickable-image">
                </p>
            </div>
        </div>
        <div class="row section" id="case-studies">
            <div class="col-lg-12">
                <h3 class="text-center title">III. Case Studies</h3>
                <p>Many members of the OpenStack community are contributing new code to various OpenStack projects, evaluating
                    the implications and benefits of containers, and using containers in production to solve challenges
                    and unlock new capabilities. This section highlights some of the most interesting case studies.</p>

                <h6>Select a Case Study</h6>
                <ul class="nav nav-tabs">
                    <li class="active col-lg-3 col-sm-6 col-xs-12">
                        <a data-toggle="tab" href="#super">
                            <img class="logo" src="themes/openstack/images/containers2/super.jpg" alt="Superfluidity">
                        </a>
                    </li>
                    <li class="col-lg-3 col-sm-6 col-xs-12">
                        <a data-toggle="tab" href="#sk">
                            <img class="logo" src="themes/openstack/images/containers2/sk.jpg" alt="SK Telecom">
                        </a>
                    </li>
                    <li class="col-lg-3 col-sm-6 col-xs-12">
                        <a data-toggle="tab" href="#att">
                            <img class="logo" src="themes/openstack/images/containers2/att.jpg" alt="AT&#38;T">
                        </a>
                    </li>
                    <li class="col-lg-3 col-sm-6 col-xs-12">
                        <a data-toggle="tab" href="#cern">
                            <img class="logo" src="themes/openstack/images/containers2/cern.jpg" alt="cern">
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="super" class="tab-pane fade in active">
                        <h4 class="subtitle">Superfluidity</h4>
                        <p>The
                            <a href="http://superfluidity.eu/">
                                Superfluidity project
                            </a>is made up of 18 partners from 12 European countries. It aims to enhance the ability
                            to instantiate services on-the-fly, run them anywhere in the network (core, aggregation,
                            edge) and shift them transparently to different locations. SUPERFLUIDITY is a European Research
                            project (Horizon 2020) trying to build the basic infrastructure blocks for 5G networks by
                            leveraging and extending well known open source projects. SUPERFLUIDITY will provide a converged
                            cloud-based 5G concept that will enable innovative use cases in the mobile edge, empower
                            new business models, and reduce investment and operational costs.</p>
                        <p>To pursue these goals, the project consortium is shifting away from legacy, VM-based applications
                            to Cloud Native containerized applications. Kuryr serves as a bridge between OpenStack virtual
                            machines, and Kubernetes and OpenShift containerized services.</p>
                        <p>The project makes use of ManageIQ as a central NFVO orchestrator, Ansible for Application deployment
                            and lifecycle management, OpenStack services including Heat, Neutron, and Octavia, and Kubernetes
                            through OpenShift for VMs and Containers integration.</p>
                        <p class="text-center">
                            <img class="medium-img" src="themes/openstack/images/containers2/super-table.jpg" alt="Superfluidity">
                        </p>
                        <p>By leveraging Ansible playbooks excuted from the ManageIQ appliance, SUPERFLUIDITY offers a common
                            way to deploy applications. These applications in turn use the cloud orchestration functionality
                            provided by OpenStack Heat templates and OpenShift templates.</p>
                        <p>The consortium is deploys 5G CRAN (rrh, bbu, or epc) or MEC (traffic offloading functions and
                            MEC orchestrator) components within containers. It also deploys high throughput applications
                            like video streaming on top of the distributed infrastructure.</p>
                        <p>Shifting toward a cloud native approach to application delivery allows for rapid and resilient
                            SUPERFLUIDITY installations. It enables a smooth transitions from VM-based applications and
                            components to containers. By building this platform on OpenStack it gives the project the
                            the versatility to enable VMs for some specific purpose applications. An example of this
                            are the special security protections or network acceleration required by SRIOV.
                        </p>
                        <p>In scale performance testing, SUPERFLUIDITY was able to launch approximately 1000 pods at a rate
                            of 22 pods/second (with time measured from creation to running). This remarkable performance
                            was achieved by running OpenShift on VMs managed by OpenStack, with Kuryr acting as a pod
                            network driver to avoid double-encapsulation performance hits.
                        </p>
                    </div>
                    <div id="sk" class="tab-pane fade">
                        <h4 class="subtitle">SK Telecom</h4>
                        <p>SK Telecom, South Korea’s largest telecommunications operator, has been exploring optimized approaches
                            for deploying OpenStack on Kubernetes with the aim of putting core business functions on
                            containerized OpenStack by the end of 2018. SKT leverages Kolla and Openstack-Helm to deploy
                            containerized Openstack on Kubernetes (with deployments automated by
                            <a href="https://github.com/kubernetes-incubator/kubespray">
                                Kubespray
                            </a>). SKT devotes nearly 100% of it’s development efforts to OpenStack-Helm, and works closely
                            with AT&#38;T to make OpenStack-Helm successful.</p>
                        <p>SKT has also incorporated other tools into their OpenStack on Kubernetes efforts. For logging,
                            monitoring, and alarms, they are using Prometheus and Elasticsearch, Fluent-bit, and Kibana,
                            all of which are default reference tools in the OpenStack-Helm community. SKT combines all
                            of these into a single closed-integrated solution called TACO: SKT All Container OpenStack.
                        </p>
                        <p class="text-center">
                            <img class="medium-img" src="themes/openstack/images/containers2/sk-diagram.jpg" alt="SK Telecom">
                        </p>
                        <p>SKT specifically emphasizes automated CI/CD pipeline around containerized Openstack on Kubernetes.
                            SKT’s CI system consists of Jenkins, Rally, Tempest, Docker Registry, and some commercial
                            products from Atlassian (Jira, Bitbucket). SKT also developed an open source software called
                            <a href="https://github.com/sktelecom-oslab/cookiemonster">
                                Cookiemonster
                            </a>, a chaos-monkey like resiliency test tool for Kubernetes deployment, to do perform resiliency
                            tests in their CI pipeline. </p>
                        <p>With every change, SKT automatically builds and tests both the OpenStack containers and Helm
                            charts. Daily, they automatically install a highly available OpenStack deployment with three
                            control nodes and two compute-nodes, run 400 test cases from Tempest against it to validate
                            the services, and finally run resiliency testing with Cookiemonster and Rally. The complete
                            CI system is illustrated in the following diagram:.</p>
                        <p class="text-center">
                            <img class="medium-img" src="themes/openstack/images/containers2/sk-diagram2.jpg" alt="SK Telecom">
                        </p>
                        <p>SKT automates its deployments with Armada, an
                            <a href="https://github.com/att-comdev/armada">
                                open source software project
                            </a> developed by AT&#38;T. SKT actively contributes to the Armada project, providing enhancements
                            to the project based on their production uses.
                        </p>
                        <p>In practical use, SKT has already seen a large number of benefits from deploying OpenStack on
                            Kubernetes including:
                        </p>
                        <ul>
                            <li>
                                <p>Simple and Easy Installations.</p>
                            </li>
                            <li>
                                <p>Cluster Auto-Healing.</p>
                            </li>
                            <li>
                                <p>An ability to upgrade and update OpenStack with minimal impact to running services.</p>
                            </li>
                            <li>
                                <p>Rapid adoption of advanced release methodologies, including blue-green deployment, canary
                                    releases.
                                </p>
                            </li>
                            <li>
                                <p>Complete automated management of Python dependencies through container isolation.</p>
                            </li>
                            <li>
                                <p>Secure secret and configuration management.</p>
                            </li>
                            <li>
                                <p>Fast and flexible roll-outs of cluster updates.</p>
                            </li>
                        </ul>
                        <p>SKT is still testing the approach, but is actively moving towards running their OpenStack-Helm
                            deployments in production. By end of this year, SKT will have at least three production clusters,
                            with the fourth and largest coming online in 2019. These use cases for them include:
                        </p>
                        <ul>
                            <li>
                                <p>A Big Data platform (planned to go live Q4 2018) </p>
                            </li>
                            <li>
                                <p>A virtual desktop infrastructure platform (production ready by Q4 2018) .</p>
                            </li>
                            <li>
                                <p>A General purpose Internal Private Cloud (planned to go live Q3 2018)</p>
                            </li>
                            <li>
                                <p>A telco network infrastructure built on virtual network functions (planned to open sometime
                                    in 2019).</p>
                            </li>
                        </ul>
                        <p>SKT is also trying to improve automation on telecom infrastructure operation by utilizing containerized
                            virtual network functions (VNFs) and leveraging containers’ auto healing and fast scale-out
                            features. In order to allow interaction between virtual machine based VNFs and containerized
                            VNFs,
                            <a href="https://wiki.onosproject.org/display/ONOS/SONA%3A+DC+Network+Virtualization">
                                Simplified Overlay Network Architecture
                            </a> (SONA), which is a virtual network solution for OpenStack, will support communication
                            between VMs and containers. SONA uses the Kuryr project for integration of OpenStack and
                            Kubernetes, and it optimizes network performance using software defined networking technologies.
                        </p>
                        <p>Overall, SKT is finding that Kubernetes helps solve many of the complexities of deploying and
                            operating OpenStack. Simplifying OpenStack gives them a powerful approach to deliver advanced
                            infrastructure innovation for the 5G era. Focusing efforts on Openstack on Kubernetes dramatically
                            increased their internal capability to deal with the evolving shift toward microservices
                            in containers and become a critical infrastructure for delivering Artificial Intelligence,
                            Internet of Things, and Machine Learning.
                        </p>
                    </div>
                    <div id="att" class="tab-pane fade">
                        <h4 class="subtitle">AT&#38;T</h4>
                        <p>AT&#38;T, one of the largest telecommunications companies in the world, leverages container technology
                            to deploy and manage OpenStack itself, relying on infrastructure containers to generate simplicity
                            and efficiency, with the aim of building their 5G infrastructure on containerized OpenStack.</p>
                        <p>To accomplish their goals, AT&#38;T is using OpenStack-Helm project to orchestrate LOCI-based
                            OpenStack images across a Kubernetes cluster, also leveraging Kubernetes, Docker, and the
                            core OpenStack services. They’re also using Bandit, Tempest, Patrole, and many other OpenStack
                            projects. AT&#38;T is also collaborating in the community to introduce a collection of undercloud
                            projects called "Ocean", which will provision clouds from bare-metal to production-grade
                            Kubernetes running OpenStack workloads.</p>
                        <p class="text-center">
                            <img class="medium-img" src="themes/openstack/images/containers2/att-diagram.jpg" alt="AT&#38;T">
                        </p>
                        <p>AT&#38;T is finding that containerization allows them to shift traditional deployment-type activities
                            far to the left, and to validate them using CI/CD. Kubernetes additionally provides massive
                            scalability and resiliency, as well as hooks to allow OpenStack-Helm to declaratively configure
                            operational behavior, inject configuration, and accomplish rolling upgrades and updates without
                            impacting tenant workloads.</p>
                        <p>Leveraging container technology to deploy and manage OpenStack shouldn’t have much obvious impact
                            on tenants — with the exception that they will have a more highly resilient platform, and
                            will be able to get cloud features more frequently and with minimal interruption. Our operations
                            teams' new experience will shift more of their efforts to defining the declarative configuration
                            for a site, and to let the Kubernetes-oriented automation carry out the deployments themselves.</p>
                        <p>AT&#38;T aims to use this architecture to power the virtual network functions that form the backbone
                            of its consumer and business-focused products and services. The initial use case for AT&#38;T's
                            containerized Network Cloud will be the initial deployment of VNFs for the emerging 5G networking.
                            OpenStack has been, is, and will be an excellent fit for AT&#38;T's VNF-focused cloud use
                            cases. Containerization is simply an evolution that allows us to deploy, manage, and scale
                            our OpenStack infrastructure in a more reliable, rapid, zero-touch manner.</p>
                        <p>Operationally, AT&#38;T is still testing this approach has committed to getting 5G service into
                            production before the end of the year. OpenStack and container technology will form the backbone
                            of this service, which is strategically important for AT&#38;T’s millions of users as well
                            as proving the relevance of OpenStack and containers in a massively distributed production
                            environment.
                        </p>
                    </div>
                    <div id="cern" class="tab-pane fade">
                        <h4 class="subtitle">Cern</h4>
                        <p>CERN, the European Organization for Nuclear Research, enables physicists and engineers to probe
                            the fundamental structure of the universe, using the world's largest and most complex scientific
                            instruments to study the basic constituents of matter – the fundamental particles. The CERN
                            cloud provides physicists with compute resources for scientific computing, analyzing data
                            coming from the Large Hadron Collider and other experiments.</p>
                        <p>CERN has been running OpenStack in production since 2013 and is now providing services for virtual
                            machines, bare-metal and containers within a single cloud. Containers run on either virtual
                            machines or bare-metal depending on the use cases, all provisioned via OpenStack Magnum.
                            A selection of different container technologies are available including Kubernetes, Docker
                            Swarm and DC/OS.</p>
                        <p>CERN is currently running 250 container clusters provisioned through Magnum on top of OpenStack.</p>
                        <p class="text-center">
                            <img class="medium-img" src="themes/openstack/images/containers2/cern-diagram.jpg" alt="Cern">
                        </p>
                        <p>CERN’s OpenStack cloud gives users self-service access to request a configured container engine
                            with a couple of commands or via a web GUI. This allows rapid utilization of the technologies
                            and can scale to 1000s of nodes if needed. Best practice configurations are available with
                            built in monitoring and integration into CERN storage and authentication services. </p>
                        <p>Running this resource pool efficiently, scaling it without needing extra operations manpower
                            requires consistent management processes and tools. Adding containers via Magnum on top of
                            OpenStack enabled the service to use the automation previously developed, such as hardware
                            repair processes and consistent authorisation models while supporting rapidly reallocation
                            of resources depending on user needs.</p>
                        <p>As a publicly funded laboratory, open source solutions such as Kubernetes and OpenStack provide
                            a framework to collaborate with other organisations and give back to the communities. CERN
                            has worked with a number of vendors through the
                            <a href="https://openlab.cern/"> CERN openlab framework </a>, such as Rackspace and Huawei, to provide clouds at scale with
                            functionalities like Magnum and federation. These experiences are also shared through OpenStack
                            Special Interest Groups, with other sciences such as the Square Kilometer Array (SKA), public
                            presentations such as Kubecon Europe and blogs such as the
                            <a href="http://openstack-in-production.blogspot.fr">
                                OpenStack in Production</a>.</p>
                        <p class="text-center">
                            <img class="medium-img" src="themes/openstack/images/containers2/cern-diagram2.jpg" alt="Cern">
                        </p>
                        <p>At CERN, several workloads run within containers provisioned by Magnum, these include:</p>
                        <ul>
                            <li>
                                <p>Reana/Recast
                                    <ul>
                                        <li>
                                <p>These tools provide a framework for executing reusable workflows in
                                    <a href="https://github.com/recast-hep"> High Energy Physics</a>. It schedules and runs analyses on the CERN cloud
                                    based on Kubernetes and Yadage Workflows supporting analysis and data
                                    preservation activities.</p>
                            </li>
                        </ul>
                        </p>
                        </li>
                        <li>
                            <p>Spark as a Service
                                <ul>
                                    <li>
                            <p>Recently, Kubernetes was added as a resource manager for Spark. Spark can
                                spawn drivers and executors as pods and Kubernetes is responsible for
                                the scheduling and lifecycle. A team in the CERN IT department is developing
                                a service where users can create Kubernetes clusters on demand with OpenStack
                                Magnum and deploy Spark on Kubernetes, providing all the required integrations
                                with CERN’s specialized filesystems and data sources in a secure way.
                                Users with a few commands can effectively create a Spark deployment with
                                the desired size, only for the time they need it and with the option
                                to scale up or down their deployment while running.</p>
                        </li>
                        </ul>
                        </p>
                        </li>
                        <li>
                            <p>ATLAS detector trigger simulator for LHC upgrade
                                <ul>
                                    <li>
                            <p>The LHC is due to be upgraded to higher luminosity during the 2020s which
                                requires significant enhancements in the trigger farms which filter the
                                collisions. Large scale Kubernetes clusters have been created to simulate
                                the different approaches and validate the design.
                            </p>
                        </li>
                        </ul>
                        </p>
                        </li>
                        <li>
                            <p>Gitlab Continuous Integration Runners
                                <ul>
                                    <li>
                            <p>Gitlab enables user to build CI/CD jobs and execute them on gitlab runners.
                                CERN users can leverage the CERN Containers service to build kubernetes
                                or docker swarm clusters to test and build software, for example, build
                                and publish container images and documentation or build full pipelines
                                with different stages to build, tests, deploy and review applications.
                            </p>
                        </li>
                        </ul>
                        </p>
                        <p>Federated Kubernetes compute farms with external clouds
                            <ul>
                                <li>
                        <p>CERN uses federations of Kubernetes clusters to support multi-cloud operations.
                            Multiple clusters can be seamlessly integrated across clouds of varying
                            technologies, including AWS, GCE and OpenStack clouds such as CERN and
                            the T-Systems Open Telekom Cloud
                            <a href="https://www.youtube.com/watch?v=2PRGUOxL36M">as demonstrated at Kubecon 2018</a>.

                        </p>
                        </li>
                        </ul>
                        </p>
                        </li>
                        </ul>

                        <p>CERN sees several advantages to utilizing containers within OpenStack. Integrating virtual machines,
                            container engines and bare-metal under a single framework provides for easy views on usage
                            accounting, ownership and quota. Manila Storage drivers for Kubernetes allows transparent
                            provisioning of file shares. This supports both the IT department in capacity planning and
                            the experiment resource coordinators in defining the priorities for their working groups.
                            Resource management policies such as reassignment or expiry of resources on departure of
                            staff are handled in consistent workflows.</p>
                    </div>
                </div>
                <div class="studies">
                    <a class="btn btn-primary" href="#" id="case-studies-btn" title="Go to top">
                        <span> Select Another Case Study </span>
                        <i class="fa fa-caret-up"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row section" id="conclusion">
            <div class="col-lg-12">


                <h3 class="text-center title">IV. Conclusion</h3>
                <p>Over the past few years, as containers have become an important tool for developers and organizations
                    alike, OpenStack has leveraged its modular design and expansive community to integrate container
                    technologies at many levels. This can be seen both by the various organizations bringing containers
                    and OpenStack into production, and the number of projects that work alongside containers to deliver
                    new capabilities. The OpenStack Foundation is committed to ensuring that emerging technologies can
                    be incorporated and utilized within OpenStack, and containers are an important example of that commitment.</p>
                <p>To learn more, visit the
                    <a href="https://www.openstack.org/containers/"> Containers Landing Page </a>, where you can find a copy of this document as well as links to dozens
                    of videos focused on the integrations of OpenStack and containers.
                    <a href="https://github.com/kubernetes/community/tree/master/sig-openstack">
                        Kubernetes SIG-OpenStack</a> has a Slack channel, mailing list, and weekly meeting if you engage
                    directly with the community that’s building Kubernetes and OpenStack integrations.</p>
            </div>

        </div>

        <div class="row section" id="proyect-index">
            <div class="col-lg-12">
                <h3 class="text-center title">V. Open Source Project Index</h3>

                <div id="accordion" class="panel-group">
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#ansible" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion">Ansible</a>
                            </h4>
                        </div>
                        <div id="ansible" class="panel-collapse collapse in">
                            <div class="panel-body">
                                <p>Ansible is a commonly used orchestration tool used to deploy and manage OpenStack installations.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#cinder" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Cinder</a>
                            </h4>
                        </div>
                        <div id="cinder" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>OpenStack Cinder offers block storage as a service, providing a single API backed by
                                    over seventy different possible storage drivers.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#cloud-provider-openstack" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Cloud Provider OpenStack</a>
                            </h4>
                        </div>
                        <div id="cloud-provider-openstack" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Cloud Provider OpenStack is the implementation of the Kubernetes Cloud Provider interface.
                                    It allows an OpenStack-hosted Kubernetes cluster to directly access storage and load
                                    balancer resources in the OpenStack cloud.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#calico" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Calico</a>
                            </h4>
                        </div>
                        <div id="calico" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Calico is a network overlay with drivers for both Kubernetes and OpenStack that features
                                    L3-only routing.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#cyborg" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Cyborg</a>
                            </h4>
                        </div>
                        <div id="cyborg" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Cyborg is an OpenStack project that provides a general management framework for hardware
                                    accelerators including FPGA, GPU, ASIC, and others. Work is in progress to surface
                                    a general hardware interface to pods.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#docker" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Docker</a>
                            </h4>
                        </div>
                        <div id="docker" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Docker is an open source container virtualization framework, used to host containerized
                                    applications.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#helm" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Helm</a>
                            </h4>
                        </div>
                        <div id="helm" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Helm is the official package manager for Kubernetes. Application deployments are described
                                    by Helm-Charts, which can be automatically deployed and managed on a Kubernetes cluster.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#ironic" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Ironic</a>
                            </h4>
                        </div>
                        <div id="ironic" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Ironic is the OpenStack bare-metal service. Running either as a standalone service or
                                    as a driver to OpenStack Nova, it can manage the complete life-cycle of bare-metal
                                    systems, including enrollment, provisioning, maintenance, and decommissioning.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#loci" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Loci</a>
                            </h4>
                        </div>
                        <div id="loci" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Loci is an OpenStack project to build lightweight, OCI compliant containers for OpenStack
                                    projects.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#lxc" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">LXC</a>
                            </h4>
                        </div>
                        <div id="lxc" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>LXC is a low-level container virtualization interface that takes advantage of Linux kernel
                                    namespace isolation and other technologies to create isolated linux runtimes.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#kata-containers" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Kata Containers</a>
                            </h4>
                        </div>
                        <div id="kata-containers" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Kata Containers is an open source project and community, hosted by the OpenStack Foundation,
                                    that is working to build a standard implementation of lightweight Virtual Machines
                                    (VMs) that feel and perform like containers, but provide the workload isolation and
                                    security advantages of VMs.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#keystone" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Keystone</a>
                            </h4>
                        </div>
                        <div id="keystone" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Keystone is the OpenStack Identity service that provides means for authenticating and
                                    managing user accounts and role information primarily for the OpenStack cloud environment,
                                    but also as a plugin to other environments, including Kubernetes.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#kolla-containers" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Kolla (Containers)</a>
                            </h4>
                        </div>
                        <div id="kolla-containers" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Kolla (Containers) is an OpenStack project to build containers for each OpenStack service.
                                    It includes a sophisticated build and templating systems, and is capable of building
                                    containers from both source and packages on a variety of host operating systems.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#kolla-ansible" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Kolla Ansible</a>
                            </h4>
                        </div>
                        <div id="kolla-ansible" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Kolla Ansible is an OpenStack project that uses Ansible to deploy and maintain a full
                                    OpenStack installation using Kolla containers.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#kubernetes" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Kubernetes</a>
                            </h4>
                        </div>
                        <div id="kubernetes" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Kubernetes is a container orchestration system that delivers robust and highly-available
                                    applications on top of cloud-infrastructure.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#kuryr" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Kuryr</a>
                            </h4>
                        </div>
                        <div id="kuryr" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Kuryr is an OpenStack project that provides a Neutron network overlay to container runtimes,
                                    including Docker and Kubernetes. It aims to be the “integration bridge” for container
                                    and VM networks.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#magnum" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Magnum</a>
                            </h4>
                        </div>
                        <div id="magnum" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Magnum is an OpenStack project that offers managed container platforms as a service,
                                    including Kubernetes, Docker Swarm, Mesos, and DC/OS platforms. It is capable of
                                    creating tenant isolated application platforms through a simple user-facing API.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#neutron" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Neutron</a>
                            </h4>
                        </div>
                        <div id="neutron" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Neutron is the OpenStack software-defined networking service, offering a single API to
                                    deliver dynamic network infrastructure backed by dozens of network drivers.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#openstack-ansible" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">OpenStack Ansible</a>
                            </h4>
                        </div>
                        <div id="openstack-ansible" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>OpenStack Ansible is a project for building OpenStack services into LXC containers, and
                                    for deploying and managing OpenStack installations within those containerized services.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#openstack-helm" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">OpenStack Helm</a>
                            </h4>
                        </div>
                        <div id="openstack-helm" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>OpenStack Helm is an OpenStack project that deploys and manages the lifecycle of OpenStack
                                    and supporting infrastructure on top of Kubernetes (eg Ceph and MariaDB) , delivering
                                    production ready deployments, for a range of use cases from small edge deployments
                                    to large central offices. Leveraging the Helm package management system. OpenStack
                                    Helm has support for both baremetal (Ironic) and virtual (Nova/KVM) workload management,
                                    and is image agnostic supporting both LOCI and Kolla containers.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#qinling" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Qinling</a>
                            </h4>
                        </div>
                        <div id="qinling" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Qinling is an OpenStack project to deliver Functions as a Service. Qinling supports different
                                    container orchestration platforms, such as Kubernetes and Docker Swarm, as well as
                                    different function package storage backends such as local file-store, OpenStack Swift,
                                    and S3.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#triple-o" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Triple-O</a>
                            </h4>
                        </div>
                        <div id="triple-o" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>TripleO is a project aimed at installing, upgrading and operating OpenStack clouds using
                                    OpenStack’s cloud services as the foundation - building on Nova, Ironic, Neutron,
                                    Heat and Ansible to automate cloud management.</p>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a href="#zun" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion">Zun</a>
                            </h4>
                        </div>
                        <div id="zun" class="panel-collapse collapse">
                            <div class="panel-body">
                                <p>Zun is the OpenStack Containers service. It aims to provide an API service for running
                                    application containers without the need to manage servers or clusters.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row section" id="authors">
            <div class="col-lg-12">
                <h3 class="text-center title">VI. Authors</h3>
                <h4 class="subtitle">Members of the OpenStack SIG-Kubernetes Community</h4>
                <p>
                    <small class="italic">(alphabetical by surname)</small>
                </p>
                <ul>
                    <li>
                        <p>Jaesuk Ahn, SK Telecom</p>
                    </li>
                    <li>
                        <p>Christian Berendt, Betacloud Solutions GmbH</p>
                    </li>
                    <li>
                        <p>Anne Bertucio, OpenStack Foundation</p>
                    </li>
                    <li>
                        <p>Pete Birley, AT&#38;T</p>
                    </li>
                    <li>
                        <p>Chris Hoge, OpenStack Foundation</p>
                    </li>
                    <li>
                        <p>Lingxian Kong, Catalyst Cloud</p>
                    </li>
                    <li>
                        <p>Hongbin Lu, Huawei</p>
                    </li>
                    <li>
                        <p>Daniel Mellado, Red Hat, Inc.</p>
                    </li>
                    <li>
                        <p>Allison Price, OpenStack Foundation</p>
                    </li>
                    <li>
                        <p>David Rabel, B1 Systems GmbH</p>
                    </li>
                    <li>
                        <p>Sangho Shin, SK Telecom</p>
                    </li>
                    <li>
                        <p>Davanum Srinivas, Huawei</p>
                    </li>
                    <li>
                        <p>Luis Tomás, Red Hat, Inc.</p>
                    </li>
                </ul>

                <h4 class="subtitle">Editor</h4>
                <ul>
                    <li>
                        <p>Brian E Whitaker, Zettabyte Content LLC</p>
                    </li>
                </ul>


            </div>
        </div>
    </div>
    <div class="scroll-top">
        <button class="btn btn-default" id="btn-top" onclick="topFunction()">
            <i class="fa fa-angle-up"></i>
            <span> Top</span>
        </button>
    </div>
</section>

<div id="lightbox" class="modal">
    <span class="close cursor" onclick="closeModal()">&times;</span>
    <div class="modal-content">
        <div class="mySlides">
            <img src="" style="width:100%">
        </div>
    </div>
</div>