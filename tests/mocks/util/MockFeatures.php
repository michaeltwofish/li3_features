<?php
/**
 * li3_feature plugin for Lithium: the most rad php framework.
 *
 * @author        Michael C. Harris
 * @copyright     Copyright 2012
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_features\tests\mocks\util;

class MockFeatures extends \li3_features\util\Features {
	/**
	 * Dynamic class dependencies.
	 *
	 * @var array Associative array of class names & their namespaces.
	 */
  protected static $_classes = array(
    'environment' => 'li3_features\tests\mocks\core\MockEnvironment'
  );
}

