<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

function civicrm_api3_case_contribution_Get($params) {
  $returnValues = CRM_Casecontribution_BAO_CaseContribution::getValues($params);
  return civicrm_api3_create_success($returnValues, $params, 'CaseContribution', 'Get');
}