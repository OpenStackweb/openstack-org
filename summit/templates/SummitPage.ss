<!DOCTYPE html>
<html lang="en" $OGNS>

<head>
    <% include Head %>
    <% include Analytics %>
    $FBTrackingCode
    $TwitterTrackingCode
    <link rel="stylesheet" type="text/css" href="/themes/openstack/static/css/tooltipster.css" />
</head>

<body>
<div class="main-body">
    <div  class="summit-hero-wrapper<% if $top_section != 'full' %> condensed<% end_if %><% if HeroCSSClass %> $HeroCSSClass<% end_if %>" <% if $SummitImage %>style="background: rgba(0, 0, 0, 0) url('{$SummitImage.Image.link}') no-repeat scroll center bottom / cover ;"<% end_if %> >
        <div class="container">
            <div class="row">
                <% with $Summit %>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <a href="/summit">
                            <img class="summit-hero-logo" src="/summit/images/summit-logo-small.svg" onerror="this.onerror=null; this.src='/summit/images/summit-logo-small.png'" alt="OpenStack Summit">
                        </a>
                        <h2>
                            $DateLabel
                        </h2>
                        <h1>
                            $Title
                        </h1>
                        <div class="landing-action">
                            <% if $RegistrationLink %>
                                <a href="{$RegistrationLink}" class="btn register-btn-lrg">Register Now</a>
                            <% end_if %>
                            <% if $ComingSoonBtnText %>
                                <button class="btn register-btn-lrg soon" href="#">{$ComingSoonBtnText}</button>
                            <% end_if %>
                        </div>
                        <% if IsUpComing %>
                            <div class="inner-countdown-wrapper">
                                <div class="countdown">
                                    $Top.CountdownDigits
                                </div>
                                <div class="countdown-text">
                                    Days until $Name
                                </div>
                            </div>
                        <% else_if IsCurrent %>
                            <div class="inner-countdown-wrapper">
                                <div class="countdown">
                                    <span>N</span>
                                    <span>O</span>
                                    <span>W</span>
                                </div>
                                <div class="countdown-text">
                                    The Summit is Happening Now!
                                </div>
                            </div>
                        <% end_if %>
                    </div>
                <% end_with %>
            </div>
            <a href="#" class="open-panel"><i class="fa fa-bars fa-2x collapse-nav"></i></a>
        </div>
        <div class="hero-tab-wrapper">
            <!-- Microsite Navigation -->
            <% include SummitNav %>
            <!-- End Microsite Navigation -->
        </div>
        <% if $SummitImage %><a href="#" class="photo-credit" data-toggle="tooltip" data-placement="left" title="{$SummitImage.Attribution}"><i class="fa fa-info-circle"></i></a><% end_if %>
    </div>
    <div class="mobile-city-date">
        <% with $Summit %>
            <h2>$DateLabel</h2>
            <h1>$Title</h1>

            <% if $RegistrationLink %>
                <a href="{$RegistrationLink}" class="btn register-btn-lrg">Register Now</a>
            <% end_if %>
            <% if $ComingSoonBtnText %>
                <button class="btn register-btn-lrg soon" href="#">{$ComingSoonBtnText}</button>
            <% end_if %>
        <% end_with %>
    </div>
    <div id="wrap">

        <!-- Begin Page Content -->
        $Layout
        <!-- End Page Content -->
        <div id="push"></div>
    </div>

    <% if not Summit.RegistrationLink %>

        <% include RegistrationModal %>

    <% end_if %>

    <% include DownloadAppModal %>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row footer-links">
                <div class="col-lg-2 col-sm-2">
                    <h3>OpenStack</h3>
                    <ul>
                        <li><a href="/foundation">About The Foundation</a></li>
                        <li><a href="http://openstack.org/projects/">Projects</a></li>
                        <li><a href="http://openstack.org/projects/openstack-security/">OpenStack Security</a></li>
                        <li><a href="http://openstack.org/projects/openstack-faq/">Common Questions</a></li>
                        <li><a href="http://openstack.org/blog/">Blog</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-sm-2">
                    <h3>Community</h3>
                    <ul>
                        <li><a href="http://openstack.org/community/">User Groups</a></li>
                        <li><a href="http://openstack.org/community/events/">Events</a></li>
                        <li><a href="http://openstack.org/community/jobs/">Jobs</a></li>
                        <li><a href="http://openstack.org/foundation/companies/">Companies</a></li>
                        <li><a href="https://wiki.openstack.org/wiki/How_To_Contribute">Contribute</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-sm-2">
                    <h3>Documentation</h3>
                    <ul>
                        <li><a href="http://docs.openstack.org">OpenStack Manuals</a></li>
                        <li><a href="http://openstack.org/software/start/">Getting Started</a></li>
                        <li><a href="http://developer.openstack.org">API Documentation</a></li>
                        <li><a href="https://wiki.openstack.org">Wiki</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-sm-2">
                    <h3>Branding & Legal</h3>
                    <ul>
                        <li><a href="http://openstack.org/brand/">Logos & Guidelines</a></li>
                        <li><a href="http://openstack.org/brand/openstack-trademark-policy/">Trademark Policy</a></li>
                        <li><a href="http://openstack.org/privacy/">Privacy Policy</a></li>
                        <li><a href="https://wiki.openstack.org/wiki/How_To_Contribute#Contributor_License_Agreement">OpenStack CLA</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-sm-4">
                    <h3>Stay In Touch</h3>
                    <a href="https://twitter.com/OpenStack" target="_blank" class="social-icons footer-twitter"></a>
                    <a href="https://www.facebook.com/openstack" target="_blank" class="social-icons footer-facebook"></a>
                    <a href="https://www.linkedin.com/company/openstack" target="_blank" class="social-icons footer-linkedin"></a>
                    <a href="https://www.youtube.com/user/OpenStackFoundation" target="_blank" class="social-icons footer-youtube"></a>
                    <!-- <form class="form-inline">
                        <div class="form-group newsletter-form">
                            <label>Join Our Newsletter</label>
                            <input class="newsletter-input" type="input" placeholder="Email">
                            <button type="submit" class="newsletter-btn">Join</button>
                        </div>
                    </form> -->
                    <p class="fine-print">
                        The OpenStack project is provided under the Apache 2.0 license. Openstack.org is powered by <a href="http://rackspace.com" target="_blank">Rackspace Cloud Computing</a>.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Hidden Sidebar Nav -->
    <div class="sidebar-nav">
        <nav>
            <a href="#" class="close-panel"><i class="icon-remove-sign icon-large"></i></a>
            <ul class="sidebar-menu">
                <!-- Microsite Navigation -->

                <% include SummitNav %>

                <!-- End Microsite Navigation -->
            </ul>

            <% if $Summit.RegistrationLink %>
                <a href="$Summit.RegistrationLink" class="btn register-btn-lrg">Register Now</a>
            <% end_if %>

        </nav>
    </div>
</div>
    <% include Quantcast %>
</body>

<script type="text/javascript" src="/themes/openstack/static/js/jquery.tooltipster.min.js"></script>
<script>
    $(document).ready(function() {
        $('.tracks-tooltip').tooltipster({
            maxWidth: '300'
        });
    });
</script>

</html>
