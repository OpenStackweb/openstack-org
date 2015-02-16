<aside>
	$FilterForm
</aside>
<div class="content-container unit size3of4 lastUnit">
	<article>	
		<h1>$Title</h1>
		<div class="content">
			<% if $Results %>
				<p>Showing $Results.count results</p>
				<% loop $Results %>
				<div style="width=300px;float:left;height:300px;">
					<a href="$Link">$VideoThumbnail.CroppedImage(200,200)</a>
					<h3><a href="$Link">$Title</a></h3>
					<h4>Category: $Category.Title</h4>
					<p>$EventStart.Nice - $EventEnd.Nice</p>
					$Description.FirstSentence
				</div>
				<% end_loop %>
				<% if $Results.MoreThanOnePage %>
				    <div id="PageNumbers">
				        <p>
				            <% if $Results.NotFirstPage %>
				                <a class="prev" href="$Results.PrevLink" title="View the previous page">Prev</a>
				            <% end_if %>
				            <span>
				                    <% loop $Results.PaginationSummary(4) %>
				                    <% if $CurrentBool %>
				                        $PageNum
				                    <% else %>
				                        <% if $Link %>
				                            <a href="$Link" title="View page number $PageNum">$PageNum</a>
				                        <% else %>
				                            &hellip;
				                        <% end_if %>
				                    <% end_if %>
				                <% end_loop %>
				            </span>
				            <% if $Results.NotLastPage %>
				                <a class="next" href="$Results.NextLink" title="View the next page">Next</a>
				            <% end_if %>
				        </p>
				    </div>
				<% end_if %>				
			<% else %>
				<p>Sorry, no presentations matched your search criteria
			<% end_if %>
		</div>
	</article>
</div>