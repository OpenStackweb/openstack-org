<% if Capabilities %>
    <script  type="text/javascript">
        var coverages = [];
    </script>
    <div class="row">
        <div class="col-sm-12">
            <div class="services-table-wrapper">
                <h3 style="color: #000000 !important;">OpenStack Services Enabled</h3>
                <table class="marketplace-services-table">
                    <tbody>
                        <tr>
                            <th>Service</th>
                            <th>Release</th>
                            <th>API Coverage</th>
                        </tr>
                        <% loop Capabilities %>
                            <script type="text/javascript">
                                coverages.push($CoveragePercent);
                            </script>
                            <tr>
                                <td>
                                    <% loop ReleaseSupportedApiVersion %>
                                        <% if ApiVersion %>
                                            <% loop OpenStackComponent %>
                                                $Name API <% if SupportsExtensions %> & Extensions<% end_if %>
                                            <% end_loop %>
                                        <% else %>
                                            <% loop OpenStackComponent %>
                                                $Name
                                            <% end_loop %>
                                        <% end_if %>
                                    <% end_loop %>
                                </td>
                                <td>
                                    <% loop ReleaseSupportedApiVersion %>
                                        <% with Release %>$Name<% end_with %>
                                        (<% with OpenStackComponent %>$CodeName<% end_with %><% with ApiVersion %> $Version<% end_with %>)
                                    <% end_loop %>
                                </td>
                                <td>
                                    $CoverageForFrontEnd
                                </td>
                            </tr>
                        <% end_loop %>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<% end_if %>

