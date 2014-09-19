<div class="grey-bar">
    <div class="container">
        <p class="filter-label">Filter Consultants</p>
        <input type="text" placeholder="ANY NAME"     name="name-term"     id="name-term">
        $ServicesCombo
        $LocationCombo
    </div>
</div>
<script type="text/javascript" >
    var offices = $AllOfficesLocationsJson;
</script>
<div class="container">
    <a href="#" id="show-map" style="display: none;">show map</a>
</div>
<div class="container">
    <!--map-->
    <div style="width: 100%; height: 400px; position: relative;"  id="map" tabindex="0">
    </div>
    <!--end of map-->
    <div id="consultants-list" class="span-18">
        <% if Consultants %>
            <% loop Consultants %>
                <% include ConsultantsDirectoryPage_CloudBox ConsultantLink=Top.Link %>
            <% end_loop %>
        <% else %>
            &nbsp;
        <% end_if %>
    </div>
    <div class="span-6 last">
        <h3>OpenStack Online Help</h3>
        <ul class="resource-links">
            <li>
                <a href="http://docs.openstack.org/">Online Docs</a>
            </li>
            <li>
                <a href="http://docs.openstack.org/">Operations Guide</a>
            </li>
            <li>
                <a href="http://docs.openstack.org/">Security Guide</a>
            </li>
            <li>
                <a href="http://docs.openstack.org/">Getting Started</a>
            </li>
        </ul>
        <div class="add-your-course">
            <p>
                Does your company offer training for OpenStack? Be listed here!
                <a href="mailto:info@openstack.org">Email us for details</a>
            </p>
        </div>
    </div>
</div>