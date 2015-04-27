<div id="reviews" style="min-height: 400px;">
    <h3 style="color: #{$Company.CompanyColor} !important;">Reviews</h3>
    <div class="review_top_buttons">
        <input type="button" id="read_reviews" value="Read Reviews" class="<% if MarketPlaceReviews %>tab_selected<% end_if %>" />
        <input type="button" id="write_review" value="Write Review" class="<% if MarketPlaceReviews == '' %>tab_selected<% end_if %>" />
    </div>
    <div class="review_form_div" <% if MarketPlaceReviews %>style="display:none"<% end_if %> >
        <div class="login_overlay"><p>Please log in to submit a review.</p></div>
        <div class="success_overlay"><p>Your review has been submitted, it will be shown upon approval.</p></div>
        $MarketPlaceReviewForm
    </div>
    <div class="review_list_div" <% if MarketPlaceReviews == '' %>style="display:none"<% end_if %> >
        $MarketPlaceReviews
        <% if MarketPlaceReviews == '' %>No reviews yet.<% end_if %>
    </div>
</div>