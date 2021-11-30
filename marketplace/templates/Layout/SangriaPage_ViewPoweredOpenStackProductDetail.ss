<a href="/sangria/ViewPoweredOpenStackProducts"><< back</a>
<h1>$Product.Name - $Product.Company.Name ( $Product.MarketPlaceType.Name )</h1>

<a target="_blank" href="/marketplace/{$Product.MarketPlaceType.LinkPath}/{$Product.Company.URLSegment}/{$Product.Slug}">View on MarketPlace</a>

<h3>Administrators</h3>
<ul class="list-unstyled">
    <% loop $Product.AdminEmails %>
        <li>
            <a target="_blank" href="mailto:{$Email}">$Email</a>
        </li>
    <% end_loop %>
</ul>
<h3>Info</h3>
<form style="width: 50%" id="form-product-main-info" name="form-product-main-info">
    <div class="checkbox">
        <label>
            <input type="checkbox" id="required_for_compute" <% if $Product.CompatibleWithCompute %>checked<% end_if %> name="required_for_compute"> Compatible with Compute
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" id="required_for_storage" <% if $Product.CompatibleWithStorage %>checked<% end_if %> name="required_for_storage"> Compatible with Storage
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" id="federated_identity" <% if $Product.CompatibleWithFederatedIdentity %>checked<% end_if %> name="federated_identity"> Compatible with Federated Identity
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" id="uses_ironic" <% if $Product.UsesIronic %>checked<% end_if %> name="uses_ironic"> Uses Ironic
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" id="required_for_dns" <% if $Product.CompatibleWithDNS %>checked<% end_if %> name="required_for_dns"> Required for DNS
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" id="required_for_orchestration" <% if $Product.CompatibleWithOrchestration %>checked<% end_if %> name="required_for_orchestration"> Required for Orchestration
        </label>
    </div>
     <div class="checkbox">
        <label>
            <input type="checkbox" id="required_for_shared_file_system" <% if $Product.CompatibleWithSharedFileSystem %>checked<% end_if %> name="required_for_shared_file_system"> Required for Shared File System
        </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" id="required_for_platform" <% if $Product.CompatibleWithPlatform %>checked<% end_if %> name="required_for_platform"> Required for Powered Platform
        </label>
    </div>
    <div class="form-group">
        <label for="expiry_date">Expiry Date</label>
        <input type="text" class="form-control" value="{$Product.ExpiryDate}" id="expiry_date" name="expiry_date">
    </div>
    <div class="form-group">
        <label for="program_version_id">Program Version</label>
        <select id="program_version_id" class="form-control" name="program_version_id">
            <option value="">-- SELECT ONE --</option>
            <% loop Programs %>
                <option value="{$ID}" <% if $Top.Product.ProgramVersionID == $ID %>selected<% end_if %> >$Name</option>
            <% end_loop %>
        </select>
    </div>
    <div class="form-group">
        <label for="reported_release_id">Reported Release</label>
        <select id="reported_release_id" class="form-control"  name="reported_release_id">
            <option value="">-- SELECT ONE --</option>
            <% loop Releases %>
                <% if $Name != 'Trunk' %>
                    <option value="{$ID}" <% if $Top.Product.ReportedReleaseID == $ID %>selected<% end_if %> >$Name</option>
                <% end_if %>
            <% end_loop %>
        </select>
    </div>
    <div class="form-group">
        <label for="passed_release_id">Passed Release</label>
        <select id="passed_release_id" class="form-control" name="passed_release_id">
            <option value="">-- SELECT ONE --</option>
            <% loop Releases %>
                <% if $Name != 'Trunk' %>
                    <option value="{$ID}" <% if $Top.Product.PassedReleaseID == $ID %>selected<% end_if %> >$Name</option>
                <% end_if %>
            <% end_loop %>
        </select>
    </div>
    <div class="form-group">
        <label for="notes">Notes</label>
        <textarea class="form-control" id="notes" name="notes">$Product.Notes</textarea>
    </div>
    <input type="hidden" id="service_id" name="service_id" value="{$Product.ID}"/>
    <button type="submit" class="btn btn-default">Submit</button>
</form>

<h3>RefStack Links</h3>
<% if $Product.RefStackLinks %>
<table class="table">
    <% loop $Product.RefStackLinks %>
        <tr>
            <td>
                $Link
            </td>
            <td>
                <button data-id="{$ID}" data-service-id="{$Top.Product.ID}" class="delete-service-link" data-type-link="refstack">Delete</button>
            </td>
        </tr>
    <% end_loop %>
</table>
<% end_if %>

<form class="form-inline" id="add-refstack-form">
    <div class="form-group">
        <input class="form-control" id="new_refstack_link" placeholder="Add RefStack Link" name="new_refstack_link" type="text"/>
    </div>
    <button id="new_refstack_link_button" class="add-service-link btn btn-default"data-service-id="{$Product.ID}" data-type-link="refstack">Add</button>
</form>

<h3>ZenDesk Links</h3>
<% if $Product.ZenDeskLinks %>
<table class="table">
    <% loop $Product.ZenDeskLinks %>
        <tr>
            <td>
                $Link
            </td>
            <td>
                <button data-id="{$ID}" data-service-id="{$Top.Product.ID}" data-type-link="zendesk" class="delete-service-link">Delete</button>
            </td>
        </tr>
    <% end_loop %>
</table>
<% end_if %>
<form class="form-inline" id="add-zendesk-form">
    <div class="form-group">
        <input class="form-control" id="new_zendesk_link" placeholder="Add ZenDesk Link" name="new_zendesk_link" type="text"/>
    </div>
    <button id="new_zendesk_link_button" class="add-service-link btn btn-default" data-service-id="{$Product.ID}" data-type-link="zendesk">Add</button>
</form>
<h3>Data History (OpenStack Powered)</h3>
<table class="table">
    <thead>
        <th>
            Date
        </th>
        <th>
            Author
        </th>
        <th>
            From
        </th>
        <th>
            To
        </th>
    </thead>

    <% loop $Product.StateSnapshots.Sort(Created,DESC) %>
        <tr>
            <td>
                $Created
            </td>
            <td>
                <a target="_blank" href="mailto:{$Owner.Email}">$Owner.Email</a>
            </td>
            <td>
                <ul class="list-unstyled">
                    <li>
                       compatible with compute: <% if $CompatibleWithComputeBefore > 0  %>Yes<% else %>No<% end_if %>
                    </li>
                    <li>
                        compatible with storage: <% if $CompatibleWithStorageBefore > 0  %>Yes<% else %>No<% end_if %>
                    </li>
                    <li>
                        expiry date: $ExpiryDateBefore
                    </li>
                    <li>
                        program version: $ProgramVersionNameBefore
                    </li>
                    <li>
                        reported release: $ReportedReleaseNameBefore
                    </li>
                    <li>
                        passed release: $PassedReleaseNameBefore
                    </li>
                    <li>
                        notes: $NotesBefore
                    </li>
                </ul>
            </td>

            <td>
                <ul class="list-unstyled">
                    <li>
                        compatible with compute: <% if $CompatibleWithComputeCurrent > 0  %>Yes<% else %>No<% end_if %>
                    </li>
                    <li>
                        compatible with storage: <% if $CompatibleWithStorageCurrent > 0  %>Yes<% else %>No<% end_if %>
                    </li>
                    <li>
                        expiry date: $ExpiryDateCurrent
                    </li>
                    <li>
                        program version: $ProgramVersionNameCurrent
                    </li>
                    <li>
                        reported release: $ReportedReleaseNameCurrent
                    </li>
                    <li>
                        passed release: $PassedReleaseNameCurrent
                    </li>
                    <li>
                        notes: $NotesCurrent
                    </li>
                </ul>
            </td>
        </tr>
    <% end_loop %>
</table>