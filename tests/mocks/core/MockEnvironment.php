<?php
/**
 * li3_feature plugin for Lithium: the most rad php framework.
 *
 * @author        Michael C. Harris
 * @copyright     Copyright 2012
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_features\tests\mocks\core;

class MockEnvironment extends \lithium\core\Environment {

	/**
	 * Override `Enviroment::set()` to allow setting an environment regardless of 
	 * whether there is a matching configuration.
	 *
	 * @param string $env The name of the environment to switch to.
	 * @param array $config Ignored.
	 *
	 * @return null 
	 */
	public static function set($env, $config = null) {
		static::$_current = $env;
		return;
	}

}

?>
