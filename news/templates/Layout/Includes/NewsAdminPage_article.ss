<tr class="article_row" height="5px">
    <td class="date_release" >$formattedDate</td>
    <% if $Type == standby %>
        <td class="title">$shortenText($Headline,30)</td>
    <% else %>
        <td class="title">$shortenText($Headline,60)</td>
    <% end_if %>
    <td class="image" data-toggle="popover">
        <% if Image.Exists %>
            <i class="fa fa-picture-o"></i>
            <span class="image_html" style="display:none">$Image.SetWidth(300)</span>
        <% else %>
            -
        <% end_if %>
    </td>
    <td class="edit">
        <span class="newsPreview"><a target="_blank" href="news/view/$ID/$HeadlineForUrl"><i class="fa fa-file-image-o"></i></a></span>
        <span class="newsEdit"><a href="news-add?articleID=$ID"><i class="fa fa-pencil-square-o"></i></a></span>
        <span class="newsRemove"><i class="fa fa-times"></i></span>
    </td>
    <td>
        <input type="hidden" class="article_id" value="$ID" />
        <input type="hidden" class="article_rank" value="$Rank" />
        <input type="hidden" class="article_type" value="$Type" />
    </td>
</tr>