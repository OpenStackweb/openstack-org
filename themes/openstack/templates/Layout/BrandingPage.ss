<div class="container">
	<% if Menu(2) %>
		<div class="row">
			<div class="col-lg-9 col-lg-push-3">
	<% end_if %>

	<h1>OpenStack Interoperability</h1>
    <p>
        OpenStack began with the mission to produce a ubiquitous open source cloud computing platform. A key component
        of that mission is building not only software, but a large OpenStack ecosystem that support its growth and adds
        value to the core technology platform. In carrying out that mission, the OpenStack Foundation has created a set
        of requirements to ensure that the various products and services bearing the OpenStack marks achieve a high level
        of interoperability. They consist of must-pass tests for required capabilities and designated code.
    </p>
    <p>
        The goal is to help users make informed decisions and adopt the OpenStack products that best meet their business needs.
        They should be able to easily identify products that meet interoperability requirements via the OpenStack Powered logo,
        as well as evaluate product capabilities in the OpenStack Marketplace by viewing the test results.
    </p>
    <h3>Overview of &ldquo;OpenStack Powered&rdquo; Marketing Programs</h3>
    <p>
        There are three different trademark licensing programs which apply to products that contain the OpenStack software,
        all under a unified logo called "OpenStack Powered." Though the programs share a single logo, each of the licensing
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
    <h3>Qualifying for the OpenStack Powered Marketing Programs</h3>
    <p>
        OpenStack-based products containing a recent version of the software may qualify for one of the three OpenStack Powered
        marketing programs, which consist of a logo and unique product naming rights.
    </p>
    <p>
        Products must comply with one of the two most recent versions of requirements approved by the OpenStack Foundation
        Board of Directors. These versions are numbered based on the date when they were approved, such as &ldquo;2015.05&rdquo;
        for the version approved in May, 2015.
    </p>
    <p>
        The two most recent versions approved by the board are
        <a title="DefCore 2015.04 Capabilities" href="http://git.openstack.org/cgit/openstack/defcore/tree/2015.04.json" target="_blank">2015.04</a>
         and <a title="2015.05 DefCore Capabilities" href="http://git.openstack.org/cgit/openstack/defcore/tree/2015.05.json" target="_blank">2015.05</a>.
         The list of required capabilities (with must-pass tests) and designated code sections are published on
         <a title="OpenStack DefCore Repository" href="http://git.openstack.org/cgit/openstack/defcore/tree/" target="_blank">git.openstack.org</a>
         &nbsp;and summarized below. Once a company verifies their products include the appropriate designated sections and submit API test results,
         they will be asked to sign the license agreements.
    </p>
    <p>
        You&rsquo;ll note that the &ldquo;Platform&rdquo; program technical requirements are essentially the combination of
         &ldquo;Compute&rdquo; and &ldquo;Object Storage&rdquo; requirements.
    </p>

    <h3>
        Version
        <select id="interop_version">
            <% loop getVersions() %>
            <option value="$ID">$Name</option>
            <% end_loop %>
        </select>
    </h3>

    <% loop getVersions() %>
        <div style="display:<% if First %>block<% else %>none<% end_if %>;" id="version_$ID" class="version_wrapper">
            <table style="width: 80%;" border="0" cellspacing="10" cellpadding="10">
                <tbody>
                    <tr>
                        <td width="50%">
                            <h3 style="margin-bottom: 5px;">Required Capabilities</h3>
                        </td>
                        <td colspan="3" width="30%">
                            <h3 style="margin-bottom: 5px;">Licensing Program</h3>
                        </td>
                    </tr>
                    <tr style="border: solid 1px #ccc;">
                        <td width="50%"><strong>&nbsp;</strong></td>
                        <td width="10%"><strong>Platform</strong></td>
                        <td width="10%"><strong>Compute</strong></td>
                        <td width="10%"><strong>Object Storage</strong></td>
                    </tr>
                    <tr style="border: solid 1px #ccc;">
                        <td width="50%"><strong>Compute Capabilities</strong></td>
                        <td width="10%">&nbsp;</td>
                        <td width="10%">&nbsp;</td>
                        <td width="10%">&nbsp;</td>
                    </tr>
                    <% loop getCapabilitiesByType('Compute Capabilities') %>
                        <tr style="border: solid 1px #ccc;">
                            <td>$Name</td>
                            <td style="text-align: center;">✓</td>
                            <% if Status == 'Required' %>
                                <td style="text-align: center;">✓</td>
                            <% else %>
                                <td style="text-align: center;">$Status</td>
                            <% end_if %>
                            <td>&nbsp;</td>
                        </tr>
                    <% end_loop %>
                    <tr style="border: solid 1px #ccc;">
                        <td><strong>Object Storage Capabilities</strong></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <% loop getCapabilitiesByType('Object Storage Capabilities') %>
                        <tr style="border: solid 1px #ccc;">
                            <td>$Name</td>
                            <td style="text-align: center;">✓</td>
                            <td>&nbsp;</td>
                            <% if Status == 'Required' %>
                                <td style="text-align: center;">✓</td>
                            <% else %>
                                <td style="text-align: center;">$Status</td>
                            <% end_if %>
                        </tr>
                    <% end_loop %>
                    <% if getCapabilitiesByType('Future Required Capabilities') %>
                        <tr style="border: solid 1px #ccc;">
                            <td><strong>Future Required Capabilities</strong></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <% loop getCapabilitiesByType('Future Required Capabilities') %>
                            <tr style="border: solid 1px #ccc;">
                                <td>$Name</td>
                                <td style="text-align: center;">Advisory</td>
                                <td style="text-align: center;">Advisory</td>
                                <td>&nbsp;</td>
                            </tr>
                        <% end_loop %>
                    <% end_if %>
                </tbody>
            </table>
            <p>&nbsp;</p>
            <table style="width: 80%;" border="0" cellspacing="10" cellpadding="10">
                <tbody>
                    <tr>
                        <td width="50%">
                            <h3 style="margin-bottom: 5px;">Designated Sections</h3>
                        </td>
                        <td width="10%">&nbsp;</td>
                        <td width="10%">&nbsp;</td>
                        <td width="10%">&nbsp;</td>
                    </tr>
                    <tr style="border: solid 1px #ccc;">
                        <td>&nbsp;</td>
                        <td><strong>Platform</strong></td>
                        <td><strong>Compute</strong></td>
                        <td><strong>Object Storage</strong></td>
                    </tr>
                    <% loop getDesignatedSectionsByProgramType('OpenStack Powered Compute') %>
                        <tr style="border: solid 1px #ccc;">
                            <td>$Guidance</td>
                            <td style="text-align: center;">✓</td>
                            <% if Status == 'Required' %>
                                <td style="text-align: center;">✓</td>
                            <% else %>
                                <td style="text-align: center;">$Status</td>
                            <% end_if %>
                            <td>&nbsp;</td>
                        </tr>
                    <% end_loop %>
                    <% loop getDesignatedSectionsByProgramType('OpenStack Powered Object Storage') %>
                        <tr style="border: solid 1px #ccc;">
                            <td>$Guidance</td>
                            <td style="text-align: center;">✓</td>
                            <td>&nbsp;</td>
                            <% if Status == 'Required' %>
                                <td style="text-align: center;">✓</td>
                            <% else %>
                                <td style="text-align: center;">$Status</td>
                            <% end_if %>
                        </tr>
                    <% end_loop %>
                </tbody>
            </table>
        </div>
    <% end_loop %>
    <p>
        To apply for one of the OpenStack Powered marketing programs today, please review the requirements and
        <a href="brand/openstack-powered">submit via the online form</a>.
    </p>
    <h3>How to Run the Tests</h3>
    <p>
        OpenStack interoperability tests are part of the Tempest project suite of tests. To run the tests, you will need
        to <a href="https://git.openstack.org/cgit/openstack/tempest" target="_blank">install Tempest manually</a> or with
        some wrapper tool such as the <a href="https://git.openstack.org/cgit/stackforge/refstack-client" target="_blank">RefStack Client</a>.
        After configuring for your particular product, Tempest can be run with a precompiled inventory of tests available from the Defcore
        repository. You can use <a href="https://raw.githubusercontent.com/openstack/defcore/tree/2015.04/2015.04.required.txt">this file</a>
        to configure Tempest test runner to execute only the required tests. We prefer that you run the RefStack Client to produce a JSON file
        of all tests that have passed. You can email the full json test results file to&nbsp;
        <a href="mailto:interop@openstack.org">interop@openstack.org</a>&nbsp;or upload the results to the RefStack server and mail
        the returned test identification link.
    </p>
    <p>
        For more detailed instructions to run the tests, please consult
        <a title="Procedure for Running Defcore Interop Tests" href="https://git.openstack.org/cgit/openstack/defcore/tree/2015.05/procedure.rst">this document</a>.
        If you need help getting started, contact Chris Hoge, the Foundation's Interop Engineer, by emailing
        <a href="mailto:interop@openstack.org">interop@openstack.org</a>. The Foundation is looking for feedback from companies
        who are running tests in order to improve the testing process and shape policy in the future.
    </p>

	<% if Menu(2) %>
			</div> <!-- Close content div -->
			<div class="col-lg-3 col-lg-pull-9">
				<% include SubMenu %>
			</div>
		</div> <!-- Close row div -->
	<% end_if %>
</div>