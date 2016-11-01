<?php

/**
 * CaseContribution.Delete API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_case_contribution_Delete_spec(&$spec) {
  $spec['id']['api.required'] = 0;
  $spec['contribution_id']['api.required'] = 1;
  $spec['case_id']['api.required'] = 1;
}

/**
 * CaseContribution.Delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_case_contribution_Delete($params) {
  $returnValues = array();
  if (empty($params['contribution_id'])) {
    return civicrm_api3_create_error('contribution_id is not set');
  }
  if (empty($params['case_id'])) {
    return civicrm_api3_create_error('case_id is not set');
  }
  CRM_Casecontribution_BAO_CaseContribution::deleteCaseContribution($params['contribution_id'], $params['case_id']);
  return civicrm_api3_create_success($returnValues, $params, 'CaseContribution', 'Delete');
}

