<div class="container">
	<% if Menu(2) %>
		<div class="row">
			<div class="col-lg-9 col-lg-push-3">
	<% end_if %>

	<h1>OpenStack Interoperability</h1>
    <p>
        OpenStack began with the mission to produce a ubiquitous open source cloud computing platform. A key component
        of that mission is building not only software, but a large OpenStack ecosystem that supports its growth and adds
        value to the core technology platform. In carrying out that mission, the OpenStack Foundation has created, with
        the community, requirements to ensure that the various products and services bearing the OpenStack marks achieve
        a high level of interoperability.     
    </p>
    <p>
        The goal is to help users make informed decisions and adopt the OpenStack products that best meet their business
        needs. They should be able to easily identify products that meet interoperability requirements via the OpenStack
        logos, as well as evaluate product capabilities in the OpenStack Marketplace by viewing test results and other
        technical product details.
    </p>

    <h2>Overview of OpenStack Logos</h2>
    <p>
        The OpenStack Foundation offers two different logos for vendors. 
        "<a href="http://www.openstack.org/brand/openstack-powered/">OpenStack Powered</a>" is for products that run fully
        functional instances of the OpenStack software. Details on the technical and testing requirements to qualify for
        the "Powered" program are <a href="http://www.openstack.org/brand/interop#openstackpowered">here</a>.
    </p>
    <p>
	   "<a href="http://www.openstack.org/brand/openstack-compatible/">OpenStack Compatible</a>" is for software solutions
       that interact with "OpenStack Powered" systems, and hardware solutions that are designed to run the OpenStack
       software. In June 2015, the OpenStack Board approved the development of testing programs for "OpenStack Compatible"
       products. The OpenStack Foundation is rolling out these testing programs with new requirements for storage drivers
       starting in November 2015. Network driver testing and application testing requirements are scheduled for 2016.
       Details on the technical requirements to qualify for the the storage driver "Compatible" logo are
       <a href="http://www.openstack.org/brand/interop#openstackcompatible">here</a>.
    </p>
	<h3 id="openstackpowered">OpenStack Powered</h3>
    <p>
        There are three different trademark licensing programs which apply to products that contain the OpenStack software,
        all under unified "OpenStack Powered" logo. Though the programs share a single logo, each of the licensing
        programs have a unique list of technical requirements appropriate to their use case, which include required capabilities
        validated by must-pass tests and designated sections of OpenStack software code.
    </p>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 communityBoxes">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <h2>Program Name</h2>
            </div>
            <div class="events col-lg-3 col-md-3 col-sm-3">
                <h2>Required Code</h2>
            </div>
            <div class="partners col-lg-3 col-md-3 col-sm-3">
                <h2>Trademark Use (must be approved by Foundation)</h2>
            </div>
            <div class="members col-lg-3 col-md-3 col-sm-3 last">
                <h2>Product Examples</h2>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 communityBoxes">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <h2>OpenStack Powered Platform</h2>
            </div>
            <div class="events col-lg-3 col-md-3 col-sm-3">
                <p>Must include all designated sections and pass all capabilities tests</p>
            </div>
            <div class="partners col-lg-3 col-md-3 col-sm-3">
                <p>Qualifying products may use the OpenStack Powered logo and use the word "OpenStack" in their product name</p>
            </div>
            <div class="members col-lg-3 col-md-3 col-sm-3 last">
                <p>Public cloud or distribution</p>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 communityBoxes">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <h2>OpenStack Powered Compute</h2>
            </div>
            <div class="events col-lg-3 col-md-3 col-sm-3">
                <p>Must include all compute-specific code and pass all compute-specific capabilities tests</p>
            </div>
            <div class="partners col-lg-3 col-md-3 col-sm-3">
                <p>Qualifying products may use the OpenStack Powered logo and use the phrase "OpenStack Powered Compute" in their product name</p>
            </div>
            <div class="members col-lg-3 col-md-3 col-sm-3 last">
                <p>Compute cloud or appliance</p>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 communityBoxes">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <h2>OpenStack Powered Object Storage</h2>
            </div>
            <div class="events col-lg-3 col-md-3 col-sm-3">
                <p>Must include all object storage-specific code and pass all object storage-specific capabilities tests</p>
            </div>
            <div class="partners col-lg-3 col-md-3 col-sm-3">
                <p>Qualifying products may use the OpenStack Powered logo and use the phrase "OpenStack Powered Storage" in their product name</p>
            </div>
            <div class="members col-lg-3 col-md-3 col-sm-3 last">
                <p>Object storage cloud or distribution</p>
            </div>
        </div>
    </div>

    <h4>Qualifying for the OpenStack Powered Marketing Programs</h4>
    <p>
        OpenStack-based products containing a recent version of the software may qualify for one of the three OpenStack
        Powered marketing programs, which consist of a logo and unique product naming rights.
    </p>
    <p>
        Products must comply with one of the two most recent guidelines approved by the OpenStack Foundation Board of Directors.
        These versions are numbered based on the date when they were approved, such as "2016.08"for the version approved in August, 2016.
    </p>
    <p>
        The two most recent versions approved by the board are
        "<a title="2016.01 DefCore Capabilities" href="http://git.openstack.org/cgit/openstack/defcore/tree/2016.01.json" target="_blank">2016.01</a>"
        and "<a title="Defcore 2016.08 Guideline" href="http://git.openstack.org/cgit/openstack/defcore/tree/2016.08.json">2016.08</a>". These
        two guidelines cover four OpenStack releases: Icehouse, Juno, Kilo, and Liberty.
        The list of required capabilities (with must-pass tests) and designated code sections are published on
        <a title="OpenStack DefCore Repository" href="http://git.openstack.org/cgit/openstack/defcore/tree/" target="_blank">git.openstack.org</a>&nbsp;
        and summarized below. Once a company verifies their products include the appropriate designated sections and submit API test results, they will
        be asked to sign the license agreements.
    </p>
    <p>
        You'll note that the "Platform" program technical requirements are essentially the combination of "Compute" and "Object Storage" requirements.
    </p>

    <h4>
        Version
        <select id="interop_version">
            <% loop getInteropProgramVersions() %>
            <option value="$ID">$Name</option>
            <% end_loop %>
        </select>
    </h4>

    <!-- $getCapabilitiesTable() -->

    <h4>How to Run the Tests</h4>
    <p>
        OpenStack interoperability tests are part of the Tempest project suite of tests. To run the tests for your license
        application, you will need
        to install <a href="https://git.openstack.org/cgit/openstack/tempest" target="_blank">Tempest</a> with the
        <a href="https://git.openstack.org/cgit/openstack/refstack-client" target="_blank">RefStack Client</a>.
        You will need to run Tempest inside of the RefStack Client and upload the results to the RefStack server.
        We prefer that you run the complete set of non-admin API tests, however, Tempest can be run with a precompiled
        inventory of tests available from the <a href="https://refstack.openstack.org/#/guidelines">RefStack server guidelines
        page</a>.  Once your results are uploaded, you can send a link to the report page to
        <a href="mailto:interop@openstack.org">interop@openstack.org</a>&nbsp.
    </p>
    <p>
        For more detailed instructions to run the tests, please consult
        <a title="Procedure for Running Defcore Interop Tests" href="http://git.openstack.org/cgit/openstack/defcore/tree/2016.01/procedure.rst">this document</a>.
        If you need help getting started, contact Chris Hoge, the Foundation's Interop Engineer, by emailing
        <a href="mailto:interop@openstack.org">interop@openstack.org</a>. The Foundation is looking for feedback from companies
        who are running tests in order to improve the testing process and shape policy in the future.
    </p>
    <p>
        To apply for one of the OpenStack Powered marketing programs, please review the requirements at the
        <a href="brand/openstack-powered">"OpenStack Powered" brand page</a> and submit your logo application test
        results via the online form</a>.
    </p>

    <h3 id="openstackcompatible">OpenStack Compatible</h3>
    <p>
    	There are two types of "OpenStack Compatible" products that fall under the unified logo program. The first is software
        applications that interact with "OpenStack Powered" systems. The second is hardware solutions and drivers that run
        OpenStack software. Starting November 1 2015, The OpenStack Foundation required that new "OpenStack Compatible" logos
        for storage drivers pass community-defined third-party integration tests. The Cinder team has a
        <a href="https://wiki.openstack.org/wiki/Cinder/tested-3rdParty-drivers">detailed overview of the requirements</a> for
        third-party testing. 
    </p>
    <p>
        Proof of successful third-party testing will be required at the time of licensing, and may include links to Gerrit runs
        and the Tempest logs of those runs. You can begin the application process by filling out the form at the
        <a href="http://www.openstack.org/brand/openstack-compatible">"OpenStack Compatible" brand page</a>.
    </p>

	<% if Menu(2) %>
			</div> <!-- Close content div -->
			<div class="col-lg-3 col-lg-pull-9">
				<% include SubMenu %>
			</div>
		</div> <!-- Close row div -->
	<% end_if %>
</div>
