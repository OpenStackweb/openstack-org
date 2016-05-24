<!-- Begin Page Content -->
<div class="container">
    <h1>Verify a Certified OpenStack Administrator</h1>
    <div class="lf-form" id="lf-verify-cert-form">
        <p style="font-size: 18px; font-weight: bold; cursor: default;">Certified OpenStack Administrator Verification Tool</p>
        <p style="font-size: 18px; cursor: default;">Individuals who become a Certified OpenStack Administrator represent the best OpenStack talent available and have the skills required to make an immediate impact.</p>
        <p style="margin: 25px 0px; cursor: default;">This verification tool allows you to confirm the validity of a job candidate's OpenStack Foundation Certification. Just enter the last name and Certificate ID number in the boxes below.</p>

        <div class="row">
            <div class="col-md-2"><label>Certificate ID #:</label></div>
            <div class="col-md-3">
                <input class="form-control" id="cert_id" placeholder="Ex: COA-1111-2222-3333"/>
            </div>
        </div>
        <div class="row" style="margin-top:20px;">
            <div class="col-md-2"><label>Last Name on Certificate:</label></div>
            <div class="col-md-3">
                <input class="form-control" id="last_name" />
            </div>
        </div>
        <input type="text" name="username" id="username" style="position:relative;left:-1000px"/>
        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <input type="checkbox" id="terms" class="">
                <label for="terms">
                    I agree to abide by the
                    <a href="{$Link()}#tos" class="tos-link" data-toggle="modal" data-target="#tos">Terms of Service</a>
                    for Certified OpenStack Administrator Verification Tool.
                </label>
            </div>
        </div>
        <div class="row" style="margin-top:20px;">
            <div class="col-md-12">
                <input type="button" id="search_exam" value="Verify" class="btn btn-lg btn-default">
            </div>
        </div>

        <div id="cert_verification">
            <p>
                <b>Owner:</b> <span id="result_name"></span><br>
                <b>Certification:</b> <span id="result_cert"></span><br>
                <b>Date of Certification Achievement:</b> <span id="result_date"></span><br>
                <b>Status:</b> <span id="result_status"></span><br>
            </p>
        </div>
        <div id="cert_empty">
            Sorry, we couldn't find any certification for the provided Certificate ID# and Last Name.
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
