<h2>Speakers</h2>

<div id="speakersExportAlert" class="alert alert-danger hidden" role="alert">
    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
    <span class="sr-only">Error:</span>
    Select at least one summit
</div>

<form id="speakersExport" method="POST" action="$Link(ExportSpeakersData)">
    <div>
        <label>Summits</label>
        <% loop $Summits %>
            <div class="checkbox">
                <label>
                    <input id="summit_$ID" name="summit_$ID" type="checkbox" value="$ID" class="submit-filter"/>$Name
                </label>
            </div>
        <% end_loop %>
    </div>
    <hr>
    <div>
        <div class="checkbox">
            <label>
                <input id="onlyApprovedSpeakers" name="onlyApprovedSpeakers" type="checkbox"/>Only Approved Speakers
            </label>
        </div>
    </div>
    <hr>
    <div>
        <label>Affiliation</label><br>
        <input id="affiliation" name="affiliation" type="text"/>
    </div>
    <hr>
    <input id="ext" name="ext" type="hidden"/>
    <button data-ext="xls">Export Speakers Users (XLS)</button>
    <button data-ext="csv">Export Speakers Users (CSV)</button>

</form>