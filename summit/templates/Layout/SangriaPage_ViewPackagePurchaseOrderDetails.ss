<script type="application/javascript">
    var urls = {
        approvePackagePurchaseOrder: 'api/v1/summits/packages/purchase-orders/%ID%/approve',
        rejectPackagePurchaseOrder: 'api/v1/summits/packages/purchase-orders/%ID%/reject'
    };
</script>
<h2>Summit Sponsorship Package Purchase Orders List</h2>
<% if PendingApprovalPackagesPurchaseOrder %>
    <table id="packages-purchare-orders-table">
        <thead>
        <tr>
            <th>First Name</th>
            <th>Surname</th>
            <th>Email</th>
            <th>Organization</th>
            <th>Package</th>
            <th>Created</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <% loop PendingApprovalPackagesPurchaseOrder %>
        <tr>
            <td class="fname"><a id="package_purchase_order_{$ID}" href="#"></a>$FirstName</td>
            <td class="lname">$Surname</td>
            <td class="email">$Email</td>
            <td class="company-name">$Organization</td>
            <td class="package">$Package.Title</td>
            <td class="created">$Created</td>
            <td width="23%">
                <a href="#" data-purchase-order-id="{$ID}" class="reject-purchase-order roundedButton">Reject</a>
                &nbsp;
                <a href="#" data-purchase-order-id="{$ID}" class="approve-purchase-order roundedButton">Approve</a>
            </td>
        </tr>
        <% end_loop %>
        </tbody>
        </table>
        <p id="empty-message" style="display: none;">* There are not any Package Purchase Orders yet.</p>
<% else %>
    <p>* There are not any Package Purchase Orders yet.</p>
<% end_if %>
