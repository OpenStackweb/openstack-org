<!DOCTYPE html>
<html lang="en">

<head>
    <% include Head %>    
    <% include Page_GoogleAnalytics %>
    <% include Page_MicrosoftAdvertising %>
</head>

<body>
    <div class="main-body">
        <div id="wrap">
            <!-- Begin Page Content -->
            <div class="container simple-page">
                <p>&nbsp;</p>
                <div class="row center">
                    <a href="{$Link}/back">Back</a>
	            </div>
                $Content
            </div>
            <!-- End Page Content -->
            <div id="push"></div>
        </div>
        
        <% if not CurrentSummit.RegistrationLink %>
            
            <% include RegistrationModal %>
        
        <% end_if %>
        
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
                        The OpenStack project is provided under the Apache 2.0 license. Openstack.org is powered by <a href="https://vexxhost.com" target="_blank">VEXXHOST</a>.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <% include Quantcast %>
</body>
    <% include Page_LinkedinInsightTracker %>
</html>
