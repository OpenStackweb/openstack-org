<div id="reviews" style="min-height: 400px;">
    <h3 style="color: #{$Company.CompanyColor} !important;">Reviews</h3>
    <div class="review_top_buttons">
        <input type="button" id="read_reviews" value="Read Reviews" />
        <input type="button" id="write_review" value="Write Review" />
    </div>
    <div class="review_form_div">
        <div class="login_overlay"><p>Please log in to submit a review.</p></div>
        <div class="success_overlay"><p>Your review has been submitted, it will be shown upon approval.</p></div>
        $MarketPlaceReviewForm
    </div>
    <div class="review_list_div" style="display:none">
        $MarketPlaceReviews
    </div>
</div>