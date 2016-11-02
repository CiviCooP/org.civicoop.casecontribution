<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Casecontribution_BAO_CaseContribution extends CRM_Casecontribution_DAO_CaseContribution {

  /**
   * Function to get values
   *
   * @return array $result found rows with data
   * @access public
   * @static
   */
  public static function getValues($params) {
    $result = array();
    $caseContribution = new CRM_Casecontribution_BAO_CaseContribution();
    if (!empty($params)) {
      $fields = self::fields();
      foreach ($params as $key => $value) {
        if (isset($fields[$key])) {
          $caseContribution->$key = $value;
        }
      }
    }
    $caseContribution->find();
    while ($caseContribution->fetch()) {
      $row = array();
      self::storeValues($caseContribution, $row);
      $result[$row['contribution_id']] = $row;
    }
    return $result;
  }

  /**
   * Function to delete a link between a contribution and a case
   *
   * @param int $contribution_id
   * @param int $case_id
   * @access public
   * @throws Exception when link between contribution and case could not be found
   * @static
   */
  public static function deleteCaseContribution($contribution_id, $case_id) {

    $caseContribution = new CRM_Casecontribution_BAO_CaseContribution();
    $caseContribution->case_id = $case_id;
    $caseContribution->contribution_id = $contribution_id;
    if (!$caseContribution->find(TRUE)) {
      Throw new Exception('Link between contribution '.$contribution_id.' and case '.$case_id.' not found');
    }

    CRM_Utils_Hook::pre('delete', 'CaseContribution', $caseContribution->contribution_id, CRM_Core_DAO::$_nullArray);

    $caseContribution->delete();

    CRM_Utils_Hook::post('delete', 'CaseContribution', $caseContribution->contribution_id, CRM_Core_DAO::$_nullArray);

    return;
  }


  /**
   * Creates or updates a case contribution record.
   *
   * @param array $params
   *   of values to initialize the record with.
   * @return object
   */
  public static function add($params) {
    $result = array();
    if (isset($params['contribution_id'])) {
      CRM_Utils_Hook::pre('edit', 'CaseContribution', $params['contribution_id'], $params);
    }
    else {
      CRM_Utils_Hook::pre('create', 'CaseContribution', NULL, $params);
    }

    $caseContribution = new CRM_Casecontribution_BAO_CaseContribution();
    if (isset($params['contribution_id'])) {
      $caseContribution->contribution_id = $params['contribution_id'];
      $caseContribution->find(TRUE);
    }
    $caseContribution->contribution_id = $params['contribution_id'];
    $caseContribution->case_id = $params['case_id'];
    $caseContribution->save();

    if (isset($params['id'])) {
      CRM_Utils_Hook::post('edit', 'CaseContribution', $caseContribution->contribution_id, $caseContribution);
    }
    else {
      CRM_Utils_Hook::post('create', 'CaseContribution', $caseContribution->contribution_id, $caseContribution);
    }

    self::storeValues($caseContribution, $result);
    return $result;
  }

}