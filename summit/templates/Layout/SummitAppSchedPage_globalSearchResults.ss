<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-main-title">
            <div class="row">
                <div class="col-xs-12"><h1>Schedule</h1></div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <schedule-global-filter search_url="{$Top.Link(global-search)}" value="{$SearchTerm}" clear_url="{$Top.Link}"></schedule-global-filter>
                </div>
            </div>
        </div>
    </div>
    <% if PopularTerms %>
        <div class="row cloud-tags">
            <ul id="tags_list">
                <% loop PopularTerms %>
                    <li data-hits="{$Hits}" data-term="{$Term}">
                        <a href="{$Top.Link(global-search)}?t={$Term.JS}" Title="Search for {$Term.JS}">
                            <span class="label label-default tag-cloud" style="background-color: hsl(0, 0%, {$Opacity}%);font-size: {$FontSize}px;">$Term</span>
                        </a>
                    </li>
                <% end_loop %>
            </ul>
        </div>
    <% end_if %>
    <h2>Search Results</h2>
    <% if SpeakerResults && SpeakerResults.Count %>
    <div class="row">
        <div class="col-md-12">
           <div class="search-header">Speakers Matches</div>
            <div class="row people-results">
                <% loop SpeakerResults %>
                    <div class="col-xs-4 col-md-3">
                        <div class="row speaker-row">
                            <div class="col-md-4">
                                <a href="{$Top.Link(speaker)}/{$ID}">
                                    <img src="{$ProfilePhoto}" class="img-circle" alt="{$Name}">
                                </a>
                            </div>
                            <div class="col-md-8">
                                <div class="row speaker-name-row">
                                    <div class="col-md-12">
                                        <a href="{$Top.Link(speakers)}/{$ID}">$Name</a>
                                    </div>
                                </div>
                                <div class="row speaker-position-row"><div class="col-md-12">{$CurrentPosition}</div></div>
                            </div>
                        </div>
                    </div>
                <% end_loop %>
            </div>
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
                                        <div class="col-xs-12 col-md-6 col-time">
                                            <i class="fa fa-clock-o icon-clock"></i>&nbsp;<span>{$DateNice}</span>
                                        </div>
                                        <div class="col-xs-12 col-md-6 col-location"><i class="fa fa-map-marker icon-map"></i>&nbsp;<span>{$LocationNameNice}</span></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 event-title"><a href="{$Top.Link(events)}/{$ID}">$Title.RAW</a></div>
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
                                                        <span title="Search Tag" class="tag"><a href="{$Top.Link(global-search)}?t={$Tag}">{$Tag}</a><% if not $Last %>,<% end_if %>&nbsp;</span>
                                                    <% end_loop %>
                                                </div>
                                                <% end_if %>
                                            </div>
                                           </div>
                                        <div class="col-md-3 event-type-col">$TypeName</div>
                                    </div>
                                    <% if Level %>
                                    <div class="row row-level">
                                        <div class="col-xs-12 col-md-1 col-level-title">
                                            <i class="fa fa-bar-chart"></i>
                                            <span>Level:</span>
                                        </div>
                                        <div class="col-xs-12 col-md-11 col-level-content">
                                            <span class="presentation-level'"><a href="{$Top.Link(global-search)}?t={$Level}">{$Level}</a></span>
                                        </div>
                                    </div>
                                    <% end_if %>
                                    <% if Speakers %>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row speakers-row">
                                                    <div class="col-xs-12 col-md-2 col-speakers-title">
                                                        <i class="fa fa-users"></i>
                                                        <span>Speakers:</span>
                                                    </div>
                                                    <div class="col-xs-12 col-md-10 col-speakers-content">
                                                        <% loop Speakers %>
                                                            <span title="Search Speaker" class="speaker"><a href="{$Top.Link(global-search)}?t={$Name}">{$Name}</a><% if not $Last %>,<% end_if %>&nbsp;</span>
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