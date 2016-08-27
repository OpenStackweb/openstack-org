<!-- Begin Page Content -->
</div> <!-- End main container -->
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Verify a Certified OpenStack Administrator</h1>
        </div>
    </div>
</div>
<div class="verify-wrapper">
    <div class="container">
        <div class="lf-form" id="lf-verify-cert-form">
            <div class="row">
                <div class="col-sm-12">
                    <h3>Certified OpenStack Administrator Verification Tool</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <p>Individuals who become a Certified OpenStack Administrator represent the best OpenStack talent available and have the skills required to make an immediate impact.</p>
                    <p>This verification tool allows you to confirm the validity of a Certified OpenStack Administrator certification. Enter the last name and Certificate ID number in the boxes below.</p>
                    <p>Verification may take up to three business days after taking the exam to appear. If you believe these results are incorrect or to report a fraudulent certificate, please contact <a href="mailto:cert@openstack.org">cert@openstack.org</a>.</p>
                </div>
            </div>
            <div class="row">
                <div class="verify-input-wrapper">
                    <div class="col-sm-6">
                        <label>Certificate ID #:</label>
                        <input class="form-control verify-input" id="cert_id" placeholder="Ex: COA-1111-2222-3333"/>
                    </div>
                </div>
                <div class="verify-input-wrapper">
                    <div class="col-sm-6">
                        <label>Last Name on Certificate:</label>
                        <input class="form-control verify-input" id="last_name" />
                    </div>
                </div>
            </div>
            <input type="text" name="username" id="username" style="position:relative;left:-1000px;display:none;"/>
            <div id="cert_verification">
                <table>
                    <tr>
                        <td>
                            Owner:
                        </td>
                        <td>
                            <span id="result_name"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Certification:
                        </td>
                        <td>
                            <span id="result_cert"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Date of Certification:
                        </td>
                        <td>
                            <span id="result_date"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Status:
                        </td>
                        <td>
                            <span id="result_status"></span>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="cert_empty">
                Sorry, we couldn't find any certification for the provided Certificate ID# and Last Name.
            </div>
            <div class="checkbox">
                <label>
                  <input type="checkbox" id="terms" class="">
                    I agree to abide by the
                    <a href="{$Link()}#tos" class="tos-link" data-toggle="modal" data-target="#tos">Terms of Service</a>
                    for Certified OpenStack Administrator Verification Tool.
                </label>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <input type="button" id="search_exam" value="Verify" class="verify-btn">
                </div>
            </div>
        </div>
    </div>
</div>
    <div id="tos" class="modal fade" role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Terms Of Service</h4>
                </div>
                <div class="modal-body">
                    $TosText
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Page Content -->
