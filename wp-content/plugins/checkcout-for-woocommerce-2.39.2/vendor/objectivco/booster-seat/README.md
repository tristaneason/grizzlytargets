Installation
============
TODO

CHANGELOG
=========
* 1.0.10
  * Moved wordpress dependency to dev
* 1.0.9
  * Dropped dotenv version from 3.2.x to 2.5
* 1.0.8
  * Add wordpress core library and wp-phpunit to composer and files to facilitate automatic test setup.
  * Add ability to pass parameters to singleton instance
  * Added test for the new functionality
  * Bumped minimum PHP version from 5.4 > 5.6
* 1.0.7
  * Add remove_all_action before ajax actions are hooked.
* 1.0.6
  * Add the ability to control critical load attributes with overloaded constructor variables.
* 1.0.5
  * Removed return type that should not be there
* 1.0.4
  * Removed bin folder
* 1.0.3
  * Fixed issue with Singleton class.
    * Description: When using the library in multiple plugins the Singleton class was only returning the class firstly ever
    used by it. Obviously this was not intended behavior. It now returns the only the first used instance of each class type