=== Widget CSS Classes ===
Contributors: elusivelight, keraweb
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=cindy@cleverness.org
Author URI: http://cleverness.org
Plugin URI: https://github.com/cleverness/widget-css-classes
Tags: widgets, classes, css, widget classes, widget css
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.3
Tested up to: 5.0
Requires PHP: 5.2.4
Stable tag: 1.5.3

Add custom classes and ids plus first, last, even, odd, and numbered classes to your widgets.

== Description ==

### Widget CSS Classes gives you the ability to add custom classes and ids to your WordPress widgets ###

_Please note that this plugin doesn't enable you to enter custom CSS. You'll need to edit your theme's style.css or add another
 plugin that allows you to input custom CSS._

__This plugin also adds additional classes to widgets to help you style them easier:__

* widget-first: added to the first widget in a sidebar
* widget-last: added to the last widget in a sidebar
* widget-odd: added to odd numbered widgets in a sidebar
* widget-even: added to even numbered widgets in a sidebar
* widget-#: added to every widget, such as widget-1, widget-2

= Features =

* Adds a text field to a widget for defining a class
* You can specify multiple classes by putting a space between them
* Optionally adds checkboxes with predefined classes
* Optionally adds a text field to add an id to a widget
* Adds first and last classes to the first and last widget instances in a sidebar
* Adds even/odd classes to widgets
* Adds number classes to widgets
* Fully translatable
* Multi-site compatible
* Compatible with Widget Logic, Widget Context, and WP Page Widget plugins
* Has filters and hooks for customizing output including class names

[Plugin Website](https://github.com/cleverness/widget-css-classes/wiki)

== Installation ==

1. Upload the folder _/widget-css-classes/_ to the _/wp-content/plugins/_ directory
2. Activate the plugin through the __Plugins__ menu in WordPress
3. Configure the settings under __Settings > Widget CSS Classes__
4. Visit __Appearance > Widgets__ to add or change the custom classes and ids for a widget.
5. Expand the appropriate widget in the desired sidebar.
6. You'll see a field labeled __CSS Class__. Depending on your settings, this will be a text field and/or checkboxes.
7. If you are using the text field you can enter multiple class names by separating them with a space.
8. If you've enabled the id field, you will see a text field called __CSS ID__.

== Frequently Asked Questions ==

= Why aren't the classes showing up in my widget? =
You need to make sure you have an HTML element defined for `before_widget` and `after_widget` in your active theme's `register_sidebar` functions,
usually located in your theme's functions.php (_/wp-content/themes/yourtheme/functions.php_).

This HTML element must have class and id attributes. This plugin will not work if `before_widget` and `after_widget` are blank.

Example:
```
register_sidebar( array(
	'name'          => 'Sidebar',
	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h2 class="widget-title">',
	'after_title'   => '</h2>'
) );
```

= How do I add the CSS for my custom class? =
There are two ways:

1. Edit your theme's style.css file (usually located in _/wp-content/themes/yourtheme/_).
2. Use a plugin such as [Simple Custom CSS](http://wordpress.org/plugins/simple-custom-css/).

= How I export the Settings? =
You can export the Settings from __Settings > Widget CSS Classes > Import/Export__.

= What should I do if I find a bug? =

Please file a [bug report on GitHub](https://github.com/cleverness/widget-css-classes/issues/new).

== Screenshots ==

1. Basic Widget
2. Widget with ID field and Predefined Choices
3. Settings Page
4. Generated HTML

== Changelog ==

= 1.5.3 =
* **Compatibility:** dFactory Responsive Lightbox widget option. #33
* Tested with WP 5.0.

= 1.5.2.1 =
* **i18n:** Remove sv_SE translation from the plugin directory. It's 95% on translate.wordpress.org and accepted as the better version. [#23](https://github.com/cleverness/widget-css-classes/issues/23) 
* **Documentation:** Readme & Wiki. [#31](https://github.com/cleverness/widget-css-classes/issues/31)
* **Compatibility:** Tested with WordPress 4.9

Detailed info: [PR on GitHub](https://github.com/cleverness/widget-css-classes/pull/32)

= 1.5.2 =
* **Enhancement:** Make translations of core widget classes optional instead of default. [#29](https://github.com/cleverness/widget-css-classes/issues/29)
* **Enhancement:** Allow vertical resize of defined classes box for CSS3 compatible browsers.

Detailed info: [PR on GitHub](https://github.com/cleverness/widget-css-classes/pull/30)

= 1.5.1 =
* **Fix:** Widget Logic `widget_content` filter compatibility. [#27](https://github.com/cleverness/widget-css-classes/issues/27)
* **Enhancement:** Make uninstall script compatible with network installations.

= 1.5.0 =
* **Feature:** Option to try and fix the widget parameters if they are invalid. [#24](https://github.com/cleverness/widget-css-classes/issues/24)
* **Feature:** Option to remove duplicate classes. [#25](https://github.com/cleverness/widget-css-classes/issues/25)
* **Enhancement:** Sort classes based on the predefined classes on the frontend by default. [#19](https://github.com/cleverness/widget-css-classes/issues/19)
* **Enhancement:** Classes filter for frontend (for sorting or modifications). [#19](https://github.com/cleverness/widget-css-classes/issues/19)
  - `widget_css_classes`: modify all classes added by this plugin.
  - `widget_css_classes_custom`: modify custom input classes.
* **Enhancement:** Plugin settings filter (`widget_css_classes_set_settings`), overwrites user settings. [#16](https://github.com/cleverness/widget-css-classes/issues/16)
* **Enhancement:** Plugin default settings filter (`widget_css_classes_default_settings`). [#4](https://github.com/cleverness/widget-css-classes/issues/4)
* **Enhancement:** Capability filters for form fields. [#21](https://github.com/cleverness/widget-css-classes/issues/21)
  - `widget_css_classes_id_input_capability`: ID input
  - `widget_css_classes_class_input_capability`: classes input
  - `widget_css_classes_class_select_capability`: predefined classes select (also hides classes input if invalid)
* **Compatibility:** WP External Links. [#17](https://github.com/cleverness/widget-css-classes/issues/17), thanks to Victor [@freelancephp](https://profiles.wordpress.org/freelancephp)
* **Fix:** Form wrapper div style. [#18](https://github.com/cleverness/widget-css-classes/issues/18), thanks to Chuck Reynolds [@ryno267](https://profiles.wordpress.org/ryno267)
* **Fix:** Enable sortable input selection (IE-11 fix). [#20](https://github.com/cleverness/widget-css-classes/issues/20)
* **UI:** Enhance setting page JavaScript and remove relCopy library dependency.
* **i18n:** Remove Dutch and Russian languages from plugin distribution (available on [translate.wordpress.org](https://translate.wordpress.org/projects/wp-plugins/widget-css-classes)). [#23](https://github.com/cleverness/widget-css-classes/issues/23)
* Started using TravisCI and CodeClimate. [#15](https://github.com/cleverness/widget-css-classes/issues/15)

Detailed info: [PR on GitHub](https://github.com/cleverness/widget-css-classes/pull/22)

= 1.4.0 =
* Feature: Sort Pre-defined classes (thanks Jory Hogeveen)
* Security: Prevent unauthenticated import of settings
* Fix: Notice message when classes is empty (thanks Jory Hogeveen)

= 1.3.0 =
* Feature: Change dropdown to checkboxes for multiple class selection
* Feature: Option to use both predefined and text input classes
* Feature: Migrate classes when predefined classes are available
* Improvement: Do not show previously defined classes that are removed in the settings page when a widget is not updated yet
* Fix: Only show stored classes if the field-type in the setting page is correct
* Fix: When predefined is selected, show previous text input classes if they are defined
* Fix: Ids index notice
* i18n: Added Dutch translation by [Jory Hogeveen at Keraweb](https://www.keraweb.nl/)
* i18n: Added Russian translation by Наталия Завьялова
* i18n: Added Swedish translation by [Olle Gustafsson](http://www.ollegustafsson.com/)

= 1.2.9 =
* Changed h2 to h1 on settings page
* Changed plus/minus icons on settings page to dashicons

= 1.2.8 =
* Added text domain to plugin header in preparation for automatic language translations

= 1.2.7 =
* Changed class and ID fields to full-width
* Added missing escaping from settings page
* Enqueue admin scripts on correct hook
* Fixed undefined notice when option was not found

= 1.2.6 =
* Fixed error notice

= 1.2.5 =
* Fixed notice

= 1.2.4 =
* Added Serbo-Croatian translation by [Borisa Djuraskovic at WebHostingHub](http://www.webhostinghub.com/)
* Added support for WP Page Widget

= 1.2.3 =
* Added Polish translation, Slovak translation files renamed by [Tomasz Wesołowski](https://github.com/ittw)
* Added Spanish translation by [Maria Ramos at WebHostingHub](http://www.webhostinghub.com/)

= 1.2.2 =
* Fix for notice on line 103 when using Widget Logic
* Tested with WordPress 3.7 beta 1

= 1.2.1 =
* Added Slovak translation by Branco [WebHostingGeeks.com](http://webhostinggeeks.com/user-reviews/)
* Updated Widget Context compatibility fix plus notice fix by [Joan Piedra](http://joanpiedra.com/)
* Changed jQuery live to on for 1.9 compatibility

= 1.2 =
* Replace ID with custom ID rather than appending to existing ID
* Added settings to not show numbered widget classes, first/last classes, and even/odd classes

= 1.1 =
* Added support for Widget Context plugin
* Fixed notices appearing when Widget Logic plugin was enabled but filter was disabled
* Added Hide option for the Class Field Type in Settings
* Don't show any previously added IDs in front end if Show Additional Field for ID is set to No
* Don't show any previously added classes in front end if Class Field Type is set to Hide

= 1.0 =
* First version

== Credits ==

- [Adding Custom CSS Classes to WordPress Widgets](http://ednailor.com/2011/01/24/adding-custom-css-classes-to-sidebar-widgets/)
- [Add .first & .last CSS Class Automatically To WordPress Widgets](http://wpshock.com/add-first-last-css-class-automatically-to-wordpress-widgets/)
- Widget Context compatibility fix provided by [Joan Piedra](http://joanpiedra.com/)
- Slovak translation by Branco [WebHostingGeeks.com](http://webhostinggeeks.com/user-reviews/)
- Polish translation added, Slovak translation files renamed by [Tomasz Wesołowski](https://github.com/ittw)
- Spanish translation by [Maria Ramos at WebHostingHub](http://www.webhostinghub.com/)
- Serbo-Croatian translation by [Borisa Djuraskovic at WebHostingHub](http://www.webhostinghub.com/)
- Dutch translation and predefined classes fix by [Jory Hogeveen at Keraweb](https://www.keraweb.nl/)
- Russian translation by Наталия Завьялова
- Swedish translation by [Olle Gustafsson](http://www.ollegustafsson.com/)
- Fix ids notice by [Ricardo Lüders](http://www.luders.com.br/)

[GitHub Contributors](https://github.com/cleverness/widget-css-classes/graphs/contributors)
