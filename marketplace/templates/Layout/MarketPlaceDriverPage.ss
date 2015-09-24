<div class="grey-bar">
    <div class="container">
        &nbsp;
    </div>
</div>
<div class="container">

  $Content

<% cached 'drivertable', ID %>
<div class="">
  <table class="table table-striped" id="releaseTable">
    <tbody>
        <tr>
          <th class="project">Project</th>
          <th class="vendor">Vendor</th>
          <th class="driver">Driver</th>
          <th class="ships">Ships with OpenStack</th>
          <th class="tested">Tested</th>
        </tr>

        <% loop DriverTable2 %>

        <tr>
          <td>$Project</td>
          <td>$Vendor</td>
          <td>
            <a href="{$Url}">$Name</a>
            <p>$Description</p>
          </td>
          <td class="releases">
            <% if Releases %>
              <% loop Releases %>
                <a href="{$Url}">$Name</a>
              <% end_loop %>
            <% end_if %>
          </td>
          <td class="tested-listing" style="width:90px">
            <% if $Tested %>
                <i class="fa fa-check-square"></i>
                <div class="tested-listing-title">Tested</div>
            <% end_if %>
          </td>
        </tr>

        <% end_loop %>

    </tbody>
  </table>
</div>
<% end_cached %>
</div>