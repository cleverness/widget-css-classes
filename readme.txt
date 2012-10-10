=== Widget CSS Classes ===
Contributors: elusivelight
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=cindy@cleverness.org
Author URI: http://cleverness.org
Plugin URI: http://cleverness.org/plugins/widget-css-classes
Tags: widgets, classes, css, widget classes, widget css
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.3
Tested up to: 3.4.2
Stable tag: 1.1

Add custom classes and ids plus first, last, even, odd, and numbered classes to your widgets.

== Description ==

Widget CSS Classes gives you the ability to add custom classes and ids to your WordPress widgets.

This plugin also adds additional classes to help you style your widgets easier:

* widget-first: added to the first widget in a sidebar
* widget-last: added to the last widget in a sidebar
* widget-odd: added to odd numbered widgets in a sidebar
* widget-even: added to even numbered widgets in a sidebar
* widget-#: added to every widget, such as widget-1, widget-2

Features:

* Adds a text field to a widget for defining a class
* You can specify multiple classes by putting a space between them
* Optionally adds a dropdown menu with predefined classes instead of a text field
* Optionally adds a text field to add an id to a widget
* Adds first and last classes to the first and last widget instances in a sidebar
* Adds even/odd classes to widgets
* Adds number classes to widgets
* Fully translatable
* Multi-site compatible
* Compatible with Widget Logic and Widget Context plugins
* Has filters and hooks for customizing output including class names

[Plugin Website](http://cleverness.org/plugins/widget-css-classes/)

== Installation ==

1. Upload the folder '/widget-css-classes/' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the settings under Settings > Widget CSS Classes
4. Visit Appearance > Widgets to add or change the custom classes and ids for a widget.
5. Expand the appropriate widget in the desired sidebar.
6. You'll see a field labeled CSS Class. Depending on your settings, this will be a text field or a dropdown field.
7. If you are using the text field you can enter multiple class names by separating them with a space.
8. If you've enabled the id field, you will see a text field called CSS ID and can also enter multiple ids by separating them with a space.

== Frequently Asked Questions ==

= Why aren't the classes showing up in my widget? =
You must make sure you have an HTML element defined for `before_widget` and `after_widget` in your `register_sidebar` functions. This HTML element must have class and id attributes. This plugin will not work if `before_widget` and `after_widget` are blank.

Example:
`register_sidebar( array(
	'name'          => 'Sidebar',
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h2 class="widget-title">',
	'after_title'   => '</h2>'
) );`

= How I export the Settings? =
You can export the Settings from Settings > Widget CSS Classes > Import/Export.

= What should I do if I find a bug? =

Visit [the plugin website](http://cleverness.org/plugins/widget-css-classes/) and [leave a comment](http://cleverness.org/plugins/widget-css-classes/#respond) or [contact me](http://cleverness.org/contact/).

== Screenshots ==

1. Basic Widget
2. Widget with ID field and Dropdown
3. Settings Page
4. Generated HTML

== Changelog ==

= 1.1 =
* Added support for Widget Context plugin
* Fixed notices appearing when Widget Logic plugin was enabled but filter was disabled
* Added Hide option for the Class Field Type in Settings
* Don't show any previously added IDs in front end if Show Additional Field for ID is set to No
* Don't show any previously added classes in front end if Class Field Type is set to Hide

= 1.0 =
* First version

== Upgrade Notice ==

= 1.1 =
Compatibility fix, bug fix, new feature

= 1.0 =
First version

== Credits ==

[Adding Custom CSS Classes to WordPress Widgets](http://ednailor.com/2011/01/24/adding-custom-css-classes-to-sidebar-widgets/)

[Add .first & .last CSS Class Automatically To WordPress Widgets](http://wpshock.com/add-first-last-css-class-automatically-to-wordpress-widgets/)

Plus/Minus Icons from [Farm Fresh Icons](http://www.fatcow.com/free-icons) by Fat Cow Hosting

Widget Context compatibility fix provided by [Joan Piedra](http://joanpiedra.com/)

== License ==

This file is part of Widget CSS Classes.

Widget CSS Classes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

Widget CSS Classes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this plugin. If not, see <http://www.gnu.org/licenses/>.