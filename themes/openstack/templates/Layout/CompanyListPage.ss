		<div class="row">
			<div class="col-sm-10 col-sm-push-1">
				<h1>Companies Supporting The OpenStack Foundation</h1>
				<p class="center">The OpenStack Foundation would not exist without the support of the Platinum, Gold, and Corporate Sponsors listed below.<br>Learn more about <a href="/join/#sponsor">how your company can help</a>.</p>
			</div>
		</div>
		

		<!-- Platinum Members -->
		<div class="row">
			<div class="col-sm-12">
				<hr/>
				<h2>Platinum Members</h2>
				<p>
				OpenStack Foundation Platinum Members provide a significant portion of the funding to achieve the Foundation's mission of protecting, empowering and promoting the OpenStack community and software. Each Platinum Member's company strategy aligns with the OpenStack mission and is responsible for committing full-time resources toward the project.  There are eight Platinum Members at any given time, each of which holds a seat on the Board of Directors. Thank you to the following Platinum Members who are committed to OpenStack's success.
				</p>
			</div>
		</div>

		<div class="row logos">
			<% loop DisplayedCompanies(Platinum) %>
				<div class="col-sm-2 col-xs-6">
		                <a <% if IsExternalUrl %>rel="nofollow"<% end_if %> href="$ShowLink">
		                	<div class="img-wrapper">
								<% loop Logo %>
									<img src="{$SetWidth(138).URL}" alt="$Name">
									<!-- <span style="background-image: url({$SetWidth(138).URL});"></span> -->
								<% end_loop %>
							</div>
							<p class="center">$Name</p>
						</a>
				</div>
			<% end_loop %>

		</div>
		<!-- Gold Members -->
		<% if DisplayedCompanies(Gold) %>
			<div class="row">
				<div class="col-sm-12">
					<hr/>
					<h2>Gold Members</h2>
					<p>
					OpenStack Foundation Gold Members provide funding and pledge strategic alignment to the OpenStack mission. There can be up to twenty-four Gold Members at any given time, subject to board approval. If your organization is highly involved with OpenStack and interested in becoming a Gold Member, read more about <a href="/join">joining the Foundation</a>. Thank you to the following Gold Members who are committed to OpenStack's success.
					</p>
				</div>
			</div>

			<div class="row logos">
				<% loop DisplayedCompanies(Gold) %>
					<div class="col-sm-2 col-xs-6">
		                <a <% if IsExternalUrl %>rel="nofollow"<% end_if %> href="$ShowLink">
		                	<div class="img-wrapper">
								<% loop Logo %>
									<img src="{$SetWidth(138).URL}" alt="$Name">
								<% end_loop %>
							</div>
							<p class="center">$Name</p>
						</a>
						</div>
					<% end_loop %>
			</div>
		<% end_if %>

		<!-- Infrastructure Donors -->
		<div class="row">
			<div class="col-sm-12">
				<hr/>
				<a name="infra-donors"></a><h2>Infrastructure Donors</h2>
				<p>
					Infrastructure donors are companies running OpenStack clouds, donating cloud resources to the OpenStack project infrastructure. Those resources are mostly used in our <a href="http://docs.openstack.org/infra/system-config/contribute-cloud.html" target="_blank">automated testing</a> framework to support OpenStack development efforts.
				</p>
			</div>
		</div>

		<div class="row logos">
			<div class="col-sm-2 col-xs-6">
				<a rel="nofollow" href="http://www.rackspace.com/">
					<div class="img-wrapper">
						<img src="/assets/Uploads/_resampled/SetWidth138-rackspace-sm.png" alt="Rackspace">
					</div>
					<p class="center">Rackspace</p>
				</a>
			</div>
			<div class="col-sm-2 col-xs-6">
				<a href="/foundation/companies/profile/ovh-group">
					<div class="img-wrapper">
						<img src="/assets/Uploads/_resampled/SetWidth138-Group-NORMAL-15-cm-copy.png" alt="OVH Group">
					</div>
					<p class="center">OVH Group</p>
				</a>
			</div>
			<div class="col-sm-2 col-xs-6">
					<div class="img-wrapper">
					</div>
					<p class="center">OpenStack Innovation Center</p>
			</div>
		</div>

		<!-- Corporate & Startup Members -->
		<% if DisplayedCompanies(Combined) %>
			<div class="row">
				<div class="col-sm-12">
					<hr/>
					<h2>Corporate Sponsors</h2>
					<p>
					Corporate Sponsors provide additional funding to support the Foundation's mission of protecting, empowering and promoting OpenStack. If you are interested in becoming a corporate sponsor, read more about <a href="/join">supporting the Foundation</a>. Thank you to the following corporate sponsors for supporting the OpenStack Foundation.
					</p>
				</div>
			</div>

			<div class="row logos">
				<% loop DisplayedCompanies(Combined) %>
					<div class="col-sm-2 col-xs-6">
	                <a <% if IsExternalUrl %>rel="nofollow"<% end_if %> href="$ShowLink">
	                	<div class="img-wrapper">
							<% loop Logo %>
								<img src="{$SetWidth(138).URL}" alt="$Name">
							<% end_loop %>
						</div>
						<p class="center">$Name</p>
					</a>
					</div>
				<% end_loop %>
			</div>
		<% end_if %>


		<!-- Mention Members -->
		<% if DisplayedCompanies(Mention) %>
			<div class="row">
				<div class="col-sm-12">
					<hr/>
					<h2>Supporting Organizations</h2>
					<p>
					The resources provided provided by the Members and Sponsors are critical to making the OpenStack Foundation successful, but there are many ways to support the OpenStack mission, whether you're contributing code, building an OpenStack product or helping build the community. Below are companies who are actively involved in making OpenStack successful. If you would like your company listed here, please complete the <a href="https://openstack.echosign.com/public/hostedForm?formid=4TBJIEXJ4M7X2Q" target="_new">logo authorization form</a> and <a href="mailto:supporterlogos@openstack.org">send your logo</a>.
					</p>
				</div>
			</div>

			<div class="row small-logos">

				<% loop DisplayedCompanies(Mention) %>
					<div class="col-sm-2 col-xs-4">
	            		<a <% if IsExternalUrl %>rel="nofollow"<% end_if %> href="$ShowLink">
	            			<div class="img-wrapper">
								<% loop Logo %>
									<img src="{$SetWidth(70).URL}" alt="$Name">
								<% end_loop %>
							</div>
						</a>
					</div>
				<% end_loop %>
			</div>
		<% end_if %>
