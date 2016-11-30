<!DOCTYPE html>
<html lang="en">
   <head>
   	  <% base_tag %>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="">
      $MetaTags(false)

      <title>Vote For $Parent.Title Summit Presentations | OpenStack Open Source Cloud Computing Software</title>
      <!-- Bootstrap Core CSS -->
      <% require css("themes/openstack/css/bootstrap3.css") %>
      <!-- Custom CSS -->
      <% require css("summit/css/voting-app.css") %>
      <!-- Fonts -->
      <% require css("themes/openstack/css/font-awesome.min.css") %>
      <link href='//fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
      <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
      <!--[if lt IE 9]>
      <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->      
      $ModuleCSS('main')      
      <!-- Page-specific CSS -->
      <% include Analytics %>
   </head>
   <body class="voting-body">
      <div class="outer-wrap">
         <!-- For FB sharing -->
         <div id="fb-root"></div>
         <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=600413776716536&version=v2.0";
            fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
         </script>
         <!-- For Google+ Sharing -->
         <script src="//apis.google.com/js/platform.js">
         	  {parsetags: 'explicit'}
         </script>
         <script src="//platform.twitter.com/widgets.js"></script>
         <div class="main-body">
            <div id="wrap">
               <!-- Begin Page Content -->
               <div class="container">
                  <div class="row">
                     <div class="voting-header">
                        <div class="col-lg-3 col-md-3 col-sm-3">
                           <div class="voting-app-logo">
                              <img class="summit-hero-logo" src="/summit/images/voting-logo.png" alt="OpenStack Summit" />
                           </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                           <div class="voting-app-title">
                              <h1>
                                 Vote For Presentations
                                 <span>
                                 Deadline: Monday, August 8 at 11:59pm PT / Tuesday, August 9 at 6:59am UTC
                                 </span>
                              </h1>
                           </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3">
                           <div class="voting-app-details">
                              <a href="/summit/" class="btn">Summit Details</a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div id="voting-app"></div>
                  <script type="text/javascript">
                  	window.VotingAppConfig = $JSONConfig;
                  </script>
				  $ModuleJS('main')
               </div>
            </div>
            <!-- End Page Content -->
         </div>
      </div>
      <% include Quantcast %>
   </body>
</html>
