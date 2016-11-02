<?php

require_once 'casecontribution.civix.php';

function casecontribution_civicrm_caseSummary($caseId) {
  // Add a list of contributions to the manage case screen.
  $contributionsTab = new CRM_Casecontribution_Page_CaseTab($caseId);
  $content['casecontributions_contributions']['value'] = $contributionsTab->run();
  return $content;
}

function casecontribution_civicrm_buildForm($formName, &$form) {
  if ($form instanceof CRM_Contribute_Form_Contribution) {
    // Hide the custom field for case_id
    $config = CRM_Casecontribution_Config::singleton();
    $viewCustomData = $form->get_template_vars('groupTree');
    unset($viewCustomData[$config->getCaseContributionCustomGroup('id')]);
    $form->assign_by_ref('groupTree', $viewCustomData);
  }
  if ($form instanceof CRM_Contribute_Form_ContributionView) {
    // Hide the custom field for case_id
    $config = CRM_Casecontribution_Config::singleton();
    $viewCustomData = $form->get_template_vars('viewCustomData');
    unset($viewCustomData[$config->getCaseContributionCustomGroup('id')]);
    $form->assign_by_ref('viewCustomData', $viewCustomData);
  }
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

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function casecontribution_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function casecontribution_civicrm_navigationMenu(&$menu) {
  _casecontribution_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'org.civicoop.casecontribution')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _casecontribution_civix_navigationMenu($menu);
} // */
