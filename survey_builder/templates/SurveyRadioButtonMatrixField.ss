<script type="application/javascript">
    var {$Name}_columns =
    [
        <% loop Columns %>
            { id : $ID, label: "{$Label}"},
        <% end_loop %>
    ];
</script>
<p>$Label</p>
<table width="600">
    <tbody>
    <tr>
        <td width="50%">$RowsLabel</td>
        <% loop Columns %>
            <td class="input-cell">$Label</td>
        <% end_loop %>
    </tr>
    <% loop Rows %>
    <tr>
        <td>$Label</td>
          <% loop Columns %>
            <td class="input-cell"><input data-row-id="{$Up.ID}" class="radio_{$Up.ID} radio_opt" <% if $Top.isChecked($Up.ID,$ID) %>checked<% end_if %> type="radio" value="$ID"  name="{$Top.Name}_{$Up.ID}" id="{$Up.ID}_{$ID}"></td>
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
            <td class="input-cell">$Label</td>
        <% end_loop %>
    </tr>
    <% if AlreadyAddedAdditionalRows %>
        <% loop AlreadyAddedAdditionalRows %>
        <tr>
            <td>$Label</td>
            <% loop Columns %>
                <td class="input-cell"><input data-row-id="{$Up.ID}" class="radio_{$Up.ID} radio_opt" <% if $Top.isChecked($Up.ID,$ID) %>checked<% end_if %> type="radio" value="$ID"  name="{$Top.Name}_{$Up.ID}" id="{$Up.ID}_{$ID}"></td>
            <% end_loop %>
        </tr>
        <% end_loop %>
    <% end_if %>
    <% if AdditionalRows %>
    <tr class="tr-add-container">
        <td colspan="4"><br>
            $AdditionalRowsDescription
            <br>
            <select id="{$Top.ID}_additional_rows" class="survey-radio-matrix-field-additional-rows-select">
                <option selected="" value="">$Top.EmptyString</option>
                <% loop AdditionalRows %>
                    <option value="$ID">$Label</option>
                <% end_loop %>
            </select>
        </td>
    </tr>
    <% end_if %>
    <% end_if %>
    </tbody>
</table>
<input type="hidden" name="$Question.Name" id="$Question.Name" value="" class="ctrl_hidden_value" $ValidationAttributesHTML />