=== WelcomeWP ===
Contributors: taskotr
Author URI: https://www.tarascodes.com
Requires at Least: 5.9
Tested Up To: 6.0
Tags: welcome message, greeting message, modal, message, note, alert, simple
Stable tag: 1.0.0
Requires PHP: 7.4
Text Domain: welcomewp
License: GPLv2 or later
License URI:  http://www.gnu.org/licenses/gpl-2.0.html

A simple and easy way to create a welcome message on a WordPress website.

== Description ==

WelcomeWP is a WordPress plugin, designed to show a welcome message on a WordPress website.

The plugin uses a native WordPress API to provide an intuitive and a secure experience of creating a short message in a foreground of the website content, while keeping the website readable.

Use a welcome message to elevate your customer support, marketing, sales, etc.

== Installation ==

1. Unzip and Upload the directory 'welcomewp' to the '/wp-content/plugins/' directory

2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

== Frequently Asked Questions ==

= What is a welcome message ? =

It is a short text within in a small window that appears in a front of the website content at the bottom corner at the screen, without causing a lot of distraction for visitors.

A website visitor can close the message when needed.

= How it works ? =

After installing and activating the plugin, Greeters link is added to sidebar in dashboard.

The role of a Greeter is to deliver a welcome message to website visitors (Greeter = welcome message).

The Greeters functionality has a similar interface to Posts and Pages. It allows to create, edit and remove messages.

The plugin allows to have a maximum of three (3) active greeters with different display options.

**Available display options:**

* **Inactive**: Greeter with this option is not visible to any website visitors.
* **All pages**: Greeter with this option displays a welcome message on all pages of a website, unless other greeters with Singular or Archive display option are set.
* **Singular views**: Greeter with this option displays a welcome message in single-view pages.
* **Archive views**: Greeter with this option displays a welcome message in archive views, including a blog page.

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

This marking has an expiration time, which is by default 5 minutes. The plugin allows to configure it (Settings > WelcomeWP).

Also, the user can clear this information from browser any time using browser cleaning functionality.

== Changelog ==

= 1.0.0 - July 06, 2022 =

* Initial beta release.