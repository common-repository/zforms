=== Plugin Name ===
Contributors: jesse_dev
Donate link: http://www.jessehanson.com/
Tags: forms, Zend Framework, ZF
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 1.2

Easily create forms with an admin interface and customize destinations using Zend Framework. requires WP-ZFF Zend Framework Full

== Description ==

With ZForms, you can create forms using an admin interface, and display the forms using a shortcode. 
It uses Zend Framework to create and validate the forms, so it requires another plugin to load Zend Framework.
Flexible email options are built-in. ReCaptcha support is built-in.
You can control what happens before and after form submission.
It includes the option of requiring a session key to get form access.
It has an api for developers to create custom form handlers eg logging into facebook or sending a transaction to authorize.net. 
You can create your own destination for the validated data, with the option of using the Zend Framework api.
Contact jesse_dev on twitter with plugin-related questions.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Install http://wordpress.org/extend/plugins/wp-zff-zend-framework-full/
1. Upload `zforms` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place shortcode `[zform key="form_key" step="1"]` in your post or page.

== Frequently Asked Questions ==

= What is an example of using the shortcode? =

[zform key="my_form" step="1"]

= What functions are required in my custom handler? =

zfHandle($formData) 
zfSuccess($formData) 
zfError($formData)

== Screenshots ==

1. http://www.jessehanson.com/zforms-screenshot.png
2. http://www.jessehanson.com/zforms-shortcode.png

== Changelog ==

= 1.2 =
* Added submit input control
* Added handler for authorize.net
* Condensed decorator logic
* Hook into WP-ZFF load event

= 1.1 =
* Fixing error with header.

= 1.0 =
* First release.

== Upgrade Notice ==

= 1.2 =
* Added submit input control
* Added handler for authorize.net
* Condensed decorator logic
* Hook into WP-ZFF load event

= 1.1 =
* Fixing error with header.

== Arbitrary section ==

Nothing here for now.
