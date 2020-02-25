<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Casecontribution_Form_AddToCase extends CRM_Core_Form {

  private $_contributionId;

  private $_currentCaseId;

  /**
   * Build all the data structures needed to build the form.
   *
   * @return void
   */
  public function preProcess() {
    $this->_contributionId = CRM_Utils_Request::retrieve('id', 'Positive');
    if (!$this->_contributionId) {
      CRM_Core_Error::fatal('required contribution id is missing.');
    }

    $this->_currentCaseId = CRM_Utils_Request::retrieve('caseId', 'Positive');
    $this->assign('currentCaseId', $this->_currentCaseId);
  }

  /**
   * Build the form object.
   *
   * @return void
   */
  public function buildQuickForm() {
    $this->addEntityRef('file_on_case_unclosed_case_id', ts('Select Case'), [
      'entity' => 'Case',
      'select' => ['minimumInputLength' => 0],
      'api' => [
        'extra' => ['contact_id'],
        'params' => [
          'case_id' => ['!=' => $this->_currentCaseId],
          'case_id.is_deleted' => 0,
          'case_id.status_id' => ['!=' => 'Closed'],
          'case_id.end_date' => ['IS NULL' => 1],
        ],
      ],
    ], TRUE);

    $this->addElement('hidden', 'id', $this->_contributionId);
    $this->addElement('hidden', 'caseId', $this->_currentCaseId);

    $this->addButtons([
      [
        'type' => 'upload',
        'name' => ts('Save'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ],
    ]);
  }

  /**
   * Set default values for the form. For edit/view mode
   * the default values are retrieved from the database
   *
   *
   * @return array
   */
  public function setDefaultValues() {
    $defaults = [];
    $contribution = [];
    try {
      $cid = civicrm_api3('Contribution', 'getvalue', [
        'return' => 'contact_id',
        'id' => $this->_contributionId,
      ]);
    }
    catch (Exception $e) {
      // no contact id found for contirbution
      $cid = NULL;
    }

    if ($cid) {
      $caseParams = [
        'contact_id' => $cid,
        'case_id.status_id' => ['!=' => "Closed"],
        'case_id.is_deleted' => 0,
        'case_id.end_date' => ['IS NULL' => 1],
        'options' => ['limit' => 1],
        'return' => 'case_id',
      ];

      if ($this->_currentCaseId) {
        $caseParams['case_id'] = ['!=' => $this->_currentCaseId];
      }

      try {
        $defaults['file_on_case_unclosed_case_id'] = civicrm_api3('CaseContact', 'getvalue', $caseParams);
      }
      catch (Exception $e) {
        // No open cases for the contact.
      }
    }

    return $defaults;
  }

  public function postProcess() {
    $params['contribution_id'] = $this->_contributionId;
    $params['case_id'] = $this->_submitValues['file_on_case_unclosed_case_id'];
    civicrm_api3('CaseContribution', 'create', $params);
  }

}
