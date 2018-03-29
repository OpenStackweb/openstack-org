<script type="application/javascript">
    var {$Name}_columns =
    [
        <% loop Columns %>
            { id : $ID, label: "{$_T("survey_template", $Label)}"},
        <% end_loop %>
    ];
</script>
<p>$Label</p>
<p><a href="#" class="survey-radui-button-matrix-clear">$_T("survey_ui","clear all")</a></p>
<table width="600">
    <tbody>
    <tr>
        <td width="50%">$_T("survey_template", $RowsLabel)</td>
        <% loop Columns %>
            <td class="input-cell">$_T("survey_template", $Label)</td>
        <% end_loop %>
    </tr>

    <% if HasGroups %>
        <% loop Groups %>
            <tr class="group-header">
                <td colspan="4">{$Label}</td>
            </tr>
            <% loop OrderedValues %>
                <tr class="{$EvenOdd}">
                    <td>$Label</td>
                    <% loop Columns %>
                        <td class="input-cell<% if $Top.mustHighlite($Up.ID,$ID) %> highlite-row<% end_if %>"><input data-row-id="{$Up.ID}" data-col-id="{$ID}" class="radio_{$Up.ID} radio_opt" <% if $Top.isChecked($Up.ID,$ID) %>checked<% end_if %> type="radio" name="{$Top.Name}_{$Up.ID}" id="{$Up.ID}_{$ID}"></td>
                    <% end_loop %>
                </tr>
            <% end_loop %>
        <% end_loop %>
    <% end_if %>

    <% if HasGroups %>
        <% if HasRows %>
            <tr class="group-header">
                <td colspan="4">{$Top.DefaultGroupLabel}</td>
            </tr>
        <% end_if %>
    <% end_if %>
    <% loop Rows %>

    <tr class="{$EvenOdd}">
        <td>$Label</td>
          <% loop Columns %>
            <td class="input-cell<% if $Top.mustHighlite($Up.ID,$ID) %> highlite-row<% end_if %>"><input data-row-id="{$Up.ID}" data-col-id="{$ID}" class="radio_{$Up.ID} radio_opt" <% if $Top.isChecked($Up.ID,$ID) %>checked<% end_if %> type="radio" name="{$Top.Name}_{$Up.ID}" id="{$Up.ID}_{$ID}"></td>
        <% end_loop %>
    </tr>
    <% end_loop %>

    <% if AdditionalRows  || AlreadyAddedAdditionalRows %>
        <tr>
            <td colspan="4"><hr></td>
        </tr>
        <tr class="tr-additional">
            <td width="50%">$AdditionalRowsLabel</td>
            <% loop Columns %>
                <td class="input-cell">$_T("survey_template", $Label)</td>
            <% end_loop %>
        </tr>

        <% if AlreadyAddedAdditionalRows %>
            <% loop AlreadyAddedAdditionalRows %>
            <tr>
                <td>$_T("survey_template", $Label)</td>
                <% loop Columns %>
                    <td class="input-cell<% if $Top.mustHighlite($Up.ID,$ID) %> highlite-row<% end_if %>"><input data-row-id="{$Up.ID}" data-col-id="{$ID}" class="radio_{$Up.ID} radio_opt" <% if $Top.isChecked($Up.ID,$ID) %>checked<% end_if %> type="radio" name="{$Top.Name}_{$Up.ID}" id="{$Up.ID}_{$ID}"></td>
                <% end_loop %>
            </tr>
            <% end_loop %>
        <% end_if %>

        <% if AdditionalRows %>
        <tr class="tr-add-container">
            <td colspan="4"><br>
                $_T("survey_template", $AdditionalRowsDescription)
                <br>
                <select id="{$Top.ID}_additional_rows" class="survey-radio-matrix-field-additional-rows-select">
                    <option selected="" value="">$_T("survey_template", $Top.EmptyString)</option>
                    <% loop AdditionalRows %>
                        <option value="$ID">$_T("survey_template", $Label)</option>
                    <% end_loop %>
                </select>
            </td>
        </tr>
        <% end_if %>

    <% end_if %>
    </tbody>
</table>
<input type="hidden" name="$Question.Name" id="$Question.Name" value="{$AnswerValue}" class="ctrl_hidden_value" $ValidationAttributesHTML />