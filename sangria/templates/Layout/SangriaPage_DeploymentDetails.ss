<a href="$BackUrl">Back</a>&nbsp;|&nbsp;<a href="javascript:window.print()">Print This Page</a>
<% with Deployment %>
<h1>Deployment # {$ID}</h1>
<h2>$Label</h2>
<hr>
<b>Survey:</b>&nbsp;<a href="$Top.Link(SurveyDetails)/{$DeploymentSurvey.ID}?BackUrl={$Top.Link(DeploymentDetails)}/{$ID}" title="view associated survey"># $DeploymentSurvey.ID</a><br>
<b>Last Updated:</b>&nbsp;$UpdateDate<br>
<br>
<b>Would you like to keep this information confidential or allow the Foundation to share information about this deployment publicly?</b>&nbsp;<% if IsPublic %>Willing to share<% else %>Confidential<% end_if %><br>
<b>In what stage is your OpenStack deployment?</b>&nbsp;$DeploymentStage<br>
<b>In which country / countries is this OpenStack deployment physically located?</b>&nbsp;$CountriesPhysicalLocation<br>
<b>In which country / countries are the users / customers for this deployment physically located?</b>&nbsp;$CountriesUsersLocation<br>
<b>Deployment Type</b>&nbsp;$DeploymentType<br>
<b>Projects Used</b>&nbsp;$ProjectsUsed<br>
<b>What releases are you currently using?</b>&nbsp;$CurrentReleases<br>
<b>Services Deployments - workloads designed to be accessible for external users / customers</b>&nbsp;$ServicesDeploymentsWorkloads $OtherServicesDeploymentsWorkloads<br>
<b>Enterprise Deployments - workloads designed to be run internally to support business</b>&nbsp;$EnterpriseDeploymentsWorkloads $OtherEnterpriseDeploymentsWorkloads<br>
<b>Horizontal Workload Frameworks</b>&nbsp;$HorizontalWorkloadFrameworks $OtherHorizontalWorkloadFrameworks<br>
<h3>Telemetry</h3>
<hr>
<b>What is the main operating system running this OpenStack cloud deployment?</b>&nbsp;$OperatingSystems $OtherOperatingSystems<br>
<b>What packages does this deployment use…?</b>&nbsp;$UsedPackages<br>
<b>If you have modified packages or have built your own packages, why?</b>&nbsp;$CustomPackagesReason $OtherCustomPackagesReason<br>
<b>What tools are you using to deploy / configure this cluster</b>&nbsp;$DeploymentTools $OtherDeploymentTools<br>
<b>What Platform-as-a-Service (PaaS) tools are you using to manage applications on this OpenStack deployment?</b>&nbsp;$PaasTools $OtherPaasTools<br>
<b>If this deployment uses <b>OpenStack Compute (Nova)</b>, which hypervisors are you using</b>&nbsp;$Hypervisors $OtherHypervisor<br>
<b>Which compatibility APIs does this deployment support?</b>&nbsp;$SupportedFeatures $OtherSupportedFeatures<br>
<b>What database do you use for the components of this OpenStack cloud?</b>&nbsp;$UsedDBForOpenStackComponents $OtherUsedDBForOpenStackComponents<br>
<b>If this deployment uses <b>OpenStack Network (Neutron)</b>, which drivers are you using?</b>&nbsp;$NetworkDrivers $OtherNetworkDriver<br>
<b>If you are using <b>OpenStack Identity Service (Keystone)</b> which OpenStack identity drivers are you using?</b>&nbsp;$IdentityDrivers $OtherIndentityDriver<br>
<b>If this deployment uses <b>OpenStack Block Storage (Cinder)</b>, which drivers are </b>&nbsp;$BlockStorageDrivers $OtherBlockStorageDriver<br>
<b>With what other clouds does this OpenStack deployment interact?</b>&nbsp;$InteractingClouds $OtherInteractingClouds<br>
<b>Number of users</b>&nbsp;$NumCloudUsers<br>
<b>Physical compute nodes'</b>&nbsp;$ComputeNodes<br>
<b>Processor cores</b>&nbsp;$ComputeCores<br>
<b>Number of instances</b>&nbsp;$ComputeInstances<br>
<b>Number of fixed / floating IPs</b>&nbsp;$NetworkNumIPs<br>
<b>If this deployment uses <b>OpenStack Block Storage (Cinder)</b>, what is the size of its block storage?</b>&nbsp;$BlockStorageTotalSize<br>
<b>If this deployment uses <b>OpenStack Object Storage (Swift)</b>, what is the size of its block storage?</b>&nbsp;$ObjectStorageSize<br>
<b>If this deployment uses <b>OpenStack Object Storage (Swift)</b>, how many total objects are stored?</b>&nbsp;$ObjectStorageNumObjects<br>
<h3>Spotlight</h3>
<hr>
<b>If this deployment uses nova-network and not OpenStack Network (Neutron), what would allow you to migrate to Neutron?</b>&nbsp;$WhyNovaNetwork $OtherWhyNovaNetwork<br>
<b>Are you using Swift\'s global distribution features?</b>&nbsp;$SwiftGlobalDistributionFeatures<br>
<b>If yes, what is your use case</b>&nbsp;$SwiftGlobalDistributionFeaturesUsesCases $OtherSwiftGlobalDistributionFeaturesUsesCases<br>
<b>Do you have plans to use Swift\'s storage policies or erasure codes in the next year?</b>&nbsp;$Plans2UseSwiftStoragePolicies $OtherPlans2UseSwiftStoragePolicies<br>
<b>What tools are you using charging or show-back for your users?</b>&nbsp;$ToolsUsedForYourUsers $OtherToolsUsedForYourUsers<br>
<b>If you are not using Ceilometer, what would allow you to move to it?</b>&nbsp;$Reason2Move2Ceilometer<br>
<% end_with %>
<br>
<a href="$BackUrl">Back</a>&nbsp;|&nbsp;<a href="javascript:window.print()">Print This Page</a>
