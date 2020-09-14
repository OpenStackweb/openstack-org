<!DOCTYPE html>
<html lang="en">

<head>
<% include Head %>
<% include Page_GoogleAnalytics %>
<% include Page_MicrosoftAdvertising %>
$FBTrackingCode
$TwitterTrackingCode
<!-- GoogleAdWords Start -->
$GATrackingCode
<!-- GoogleAdWords End -->
</head>

<body>
<!-- Begin Page Content -->
$Layout
<!-- End Page Content -->
<% include TwitterUniversalWebsiteTagCode %>


<div id="orderModal" class="modal fade" role="dialog">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title">Thanks for Registering! What's Next?</h4>
</div>
<div class="modal-body">
<p><b><a href="https://www.openstack.org/summit/vancouver-2020/travel/">Book your hotel room now</a></b> through one of our recommended hotels.</p>
<p>An email receipt with details of your Summit registration and purchase will be sent to the address that you registered with.</p>
</div>
</div>
</div>
</div>
</body>

<script>
var order_complete = false;
<% if $getOrder() %>
order_complete = {$getOrder()};
<% end_if %>

$(document).ready(function() {

                  if (order_complete) {
                  $('#orderModal').modal('show');
                                         }
                                         });
                    </script>

                    </html>
