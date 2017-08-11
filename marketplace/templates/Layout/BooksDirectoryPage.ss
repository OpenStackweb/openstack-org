<div class="container">
    <div id="books-list" class="col-lg-8 col-md-8 col-sm-8">
        <% if Books %>
            <% loop Books %>
                <% include BooksDirectoryPage_BookBox BookLink={$Link} %>
            <% end_loop %>
        <% else %>
            &nbsp;
        <% end_if %>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4">
        <h3>OpenStack Online Help</h3>
        <ul class="resource-links">
            <li>
                <a href="{$OnlineDocsUrl}">Online Docs</a>
            </li>
            <li>
                <a href="{$OperationsGuideUrl}">Operations Guide</a>
            </li>
            <li>
                <a href="{$SecurityGuideUrl}">Security Guide</a>
            </li>
            <li>
                <a href="{$GettingStartedUrl}">Getting Started</a>
            </li>
        </ul>
        <div class="add-your-course">
            <p>
                Does your company offer consulting for OpenStack? Be listed here!
                <a href="mailto:info@openstack.org">Email us for details</a>
            </p>
        </div>
    </div>
</div>
