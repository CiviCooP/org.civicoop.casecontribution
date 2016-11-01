<?php

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * FIXME - Add test description.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test class.
 *    Simply create corresponding functions (e.g. "hook_civicrm_post(...)" or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or test****() functions will
 *    rollback automatically -- as long as you don't manipulate schema or truncate tables.
 *    If this test needs to manipulate schema or truncate tables, then either:
 *       a. Do all that using setupHeadless() and Civi\Test.
 *       b. Disable TransactionalInterface, and handle all setup/teardown yourself.
 *
 * @group headless
 */
class api_v3_CaseContribution_Test extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }

  /**
   * Test to check whether we can link a contribution to a case with the API.
   *
   * It will follow the pattern of
   * 1. create a case
   * 2. create a contribution
   * 3. Link them together with the CaseContribution.Create api
   * 4. Look the link up with the CaseContribution.Get api
   * 5. Delete the link with the CaseContribution.Delete api
   *
   * All steps should be run successfully
   */
  public function testApi() {
    $client = civicrm_api3('Contact', 'create', array(
      'first_name' => 'John',
      'last_name' => 'Johnson',
      'contact_type' => 'Individual',
    ));
    $this->assertArrayHasKey('id', $client, 'Could not create client');

    $case1Params['case_type_id'] = 1; //Assuming housing support
    $case1Params['contact_id'] = $client['id'];
    $case1Params['subject'] = 'Case Contribution API Unit Test 1';
    $case1 = civicrm_api3('Case', 'create', $case1Params);

    $case2Params['case_type_id'] = 1; //Assuming housing support
    $case2Params['contact_id'] = $client['id'];
    $case2Params['subject'] = 'Case Contribution API Unit Test 2';
    $case2 = civicrm_api3('Case', 'create', $case2Params);

    $contribution1Params['contact_id'] = $client['id'];
    $contribution1Params['financial_type_id'] = "Donation";
    $contribution1Params['total_amount'] = 10.50;
    $contribution1 = civicrm_api3('Contribution', 'create', $contribution1Params);

    $contribution2Params['contact_id'] = $client['id'];
    $contribution2Params['financial_type_id'] = "Donation";
    $contribution2Params['total_amount'] = 25.75;
    $contribution2 = civicrm_api3('Contribution', 'create', $contribution2Params);

    // Create a third contribution which we are going to link to the second case
    $contribution3Params['contact_id'] = $client['id'];
    $contribution3Params['financial_type_id'] = "Donation";
    $contribution3Params['total_amount'] = 75.80;
    $contribution3 = civicrm_api3('Contribution', 'create', $contribution3Params);

    // Create a fourth contribution which we are not going to link a case
    $contribution4Params['contact_id'] = $client['id'];
    $contribution4Params['financial_type_id'] = "Donation";
    $contribution4Params['total_amount'] = 53.45;
    $contribution4 = civicrm_api3('Contribution', 'create', $contribution4Params);

    $this->assertArrayHasKey('id', $case1, 'Could not create case 1');
    $this->assertArrayHasKey('id', $case2, 'Could not create case 2');
    $this->assertArrayHasKey('id', $contribution1, 'Could not create contribution 1');
    $this->assertArrayHasKey('id', $contribution2, 'Could not create contribution 2');
    $this->assertArrayHasKey('id', $contribution3, 'Could not create contribution 3');
    $this->assertArrayHasKey('id', $contribution4, 'Could not create contribution 4');

    $caseContribution1Params['case_id'] = $case1['id'];
    $caseContribution1Params['contribution_id'] = $contribution1['id'];
    $caseContribution1 = civicrm_api3('CaseContribution', 'create', $caseContribution1Params);

    $caseContribution2Params['case_id'] = $case1['id'];
    $caseContribution2Params['contribution_id'] = $contribution2['id'];
    $caseContribution2 = civicrm_api3('CaseContribution', 'create', $caseContribution2Params);

    $caseContribution3Params['case_id'] = $case2['id'];
    $caseContribution3Params['contribution_id'] = $contribution3['id'];
    $caseContribution3 = civicrm_api3('CaseContribution', 'create', $caseContribution3Params);

    $this->assertArrayHasKey('id', $caseContribution1, 'Could not create case contribution 1');
    $this->assertArrayHasKey('id', $caseContribution2, 'Could not create case contribution 2');
    $this->assertArrayHasKey('id', $caseContribution3, 'Could not create case contribution 3');

    $allCaseContributions = civicrm_api3('CaseContribution', 'get', array());
    $this->assertArrayHasKey('count', $allCaseContributions, 'Count is not set');
    $this->assertEquals(3, $allCaseContributions['count'], 'Count is invalid');

    $caseContributions = civicrm_api3('CaseContribution', 'get', array('case_id' => $case1['id']));
    $this->assertArrayHasKey('count', $caseContributions, 'Count is not set');
    $this->assertEquals(2, $caseContributions['count'], 'Count is invalid');

    civicrm_api3('CaseContribution', 'delete', array('case_id' => $case1['id'], 'contribution_id' => $caseContribution1['id']));

    $caseContributions = civicrm_api3('CaseContribution', 'get', array('case_id' => $case1['id']));
    $this->assertArrayHasKey('count', $caseContributions, 'Count is not set');
    $this->assertEquals(1, $caseContributions['count'], 'Count is invalid');

    /** Invalid API parameters testing
     *
     * CaseContribution.create
     *   1. case_id not given
     *   2. contribution_id not given
     *   3. case_id and contribution_id not given
     * CaseContribution.delete
     *   1. case_id not given
     *   2. contribution_id not given
     *   3. case_id and contribution_id not given
     */
    $result = civicrm_api('CaseContribution', 'create', array());
    $this->assertArrayHasKey('is_error', $result);
    $this->assertNotEmpty($result['is_error'], 'CaseContribute.create API does not fail when no parameters are provided');
    $result = civicrm_api('CaseContribution', 'create', array('contribution_id' => $contribution4['id']));
    $this->assertArrayHasKey('is_error', $result);
    $this->assertNotEmpty($result['is_error'], 'CaseContribute.create API does not fail when no case_id parameter is provided');
    $result = civicrm_api('CaseContribution', 'create', array('case_id' => $case2['id']));
    $this->assertArrayHasKey('is_error', $result);
    $this->assertNotEmpty($result['is_error'], 'CaseContribute.create API does not fail when no contribution_id parameter is provided');
    $result = civicrm_api('CaseContribution', 'delete', array());
    $this->assertArrayHasKey('is_error', $result);
    $this->assertNotEmpty($result['is_error'], 'CaseContribute.delete API does not fail when no parameters are provided');
    $result = civicrm_api('CaseContribution', 'delete', array('contribution_id' => $contribution4['id']));
    $this->assertArrayHasKey('is_error', $result);
    $this->assertNotEmpty($result['is_error'], 'CaseContribute.delete API does not fail when no case_id parameter is provided');
    $result = civicrm_api('CaseContribution', 'delete', array('case_id' => $case2['id']));
    $this->assertArrayHasKey('is_error', $result);
    $this->assertNotEmpty($result['is_error'], 'CaseContribute.delete API does not fail when no contribution_id parameter is provided');
  }

}
