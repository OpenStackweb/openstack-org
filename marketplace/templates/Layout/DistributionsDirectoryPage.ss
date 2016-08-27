<div class="grey-bar">
    <div class="container">
        <p class="filter-label">Filter Products</p>
        <input type="text" placeholder="ANY NAME"     name="name-term"     id="name-term">
        $ServicesCombo
    </div>
</div>
<div class="container">
    <div id="implementation-list" class="col-lg-8 col-md-8 col-sm-8">
        <% if getImplementations %>
            <% loop getImplementations %>
                <% include DistributionsDirectoryPage_ImplementationBox ApplianceLink=$Top.Link(appliance),  DistroLink=$Top.Link(distribution) %>
            <% end_loop %>
        <% else %>
            &nbsp;
        <% end_if %>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4">
        <h3>OpenStack Online Help</h3>
        <ul class="resource-links">
            <li>
                <a href="http://docs.openstack.org/">Online Docs</a>
            </li>
            <li>
                <a href="http://docs.openstack.org/ops/">Operations Guide</a>
            </li>
            <li>
                <a href="http://docs.openstack.org/sec">Security Guide</a>
            </li>
            <li>
                <a href="http://www.openstack.org/software/start/">Getting Started</a>
            </li>
        </ul>
        <div class="add-your-course">
            <p>
                Does your company offer distributions or appliances for OpenStack? Be listed here!
                <a href="mailto:info@openstack.org">Email us for details</a>
            </p>
        </div>
    </div>
</div>
