<h2 style="float:left;margin-top:10px;">Reviews List</h2>
<div style="float: left;margin: 18px 0 0 45px;">
<select id="select-reviews">
    <option value="not_approved">Not Approved</option>
    <option value="approved">Approved</option>
</select>
</div>
<div class="clear"></div>
<div class="approved_reviews" style="display:none">
<% if ApprovedReviews %>
    <table class="reviews_table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Product</th>
            <th>Author</th>
            <th>Company Affiliation</th>
            <th>Rating</th>
            <th>Title</th>
            <th>Comment</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
            <% loop ApprovedReviews %>
            <tr>
                <td class="review_date">$Created.Format(M jS Y)</td>
                <td class="product">
                    <a href="/marketplace/{CompanyService.MarketPlaceType.Slug}/{CompanyService.Company.URLSegment}/{CompanyService.ID}">
                        $CompanyService.Name
                    </a>
                </td>
                <td class="author">$Member.FirstName $Member.Surname</td>
                <td class="company_affiliation">$Member.Org.Name</td>
                <td class="rating">
                    <div class="rating-container rating-gly-star" data-content="">
                        <div class="rating-stars" data-content="" style="width: {$getRatingAsWidth()}%;"></div>
                    </div>
                </td>
                <td class="title">$Title</td>
                <td class="comment">$Comment</td>
                <td width="15%">
                    <a href="#" data-request-id="{$ID}" class="reject_review roundedButton addDeploymentBtn">Reject</a>
                </td>
            </tr>
            <% end_loop %>
        </tbody>
    </table>
<% else %>
    <p>* There are not any approved reviews.</p>
<% end_if %>
</div>
<div class="not_approved_reviews">
<% if NotApprovedReviews %>
    <table class="reviews_table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Product</th>
            <th>Author</th>
            <th>Company Affiliation</th>
            <th>Rating</th>
            <th>Title</th>
            <th>Comment</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
            <% loop NotApprovedReviews %>
            <tr>
                <td class="review_date">$Created.Format(M jS Y)</td>
                <td class="product">
                    <a href="/marketplace/{CompanyService.MarketPlaceType.Slug}/{CompanyService.Company.URLSegment}/{CompanyService.ID}">
                        $CompanyService.Name
                    </a>
                </td>
                <td class="author">$Member.FirstName $Member.Surname</td>
                <td class="company_affiliation">$Member.Org.Name</td>
                <td class="rating">
                    <div class="rating-container rating-gly-star" data-content="">
                        <div class="rating-stars" data-content="" style="width: {$getRatingAsWidth()}%;"></div>
                    </div>
                </td>
                <td class="title">$Title</td>
                <td class="comment">$Comment</td>
                <td width="15%">
                    <a href="#" data-request-id="{$ID}" class="reject_review roundedButton addDeploymentBtn">Reject</a>
                    &nbsp;
                    <a href="#" data-request-id="{$ID}" class="approve_review roundedButton addDeploymentBtn">Approve</a>
                </td>
            </tr>
            <% end_loop %>
        </tbody>
    </table>
<% else %>
    <p>* There are not any reviews.</p>
<% end_if %>
</div>

<div id="dialog-approve-review" title="Approve Review?" style="display: none;">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure to approve this review?</p>
</div>

<div id="dialog-reject-review" title="Delete Review ?" style="display: none;">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure to delete this review?</p>
</div>