<div class="row">
    <div class="col-sm-8">
        <div class="bio-row">
            $Event.Abstract.RAW
        </div>
    </div>
    <div class="col-sm-4">
        <% if $Event.allowSpeakers %>
            <% loop $Event.getSpeakersAndModerators() %>
            <div data-speaker-id="{$ID}" class="row speaker-row">
                <div class="speaker-name-row">
                    <div class="col-sm-12">
                        <div class="speaker-photo-left">
                            <a href="{$Top.Link(speakers)}/{$ID}" class="profile-pic-wrapper" style="background-image: url('{$ProfilePhoto(60)}')">
                            </a>
                        </div>
                        <div class="speaker-name-right">
                            <a href="{$Top.Link(speakers)}/{$ID}">$Name</a><% if $Top.Event.isModeratorByID($ID) %>&nbsp;<span class="label label-info">Moderator</span><% end_if %>
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
            <a href="{$Event.getLink(show)}" class="btn btn-primary btn-md active btn-warning btn-go-event" role="button">EVENT DETAILS</a>
        </div>
        <% include SummitAppEvent_RSVPButton Event=$Event %>
    </div>
</div>