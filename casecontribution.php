<?php

require_once 'casecontribution.civix.php';

function casecontribution_civicrm_caseSummary($caseId) {
  // Add a list of contributions to the manage case screen.
  $contributionsTab = new CRM_Casecontribution_Page_CaseTab($caseId);
  $content['casecontributions_contributions']['value'] = $contributionsTab->run();
  return $content;
}

function casecontribution_civicrm_links($op, $objectName, &$objectId, &$links, &$mask = NULL, &$values = array()) {
  if ($objectName == 'Contribution' && CRM_Core_Permission::check('edit contributions') && CRM_Case_BAO_Case::accessCiviCase()) {
    $isFiled = false;
    try {
      $caseContribution = civicrm_api3('CaseContribution', 'getsingle', array('contribution_id' => $objectId));
      if (!empty($caseContribution['case_id'])) {
        $isFiled = true;
      }
    } catch (Exception $e) {
       // Do nothing
    }
    if (!$isFiled) {
      $links[] = array(
        'name' => 'File on case',
        'url' => 'civicrm/casecontribution/fileoncase',
        'qs' => 'reset=1&action=add&id=%%id%%&cid=%%cid%%&context=%%cxt%%',
        'title' => ts('File on case'),
      );
    }
  }
}

function casecontribution_civicrm_buildForm($formName, &$form) {
  if ($form instanceof CRM_Contribute_Form_Contribution && CRM_Utils_Request::retrieve('case_id', 'Integer')) {
    $form->add('hidden', 'case_id', CRM_Utils_Request::retrieve('case_id', 'Integer'));
  }
}

function casecontribution_civicrm_postProcess($formName, &$form) {
  if ($form instanceof  CRM_Contribute_Form_Contribution && CRM_Utils_Request::retrieve('case_id', 'Integer') && $form->getVar('_action') == CRM_Core_Action::ADD) {
    $contribution_id = $form->getVar('_id');
    $case_id = CRM_Utils_Request::retrieve('case_id', 'Integer');
    civicrm_api3('CaseContribution', 'create', array(
      'case_id' => $case_id,
      'contribution_id' => $contribution_id
    ));
  }
}

function casecontribution_civicrm_post($op, $objectName, $objectId, &$objectRef) {
	if ($objectName == 'Contribution' && $op == 'delete') {
		CRM_Casecontribution_BAO_CaseContribution::deleteContribution($objectId);
	}
}

/**
 * Implements hook_civicrm_alterReportVar().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterReportVar
 */
function casecontribution_civicrm_alterReportVar($varType, &$var, &$reportForm) {
  if (is_a($reportForm, 'CRM_Report_Form_Contribute_Detail')) {
    if ($varType == 'columns') {
      $var['civicrm_case'] = [
        'dao' => 'CRM_Case_DAO_Case',
        'fields' => [
          'case_id' => [
            'title' => ts('Case ID'),
            'name' => 'id',
          ],
          'case_subject' => [
            'title' => ts('Case Subject'),
            'name' => 'subject',
          ],
          'case_start_date' => [
            'title' => ts('Case Start Date'),
            'type' => CRM_Utils_Type::T_DATE,
            'name' => 'start_date',
          ],
          'case_end_date' => [
            'title' => ts('Case End Date'),
            'type' => CRM_Utils_Type::T_DATE,
            'name' => 'end_date',
          ],
          'case_status_id' => [
            'title' => ts('Case Status'),
            'name' => 'status_id',
          ],
        ],
        'filters' => [
          'case_start_date' => [
            'name' => 'start_date',
            'title' => ts('Case Start Date'),
            'operatorType' => CRM_Report_Form::OP_DATE,
            'type' => CRM_Utils_Type::T_DATE,
          ],
          'case_end_date' => [
            'name' => 'end_date',
            'title' => ts('Case End Date'),
            'operatorType' => CRM_Report_Form::OP_DATE,
            'type' => CRM_Utils_Type::T_DATE,
          ],
          'case_status_id' => [
            'name' => 'status_id',
            'title' => ts('Case Status'),
            'type' => CRM_Utils_Type::T_INT,
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Case_BAO_Case::buildOptions('status_id', 'search'),
          ],
          'case_type_id' => [
            'title' => ts('Case Type'),
            'type' => CRM_Utils_Type::T_INT,
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Case_BAO_Case::buildOptions('case_type_id', 'search'),
          ],
        ],
      ];
      $var['civicrm_case_type'] = [
        'dao' => 'CRM_Case_DAO_Case',
        'fields' => [
          'case_type_title' => [
            'title' => ts('Case Type'),
            'name' => 'title',
          ],
        ],
      ];
    }
    if ($varType == 'sql') {
      $from = $reportForm->getVar('_from');
      $aliases = $reportForm->getVar('_aliases');
      $from .= "
        LEFT JOIN civicrm_case_contribution case_contribution
          ON case_contribution.contribution_id = {$aliases['civicrm_contribution']}.id
        LEFT JOIN civicrm_case {$aliases['civicrm_case']}
          ON {$aliases['civicrm_case']}.id = case_contribution.case_id
        LEFT JOIN civicrm_case_type {$aliases['civicrm_case_type']}
          ON {$aliases['civicrm_case_type']}.id = {$aliases['civicrm_case']}.case_type_id
      ";
      $reportForm->setVar('_from', $from);
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function casecontribution_civicrm_config(&$config) {
  _casecontribution_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param array $files
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function casecontribution_civicrm_xmlMenu(&$files) {
  _casecontribution_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function casecontribution_civicrm_install() {
  _casecontribution_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function casecontribution_civicrm_uninstall() {
  _casecontribution_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function casecontribution_civicrm_enable() {
  _casecontribution_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function casecontribution_civicrm_disable() {
  _casecontribution_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function casecontribution_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _casecontribution_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function casecontribution_civicrm_managed(&$entities) {
  _casecontribution_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * @param array $caseTypes
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function casecontribution_civicrm_caseTypes(&$caseTypes) {
  _casecontribution_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function casecontribution_civicrm_angularModules(&$angularModules) {
_casecontribution_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function casecontribution_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _casecontribution_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
