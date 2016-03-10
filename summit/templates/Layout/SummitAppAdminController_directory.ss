<div class="row">
        <div class="jumbotron">
            <h1>Summit Directory</h1>
        </div>
</div>
<div class ="row" style="padding-top: 2em;">
    <div class="col-md-12">
        <table class="table" id="summit_table">
            <tbody>
            <% loop Summits.Sort(SummitBeginDate, ASC) %>
                <tr id="summit_{$ID}">
                    <td class="summit_name">
                        $Title
                    </td>
                    <td>
                        $SummitBeginDate.Format('M jS Y')
                    </td>
                    <td>
                        $SummitEndDate.Format('M jS Y')
                    </td>
                    <td class="center_text">
                        <a href="$Top.Link/{$ID}/dashboard" class="btn btn-primary btn-sm" role="button">Control Panel</a>
                    </td>
                </tr>
            <% end_loop %>
            </tbody>
        </table>
    </div>
</div>