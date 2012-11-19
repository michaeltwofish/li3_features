# Feature toggle library for the Lithium framework.

[Feature toggling](http://martinfowler.com/bliki/FeatureToggle.html) lets you
turn features on and off by changing a configuration setting. This library
allows simple Boolean feature toggles, feature toggles based on the result of a
function, and feature toggles based on the current environment.

## Installation

Checkout the code to your library directory:

	cd libraries
	git clone https://github.com/michaeltwofish/li3_features.git

Include the library in in your `/app/config/bootstrap/libraries.php`

	Libraries::add('li3_features');

Add feature detectors to your app in `config/bootstrap/features.php` (or
somewhere that makes sense for you).

## Usage

Named features are added using `Features::add()` along with their detector,
which decides whether the feature should be active for the current request.
Features can be turned on or off with a simple Boolean detector, calculated in
some way with a closure detector, or be sensitive to the current environment by
passing an array of environment and detector pairs.

### Basic usage with Boolean detector

		// `config/bootstrap/features.php`
		Features::add('new_ui', true);

		// `controllers/Main.php`
		if (Features::check('new_ui')) {
			$template = 'add_new_ui';
		} else {
			$template = 'add';
		}
		$this->render(compact('template'));

### Closure detector

		// `config/bootstrap/features.php`
		Features::add('new_ui', function() {
				// Logic here to decide if the feature should be enabled.
				// Return true to enable and false to disable.
			}
		);

### Environment-specific detectors

		// `config/bootstrap/features.php`
		Features::add('new_ui', array(
			'production' => false,
			'development' => true,
			'staging' => function() {
				// Logic here to decide if the feature should be enabled.
			}
		));

## Credits

### Michael C. Harris

The original author of this library.

Github: [michaeltwofish](https://github.com/michaeltwofish)

Website: [Twofish Creative](http://www.twofishcreative.com/michael/blog)
