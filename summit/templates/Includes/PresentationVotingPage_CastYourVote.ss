<!-- CAST YOUR VOTE  ----------------------------------->
<h5>Cast Your Vote</h5>
<ul class="voting-rate-wrapper">
    <li class="voting-rate-single <% if $VoteValue = 3 %>current-vote<% end_if %>">
        <a href="{$TopLink}SaveRating/?id={$PresentationID}&rating=3" id='vote-3'>
            Would Love To See!
            <div class="voting-shortcut">3</div>
        </a>
    </li>
    <li class="voting-rate-single <% if $VoteValue = 2 %>current-vote<% end_if %>">
        <a href="{$TopLink}SaveRating/?id={$PresentationID}&rating=2" id='vote-2'>
            Would Try To See
            <div class="voting-shortcut">2</div>
        </a>
    </li>
    <li class="voting-rate-single <% if $VoteValue = 1 %>current-vote<% end_if %>">
        <a href="{$TopLink}SaveRating/?id={$PresentationID}&rating=1" id='vote-1'>
            Might See
            <div class="voting-shortcut">1</div>
        </a>
    </li>
    <li class="voting-rate-single <% if $VoteValue = -1 %>current-vote<% end_if %>">
        <a href="{$TopLink}SaveRating/?id={$PresentationID}&rating=-1" id='vote-0'>
            Would Not See
            <div class="voting-shortcut">0</div>
        </a>
    </li>
</ul>
<div class="voting-tip">
    <strong>TIP:</strong> You can vote quickly with your keyboard using the numbers below each
    option.
</div>