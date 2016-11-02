<div id="case-casecontribution-contributions" class="crm-accordion-wrapper collapsed">
    <div class="crm-accordion-header">{ts}Related contributions{/ts}</div>
    <div class="crm-accordion-body">
        <table>
            <thead>
            <tr>
                <th class="ui-state-default">{ts}Amount{/ts}</th>
                <th class="ui-state-default">{ts}Type{/ts}</th>
                <th class="ui-state-default">{ts}Received{/ts}</th>
                <th class="ui-state-default">{ts}Status{/ts}</th>
                <th class="no-sort ui-state-default"></th>
            </tr>
            </thead>
            <tbody>

            {foreach from=$contributions item=row}
                <tr class="{cycle values="odd,even"}">
                    <td class="right bold crm-contribution-amount">
                        <span class="nowrap">
                            {$row.total_amount|crmMoney:$row.currency}
                        </span>
                        {if $row.amount_level }
                            <br />({$row.amount_level})
                        {/if}
                        {if $row.contribution_recur_id}
                            <br />{ts}(Recurring Contribution){/ts}
                        {/if}
                    </td>
                    <td class="crm-contribution-type crm-contribution-type_{$row.financial_type_id} crm-financial-type crm-financial-type_{$row.financial_type_id}">{$row.financial_type}</td>
                    <td class="crm-contribution-receive_date">{$row.receive_date|crmDate}</td>
                    <td class="crm-contribution-status">
                        {$row.contribution_status}<br />
                        {if $row.cancel_date}
                            {$row.cancel_date|crmDate}
                        {/if}
                    </td>
                    <td>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    {literal}
    cj(function() {
        var caseContributionContributions = cj('#case-casecontribution-contributions').detach();
        cj('.crm-case-activities-block').before(caseContributionContributions);
    });
    {/literal}
</script>