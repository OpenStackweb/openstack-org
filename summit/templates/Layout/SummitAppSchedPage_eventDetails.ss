<div class="row">
    <div class="col-sm-8">
        <div class="bio-row">
            $Event.ShortDescription.RAW
        </div>
    </div>
    <div class="col-sm-4">
        <% if $Event.Speakers %>
            <% loop $Event.Speakers %>
            <div data-speaker-id="{$ID}" class="row speaker-row">
                <div class="speaker-name-row">
                    <div class="col-sm-12">
                        <div class="speaker-photo-left">
                            <a href="{$Top.Link(speakers)}/{$ID}" class="profile-pic-wrapper" style="background-image: url('{$ProfilePhoto(60)}')">
                            </a>
                        </div>
                        <div class="speaker-name-right">
                            <a href="{$Top.Link(speakers)}/{$ID}">
                            $Name
                            </a>
                            <div class="speaker-company">
                                $TitleNice
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <% end_loop %>
        <% end_if %>
        <div class="space-row">
            &nbsp;
        </div>
        <% if $Event.isPresentation %>
        <div class="level-row">
            <div class="col-sm-12 col-level-content">
                <i class="fa fa-signal level-icon"></i>
                <span>Level:</span>
                <span class="presentation-level'">
                    <a class="search-link" title="Search Presentation Level" href="{$Top.Link(global-search)}?t={$Event.Level}">{$Event.Level}</a>
                </span>
            </div>
        </div>
        <% end_if %>
        <% if $Event.Tags %>
        <div class="tags-row">
            <div class="col-sm-12 col-tags-content">
                <i class="fa fa-tags"></i>
                <span>Tags:</span>
                <% loop $Event.Tags %>
                <span title="Search Tag" class="tag">
                    <a class="search-link" href="{$Top.Link(global-search)}?t={$TagURL}">{$Tag}<% if not Last %>,&nbsp;<% end_if %></a>
                &nbsp;
                </span>
                <% end_loop %>
            </div>
        </div>
        <% end_if %>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="event-btn">
            <form action="{$Top.Link(events)}/{$Event.ID}" method="POST">
                <input type="hidden" name="goback" value="1" />
                <button type="submit" class="btn btn-primary btn-md active btn-warning btn-go-event" role="button">EVENT DETAILS</button>
            </form>
        </div>
        <% if $Event.RSVPLink %>
        <div class="event-btn">
            <a href="{$Event.RSVPLink}" class="btn btn-primary btn-md active btn-warning btn-rsvp-event" target="_blank" role="button">RSVP to this Event</a>
        </div>
        <% end_if %>
    </div>
</div>