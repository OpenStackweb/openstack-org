<div class="row">
    <div class="col-sm-8">
        <div class="bio-row">
            $Event.ShortDescription.RAW
        </div>
    </div>
    <div class="col-sm-4">
        <% if $Event.Moderator %>
            <% with $Event.Moderator %>
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
            <% end_with %>
        <% end_if %>
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
        <% if Event.RSVPTemplate.Exists() %>
        <div class="info_item">
            <button type="button" class="btn btn-primary btn-md active btn-warning btn-rsvp-event" data-toggle="modal" data-target="#rsvpModal_{$Event.ID}">RSVP to this Event</button>
        </div>
        <div id="rsvpModal_{$Event.ID}" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">RSVP</h4>
              </div>
              <div class="modal-body">
                $RSVPForm($Event.ID)
              </div>
            </div>
          </div>
        </div>
        <% else_if Event.RSVPLink %>
        <div class="event-btn">
            <a href="{$Event.RSVPLink}" class="btn btn-primary btn-md active btn-warning btn-rsvp-event" target="_blank" role="button">RSVP to this Event</a>
        </div>
        <% end_if %>
    </div>
</div>