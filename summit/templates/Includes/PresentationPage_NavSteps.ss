<% if $Presentation.exists %>
    <div class="row">
        <div class="col-lg-12">
            <div class="presentation-steps">
                <a href='$Presentation.EditLink' class="step step-main">1.&nbsp;Presentation&nbsp;Summary&nbsp;&nbsp;<i class="fa fa-file-text-o"></i></a>
                <a href='$Presentation.EditTagsLink' class="step step-tags">2.&nbsp;Presentation&nbsp;Tags&nbsp;&nbsp;<i class="fa fa-tags"></i></a>
                <a href='$Presentation.EditSpeakersLink' class="step step-speakers">3.&nbsp;Speakers&nbsp;&nbsp;<i class="fa fa-user"></i></a>
            </div>
        </div>
    </div>
    <script>
        $('.step').removeClass("active");
        <% if $Step == 1 %>
            $('.step-main').addClass("active");
        <% else_if $Step == 2 %>
            $('.step-tags').addClass("active");
            $(document).persistableForm('clearAll');
        <% else_if $Step == 3 %>
            $('.step-speakers').addClass("active");
            $(document).persistableForm('clearAll');
        <% end_if %>
    </script>
<% end_if %>