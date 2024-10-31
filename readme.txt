=== New Post Automatic XML Backup by Email ===
Contributors: Xosen
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=xosen3%40gmail%2ecom&lc=US&item_name=New%20Post%20XML%20Backup%20Email&no_note=0&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_LG%2egif%3aNonHostedGuest
Tags: publish, post, xml, backup, email
Requires at least: 3.1
Tested up to: 3.8.1
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

When a post is published, a backup email is send to the admin with the post content in XML format.

== Description ==

When a post is published or updated, this plugin will send a backup email to the administrator of the WordPress
blog with an attachment file that contains the post in XML format. The XML follows the format used when Wordpress
exports blog contents so that it can be easily used to restore the post.

The general idea of this plugin is to never loose any of your posts. If you have scheduled a backup of your
blog once a week, its possible that you will loose the new posts published during that last week. With this
plugin you will have a backup of all your new published posts in your email account.

== Installation ==

1. Upload the `new-post-automatic-xml-backup-email` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Enjoy!

== Frequently Asked Questions ==

= How to know if the plugin is working? =

Publish a new post or update a previously published post. The blog administrator account should receive an
email with an XML file attachment.

== Changelog ==

= 1.0.3 =
* Changed all functions names to avoid collisions with functions that shared the same name
* Updated the code to the latest WordPress export code
* Sometimes, the xml file had no name since the post_name was blank. In that case we use the post ID as the xml file name

= 1.0.2 =
* Fixed the main file name and the plugin directory name (again)

= 1.0.1 =
* Fixed the main file name and the plugin directory name

= 1.0.0 =
* Initial release
* Sends title, author, last modify by, modify date in the body of the email
* Sends XML file as an attachment

== Upgrade Notice ==

= 1.0.2 =
This version does not work correctly. Please upgrade to latest version.

= 1.0.1 =
This version does not work correctly. Please upgrade to latest version.