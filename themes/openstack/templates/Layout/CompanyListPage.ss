		<div class="row">
			<div class="col-sm-10 col-sm-push-1">
				<h1>Organizations Supporting The OpenInfra Foundation</h1>
				<p class="center">The Open Infrastructure Foundation would not exist without the support of the Platinum, Gold, and Silver Sponsors listed below.<br>Learn more about <a href="https://openinfra.dev/join/members">how your company can help</a>.</p>
			</div>
		</div>
		

		<!-- Platinum Members -->
		<div class="row">
			<div class="col-sm-12">
				<hr/>
				<h2>Platinum Members</h2>
				<p>
				Open Infrastructure Foundation Platinum Members provide a significant portion of the funding to achieve the Foundation's mission of protecting, empowering and promoting the Open Infrastructure community and open source software projects. Each Platinum Member's company strategy aligns with the OpenInfra Foundation mission and is responsible for committing full-time resources toward the project. Thank you to the following Platinum Members who are committed to OpenStack's success. <a href="https://openinfra.dev/join/members">Learn more about becoming a member</a>.
				</p>
			</div>
		</div>

		<div class="row logos">
			<% loop DisplayedCompanies(Platinum) %>
				<div class="col-sm-3 col-xs-6">
		                <a <% if IsExternalUrl %>rel="nofollow"<% end_if %> href="$ShowLink">
		                	<div class="img-wrapper">
								<img class="big_logo" src="$getLogoUrl" alt="$Name">
							</div>
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
					Open Infrastructure Foundation Gold Members provide funding and pledge strategic alignment to the OpenInfra Foundation mission. There can be up to twenty-four Gold Members at any given time, subject to board approval. If your organization is highly involved with the Open Infrastructure community and interested in becoming a Gold Member, read more about joining the Foundation. Thank you to the following Gold Members who are critical to OpenStack's success. <a href="https://openinfra.dev/join/members">Learn more about becoming a member</a>.
					</p>
				</div>
			</div>

			<div class="row logos">
				<% loop DisplayedCompanies(Gold) %>
					<div class="col-sm-3 col-xs-6">
		                <a <% if IsExternalUrl %>rel="nofollow"<% end_if %> href="$ShowLink">
		                	<div class="img-wrapper">
                                <img class="small_logo" src="$getLogoUrl" alt="$Name">
							</div>
						</a>
					</div>
				<% end_loop %>
			</div>
		<% end_if %>
		<div class="row">
			<div class="col-sm-12">
				<hr/>
				<p><strong>What's the OpenInfra Foundation Member Spotlight and how can I get my company featured?</strong></p>
				<p>The OpenInfra Foundation Member Spotlight on the OpenStack homepage highlights the OpenInfra Foundation Gold and Platinum Members and their products and services. If you don't see your company listed, please complete your company profile and email <a href="mailto:ecosystem@openstack.org">ecosystem@openstack.org</a>.</p>
			</div>
		</div>
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
			<% loop getDonorsOrdered() %>
						    <div class="col-sm-3 col-xs-6">
                    <a <% if IsExternalUrl %>rel="nofollow"<% end_if %> href="$ShowLink">
                        <div class="img-wrapper">
                            <img class="small_logo" src="$getLogoUrl" alt="$Name">
                        </div>
                    </a>
                </div>
			<% end_loop %>
		</div>

		<!-- Silver & Startup Members -->
		<% if DisplayedCompanies(Combined) %>
			<div class="row">
				<div class="col-sm-12">
					<hr/>
					<h2>Silver Sponsors</h2>
					<p>
					Open Infrastructure Foundation Silver Members provide funding and pledge strategic alignment to the OpenInfra Foundation mission. If your organization is highly involved with the Open Infrastructure community and interested in becoming a Silver Member, read more about joining the Foundation. Thank you to the following Silver Members who are critical to OpenStack's success. <a href="https://openinfra.dev/join/members">Learn more about becoming a member</a>.
					</p>
				</div>
			</div>

			<div class="row logos">
				<% loop DisplayedCompanies(Combined) %>
					<div class="col-sm-3 col-xs-6">
	                <a <% if IsExternalUrl %>rel="nofollow"<% end_if %> href="$ShowLink">
	                	<div class="img-wrapper">
                            <img class="small_logo" src="$getLogoUrl" alt="$Name">
						</div>
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
					The resources provided by the Members are critical to making the OpenInfra Foundation successful, but there are many ways to support the OpenStack mission, whether you're contributing code, building an OpenStack product or helping build the community. Below are companies who are actively involved in making OpenStack successful. If you would like your company listed here, please complete the <a href="https://openstack.na1.echosign.com/public/esignWidget?wid=CBFCIBAA3AAABLblqZhAwCNf4bl-p05NMZiwtG6R9VEvPsqRKfyPpVy47b7apdl09bEht3qU8O6SYXQKA5Ww*" target="_new">logo authorization form</a>.
					</p>
				</div>
			</div>

			<div class="row small-logos">

				<% loop DisplayedCompanies(Mention) %>
					<div class="col-sm-2 col-xs-6">
	            		<a <% if IsExternalUrl %>rel="nofollow"<% end_if %> href="$ShowLink">
	            			<div class="img-wrapper">
                                <img class="small_logo_tiny" src="{$getLogoUrl(138)}" alt="$Name">
							</div>
						</a>
					</div>
				<% end_loop %>
			</div>
		<% end_if %>
