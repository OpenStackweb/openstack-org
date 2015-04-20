<h2>OpenStack Powered</h2>
<table style="width: 50% !important;">
    <tr>
        <td width="30%">
            <label>Required for Compute</label><input title="full compatible API with OpenStack Compute" type="checkbox" id="compatible_compute" name="compatible_compute"/>
        </td>
   </tr>
    <tr>
        <td width="30%">
            <label>Required for Storage</label><input title="full compatible API with OpenStack Storage" type="checkbox" id="compatible_storage" name="compatible_storage"/>
        </td>
    </tr>
    <tr>
        <td width="30%">
            <% if InteropProgramVersions %>
                <label>Program Version Compatibility</label>
                <ul>
                <% loop  InteropProgramVersions %>
                    <li>
                        <label>$Name</label>
                        <input id="interop_program_version_{$ID}" value="$ID" name="interop_program_version[]" class="interop-program-version" data-version-id="{$ID}" type="checkbox"/>
                    </li>
                <% end_loop %>
                </ul>
            <% end_if %>
        </td>
    </tr>
</table>
<h2>OpenStack Federated Identity</h2>
<label for="compatible_federated_identity">Compatible with Federated Identity</label>
<input title="supports OpenStack Federated Identity" type="checkbox" id="compatible_federated_identity" name="compatible_federated_identity"><BR><BR>