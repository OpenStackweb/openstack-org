<div style="clear:both;">
    <h2>Search Books</h2>
    <div class="addDeploymentForm">
        <form id="search_books" name="search_books" action="$Top.Link(books)">
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Filter Products</th>
                        <th>Company Name</th>
                        <th>Search</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <input type="text" value="" name="name" id="name">
                    </td>
                    <td>
                        <select name="company_id" id="company_id">
                            <option  value="">--select--</option>
                            <% if Companies %>
                                <% loop Companies %>
                                    <option  value="$ID">$Name</option>
                                <% end_loop %>
                            <% end_if %>
                        </select>
                    </td>
                    <td>
                        <input type="submit" style="white-space: nowrap;" value="Search" class="roundedButton">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <div style="clear:both; margin-bottom: 40px">
        <h2>Books</h2>
        <p>Click heading to sort:</p>
        <table class="main-table">
            <thead>
                <tr>
                    <th><a href="$Top.Link(books)?sort=company">Company ^</a></th>
                    <th><a href="$Top.Link(books)?sort=title">Title ^</a></th>
                    <th>Authors</th>
                    <th>Link</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <% if Books %>
                    <% loop Books %>
                    <tr>
                        <td>
                            $Company.Name
                        </td>
                        <td>
                            $Title
                        </td>
                        <td>
                            $Link
                        </td>
                        <td>
                            <% loop $Authors %>
                                $FirstName $LastName,
                            <% end_loop %>
                        </td>
                        <td style="min-width: 200px" width="30%">
                            <a class="product-button roundedButton addDeploymentBtn" href="$Top.Link(book)?id=$ID">Edit</a>
                            <a class="roundedButton delete-book product-button addDeploymentBtn" href="#" data-id="{$ID}">Delete</a>
                        </td>
                    </tr>
                    <% end_loop %>
                <% end_if %>
            </tbody>
        </table>
    </div>
</div>
