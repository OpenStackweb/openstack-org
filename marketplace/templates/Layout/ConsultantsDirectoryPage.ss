<div class="grey-bar">
    <div class="container">
        <p class="filter-label">Filter Providers</p>
        <input type="text" placeholder="ANY NAME" name="name-term" id="name-term" value="{$Keyword}">
        $ServicesCombo
        $LocationCombo
        $RegionCombo
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
    <div id="consultants-list" class="col-lg-8 col-md-8 col-sm-8">
        <% if Consultants %>
            <% loop Consultants %>
                <% include ConsultantsDirectoryPage_CloudBox ConsultantLink=Top.Link %>
            <% end_loop %>
        <% else %>
            &nbsp;
        <% end_if %>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4">
        <% include MarketPlaceHelpLinks %>
        <div class="add-your-course">
            <p>
                Does your company offer consulting for OpenStack? Be listed here!<a href="mailto:ecosystem@openstack.org">Email us for details</a> or <a href="https://calendly.com/jimmy-mcarthur">put some time on our calendar</a> to meet remotely.
            </p>
        </div>
    </div>
</div>
