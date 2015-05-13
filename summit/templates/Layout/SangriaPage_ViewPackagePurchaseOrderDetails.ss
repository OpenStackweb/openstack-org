<script type="application/javascript">
    var urls = {
        approvePackagePurchaseOrder: 'api/v1/summits/packages/purchase-orders/%ID%/approve',
        rejectPackagePurchaseOrder: 'api/v1/summits/packages/purchase-orders/%ID%/reject'
    };
</script>
<h2>Summit Sponsorship Package Purchase Orders List</h2>
<form id="purchase_order_filters" name="purchase_order_filters" method="post" action="{$Top.Link(ViewPackagePurchaseOrderDetails)}">
    <select id="purchase_order_status" name="status">
        <option <%if FilterParamStatus == "pending" %>selected<% end_if %> value="pending">Pending for Approval</option>
        <option <%if FilterParamStatus == "approved" %>selected<% end_if %> value="approved">Approved</option>
        <option <%if FilterParamStatus == "rejected" %>selected<% end_if %> value="rejected">Rejected</option>
    </select>
</form>
<% if PackagesPurchaseOrder %>
    <table id="packages-purchare-orders-table">
        <thead>
        <tr>
            <th>First Name</th>
            <th>Surname</th>
            <th>Email</th>
            <th>Organization</th>
            <th>Package</th>
            <th><%if FilterParamStatus == "approved" %>Approved Date<% end_if %><%if FilterParamStatus == "rejected" %>Rejected Date<% end_if %><%if FilterParamStatus == "pending" %>Created Date<% end_if %></th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <% loop PackagesPurchaseOrder %>
        <tr>
            <td class="fname"><a id="package_purchase_order_{$ID}" href="#"></a>$FirstName</td>
            <td class="lname">$Surname</td>
            <td class="email">$Email</td>
            <td class="company-name">$Organization</td>
            <td class="package">$Package.Title</td>
            <td class="created">
                <%if $Top.FilterParamStatus == "approved" %>$ApprovedDate<% end_if %>
                <%if $Top.FilterParamStatus == "rejected" %>$RejectedDate<% end_if %>
                <%if $Top.FilterParamStatus == "pending" %>$Created<% end_if %>
            </td>
            <td width="23%">
                <%if $Top.FilterParamStatus == "pending" %>
                    <a href="#" data-purchase-order-id="{$ID}" class="reject-purchase-order roundedButton">Reject</a>
                    &nbsp;
                    <a href="#" data-purchase-order-id="{$ID}" class="approve-purchase-order roundedButton">Approve</a>
                 <% else %>
                    &nbsp;
                <% end_if %>
            </td>
        </tr>
        <% end_loop %>
        </tbody>
        </table>
        <p id="empty-message" style="display: none;">* There are not any Package Purchase Orders yet.</p>
<% else %>
    <p>* There are not any Package Purchase Orders yet.</p>
<% end_if %>
