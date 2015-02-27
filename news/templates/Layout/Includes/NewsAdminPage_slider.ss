<tr class="article_row" height="5px">
    <td class="date_release" >$formattedDate</td>
    <td class="title">$shortenText($Headline,40)</td>
    <td class="summary">$shortenText($RAW_val(Summary),80)</td>
    <td class="image" data-toggle="popover">
        <% if Image.Exists %>
            <i class="fa fa-picture-o"></i>
            <span class="image_html" style="display:none">$Image.SetWidth(300)</span>
        <% else %>
            -
        <% end_if %>
    </td>
    <td class="edit">
        <span class="newsEdit"><a href="news-add?articleID=$ID"><i class="fa fa-pencil-square-o"></i></a></span>
        <span class="newsRemove"><i class="fa fa-times"></i></span>
    </td>

</tr>