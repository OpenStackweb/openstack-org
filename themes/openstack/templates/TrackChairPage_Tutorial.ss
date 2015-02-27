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

	<% loop SelectedTalkList %>
    <script type="text/javascript">
      var selectedTalkListID = {$ID};
      var processingLink = "{$Top.Link}SaveSortOrder/{$ID}/?";
    </script>
    <% end_loop %>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>    
	<script type="text/javascript" src="/{$ThemeDir}/javascript/trackchair/sorting.js"></script>
  </head>
  
  <body class="index">
    <!-- ========== Title Bar ========== -->
<div class='container'>

  <div class="row">
  <div class='col-lg-1'></div>
  <div class='col-lg-11'>
  <h2 class='title'>Quick Tutorial</h2>
  <div id="info" style="display:none;"></div>
  </div>
  <div>

  <div class='row'>
    <div class='col-lg-1' id='left-sidebar'>
      <% include TrackChairsSideNav %>
    </div>
    <div class='col-lg-11'>
      <div class='row'>
  <div class='col-lg-8'>

<iframe width="640" height="480" src="//www.youtube.com/embed/kBs6D4e1_XI?rel=0" frameborder="0" allowfullscreen></iframe>

</div>
<div class='col-lg-4'>
<h4>Tutorial</h4>
<p>A quick (5-minute) overview for using the Track Chair tool.</p>
<h4>More Helpful Information</h4>
    <p><strong>Summit Schedule:</strong> You can use this <a href="https://docs.google.com/spreadsheets/d/1VHeTsRIEPv14OWf29m3EjeeK3ZM5hiIQc2GRf0wAsKE/edit?usp=sharing">Draft Session Schedule</a> to help understand where their sessions will fall on the 4 day main conference schedule. </p>
    <p><strong>Email List:</strong> The group alias for track chairs is <a href="mailto:openstack-track-chairs@lists.openstack.org">openstack-track-chairs@lists.openstack.org</a>.</p>
</div>

</div>

    </div>
  </div>


</div>

  </body>
</html>