<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Casecontribution_Upgrader extends CRM_Casecontribution_Upgrader_Base {

  public function install() {
    $this->executeCustomDataFile('xml/case_contribution.xml');
  }


  public function uninstall() {
    $case_contribution_gid = civicrm_api3('CustomGroup', 'getvalue', array('name' => 'case_contribution', 'return' => 'id'));
    $fields = civicrm_api3('CustomField', 'get', array('custom_group_id' => $case_contribution_gid));
    foreach($fields['values'] as $field) {
      civicrm_api3('CustomField', 'delete', array('id' => $field['id']));
    }
    civicrm_api3('CustomGroup', 'delete', array('id' => $case_contribution_gid));
  }

}
