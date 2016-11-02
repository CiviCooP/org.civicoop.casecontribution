<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Casecontribution_Page_CaseTab {

  private $caseId;

  public function __construct($caseId) {
    $this->caseId = $caseId;
  }

  public function run() {
    $caseContributions = civicrm_api3('CaseContribution', 'get', array('case_id' => $this->caseId));
    $template = CRM_Core_Smarty::singleton();
    $contributions = array();
    foreach($caseContributions['values'] as $caseContribution) {
      $contributions[] = civicrm_api3('Contribution', 'getsingle', array('id' => $caseContribution['entity_id']));
    }
    $template->assign('contributions', $contributions);
    return $template->fetch('CRM/Casecontribution/Page/CaseTab.tpl');
  }

}