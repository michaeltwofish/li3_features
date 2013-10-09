<?php
/**
 * li3_feature plugin for Lithium: the most rad php framework.
 *
 * @author        Michael C. Harris
 * @copyright     Copyright 2012
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_features\util;

use lithium\action\Dispatcher;

/**
 * `Features` provides a way to selectively release features.
 *
 */
class Features extends \lithium\core\StaticObject {

	/**
	 * A collection of the configurations stored through `Features::add()`. Each
	 * configuration holds a set of named features and their detectors.
	 *
	 * @var object `Collection` of available features.
	 */
	protected static $_features = array();

	/**
	 * Dynamic class dependencies.
	 *
	 * @var array Associative array of class names & their namespaces.
	 */
	protected static $_classes = array(
		'environment' => 'lithium\core\Environment'
	);

	/**
	 * The current request object, to be passed to closure detectors.
	 *
	 * @var object `Request` object representing the current request.
	 */
	protected static $_request = null;

	public static function __init() {
		/**
		 * Capture the current request
		 */
		Dispatcher::applyFilter('_callable', function($self, $params, $chain) {
			Features::setRequest($params['request']);
			return $chain->next($self, $params, $chain);
		});

	}

	/**
	 * Store the current request object, to be passed to closure detectors.
	 *
	 * @param object `Request` object representing the current request.
	 */
	public static function setRequest($request) {
		static::$_request = $request;
	}

	/**
	 * Add feature detectors to your app, for example in `config/bootstrap/features.php`
	 *
	 * Some examples:
	 * {{{
	 * Features::add('feature_name', true);
	 * }}}
	 *
	 * or using a closure
	 *
	 * {{{
	 * Features::add('new_ui', function() {
	 *   // Logic here to decide if the feature should be enabled.
	 *   // Return true to enable and false to disable.
	 * });
	 * }}}
	 *
	 * or sensitive to the environment
	 *
	 * {{{
	 * // `config/bootstrap/features.php`
	 * Features::add('new_ui', array(
	 *   'production' => false,
	 *   'development' => true,
	 *   'staging' => function() {
	 *     // Logic here to decide if the feature should be enabled.
	 *   }
	 * ));
	 * }}}
	 *
	 * @param string $name The name by which this feature is referenced.
	 * @param mixed $detector The detector that will determine whether the feature
	 *        is enabled. The detector can be one of the following types:
	 *        - _Boolean_: The named feature is simple enabled or disabled for all
	 *          environments.
	 *        - _closure_: The closure is called to determine if the feature is
	 *          enabled for the current request. For example, this could be used to enable a
	 *          feature for a random user subset or allow beta testers to gain early
	 *          access to a feature.
	 *        - _array_: An array of environment and detector pairs, where the
	 *          detector is one of the above types.
	 * @return mixed Returns the stored detector.
	 *
	 */
	public static function add($name, $detector) {
		return static::$_features[$name] = $detector;
	}

	/**
	 * Performs a check of the specified feature against stored features.
	 * Features that have not been defined will always be false.
	 *
	 * @see lithium\core\Environment
	 * @param string $name The name of the feature to check.
	 * @param array $params An array of parameters to be passed to the detector.
	 *
	 * @return Boolean true if the feature should be enabled for this request and
	 * false otherwise.
	 *
	 * @todo consider if it's worth passing in closures to execute based on the
	 * truthiness or falsiness of the check.
	 * @todo consider whether to log missing features.
	 * @todo throw an exception if the environment doesn't exist
	 */
	public static function check($name, array $params = array()) {
		$defaults = array();
		$params += $defaults;
		$params['request'] = static::$_request;

		if (($detector = static::_detector($name)) === null) {
			return false;
		}
		if (is_array($detector)) {
			$env = static::$_classes['environment'];
			$env = $env::get();
			$detector = $detector[$env];
		}
		if (is_bool($detector)) {
			return $detector;
		}
		$filter = function($self, $params) {
			return $params['detector']($params);
		};
		$params = array_merge($params, compact('name', 'detector'));
		return static::_filter(__METHOD__, $params, $filter);
	}

	/**
	 * Gets the detector for the given named feature in the current
	 * environment.
	 *
	 * @param string $name Named feature.
	 * @return mixed Detector for the named feature.
	 */
	protected static function _detector($name) {
		if (!isset(static::$_features[$name])) {
			return null;
		}
		$detector = static::$_features[$name];
		return $detector;
	}


	/**
	 * Exports an array of features and whether they are currently enabled or
	 * disabled
	 *
	 * @param array $params An array of parameters that will be passed to each
	 * detector
	 * @return array Returns a list of booleans keyed by feature names
	 */
	public static function export(array $params = array()) {
		$returnable = array();
		if (empty(static::$_features)) {
			return $returnable;
		}
		foreach (array_keys(static::$_features) as $name) {
			$returnable[$name] = static::check($name, $params);
		}
		return $returnable;
	}

}
?>
