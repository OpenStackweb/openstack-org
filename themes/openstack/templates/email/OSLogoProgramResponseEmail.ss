<html>
<body>

<p>A Contact Form for Commercial Logo Inquiries was just submitted.</p>

<ul>
	<li><h3><strong>Name: </strong> $FirstName $Surname</h3></li>
	<li><strong>Company Name: </strong> $CompanyName</li>
	<li><strong>Product Name: </strong> $Product</li>
	<li><strong>Email: </strong> $Email</li>
	<li><strong>Phone: </strong> $Phone</li>
</ul>

<ul>
    <li><strong>Logo Program: </strong> $Program</li>
	<li><strong>Current Sponsor: </strong> <% if CurrentSponsor %>YES<% else %>NO<% end_if %></li>
	<li><strong>Company Details: </strong> $CompanyDetails</li>
	<li><strong>Categories: </strong> $Category</li>
	<li><strong>Regions: </strong> $Regions</li>
	<li><strong>Projects: </strong> $Projects</li>
	<li><strong>API Exposed: </strong> <% if APIExposed %>YES<% else %>NO<% end_if %></li>
</ul>

</body>
</html>