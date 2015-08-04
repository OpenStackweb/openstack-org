<chairdirectory>

	<h2>Chair Directory</h2>

	<table class="table">
		<tr>
			<th>Track</th>
			<th>Name</th>
			<th>Email</th>
		</tr>
		<tr each="{opts.chairs}">
			<td>{ category }</td>
			<td><i class="fa fa-user"></i> { first_name } { last_name }</td>
			<td><a href="mailto:{ email }">{ email }</a></td>
		</tr>
	</table>

</chairdirectory>