<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID, Active='dashboard' %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper" class="container-fluid summit-admin-container dashboard">
        <ol class="breadcrumb">
            <li><a href="$Top.Link">Home</a></li>
            <li class="active">$Summit.Name</li>
        </ol>
        <div class="page-header">
            <h1>$Summit.Title<small></small></h1>
        </div>

        <script type="application/javascript">
            var local_time = "{$Summit.getLocalTime('F jS Y, h:i:s a').JS}";
            function getTime() {
                var format = 'MMMM Do YYYY, h:mm:ss a';
                local_time = moment(local_time, format).add(1,'second').format(format);

                $('#timezone_clock').html(local_time);
            }

            $(document).ready(function(){
                setInterval(getTime,1000);
            });

        </script>

        <h2> Dates & Times </h2>

        <div class="row">
            <div class="col-md-2"> $Summit.getTimeZoneName() </div>
            <div class="col-md-4" id="timezone_clock">  </div>
        </div>

        <div class="row $Summit.isStageTime('Summit')">
            <div class="col-md-2"> <i class="fa fa-calendar"></i> Summit </div>
            <div class="col-md-4"> $Summit.getSummitBeginDate('F jS Y, h:i:s a') </div>
            <div class="col-md-1"><i class="fa fa-angle-double-right" aria-hidden="true"></i></div>
            <div class="col-md-4"> $Summit.getSummitEndDate('F jS Y, h:i:s a') </div>
        </div>
        <div class="row $Summit.isStageTime('Submission')">
            <div class="col-md-2"> <i class="fa fa-calendar"></i> Submission </div>
            <div class="col-md-4"> $Summit.getSubmissionBeginDate('F jS Y, h:i:s a') </div>
            <div class="col-md-1"><i class="fa fa-angle-double-right" aria-hidden="true"></i></div>
            <div class="col-md-4"> $Summit.getSubmissionEndDate('F jS Y, h:i:s a') </div>
        </div>
        <div class="row $Summit.isStageTime('Voting')">
            <div class="col-md-2"> <i class="fa fa-calendar"></i> Voting </div>
            <div class="col-md-4"> $Summit.getVotingBeginDate('F jS Y, h:i:s a') </div>
            <div class="col-md-1"><i class="fa fa-angle-double-right" aria-hidden="true"></i></div>
            <div class="col-md-4"> $Summit.getVotingEndDate('F jS Y, h:i:s a') </div>
        </div>
        <div class="row $Summit.isStageTime('Selection')">
            <div class="col-md-2"> <i class="fa fa-calendar"></i> Selection </div>
            <div class="col-md-4"> $Summit.getSelectionBeginDate('F jS Y, h:i:s a') </div>
            <div class="col-md-1"><i class="fa fa-angle-double-right" aria-hidden="true"></i></div>
            <div class="col-md-4"> $Summit.getSelectionEndDate('F jS Y, h:i:s a') </div>
        </div>
        <div class="row $Summit.isStageTime('Registration')">
            <div class="col-md-2"> <i class="fa fa-calendar"></i> Registration </div>
            <div class="col-md-4"> $Summit.getRegistrationBeginDate('F jS Y, h:i:s a') </div>
            <div class="col-md-1"><i class="fa fa-angle-double-right" aria-hidden="true"></i></div>
            <div class="col-md-4"> $Summit.getRegistrationEndDate('F jS Y, h:i:s a') </div>
        </div>

        <hr>

        <h2> Events & Attendees </h2>

        <div class="row">
            <div class="col-md-6">
                <i class="fa fa-users"></i>&nbsp;Attendees&nbsp;<strong>$Summit.Attendees.Count</strong>
            </div>
            <div class="col-md-6">
                <i class="fa fa-users"></i>&nbsp;Speakers&nbsp;<strong>$Summit.Speakers.Count</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <i class="fa fa-calendar-plus-o"></i>&nbsp;Submitted Events&nbsp;<strong>$Summit.Events.Count</strong>
            </div>
            <div class="col-md-6">
                <i class="fa fa-calendar-check-o"></i>&nbsp;Published Events&nbsp;<strong>$Summit.PublishedEvents.Count</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <i class="fa fa-building"></i>&nbsp;Venues&nbsp;<strong>$Summit.VenuesCount</strong>
            </div>
        </div>

        <hr>

        <h2> Emails </h2>

        <div class="row">
            <div class="col-md-4">
                <i class="fa fa-paper-plane"></i>&nbsp;Accepted&nbsp;
                <strong>$Summit.SpeakerAnnouncementEmails.Filter('AnnouncementEmailTypeSent', 'ACCEPTED').Count</strong>
            </div>
            <div class="col-md-4">
                <i class="fa fa-paper-plane"></i>&nbsp;Rejected&nbsp;
                <strong>$Summit.SpeakerAnnouncementEmails.Filter('AnnouncementEmailTypeSent', 'REJECTED').Count</strong>
            </div>
            <div class="col-md-4">
                <i class="fa fa-paper-plane"></i>&nbsp;Alternate&nbsp;
                <strong>$Summit.SpeakerAnnouncementEmails.Filter('AnnouncementEmailTypeSent', 'ALTERNATE').Count</strong>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <i class="fa fa-paper-plane"></i>&nbsp;Accepted Alternate&nbsp;
                <strong>$Summit.SpeakerAnnouncementEmails.Filter('AnnouncementEmailTypeSent', 'ACCEPTED_ALTERNATE').Count</strong>
            </div>
            <div class="col-md-4">
                <i class="fa fa-paper-plane"></i>&nbsp;Accepted Rejected&nbsp;
                <strong>$Summit.SpeakerAnnouncementEmails.Filter('AnnouncementEmailTypeSent', 'ACCEPTED_REJECTED').Count</strong>
            </div>
            <div class="col-md-4">
                <i class="fa fa-paper-plane"></i>&nbsp;Alternate Rejected&nbsp;
                <strong>$Summit.SpeakerAnnouncementEmails.Filter('AnnouncementEmailTypeSent', 'ALTERNATE_REJECTED').Count</strong>
            </div>
        </div>
    </div>
<!-- /#page-content-wrapper -->
</div>