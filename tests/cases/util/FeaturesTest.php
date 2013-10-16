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
use li3_features\tests\mocks\util\MockFeatures;
use li3_features\tests\mocks\core\MockEnvironment;
use lithium\net\http\Request;
use lithium\core\Environment;

class FeaturesTest extends \lithium\test\Unit {

	public function setUp() {}

		public function tearDown() {
			MockFeatures::clearFeatures();
		}

	public function testCheckBool() {
		MockFeatures::add('feature_true', true);
		MockFeatures::add('feature_false', false);

		$result = MockFeatures::check('feature_true');
		$this->assertTrue($result);

		$result = MockFeatures::check('feature_false');
		$this->assertFalse($result);
	}

	public function testCheckClosureBool() {
		MockFeatures::add('feature_closure_true', function($params) {
			return true;
		});
		MockFeatures::add('feature_closure_false', function($params) {
			return false;
		});

		$result = MockFeatures::check('feature_closure_true');
		$this->assertTrue($result);

		$result = MockFeatures::check('feature_closure_false');
		$this->assertFalse($result);
	}

	public function testCheckClosureParams() {
		MockFeatures::add('feature_closure', function($params) {
			return $params['feature'];
		});

		$result = MockFeatures::check('feature_closure', array('feature' => true));
		$this->assertTrue($result);

		$result = MockFeatures::check('feature_closure', array('feature' => false));
		$this->assertFalse($result);
	}

	/**
	 * MockEnvironment allows setting unconfigured environments.
	 * MockFeatures overrides classes to use MockEnvironment.
	 */
	public function testCheckEnvBool() {
		MockFeatures::add('feature_env_bool', array(
			'production' => true,
			'staging' => false
		));

		// Make the current env production
		MockEnvironment::set('production');

		$result = MockFeatures::check('feature_env_bool');
		$this->assertTrue($result);

		// Make the current env staging
		MockEnvironment::set('staging');
		$result = MockFeatures::check('feature_env_bool');
		$this->assertFalse($result);
	}

	/**
	 * MockEnvironment allows setting unconfigured environments.
	 * MockFeatures overrides classes to use MockEnvironment.
	 */
	public function testCheckEnvClosure() {
		MockFeatures::add('feature_env_closure', array(
			'production' => function($params) {
				return true;
			},
			'staging' => function($params) {
				return false;
			},
		));

		// Make the current env production
		MockEnvironment::set('production');

		$result = MockFeatures::check('feature_env_closure');
		$this->assertTrue($result);

		// Make the current env staging
		MockEnvironment::set('staging');
		$result = MockFeatures::check('feature_env_closure');
		$this->assertFalse($result);
	}

	public function testCheckRequest() {
		$request = new Request();
		$request->feature = true;
		MockFeatures::setRequest($request);

		MockFeatures::add('feature_request', function($params) {
			return $params['request']->feature;
		});

		$result = MockFeatures::check('feature_request');
		$this->assertTrue($result);
	}

	public function testExport() {
		$testable = array();
		$this->assertEqual($testable, MockFeatures::export(array('true' => 123, 'false' => 123)));
		MockFeatures::add('closureTest', function($params) {
			return !empty($params['true']);
		});
		$testable['closureTest'] = true;
		$this->assertEqual($testable, MockFeatures::export(array('true' => 123, 'false' => 123)));
		$testable['closureTest'] = false;
		$this->assertEqual($testable, MockFeatures::export(array('false' => 123)));

		MockFeatures::add('trueTest', true);
		$testable['trueTest'] = true;
		$testable['closureTest'] = true;
		$this->assertEqual($testable, MockFeatures::export(array('true' => 123, 'false' => 123)));
		$testable['closureTest'] = false;
		$this->assertEqual($testable, MockFeatures::export(array('false' => 123)));

		MockFeatures::add('falseTest', false);
		$testable['falseTest'] = false;
		$testable['closureTest'] = true;
		$this->assertEqual($testable, MockFeatures::export(array('true' => 123, 'false' => 123)));
		$testable['closureTest'] = false;
		$this->assertEqual($testable, MockFeatures::export(array('false' => 123)));
	}

	public function testDefault() {
		$testable = array();
		$this->assertEqual(false, MockFeatures::check('testFeature'));
		$this->assertEqual(false, MockFeatures::check('testFeature1'));
		$this->assertEqual(false, MockFeatures::check('testFeature2'));

		MockFeatures::add('__default', function($params) {
			if (
				!empty($params['name']) &&
				in_array($params['name'], array('testFeature1', 'testFeature'))
			) {
				return true;
			}
			return false;
		});

		$this->assertEqual(true, MockFeatures::check('testFeature'));
		$this->assertEqual(true, MockFeatures::check('testFeature1'));
		$this->assertEqual(false, MockFeatures::check('testFeature2'));

		MockFeatures::add('testFeature', function($params) {
			return false;
		});

		$this->assertEqual(false, MockFeatures::check('testFeature'));
		$this->assertEqual(true, MockFeatures::check('testFeature1'));
		$this->assertEqual(false, MockFeatures::check('testFeature2'));
	}
}

?>
