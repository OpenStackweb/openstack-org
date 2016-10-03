<% if $Group == 'single' %>
    <% loop $Items %>
        <li>
            $Thumbnail.getTag()
            <p>$Label</p>
            <a class="download" href="$Doc.Link()" target="_blank">Download</a>
        </li>
    <% end_loop %>
<% else %>
    <li>
        $Items.First().Thumbnail.getTag()
        <p>$Group</p>
        <a class="download" href="#" data-toggle="modal" data-target="#{$GroupID}_modal">View All ($Items.Count())</a>
        <div class="modal fade" id="{$GroupID}_modal" role="dialog" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">$Group ($Items.Count())</h4>
                    </div>
                    <div class="modal-body">
                        <ul class="content-list">
                            <% loop $Items %>
                            <li>
                                $Thumbnail.getTag()
                                <p>$Label</p>
                                <a class="download" href="$Doc.Link()" target="_blank">Download</a>
                            </li>
                            <% end_loop %>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </li>
<% end_if %>