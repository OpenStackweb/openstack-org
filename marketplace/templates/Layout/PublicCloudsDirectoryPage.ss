<div class="grey-bar">
    <div class="container">
        <p class="filter-label">Filter Clouds</p>
        <input type="text" placeholder="ANY NAME"     name="name-term"     id="name-term">
        $ServicesCombo
        $LocationCombo
    </div>
</div>

<div class="container">
    <a href="#" id="show-map" style="display: none;">show map</a>
</div>
<!--end of map-->
<div class="container">
    <!--map-->
    <script type="application/javascript">
        var dc_locations = $DataCenterLocationsJson;
    </script>

    <div style="width:100%; height: 400px; position: relative;" id="map" tabindex="0">
    </div>
    <div id="public-clouds-list" class="span-18">
       <% if Clouds %>
           <% loop Clouds %>
               <% include CloudsDirectoryPage_CloudBox CloudLink=$Top.Link %>
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