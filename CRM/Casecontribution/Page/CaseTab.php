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
    $case = civicrm_api3('Case', 'getsingle', array('id' => $this->caseId));
    $caseContributions = civicrm_api3('CaseContribution', 'get', array('case_id' => $this->caseId));
    $template = CRM_Core_Smarty::singleton();
    $contributions = array();
    $qfKey = CRM_Utils_Request::retrieve('key', 'String', CRM_Core_DAO::$_nullObject);

    $permissions = array(CRM_Core_Permission::VIEW);
    if (CRM_Core_Permission::check('edit contributions')) {
      $permissions[] = CRM_Core_Permission::EDIT;
      $template->assign('allowed_to_add_contribution', true);
    }
    if (CRM_Core_Permission::check('delete in CiviContribute')) {
      $permissions[] = CRM_Core_Permission::DELETE;
    }
    $mask = CRM_Core_Action::mask($permissions);

    foreach($caseContributions['values'] as $caseContribution) {
      $contribution = civicrm_api3('Contribution', 'getsingle', array('id' => $caseContribution['contribution_id']));

      $actions = array(
        'id' => $contribution['id'],
        'cid' => $contribution['contact_id'],
        'cxt' => '',
      );

      $contribution['action'] = CRM_Core_Action::formLink(
        CRM_Contribute_Selector_Search::links($this->caseId,
          CRM_Core_Action::VIEW,
          $qfKey,
          'case'
        ),
        $mask, $actions,
        ts('more'),
        FALSE,
        'contribution.selector.row',
        'Contribution',
        $contribution['id']
      );

      $contributions[] = $contribution;

    }
    $template->assign('case_id', $this->caseId);
    $template->assign('contact_id', reset($case['client_id']));
    $template->assign('contributionsCount', count($contributions));
    $template->assign('contributions', $contributions);
    return $template->fetch('CRM/Casecontribution/Page/CaseTab.tpl');
  }

}