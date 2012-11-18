<?php
/**
 * li3_feature plugin for Lithium: the most rad php framework.
 *
 * @author        Michael C. Harris
 * @copyright     Copyright 2012
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
*/

namespace li3_features\tests\cases\util;

use li3_features\util\Features;

class FeaturesTest extends \lithium\test\Unit {

	public function setUp() {}

	public function tearDown() {}

	public function testCheckBool() {
		Features::add('feature_true', true);
		Features::add('feature_false', false);

		$result = Features::check('feature_true');
		$this->assertTrue($result);

		$result = Features::check('feature_false');
		$this->assertFalse($result);
	}

	public function testCheckClosureBool() {
		Features::add('feature_closure_true', function($params) {
      return true;
    });
		Features::add('feature_closure_false', function($params) {
      return false;
    });

		$result = Features::check('feature_closure_true');
		$this->assertTrue($result);

		$result = Features::check('feature_closure_false');
		$this->assertFalse($result);
	}

	public function testCheckClosureParams() {
		Features::add('feature_closure', function($params) {
      return $params['feature'];
    });

		$result = Features::check('feature_closure', array('feature' => true));
		$this->assertTrue($result);

		$result = Features::check('feature_closure', array('feature' => false));
		$this->assertFalse($result);
	}

  /**
   * @todo Work out how to change the environment the Features class detects
   */
//	public function testCheckEnvBool() {
//		Features::add('feature_env_bool', array(
//      'production' => true,
//      'staging' => false
//    ));
//
//    // Make the current env production
//    \lithium\core\Environment::is(function($request) { return 'production'; });
//
//		$result = Features::check('feature_env_bool');
//		$this->assertTrue($result);
//
//    // Make the current env staging
//    \lithium\core\Environment::is(function($request) { return 'staging'; });
//		$result = Features::check('feature_env_bool');
//		$this->assertFalse($result);
//	}
//
//  /**
//   * @todo Work out how to change the environment the Features class detects
//   */
//	public function testCheckEnvClosure() {
//		Features::add('feature_env_closure', array(
//        'production' => function($params) {
//          return true;
//        },
//        'staging' => function($params) {
//          return false;
//        },
//    ));
//
//    // Make the current env production
//    \lithium\core\Environment::is(function($request) { return 'production'; });
//
//		$result = Features::check('feature_env_closure');
//		$this->assertTrue($result);
//
//    // Make the current env staging
//    \lithium\core\Environment::is(function($request) { return 'staging'; });
//		$result = Features::check('feature_env_closure');
//		$this->assertFalse($result);
//	}
}

?>
