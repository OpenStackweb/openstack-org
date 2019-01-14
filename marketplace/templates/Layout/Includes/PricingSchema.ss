<% if PricingSchemas %>
        <form id="pricing_schema_form" name="pricing_schema_form" style="margin-bottom: 30px">
        <table class="admin-table" width="100%">
            <thead>
            <tr>
                <th style="text-align: center;width:20%;">Pricing Scheme</th>
                <% loop PricingSchemas %>
                    <th style="text-align: center; width:16%;" >
                        $Type
                    </th>
                <% end_loop %>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="border: 1px solid #ccc;">Mark all that apply with an X</td>
                <% loop PricingSchemas %>
                    <td>
                        <input type="checkbox" class="checkbox pricing-schema-checkbox" name="pricing_schema_{$ID}" id="pricing_schema_{$ID}" data-pricing-schema-id="{$ID}" />
                    </td>
                <% end_loop %>
            </tr>
            </tbody>
        </table>
    </form>
<% end_if %>