<aside>
	$FilterForm
</aside>
<div class="content-container unit size3of4 lastUnit">
	<article>
		<% with $Presentation %>
		<h1>$Title</h1>
		<strong>Tags</strong>: 
			<% loop $Tags %>
				<a href="{$Top.Link}?Keywords={$Title}">$Title</a><% if not $Last %>, <% end_if %>
			<% end_loop %><br>
		<strong>Speakers</strong>:
			<% loop $PresentationSpeakers %>
				<div><a href="{$Top.Link}?Speaker={$ID}">$Title</a> <% if $TwitterHandle %>(<a href="http://twitter.com/{$TwitterHandle}">Follow on Twitter</a>)<% end_if %></div>
			<% end_loop %><br>

		<div class="content">
		<% if $RelatedMedia %>
			<h4>Related Media</h4>
			<a href="$RelatedMedia.URL">Download</a>
		<% end_if %>
			<% if $YouTubeID %>
			<div>
				<iframe width="560" height="315" src="//www.youtube.com/embed/{$YouTubeID}" frameborder="0" allowfullscreen></iframe>		
			</div>
			<% end_if %>
			$Description
			<p>Category: $Category.Title</p>
			<p>Speakers: $Speakers</p>
		</div>
		<% end_with %>

	</article>
</div>