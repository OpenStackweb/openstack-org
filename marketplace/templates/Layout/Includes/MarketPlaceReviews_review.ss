<div class="review"  id="{$ID}">
    <div>
        <b>$Title</b> -
        <div class="rating-container rating-gly-star" data-content="">
            <div class="rating-stars" data-content="" style="width: {$getRatingAsWidth()}%;"></div>
        </div>
        <br>
        by $Member.FirstName $Member.Surname
        <% if Member.Org.Name %>
            from $Member.Org.Name
        <% end_if %>
        on $Created.Format(M jS Y)
        <br>
        <i>$Comment</i>
    </div>
</div>