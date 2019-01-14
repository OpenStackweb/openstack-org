<% if OpenStackAvailableComponents %>
    <hr>
    <form id="components_form" name="components_form" style="margin-bottom: 30px">
    <div style="display: block;overflow-y:auto; height: 500px">
    <table style="border: 1px solid #ccc; border-collapse:collapse;clear:both;max-width:99%"  width="100%" >
        <tbody style="width: auto">
        <tr>
            <th style="border: 1px solid #ccc;text-align: center;">OpenStack-powered Capabilities Offered</th>
            <th style="border: 1px solid #ccc;text-align: center;">Mark all that apply with an X</th>
            <th style="border: 1px solid #ccc;width:9%;text-align: center;">Version of OpenStack used (e..g Grizzly, Havana)</th>
            <th style="border: 1px solid #ccc;text-align: center;">API Version Supported</th>
            <th style="border: 1px solid #ccc;text-align: center;">API supported</th>
        </tr>
        <% loop OpenStackAvailableComponents %>
        <tr style="border: 1px solid #ccc;background:#eaeaea;" width="10%">
            <td style="padding-left: 10px">$Name ($CodeName)</td>
            <td style="border: 1px solid #ccc;background:#fff;text-align:center;">
                <input type="checkbox" class="checkbox available-component" id="component_{$ID}" value="{$ID}" data-supports-versioning="{$SupportsVersioning}" name="component_{$ID}">
            </td>
            <td style="border: 1px solid #ccc;background:#fff;width:9%;text-align: center">
                <div style="display:inline-block;max-width:90%;">
                    <select style="width:100%" id="releases_component_{$ID}" name="releases_component_{$ID}" class="component-releases" data-component-id="{$ID}" data-component-supports-versioning="{$SupportsVersioning}" data-component-codename="{$CodeName}">
                    </select>
                </div>
            </td>
            <td style="border: 1px solid #ccc;background:#fff;text-align: center">
                <div style="display:inline-block;max-width:90%;">
                    <% if SupportsVersioning %>
                        <select style="width:100%" id="release_api_version_component_{$ID}" name="release_api_version_component_{$ID}" class="release-api-versions" data-component-id="{$ID}">
                            <option value="">-- select --</option>
                        </select>
                    <% else %>
                        <input type="text" name="api_coverage_amount_{$ID}" id="api_coverage_amount_{$ID}" value="N/A" style="border:0; color:#f6931f; font-weight:bold;width: 100%; max-width: 90%;text-align:center;">
                    <% end_if %>
                </div>
            </td>
            <td style="border: 1px solid #ccc;background:#fff;text-align: center">
                <% if SupportsVersioning %>
                    <select id="api_coverage_amount_{$ID}" name="api_coverage_amount_{$ID}" class="api-coverage">
                        <option value="">-- select --</option>
                        <option value="0">None</option>
                        <option value="50">Partial</option>
                        <option value="100">Full</option>
                    </select>
                <% else %>
                    <input type="text" name="api_coverage_amount_{$ID}" id="api_coverage_amount_{$ID}" value="N/A" style="border:0; color:#f6931f; font-weight:bold;width: 100%; max-width: 90%;text-align:center;">
                <% end_if %>
            </td>
        </tr>
        <% end_loop %>
    </table>
    </div>
    </form>
<% end_if %>