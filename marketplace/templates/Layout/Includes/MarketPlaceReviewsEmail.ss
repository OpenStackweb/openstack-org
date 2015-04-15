$getMember().FirstName $getMember().Surname
<% if getMember().Org.Name %>
    from $getMember().Org.Name
<% end_if %>
wrote on $Now.Format(M jS Y) :
<br>
<br>
<b>$Title</b> -
<br>
<i>$Comment</i>
<br>
<div style="position: relative; vertical-align: middle; display: inline-block; color: #e3e3e3; overflow: hidden; font-family: 'Glyphicons Halflings'; padding-left: 2px;" data-content="">
    <div style="position: absolute;left: 0;top: 0;white-space: nowrap;overflow: hidden;color: #fde16d;" data-content="" style="width: {$getRatingAsWidth()}%;"></div>
</div>

To approve or reject this review please follow this <a href="http://www.openstack.org/sangria/ViewReviews">link</a>.