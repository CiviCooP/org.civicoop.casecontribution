<div class="crm-block crm-form-block crm-case-activitytocase-form-block">
    <table class="form-layout">
        <tr class="crm-case-activitytocase-form-block-file_on_case_unclosed_case_id">
            <td class="label">{$form.file_on_case_unclosed_case_id.label}</td>
            <td>{$form.file_on_case_unclosed_case_id.html}</td>
        </tr>
    </table>
    <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>
{literal}
    <script type="text/javascript">
        CRM.$(function($) {
            var $form = $('form.{/literal}{$form.formClass}{literal}');
            $('input[name=file_on_case_unclosed_case_id]', $form).crmSelect2({
                placeholder: {/literal}'{ts escape="js"}- select case -{/ts}'{literal},
                minimumInputLength: 1,
                formatResult: CRM.utils.formatSelect2Result,
                formatSelection: function(row) {
                    return row.label;
                },
                initSelection: function($el, callback) {
                    callback($el.data('value'));
                },
                ajax: {
                    url: {/literal}"{crmURL p='civicrm/case/ajax/unclosed' h=0}"{literal},
                    data: function(term) {
                        return {term: term, excludeCaseIds: "{/literal}{$currentCaseId}{literal}"};
                    },
                    results: function(response) {
                        return {results: response};
                    }
                }
            });
        });
    </script>
{/literal}