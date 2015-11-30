<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-main-title">
            <div class="row">
                <div class="col-xs-12"><h1>Schedule</h1></div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <schedule-global-filter global_search_action="{$Top.Link(global-search)}" value="{$SearchTerm}" clear_action="{$Top.Link}"></schedule-global-filter>
                </div>
            </div>
        </div>
    </div>
    <h2>Search Results</h2>
    <% if SpeakerResults && SpeakerResults.Count %>
    <div class="row">
        <div class="col-xs-12">
           <div class="search-header">Speakers Matches</div>
            <ul class="list-unstyled people-results">
                <% loop SpeakerResults %>
                    <li>
                        <div class="row speaker-row">
                            <div class="col-md-1">
                                <a href="{$Top.Link(speaker)}/{$ID}">
                                    <img src="{$ProfilePhoto}" class="img-circle" alt="{$Name}">
                                </a>
                            </div>
                            <div class="col-md-11">
                                <div class="row speaker-name-row">
                                    <div class="col-md-12">
                                        <a href="{$Top.Link(speaker)}/{$ID}">$Name</a>
                                    </div>
                                </div>
                                <div class="row speaker-position-row"><div class="col-md-12">{$CurrentPosition}</div></div>
                            </div>
                        </div>
                    </li>
                <% end_loop %>
            </ul>
        </div>
    </div>
    <% end_if %>
   <% if EventResults && EventResults.Count %>
        <div class="row">
            <div class="col-xs-12">
                <div class="search-header">Schedule Matches</div>
                <ul class="list-unstyled event-results">
                    <% loop EventResults %>
                        <li>

                            <div class="row event-row">
                                <div class="col-md-11 col-xs-11 event-content">
                                    <div class="row row_location">
                                        <div class="col-xs-12 col-md-3 col-time">

                                            <i class="fa fa-clock-o icon-clock"></i>&nbsp;<span>{$DateNice}</span>
                                        </div>
                                        <div class="col-xs-12 col-md-7 col-location"><i class="fa fa-map-marker icon-map"></i>&nbsp;<span>{$LocationNameNice}</span></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 event-title"><a href="{$Top.Link(event)}/{$ID}">$Title.RAW</a></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-track"><span title="Track Name" class="track"></span></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="row tags-row">
                                                <% if Tags %>
                                                <div class="col-xs-12 col-md-2 col-tags-title">
                                                    <i class="fa fa-tags"></i>
                                                    <span>Tags:</span>
                                                </div>
                                                <div class="col-xs-12 col-md-10 col-tags-content">
                                                    <% loop Tags %>
                                                        <span title="Tag" class="tag">{$Tag}<% if not $Last %>,<% end_if %>&nbsp;</span>
                                                    <% end_loop %>
                                                </div>
                                                <% end_if %>
                                            </div>
                                           </div>
                                        <div class="col-md-3 event-type-col">$TypeName</div>
                                    </div>
                                    <% if Speakers %>
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="row tags-row">
                                                    <div class="col-xs-12 col-md-2 col-tags-title">
                                                        <i class="fa fa-users"></i>
                                                        <span>Speakers:</span>
                                                    </div>
                                                    <div class="col-xs-12 col-md-10 col-tags-content">
                                                        <% loop Speakers %>
                                                            <span title="Speaker" class="tag">{$Name}<% if not $Last %>,<% end_if %>&nbsp;</span>
                                                        <% end_loop %>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <% end_if %>
                                </div>
                            </div>
                        </li>
                    <% end_loop %>
                </ul>
            </div>
        </div>
    <% end_if %>
</div>
<script src="summit/javascript/schedule/schedule.bundle.js" type="application/javascript"></script>