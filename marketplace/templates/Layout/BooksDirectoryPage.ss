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
        <% include MarketPlaceHelpLinks %>
        <div class="add-your-course">
            <p>
                Have you written a technical publication about OpenStack? Find out what it takes to be listed here! <a href="mailto:ecosystem@openstack.org">Email us for details</a>.
            </p>
        </div>
    </div>
</div>
