<!-- Begin Page Content -->
<div class="coa-hero" style="background-image: url('{$Top.HeroImageUrl}');">
    <div class="container">
        <div class="row">
            <div class="col-sm-4 center">
                <img src="/themes/openstack/images/coa/coa-badge.svg" class="coa-badge-top" alt="{$Top.BannerTitle}">
            </div>
            <div class="col-sm-8">
                <h1>$Top.BannerTitle</h1>
                $Top.BannerText
                <hr>
                <div class="coa-action-top">
                    <a href="/coa#coa-get-started" class="coa-action-btn">How To Get Started <i class="fa fa-chevron-right"></i></a>
                    <span class="coa-already-registered">Exam Fee: $Top.ExamCost</span> &nbsp; | &nbsp;
                    <a href="/coa#coa-details" class="coa-already-registered">Pricing & Exam Details</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="coa-actions-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-3 coa-action-single">
                <a href="{$Top.Link(get-started)}" class="coa-action-btn">Purchase Exam</a>
                <p>
                    Get started with a new COA exam.
                </p>
            </div>
            <div class="col-sm-3 coa-action-single">
                <a href="{$Top.Link(get-started)}" class="coa-action-btn">Redeem A Code</a>
                <p>
                    Redeem a code from a partner company and get started with your COA exam.
                </p>
            </div>
            <div class="col-sm-3 coa-action-single">
                <a href="{$Top.Link(already-registered)}" class="coa-action-btn">COA Portal Login</a>
                <p>
                    Login to retake the test or resume the process of scheduling an exam.
                </p>
            </div>
            <div class="col-sm-3 coa-action-single">
                <a href="/coa/coa-professional/" class="coa-action-btn">Hire A COA</a>
                <p>
                    Learn what COA means for talent teams or verify a candidate's COA credentials.
                </p>
            </div>
        </div>
    </div>
</div>
<div class="coa-points">
    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                <i class="fa fa-desktop fa-4x"></i>
                <h3>Virtual Exam</h3>
                <p class="coa-point-description">
                    Take the COA exam at your convenience, from anywhere in the world using our virtual proctor system
                </p>
            </div>
            <div class="col-sm-4">
                <i class="fa fa-area-chart fa-4x"></i>
                <h3>Performance-Based</h3>
                <p class="coa-point-description">
                    Test your skills and problem solving in the command line and Horizon dashboard, based on the OpenStack Liberty version
                </p>
            </div>
            <div class="col-sm-4">
                <i class="fa fa-check-square fa-4x"></i>
                <h3>Dozens of Training Partners</h3>
                <p class="coa-point-description">
                    Find help preparing for the exam from the large ecosystem of OpenStack training partners
                </p>
            </div>
        </div>
    </div>
</div>
<% if $Top.TrainingPartners %>
<div class="coa-partners">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2>Training Partners</h2>
                <p>
                    The best path to certification is through one of the OpenStack Foundation training partners. Dozens of companies around the world offer OpenStack training, ranging from 101 to advanced skills. Many of these companies bundle the COA exam with their training courses. Find the best fit for you in the <a href="http://www.openstack.org/training">OpenStack Training Marketplace</a>.
                </p>
                <div class="coa-partners-row-wrapper">
                    <div class="row">
                        <% loop $Top.TrainingPartners.Sort(Order) %>

                            <div class="col-sm-2 col-xs-3 coa-partner-logo">
                                <a href="#">
                                    <img src="{$MediumLogoUrl}" alt="{$Name}">
                                </a>
                            </div>

                        <% end_loop %>
                    </div>
                </div>

                <p>
                    Want your OpenStack training company to be listed here? Contact <a href="mailto:ecosystem@openstack.org">ecosystem@openstack.org</a>.
                </p>
            </div>
        </div>
    </div>
</div>
<% end_if %>
<div class="coa-actions-section">
    <div class="container">
        <div class="row">
            <div class="col-sm-12" id="coa-get-started">
                <h2>How to Get Started</h2>
                $Top.GetStartedText
                <p>
                    <a href="{$Top.Link(get-started)}" class="coa-started-btn">Get Started <i class="fa fa-angle-right"></i></a>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="coa-exam-details">
    <div class="container">
        <div class="row">
            <div class="col-sm-12" id="coa-details">
                <div class="row">
                    <div class="col-sm-8">
                        <h2>Exam Details</h2>
                        $Top.ExamDetails
                    </div>
                    <div class="col-sm-4">
                        <div class="coa-current-release">
                            <div class="title">
                                OpenStack release being tested
                            </div>
                            <img src="/themes/openstack/static/images/coa/liberty-logo.png" alt="" class="logo">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <h5 class="section-title"> What does the exam cost?</h5>
                        <p>$Top.ExamCost</p>
                        <h5 class="section-title">Format</h5>
                        $Top.ExamFormat
                        <h5 class="section-title">ID Requirements</h5>
                        $Top.ExamIDRequirements
                        <h5 class="section-title">Retake</h5>
                        $ExamRetake
                    </div>
                    <div class="col-sm-6">
                        <h5 class="section-title">How long do I have to schedule my exam?</h5>
                        $ExamHowLongSchedule
                        <h5 class="section-title">System Requirements</h5>
                        $Top.ExamSystemRequirements
                        <h5 class="section-title">Duration</h5>
                        <p>$Top.ExamDuration</p>
                        <h5 class="section-title">Scoring</h5>
                        $Top.ExamScoring
                        <h5 class="section-title">Language</h5>
                        $Top.ExamLanguage
                    </div>
                </div>
                <div class="coa-details-action">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="coa-action-bottom">
                                <p>
                                    Review policies and terms of service.
                                </p>
                                <p>
                                    <a href="{$Top.HandBookLink}" class="coa-details-btn">Download the handbook <i class="fa fa-cloud-download"></i></a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Page Content -->
