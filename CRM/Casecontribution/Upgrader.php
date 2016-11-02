<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Casecontribution_Upgrader extends CRM_Casecontribution_Upgrader_Base {

  public function install() {
    $this->executeSqlFile('sql/install.sql');
  }


  public function uninstall() {
    $this->executeSqlFile('sql/uninstall.sql');
  }

}
