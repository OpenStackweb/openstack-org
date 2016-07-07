<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Track Chairs App | OpenStack.org</title>
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:400,300,500,700">
	<% if not $WebpackDevServer %>
	    <link rel="stylesheet" type="text/css" href="summit-trackchair-app/production/css/main.css">
	<% end_if %>

</head>

<body class="top-navigation">
    <div id="trackchair-app"></div>
    <script type="text/javascript">
        window.TrackChairAppConfig = $JSONConfig;
    </script>
	<% if $WebpackDevServer %>
	    <script type="text/javascript" src="http://127.0.0.1:3000/production/js/main.js"></script>
	<% else %>
		<script type="text/javascript" src="summit-trackchair-app/production/js/main.js"></script>    
	<% end_if %>
   </body>
</html>