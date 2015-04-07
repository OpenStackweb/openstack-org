<% with Deployment %>
    <h1>New Deployment - $Label (#$ID)</h1>
    <ul>
        <li><b>Deployment Name:</b> $Label</li>
        <li><b>Organization:</b> $Org.Name</li>
        <li><b>Would you like to keep this information confidential or allow the Foundation to share information about this deployment publicly?:</b> $IsPublic</li>
        <li><b>In what stage is your OpenStack deployment?:</b> $DeploymentStage</li>
        <li><b>In which country / countries is this OpenStack deployment physically located?:</b> $CountriesPhysicalLocation</li>
        <li><b>In which country / countries are the users / customers for this deployment physically located?:</b>$CountriesUsersLocation</li>
        <li><b>Deployment Type:</b> $DeploymentType</li>
        <li><b>Projects Used:</b> $ProjectsUsed</li>
        <li><b>What releases are you currently using?:</b> $CurrentReleases</li>
        <li><b>Services Deployments - workloads designed to be accessible for external users / customers:</b> $ServicesDeploymentsWorkloads</li>
        <li><b>Other Services Deployments Workloads:</b> $OtherServicesDeploymentsWorkloads</li>
        <li><b>Enterprise Deployments - workloads designed to be run internally to support business:</b> $EnterpriseDeploymentsWorkloads</li>
        <li><b>Other Enterprise Deployments Workloads:</b> $OtherEnterpriseDeploymentsWorkloads</li>
        <li><b>Horizontal Workload Frameworks:</b> $HorizontalWorkloadFrameworks</li>
        <li><b>Other Horizontal Workload Frameworks:</b> $OtherHorizontalWorkloadFrameworks</li>
    </ul>
<% end_with %>
