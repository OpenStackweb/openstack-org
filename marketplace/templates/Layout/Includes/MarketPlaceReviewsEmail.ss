$Member.FirstName $Member.Surname
<% if Member.Org.Name %>
    from $Member.Org.Name
<% end_if %>
wrote on $Created.Format(M jS Y) :
<br>
<br>
<b>$Title</b> -
<br>
<i>$Comment</i>
<br>
<div class="rating-container rating-gly-star" data-content="">
    <div class="rating-stars" data-content="" style="width: {$getRatingAsWidth()}%;"></div>
</div>

To approve or reject this review please follow this <a href="http://www.openstack.org/sangria/ViewReviews">link</a>.