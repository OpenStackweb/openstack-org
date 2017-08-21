<% if canAdmin(books) %>
<div class="container">
    <div style="clear:both">
        <h1 style="width:50%;float:left;">Book Details</h1>
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center" class="roundedButton save-book" href="#" id="save-book" name="save-book">Save</a>
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:2%;" class="roundedButton addDeploymentBtn" href="$Top.Link(books)">&lt;&lt; Back to Products</a>
    </div>

    <div style="clear:both">
        <fieldset>
            <form id="book_form" name="book_form">
                <input id="id" name="id" type="hidden" value="0"/>
                <div class="field text " id="Title">
                    <label class="left">Title</label>
                    <div class="middleColumn">
                        <input type="text" name="title" id="title" class="text" >
                    </div>
                </div>
                <div class="field text " id="Link">
                    <label class="left">Link</label>
                    <div class="middleColumn">
                        <input type="text" name="link" id="link" class="text" >
                    </div>
                </div>

                <div class="field dropdown " id="CompanyName">
                    <label class="left">Company Name</label>
                    <div class="middleColumn">
                        <select name="company_id" id="company_id">
                            <option  value="">--select--</option>
                            <% if Companies %>
                                <% loop Companies %>
                                    <option  value="$ID">$Name</option>
                                <% end_loop %>
                            <% end_if %>
                        </select>
                    </div>
                </div>

                <div class="field text " id="Description">
                    <label class="left">Description</label>
                    <div class="middleColumn">
                        <textarea type="text" name="description" id="description" class="text" ></textarea>
                    </div>
                </div>

                <label class="left">Authors</label>
                <table style="border: 1px solid #ccc; border-collapse:collapse;clear:both;width:70%;" id="authors-table">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ccc !important;background:#eaeaea;width:45%;">First Name</th>
                            <th style="border: 1px solid #ccc !important;background:#eaeaea;width:45%;">Last Name</th>
                            <th style="border: 1px solid #ccc !important;background:#eaeaea;width:10%;">Add/Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="add-authors">
                            <td style="border: 1px solid #ccc;width:50%;background:#fff;">
                                <input type="text" style="width:300px;" value="" name="add_author_first" id="add_author_first" class="text autocompleteoff add-control">
                            </td>
                            <td style="border: 1px solid #ccc;width:50%;background:#fff;">
                                <input type="text" style="width:300px;" value="" name="add_author_last" id="add_author_last" class="text autocompleteoff add-control">
                            </td>
                            <td style="border: 1px solid #ccc;background:#eaeaea;width:10%;color:#cc0000;">
                                <a id="add-new-author" href="#">+&nbsp;Add</a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="field text " id="Image">
                    <label class="left">Image</label>
                    <div class="middleColumn">
                        <img src="" id="image_preview" />
                    </div>
                    <div class="middleColumn">
                        <input type="file" name="image" id="image" />
                    </div>
                </div>
            </form>

        </fieldset>
    </div>
    <div class="footer_buttons">
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center" class="roundedButton save-book" href="#" id="save-book">Save</a>
        <a style="overflow:hidden;white-space: nowrap;font-weight:normal;float:right;margin-bottom:50px;text-align:center;margin-right:50px;" class="roundedButton addDeploymentBtn" href="$Top.Link(books)">&lt;&lt; Back to Products</a>
    </div>
    <script type="text/javascript">
        <% if CurrentBook %>
        var book = $CurrentBookJson;
        <% end_if %>
        var listing_url = "$Top.Link(books)";
        var product_url = "$Top.Link(book)";
    </script>
</div>
<% else %>
    <p>You are not allowed to administer Books.</p>
<% end_if %>