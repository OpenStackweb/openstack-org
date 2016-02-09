<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Vote For $Summit.Name Summit Presentations | OpenStack Open Source Cloud Computing Software</title>

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

    <!-- Page-specific CSS -->

    <% include Analytics %>

</head>

<body class="voting-body">
<div class="outer-wrap">


    <!-- For FB sharing -->
    <div id="fb-root"></div>
    <script>(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=600413776716536&version=v2.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

    <!-- For Google+ Sharing -->
    <script src="//apis.google.com/js/platform.js" async defer></script>


    <div class="main-body">
        <div id="wrap">

            <!-- Begin Page Content -->
            <a href="#" class="voting-open-panel"><i class="fa fa-bars fa-2x collapse-voting-nav"></i></a>

            <div class="container">
                <div class="row">
                    <div class="voting-header">
                        <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="voting-app-logo">
                                <img class="summit-hero-logo" src="/summit/images/voting-logo.png"
                                     alt="OpenStack Summit"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="voting-app-title">
                                <h1>
                                    Vote For Presentations
						<span>
							Help us pick the presentations for The $Summit.Name Summit
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

                <!-- TOP BAR AREA ----------------------------------->
                <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 voting-sidebar">
                    <div class="voting-app-details-link">
                        <a href="#">More About The $Summit.Name Summit</a>
                    </div>
                    $SearchForm

                    <!-------------------------------------------------->
                    <!-- NORMAL MODE ----------------------------------->
                    <!-------------------------------------------------->

                    <% if SearchMode != TRUE %>

                        <!-- DROP DOWN FOR CATEGORIES ----------------------------------->
                        <div class="btn-group voting-dropdown">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                <% if $CategoryName %>
                                    $CategoryName
                                <% else %>
                                    All Presentations
                                <% end_if %>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href='{$Top.Link}Category/All'>All Categories</a>
                                </li>
                                <li class='divider'></li>

                                <% loop CategoryLinks %>
                                    <li>
                                        <a href='{$Top.Link}Category/{$ID}'>$Name</a>
                                    </li>
                                <% end_loop %>
                            </ul>
                        </div>


                        <!-- PRESENTATION LIST / BROWSER ----------------------------------->
                        <% if $CategoryName %>
                            <h5>Presentations In This Category</h5>
                        <% else %>
                            <h5>Showing All Presentations</h5>
                        <% end_if %>
                        <ul class="presentation-list">
                            <% if $CurrentMember %>
                                <% loop $CurrentMember.getRandomisedPresentations($CategoryID) %>

                                    <li
                                        <% if $Top.Presentation.ID == $ID %>
                                        class="active"
                                        <% else_if $UserVote %>
                                        class="completed"
                                        <% end_if %>

                                    ><a href="{$Top.Link}presentation/{$ID}">$Title</a></li>
                                <% end_loop %>
                            <% else %>

                                <% loop $LoggedOutPresentationList($CategoryID) %>

                                    <li
                                        <% if $Top.Presentation.ID == $ID %>
                                        class="active"
                                        <% end_if %>

                                    ><a href="{$Top.Link}presentation/{$ID}">$Title</a></li>
                                <% end_loop %>


                            <% end_if %>
                        </ul>
                    </div>

                        <!-- PRESENTATION DISPLAY ----------------------------------->
                    <div class="col-lg-9 col-md-9 col-sm-9 voting-content-body-wrapper">
                        <a href="#" class="voting-open-panel text"><i class="fa fa-chevron-left"></i>All Submissions</a>
                        <% loop Presentation %>

                    <div class="voting-content-body">

                        <% if Summit.isVotingOpen %>
                            <% if not $CurrentMember %>
                                <% include PresentationVotingPage_LogIn BackUrl=$Top.Link, PresentationID=$ID, SummitName=$Summit.Name %>
                            <% else %>
                                <% include PresentationVotingPage_CastYourVote TopLink=$Top.Link, PresentationID=$ID, VoteValue=$Top.VoteValue %>
                            <% end_if %>
                        <% else %>
                            <% include PresentationVotingPage_closed %>
                        <% end_if %>

                        <div class="voting-presentation-title">
                            <h5>Title</h5>

                            <h3>$Title</h3>
                        </div>
                        <div class="voting-presentation-track">
                            <h5>Track</h5>

                            <p>$Category.Title</p>
                        </div>
                        <div class="voting-presentation-body">

                            <p>
                            <h5>Abstract</h5>
                            </p>

                            <div>$ShortDescription</div>

                            <p>
                            <h5>Problems Addressed</h5>
                            </p>

                            <div>$ProblemAddressed</div>

                            <p>
                            <h5>Why Should This Presentation Be Selected?</h5>
                            </p>

                            <div>$SelectionMotive</div>

                            <p>
                            <h5>What Should Attendees Expect To Learn?</h5>
                            </p>

                            <div>$AttendeesExpectedLearnt</div>

                            <div class="main-speaker-wrapper">
                                <% loop Speakers %>
                                    <div class="main-speaker-row">
                                        <div class="voting-speaker-name">
                                            $FirstName $LastName
                                            <span>$Title</span>
                                        </div>
                                        <img class="voting-speaker-pic"
                                             src="<% if $Photo.SetRatioSize(80,80).URL %>$Photo.SetRatioSize(80,80).URL<% else %>/themes/openstack/images/generic-profile-photo.png<% end_if %>"/>
                                    </div>
                                    <div class="main-speaker-description">
                                        $Bio
                                    </div>
                                <% end_loop %>
                            </div>
                        </div>

                        <% if Summit.isVotingOpen %>
                            <% if $CurrentMember %>
                                <% include PresentationVotingPage_CastYourVote TopLink=$Top.Link, PresentationID=$ID, VoteValue=$Top.VoteValue %>
                                <div class="voting-tip">
                                    <strong>TIP:</strong> You can vote quickly with your keyboard using the numbers below each
                                    option.
                                </div>
                            <% end_if %>
                        <% end_if %>
                    <div class="voting-share-wrapper">
                        <h5>Share This Presentation</h5>
                        <div class="sharing-section">
                            <div class="single-voting-share">
                                <a href="https://twitter.com/share" class="twitter-share-button"
                                   data-count="none">Tweet</a>
                                <script>!function (d, s, id) {
                                    var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                                    if (!d.getElementById(id)) {
                                        js = d.createElement(s);
                                        js.id = id;
                                        js.src = p + '://platform.twitter.com/widgets.js';
                                        fjs.parentNode.insertBefore(js, fjs);
                                    }
                                }(document, 'script', 'twitter-wjs');</script>
                            </div>
                            <div class="single-voting-share">
                                <div class="g-plus" data-action="share" data-annotation="none"></div>
                            </div>
                            <div class="single-voting-share fb-share">
                                <div class="fb-share-button" data-href="https://developers.facebook.com/docs/plugins/"
                                     data-layout="button"></div>
                            </div>


                        </div>
                    </div>
                </div>
                    <% end_loop %>
                </div>

                    <!-------------------------------------------------->
                    <!-- SEARCH MODE ----------------------------------->
                    <!-------------------------------------------------->

                    <% else %>

                    <!-- PRESENTATION LIST / BROWSER ----------------------------------->
                    <h5>Search Results</h5>
                    <ul class="presentation-list">
                        <% loop $SearchResults %>

                            <li><a href="{$Top.Link}presentation/{$ID}">$Title</a></li>
                        <% end_loop %>
                    </ul>
                </div>

                <div class="col-lg-9 col-md-9 col-sm-9 voting-content-body-wrapper">
                    <a href="#" class="voting-open-panel text"><i class="fa fa-chevron-left"></i>Search Results</a>

                    <p>Select an entry on the left to see the details here.</p>
                </div>

                <% end_if %>


            </div>
        </div>

        <!-- End Page Content -->
    </div>


    <script>
        function goBack() {
            window.history.back()
        }
    </script>

</div>
</div>
    <% include Quantcast %>
</body>

</html>
