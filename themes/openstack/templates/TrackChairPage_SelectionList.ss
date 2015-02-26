<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    
    <!-- Always force latest IE rendering engine or request Chrome Frame -->
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    
    <!-- Use title if it's in the page YAML frontmatter -->
    <title>OpenStack Presentation Editor</title>
    
    <link type="text/css" href="/{$ThemeDir}/css/bootstrap3.css" media="screen" rel="stylesheet" />
    <link type="text/css" href="/{$ThemeDir}/css/track-chair.css" media="screen" rel="stylesheet" />

	<% with SelectedTalkList.MemberList %>
    <script type="text/javascript">
      var selectedTalkListID = {$ID};
      var processingLink = "{$Top.Link}SaveSortOrder/{$ID}/?";
    </script>
    <% end_with %>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
  <script type="text/javascript" src="/{$ThemeDir}/javascript/trackchair/bootstrap.js"></script>    
	<script type="text/javascript" src="/{$ThemeDir}/javascript/trackchair/sorting.js"></script>
  </head>
  
  <body class="index">
    <!-- ========== Title Bar ========== -->
<div class='container'>

  <div class="row">
  <div class='col-lg-1'></div>
  <div class='col-lg-11'>
  <h2 class='title'>Selected Presentations</h2>
  <div id="info" style="display:none;"></div>
  </div>
  <div>

  <div class='row'>
    <div class='col-lg-1' id='left-sidebar'>
      <% include TrackChairsSideNav %>

    </div>
    <div class='col-lg-11'>

            <div class='btn-toolbar'>
          <div class='btn-group'>
            Track: &nbsp;
            <a data-toggle='dropdown' href='#'>
              <span>$CurrentCategory.Name </span>
              <span class='carrot'></span>
            </a>
            &nbsp; <% if CurrentCategory.MemberIsTrackChair %>(You Are A Track Chair)<% end_if %>
            <ul class='dropdown-menu'>
              <% loop CategoryButtons %>
              <% if MemberIsTrackChair %>
              <li class="track-chair">
                <a href='{$Top.Link}SelectionList/{$ID}'>$Name</a>
              </li>
              <% end_if %>
              <% end_loop %>
            </ul>
          </div>
        </div>
        <hr>
        <p></p>

 <div class='row'><!-- Selected Talks Row -->
  <table class="track-chairs-selections"><tr>

  <td>

<h4>Your Selections</h4>
<% loop SelectedTalkList.MemberList %>

  <ul id="member-list">
  <% if SortedTalks %>
  <% loop SortedTalks %>
    <li id="listItem_{$ID}" <% if IsAlternate %> class="alternate" <% end_if %> > <% if IsAlternate %>Alternate: <% end_if %><a href="{$Top.Link}Show/{$Talk.ID}">$Talk.PresentationTitle</a> </li>
  <% end_loop %>
  <% end_if %>
  <% if UnsortedTalks %>
  <% loop UnsortedTalks %>
    <li id="listItem_{$ID}" <% if IsAlternate %> class="alternate" <% end_if %> > <% if IsAlternate %>Alternate: <% end_if %> <a href="{$Top.Link}Show/{$Talk.ID}">$Talk.PresentationTitle</a> </li>
  <% end_loop %>
  <% end_if %>

  <% if UnusedPostions %>
  <% loop UnusedPostions %>
  <li class="unused-position">$Name</li>
  <% end_loop %>
  <% end_if %>

  </ul>

<% end_loop %>

</td>
            
    <% loop FellowTrackChairs($SelectedTalkList.GroupList.SummitCategoryID) %>
        <td>
        <h4>$Member.FirstName $Member.Surname</h4>
        <% if SelectedTalkList %>
            <% loop SelectedTalkList %>
                    <ul class="team-member-selections">
                  <% if SortedTalks %>
                  <% loop SortedTalks %>
                    <li id="listItem_{$ID}" <% if IsAlternate %> class="alternate" <% end_if %> > <% if IsAlternate %>Alternate: <% end_if %><a href="{$Top.Link}Show/{$Talk.ID}">$Talk.PresentationTitle</a> </li>
                  <% end_loop %>
                  <% end_if %>
                  <% if UnsortedTalks %>
                  <% loop UnsortedTalks %>
                    <li id="listItem_{$ID}" <% if IsAlternate %> class="alternate" <% end_if %> > <% if IsAlternate %>Alternate: <% end_if %> <a href="{$Top.Link}Show/{$Talk.ID}">$Talk.PresentationTitle</a> </li>
                  <% end_loop %>
                  <% end_if %>

                  <% if UnusedPostions %>
                  <% loop UnusedPostions %>
                  <li class="unused-position">$Name</li>
                  <% end_loop %>
                  <% end_if %>

                  </ul>
                <% end_loop %>

        <% else %>
        
            <p>This track chair hasn't made any selections yet.</p>
        
        <% end_if %>
        </td>
        <% end_loop %>
        
<td>


<h4>Team Selections</h4>
<% loop SelectedTalkList.GroupList %>

  <ul id="group-list">
  <% if SortedTalks %>
  <% loop SortedTalks %>
    <li id="listItem_{$ID}" <% if IsAlternate %> class="alternate" <% end_if %> > <% if IsAlternate %>Alternate: <% end_if %><a href="{$Top.Link}Show/{$Talk.ID}">$Talk.PresentationTitle</a> </li>
  <% end_loop %>
  <% end_if %>
  <% if UnsortedTalks %>
  <% loop UnsortedTalks %>
    <li id="listItem_{$ID}" <% if IsAlternate %> class="alternate" <% end_if %> > <% if IsAlternate %>Alternate: <% end_if %> <a href="{$Top.Link}Show/{$Talk.ID}">$Talk.PresentationTitle</a> </li>
  <% end_loop %>
  <% end_if %>

  <% if UnusedPostions %>
  <% loop UnusedPostions %>
  <li class="unused-position">$Name</li>
  <% end_loop %>
  <% end_if %>

  </ul>

<% end_loop %>

</td>        
        
      </tr></table>

        
    

    </div><!-- End Selected Talks Row -->
  </div>


</div>

  </body>
</html>