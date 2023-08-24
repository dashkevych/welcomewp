=== WelcomeWP ===
Contributors: taskotr
Author URI: https://www.tarascodes.com
Requires at Least: 5.9
Tested Up To: 6.3
Tags: welcome message, greeting message, modal, message, note, alert, notice message, announcements, sticky message, notification, conversion, marketing
Stable tag: 1.0.4
Requires PHP: 7.4
Text Domain: welcomewp
License: GPLv2 or later
License URI:  http://www.gnu.org/licenses/gpl-2.0.html

A simple and easy way to create a welcome message on a WordPress website.

== Description ==

WelcomeWP helps to display a welcome message on a website.

The plugin uses native WordPress API to provide intuitive and secure experience of creating a website welcome message in a foreground, while keeping the website itself readable.

WelcomeWP can be useful for various needs:

* Communicate with your audience without being intrusive
* Increase user engagement on your website
* Elevate customer support via custom notes and announcements
* Boost sales and services of your business via promotions

The plugin allows your welcome message to be expressed as a promotion, note, announcement, greeting, ect.

### Possible use-cases

* Share links to social media, documentation, etc
* Provide important announcements
* Notify about terms and conditions changes
* Introduce yourself to new website visitors
* Greet website visitors
* Inform about launch of new products or services
* Offer time limited discount codes
* Keep posted about COVID-19 updates

== Installation ==

There are two ways to install the plugin. Choose the one that best fits your needs.

Automatic method:

1. Login to your WordPress admin and go to *Plugins > Add New*
2. Type "WelcomeWP" in the search bar and select this plugin
3. Click "Install", and then "Activate Plugin"
4. See "After activation" below

Manual method:

1. Download the plugin from WordPress.org
2. Unzip and Upload the directory `welcomewp` to the `/wp-content/plugins/` directory
3. Activate the plugin through the Plugins menu in WordPress
4. See "After activation" below

After activation:

1. Locate Greeters link in the WordPress dashboard navigation menu (sidebar)
2. Add, Edit, or Remove a Greeter (Greeter = welcome message)
3. Set global settings in *Settings > WelcomeWP*

== Screenshots ==

1. Example of a welcome message, with and active excerpt and custom colors. The message is set to be displayed only in archive views.
2. Example of a welcome message, with and active excerpt and featured image. The message is set to be displayed only in singular views.
3. Example of a welcome message, without excerpt and featured image, yet with custom colors. The message is set to be displayed on all pages in the left bottom corner of the screen.
4. Example of the settings page for the plugin.

== Frequently Asked Questions ==

= What is a welcome message ? =

It is a short text within in a small window that appears in a front of the website content at the bottom corner at the screen, without causing a lot of distraction for visitors.

A website visitor can close the message when needed.

= How the plugin works ? =

After installing and activating the plugin, Greeters link is added to sidebar in dashboard.

The role of a Greeter is to deliver a welcome message to website visitors (Greeter = welcome message).

The Greeters functionality has a similar interface to Posts and Pages. It allows to create, edit and remove messages.

The plugin allows to have a maximum of three (3) active greeters with different display options.

Here is a list of available display options:

* **Inactive** - Greeter with this option is not visible to any website visitors.
* **All pages** - Greeter with this option displays a welcome message on all pages of a website, unless other greeters with Singular or Archive display option are set.
* **Singular views** - Greeter with this option displays a welcome message in single-view pages.
* **Archive views** - Greeter with this option displays a welcome message in archive views, including a blog page.

= Is a welcome message customizable? =

Yes. The plugin allows to have different types of a welcome message, using an existing WordPress functionality such as the content area, the excerpt field and the featured image.

Note, the content area is required. Your message will be invisible if the content area is empty.

**Types of a welcome message:**

* **Message**: Your welcome message is shown without any additional actions made by the user. This type of a welcome message becomes active when you add text to the content area only.
* **Image icon with hidden message**: Initially, only an image is shown as your welcome message. After clicking on the image, your message becomes visible. This type of a welcome message becomes active when you add text to the content area and set the featured image.
* **Image icon with short text and hidden message**: Initially, only an image with a short text are shown as your welcome message. After clicking either on the image or short text, your message becomes visible. This type of a welcome message becomes active when you add text to the content area, set the featured image and enters a short text to the excerpt field.
* **Short text with hidden message**: Initially, only a short text is shown as your welcome message. After clicking on the text, your message becomes visible. This type of a welcome message becomes active when you add text to the content area and to the excerpt field but without setting the featured image.

Additionally, the plugin allows to set custom colors for a welcome message, change its position on the screen and configure a display option.

Note, each message includes a close button for a better user experience

= How the close button works? =

The plugin uses cookie web technology to let the user's browser know which message, based on message ID, should be marked as closed, after the user clicks close button.

This marking has an expiration time, which is by default 5 minutes. The plugin allows to configure it (*Settings > WelcomeWP*).

Also, the user can clear this information from browser any time using browser cleaning functionality.

= Where to report a bug? =

Please use a [support forum](https://wordpress.org/support/plugin/welcomewp/).

== Changelog ==

= 1.0.4 - August 24, 2023 =

* Update formatting in files;
* Fix: undefined variable issue in Search archives;

= 1.0.3 - February 17, 2023 =

* Update: bump up Tested Up To version;
* Fix: style inconsistency in button element in plugin settings page;

= 1.0.2 - August 06, 2022 =

* Update: improve spacing in the footer area of a main content in a welcome message;
* Update: improve spacing in the header area of a welcome message;
* Fix: potential background issue in the close button of a welcome message;
* Fix: missing onboarding help when plugin is installed;
* Fix: overflow issue in a welcome message;

= 1.0.1 - July 21, 2022 =

* Add: minified version for each stylesheet;
* Add: onboarding functionality;
* Add: minified version for each script;
* Update: directory structure for styles;
* Update: load minified version of back-end settings script when needed;
* Update: display onboarding hint when plugin installed and activated;
* Update: load minified version of front-end script when needed;
* Update: documentation for installation;
* Update: use WordPress selected color scheme for onboarding hints;
* Update: display onboarding hints only when no messages have been created yet;

= 1.0.0 - July 06, 2022 =

* Initial beta release.