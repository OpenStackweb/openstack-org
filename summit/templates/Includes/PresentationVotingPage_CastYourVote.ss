<!-- CAST YOUR VOTE  ----------------------------------->
<h5>Cast Your Vote</h5>
<ul class="voting-rate-wrapper">
    <li class="voting-rate-single <% if $Top.VoteValue = 3 %>current-vote<% end_if %>">
        <a href="{$Top.Link}SaveRating/?id={$ID}&rating=3" id='vote-3'>
            Would Love To See!
            <div class="voting-shortcut">3</div>
        </a>
    </li>
    <li class="voting-rate-single <% if $Top.VoteValue = 2 %>current-vote<% end_if %>">
        <a href="{$Top.Link}SaveRating/?id={$ID}&rating=2" id='vote-2'>
            Would Try To See
            <div class="voting-shortcut">2</div>
        </a>
    </li>
    <li class="voting-rate-single <% if $Top.VoteValue = 1 %>current-vote<% end_if %>">
        <a href="{$Top.Link}SaveRating/?id={$ID}&rating=1" id='vote-1'>
            Might See
            <div class="voting-shortcut">1</div>
        </a>
    </li>
    <li class="voting-rate-single <% if $Top.VoteValue = -1 %>current-vote<% end_if %>">
        <a href="{$Top.Link}SaveRating/?id={$ID}&rating=-1" id='vote-0'>
            Would Not See
            <div class="voting-shortcut">0</div>
        </a>
    </li>
</ul>