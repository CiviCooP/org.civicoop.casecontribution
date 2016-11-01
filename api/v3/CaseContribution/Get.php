<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

function civicrm_api3_case_contribution_Get($params) {
  if (isset($params['contribution_id'])) {
    $params['entity_id'] = $params['contribution_id'];
    unset($params['contribution_id']);
  }
  $returnValues = CRM_Casecontribution_BAO_CaseContribution::getValues($params);
  return civicrm_api3_create_success($returnValues, $params, 'CaseContribution', 'Get');
}