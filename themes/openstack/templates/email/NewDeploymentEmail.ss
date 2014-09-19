<% control Deployment %>
    <h1>New Deployment - $Label </h1>
    <h2>Main Info</h2>
    <ul>
        <li><b>Deployment Name</b> $Label</li>
        <li><b>Would you like to keep this information confidential or allow the Foundation to share information about this deployment publicly?</b> $IsPublic</li>
        <li><b>Organization</b> $OrgID</li>
        <li><b>Deployment Type</b> $DeploymentType</li>
        <li><b>Projects Used</b> $ProjectsUsed</li>
        <li><b>What releases are you currently using?</b> $CurrentReleases </li>
        <li><b>In what stage is your OpenStack deployment? (make a new deployment profile for each type of deployment)</b> $DeploymentStage </li>
        <li><b>What's the size of your cloud by number of users?</b> $NumCloudUsers</li>
        <li><b>Describe the workloads or applications running in your Openstack environment. (choose any that apply)</b> $WorkloadsDescription</li>
        <li><b>Other workloads or applications running in your Openstack environment. (optional)</b> $OtherWorkloadsDescription</li>
    </ul>
<% end_control %>