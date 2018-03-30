<div id="nav-char">
    <% loop getNavLetters %>
        <span class="nav-char-item">
            <a class="inactive" href="#{$Char}">$Char</a>
        </span>
    <% end_loop %>
</div>

<div class="light summit-users-wrapper">
    <div class="container-fluid">
        <% loop $AllSpeakers %>
            <%if $First %>
                <div class="row">
            <% end_if %>
            <div class="col-lg-3 featured" id="{$getFirstLetterFromName}">
                <div class="summit-user-section">
                    <div class="summit-user-image-box">
                        <a title="see {$getName()}'s sessions" href="{$Top.getScheduleGlobalSearchPageLink($getName)}">
                            <img src="{$ProfilePhoto(400)}" alt="{$getName()}" class="summit-user-image">
                        </a>
                    </div>
                    <div class="name">{$getName()}</div>
                    <div class="title">{$Title}</div>
                </div>
            </div>
            <%if $Last %>
                </div>
            <% else_if $MultipleOf(4) %>
                </div><div class="row">
            <% end_if %>
        <% end_loop %>
    </div>
</div>
<script>

    $( window ).on("scroll", function(e) {
        var nav_char = $('#nav-char');
        if ($(window).scrollTop() > 147) {
            nav_char.css({top: 0, position:'fixed'});
        } else {
            nav_char.css({top: 0, position:'relative'});
        }

    });
</script>