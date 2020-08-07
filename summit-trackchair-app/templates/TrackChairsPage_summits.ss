<!DOCTYPE html>
<html>

<head>
<% base_tag %>
<title>$PageTitle</title>
<meta charset= "utf-8" >
<meta name= "viewport" content= "width=device-width, initial-scale=1.0" >
<link rel= "stylesheet" type= "text/css" href= "https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" >
<link rel= "stylesheet" type= "text/css" href= "https://fonts.googleapis.com/css?family=Roboto:400,300,500,700" >
</head>

<body id="$URLSegment">
<!-- Page Content -->
<div class="container">
  <div class="row">
    <div class="col-md-6"><a class="btn btn-default" role="button" href="/Security/logout">Logout</a></div>
  </div>
  $Message
  $Layout
</div>
</body>
</html>