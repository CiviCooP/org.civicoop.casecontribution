<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Casecontribution_Config {

  private static $singleton;

  private $case_contribution_custom_group;

  private function __construct() {
    $this->case_contribution_custom_group = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'case_contribution'));
  }

  /**
   * @return CRM_Casecontribution_Config
   */
  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Casecontribution_Config();
    }
    return self::$singleton;
  }

  /**
   * @param string $key
   */
  public function getCaseContributionCustomGroup($key='id') {
    return $this->case_contribution_custom_group[$key];
  }

}