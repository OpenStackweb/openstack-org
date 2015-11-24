<div class="container-fluid">
    <h1 class="schedule_title">Schedule</h1>
    <hr>
    <div class="row">
        <div class="col-xs-12">
            <div class="row global-search-container">
                <form class="form-inline all-events-search-form" method="get" action="$Top.Link(global-search)">
                    <div class="col-lg-12">
                        <div class="input-group" style="width: 100%;">
                            <input type="text" id="global-search-term" name="global-search-term" class="form-control input-global-search" placeholder="Search for..." value="{$SearchTerm}">
                            <span class="input-group-btn" style="width: 5%;">
                                <button class="btn btn-default btn-global-search" type="submit">Go!</button>
                                <button class="btn btn-default btn-global-search-clear" type="button">Clear</button>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="application/javascript">
        $('.btn-global-search-clear').click(function(){
            window.location = '{$Top.Link}';
        });
    </script>
    <h2>Search Results</h2>
    <% if SpeakerResults && SpeakerResults.Count %>
    <div class="row">
        <div class="col-xs-12">
           <div class="search-header">Speakers Matches</div>
            <ul>
                <% loop SpeakerResults %>
                    <li>
                        <a href="{$Top.Link(speaker)}/{$ID}">$Name</a>
                    </li>
                <% end_loop %>
            </ul>
        </div>
    </div>
    <% end_if %>
    <% if AttendeeResults  && AttendeeResults.Count %>
        <div class="row">
            <div class="col-xs-12">
                <div class="search-header">Attendees Matches</div>
                <ul>
                    <% loop AttendeeResults %>
                        <li>
                            <a href="#">$Member.FullName</a>
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
                <ul>
                    <% loop EventResults %>
                        <li>
                            <a href="{$Top.Link(event)}/{$ID}">$Title</a>
                        </li>
                    <% end_loop %>
                </ul>
            </div>
        </div>
    <% end_if %>
</div>