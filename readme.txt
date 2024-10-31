=== Post To Sidebar ===
Contributors: dmallon
Tags: widget, pages, posts, sidebar
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 1.1.5

A WordPress plugin/widget that gives you the ability to put content (posts and custom post types) in your sidebar.

== Description ==

The Post To Sidebar plugin makes it easy to display post content in the sidebar areas of your site. Once the widget is activated, a multi-select dropdown of all your published pages appears on post editing screens. Select the pages upon which you want the post to be displayed and the post will appear on those pages.

There are options to hide the post title in the output and to show the content as an excerpt.

== Installation ==

1. Upload `post-to-sidebar` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the *Plugins* menu in WordPress.
1. Go to *Settings > Post to Sidebar Settings* and configure the settings.
1. Your post edit screens will now have Post To Sidebar options available.
1. Place the *Post To Sidebar Widget* in one of your sidebar areas.
1. Enjoy!


== Frequently Asked Questions ==

= Does Post To Sidebar work with custom post types? =

Yes. There are checkboxes on the *Post to Sidebar Settings* page to make the plugin options available where you want them. 

= The widget has no options. What do I do? =

Just put it in your sidebar. That's it. When you create a post, set the options in the *Post To Sidebar Options* meta box.

== Changelog ==
**Version 1.1.5

* Removed support for Justin Tadlock's "Get The Image" script. The implementation was somewhat clunky.

**Version 1.1.4

* Lots of tweaks and fixes. Changed the code to query posts and all custom post types. Uses get_post_types().

**Version 1.1.3**

* Fixed PHP constructor in *widget_post_to_sidebar.php*. Was having issues with PHP versions less than 5.3.

**Version 1.1.2**

* Fixed bad function call in *widget_post_to_sidebar.php* oops.

**Version 1.1.1**

* Now using more unique function naming structure in *widget_post_to_sidebar.php*.
* Update to description in *readme.txt*.

**Version 1.1**

* Minor update.

**Version 1.0**

* First Release.

== Screenshots ==

1. View of the *Post To Sidebar* options page settings.