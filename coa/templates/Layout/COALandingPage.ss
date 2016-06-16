<!-- Begin Page Content -->
<div class="coa-hero">
    <div class="container">
        <div class="row">
            <div class="col-sm-4 center">
                <img src="/themes/openstack/images/coa/coa-badge.svg" class="coa-badge-top" alt="Certified OpenStack Administrator">
            </div>
            <div class="col-sm-8">
                <h1>Certified OpenStack Administrator</h1>
                $Top.BannerText
                <hr>
                <div class="coa-action-top">
                    <a href="{/coa#coa-get-started" class="coa-action-btn">How To Get Started <i class="fa fa-chevron-right"></i></a>
                    <span class="coa-already-registered">Exam Fee: $300</span> &nbsp; | &nbsp; 
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
                <a href="#" class="coa-action-btn">Redeem A Code</a>
                <p>
                    Redeem a code from a partner company and get started with your COA exam.
                </p>
            </div>
            <div class="col-sm-3 coa-action-single">
                <a href="http://www.openstack.org/coa/already-registered" class="coa-action-btn">COA Portal Login</a>
                <p>
                    Login to retake the test or resume the process of scheduling an exam.
                </p>
            </div>
            <div class="col-sm-3 coa-action-single">
                <a href="#" class="coa-action-btn">Hire A COA</a>
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
                    Test your skills and problem solving in the command line and Horizon dashboard
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
                <p>
                    The Certified OpenStack Administrator exam is the only professional certification offered by the OpenStack Foundation. It was written for OpenStack professionals with at least six months of experience managing an OpenStack cloud environment. You can learn more details about the exam below, or visit our Training Marketplace to find companies that can help you prepare and often bundle the exam with their training courses. To get started with a new exam purchase or to redeem a code, you'll be prompted to log into the COA portal with an OpenStackID or equivalent.
                </p>
                <p>
                    <a href="#" class="coa-started-btn">Get Started <i class="fa fa-angle-right"></i></a>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="coa-exam-details">
    <div class="container">
        <div class="row">
            <div class="col-sm-12" id="coa-details">
                <h2>Exam Details</h2>
                $Top.ExamDetails
                <div class="row">
                    <div class="col-sm-6">
                        <h5 class="section-title"> What does the exam cost?</h5>
                        <p>
                            $300
                        </p>
                        <h5 class="section-title">Format</h5>
                        <p>
                            The COA is a performance-based exam and Candidates will need to perform tasks or solve problems using the command line interface and Horizon dashboard. For exam security, Candidates are monitored virtually by a proctor during the exam session via streaming audio, video, and screensharing feeds. The screensharing feed allows proctors to view candidates' desktops (including all monitors). The audio, video and screensharing feeds will be stored for a limited period of time in the event that there is a subsequent need for review.
                        </p>
                        <h5 class="section-title">ID Requirements</h5>
                        <p>
                            Candidates are required to provide a means of photo identification before the Exam can be launched. Acceptable forms of photo ID include current, non-expired: passport, government-issued driver's license/permit, national ID card, state or province-issued ID card, or other form of government issued identification. If acceptable proof of identification is not provided to the exam proctor prior to the exam, entry to the exam will be refused. Candidates who are refused entry due to lack of sufficient ID will not be eligible for a refund or rescheduling.
                        </p>
                        <h5 class="section-title">Retake</h5>
                        <p>
                            One (1) free retake per Exam purchase will be granted in the event that a passing score is not achieved and Candidate has not otherwise been deemed ineligible for Certification or retake. The free retake must be taken within 12 months of the date of the original Exam purchase.
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <h5 class="section-title">How long do I have to schedule my exam?</h5>
                        <p>
                            Exam may be scheduled anytime within <strong>12 months</strong> of purchase.
                        </p>
                        <h5 class="section-title">System Requirements</h5>
                        <p>
                            Candidates are required to provide their own front-end hardware (laptop or workstation) with Chrome or Chromium browser, reliable internet access, and a webcam and microphone in order to take exams. Candidates do not need to provide their own Linux installation or VM; they will be presented with a VM in their browser window using a terminal emulator. Candidates should use the <a href="https://www.examslocal.com/ScheduleExam/Home/CompatibilityCheck" target="_blank">compatibility check tool</a> to verify that their system and testing environment meet the minimum requirements.
                        </p>
                        <h5 class="section-title">Duration</h5>
                        <p>
                            2.5 hours
                        </p>
                        <h5 class="section-title">Scoring</h5>
                        <p>
                            Upon completion, exams are scored automatically and a score report will be made available within three (3) business days. If a passing score of 76 or higher is achieved and other applicable requirements for Certification have been fulfilled, a notification indicating the Candidate has been successfully Certified will follow the score report. Candidate will receive a certificate and logo for personal use.
                        </p>
                        <h5 class="section-title">Language</h5>
                        <p>
                            The COA exam is currently offered in English.
                        </p>
                    </div>
                </div>
                <div class="coa-details-action">
                    <div class="row">
                        <div class="col-sm-6 left">
                            <div class="coa-action-bottom">
                                <p>
                                    Review policies and terms of service.
                                </p>
                                <p>
                                    <a href="{$Top.HandBookLink}" class="coa-details-btn">Download the handbook <i class="fa fa-cloud-download"></i></a>
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="coa-action-bottom">
                                <p>
                                    Have a code to redeem?
                                </p>
                                <p>
                                    <a href="{$Top.Link(get-started)}" class="coa-details-btn red">Get Started <i class="fa fa-chevron-right"></i></a>
                                </p>
                                <a href="{$Top.Link(already-registered)}" class="coa-already-registered">Already Registered?</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Page Content -->
