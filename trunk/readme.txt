=== Contact Form 7 Newsletter ===
Contributors: katzwebservices, katzwebdesign
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=zackkatz%40gmail%2ecom&item_name=Constant%20Form%207%20Constant%20Contact%20Module
Tags: Contact Form 7, ContactForm7, Constant Contact, Contact Form, Newsletter, Opt In, Email Marketing, form, signup, email newsletter form, newsletter form, newsletter signup, email marketing
Requires at least: 3.2
Tested up to: 4.4
Stable tag: trunk
License: GPLv2 or later

Easily integrate email marketing with the Contact Form 7 plugin. When users contact you, they get added to your newsletter!

== Description ==

> __This plugin requires a <a href="http://wordpress.constantcontact.com" title="Sign up for a free Constant Contact trial" rel="nofollow">Constant Contact account</a>.__ <br />*Don't have an account?* Constant Contact offers a <a href="https://wordpress.constantcontact.com/email-marketing/signup.jsp" rel="nofollow">free 60 day trial</a>, so sign up and give this plugin a whirl!

Automatically add contact form submissions to Constant Contact lists that you choose.

### Get linked with Constant Contact in 60 seconds!
This plugin is very easy to get set up. The instructions are simple:

1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Contact > Constant Contact menu in the WordPress sidebar
1. Enter your Constant Contact login information
1. Configure the Constant Contact section on the Contact Form 7 "Edit" form page

...and you're done!

### Have multiple forms?
You can configure integrations on a per-form basis. Different Contact Form 7 forms can add users to different Constant Contact lists.

#### Features
* Add contacts to multiple lists at once
* Sync form fields to Constant Contact fields, including your Custom Fields
* Add a newsletter opt-in checkbox to your form (see <a href="http://wordpress.org/extend/plugins/contact-form-7-newsletter/faq/">plugin FAQs</a> to learn how)

#### This plugin requires Contact Form 7 4.3 or newer

== Installation ==

1. Upload `contact-form-7-constantcontact to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Contact > Constant Contact menu in the WordPress sidebar
1. Enter your Constant Contact login information
1. Configure the Constant Contact section on the Contact Form 7 "Edit" form page
1. Map the Integration Fields to the form fields available in the drop-down menus. (see the plugin screenshots)

== Frequently Asked Questions ==

= I don't see any settings =

As of version 2.1, this plugin requires Contact Form 7 Version 4.2 or newer. Please update Contact Form 7.

= Do I need a Constant Contact account? =
Yes, this plugin requires a <a href="https://wordpress.constantcontact.com/email-trial/signup.jsp" title="Sign up for Constant Contact" rel="nofollow">Constant Contact account</a>.

= Where is the settings page =

In the WordPress administration, navigate to Contact > Constant Contact in the WordPress sidebar. The URL should be `[yoursite.com]/wp-admin/admin.php?page=ctct_cf7`

= How do I change the name of the lists displayed to users? =

When inserting the Constant Contact List code into your form, you will see code that may look something like this:

`
[ctct ctct-563 type:checkboxes 'General Newsletter::#1' 'Another List::#40']
`

Let's look at the part of the code that is `'General Newsletter::#1'`. That part tells the plugin to create an option with the name `General Newsletter` with a List ID of `1`. `'Another List::#40'` creates another option with the name `Another List` and the List ID is `40`. To modify the name of the list, change the part before `::`. You could then rename "General Newsletter" to "Product Information" by changing the code to `'Product Information::#1'`

= I want to overwrite list selections for existing users. How? =

By default, the plugin will *add* to the subscribed lists for an existing user and not remove them. If you want to remove the lists instead, add the following code to your functions.php file:

`add_filter('ctctcf7_update_contact_lists', '__return_false');`

This will replace existing lists with the selected lists from the form submission.

== Screenshots ==

1. Configure your Constant Contact settings
2. Insert a tag that specifies how you would like users to sign up
3. Map the Contact Form 7 form fields to the Constant Contact fields
4. When a form is connected to Constant Contact, you will see this icon

== Changelog ==

= 2.2 (December 29, 2015) =
* Updated: The plugin now requires Contact Form 7 4.3 or newer
* Fixed: Compatibility with Contact Form 4.3
* Fixed: Only create or update a contact if newsletter lists were found in the form submission
* Fixed: Removed `subscribe_type_radio` class because CF7 now allows for radio inputs to set the field type
* Fixed: Generated tags didn't select properly on focus
* Fixed: Support for installation outside the `wp-content/plugins/` directory
* Fixed: Removed mail-tag instruction from tag generator; it was confusing people

= 2.1 and 2.1.1 (June 11, 2015) =
* **Requires Contact Form 7 4.2** - please update your CF7 plugin
    - Updated interface to look good with CF7 4.2
* Fixed: Updating lists for users
* Fixed: "The requested URL returned error: 400" error
* Fixed: Form submission status not displaying on submission
* Fixed: `add_query_arg()` potential security vulnerability. **Please update!**
* Added: `ctctcf7_update_contact_lists` filter (see FAQ)
* Fixed (2.1.1): Plugin didn't recognize CF7 4.2 properly

= 2.0.6.4 (May 19, 2015) = 
* Fixed: Updated help docs Javascript to fix security issue. **Please update!**

= 2.0.6.2 & 2.0.6.3 (October 24, 2014) =
* Fixed: Contacts not being added to list

= 2.0.6.1 (August 19, 2014) =
* Fixed: Submission errors for sites with error reporting enabled and no list selection added in a form

= 2.0.6 (July 5, 2014) =
* Fixed: Expired API Key. The previous Constant Contact key expired. __This is an important update that fixes the plugin not sending entries to Constant Contact.__
* Modified: Attempt to fix [issue #24](https://github.com/katzwebservices/Contact-Form-7-Newsletter/issues/24)
* Modified: Removed PressTrends integration

= 2.0.5 (February 13, 2014) =
* Fixed: Fatal error causing incomplete loading of form pages. Thanks, [liyo](https://github.com/liyo)

= 2.0 through 2.0.4 =
* Brand-new way of integrating with Contact Form 7 forms! You no longer need to copy and paste form fields. Now there's a simple drop-down menu to pick your Integration Fields.
* Added: When a form is connected to Constant Contact, you will see an icon
* Improved: Handling shortcode processing in CTCT settings
* Fixed: Compatibility with multiple plugins using the same Constant Contact scripts
* 2.0.1 - 2.0.3: Fixed jQuery error on Forms page
* 2.0.4: Added installation instructions and rating box

= 1.1 =
* Adds support for Contact Form 7 3.3 and newer
* Lists will now be updated instead of replaced for existing contacts
* If you don't set an opt-in field, users will no longer receive Welcome to Our Mailing List email.
* Added new "Full Name" tag that will automatically process a full name field into First, Last, and Middle names
* Resolved some PHP warnings

= 1.0.4 =
* Added support for more than 50 contact lists
* Now displays lists in tidy columns on modern browsers
* Restored the "Refresh Lists" link to un-cache lists

= 1.0.3 =
* Renamed Constant Contact API wrapper classes for better namespacing in order to fix <a href="http://wordpress.org/support/topic/plugin-contact-form-7-constant-contact-fatal-error-when-activating">fatal errors</a> when users already have a plugin with the same class names.
* Fixed issue with first-time form setup potentially breaking sites with PHP warnings, <a href="http://wordpress.org/support/topic/plugin-contact-form-7-constant-contact-plugin-doesnt-work-with-php-version-5310">as reported here</a>
* Improved documentation: there is now inline help when setting up form integration.
* Improved plugin translation support
* Fixed issue where forms were being sent to Constant Contact, even when integration checkbox was unchecked, <a href="http://wordpress.org/support/topic/plugin-contact-form-7-constant-contact-all-forms-are-connected-to-constant-contact">as reported here</a>

= 1.0.2 =
* Fixed issue with first-time form setup potentially breaking sites with PHP warnings, <a href="http://wordpress.org/support/topic/plugin-contact-form-7-constant-contact-plugin-doesnt-work-with-php-version-5310">as reported here</a>
* Improved documentation: there is now inline help when setting up form integration.
* Improved plugin translation support

= 1.0.1 =
* Improved documentation: there is now inline help when setting up form integration.
* Improved plugin translation support

= 1.00 =
* Liftoff!

== Upgrade Notice ==
= 2.0.6.3 (October 24, 2014) =
* Fixed: Possible conflicts with older versions of CF7

= 2.0.6.2 (October 24, 2014) =
* Fixed: Contacts not being added to list

= 2.0.6.1 (August 19, 2014) =
* Fixed: Submission errors for sites with error reporting enabled and no list selection added in a form

= 2.0.6 (July 5, 2014) =
* Fixed: Expired API Key. The previous Constant Contact key expired. __This is an important update that fixes the plugin not sending entries to Constant Contact.__
* Modified: Attempt to fix [issue #24](https://github.com/katzwebservices/Contact-Form-7-Newsletter/issues/24)
* Modified: Removed PressTrends integration

= 2.0.5 (February 13, 2014) =
* Fixed: Fatal error causing incomplete loading of form pages. Thanks, [liyo](https://github.com/liyo)

= 2.0 through 2.0.4 =
* Brand-new way of integrating with Contact Form 7 forms! You no longer need to copy and paste form fields. Now there's a simple drop-down menu to pick your Integration Fields.
* Added: When a form is connected to Constant Contact, you will see an icon
* Improved: Handling shortcode processing in CTCT settings
* Fixed: Compatibility with multiple plugins using the same Constant Contact scripts
* 2.0.1 - 2.0.3: Fixed jQuery error on Forms page
* 2.0.4: Added installation instructions and rating box

= 1.1 =
* Adds support for Contact Form 7 3.3 and newer
* Lists will now be updated instead of replaced for existing contacts
* If you don't set an opt-in field, users will no longer receive Welcome to Our Mailing List email.
* Added new "Full Name" tag that will automatically process a full name field into First, Last, and Middle names
* Resolved some PHP warnings

= 1.0.4 =
* Added support for more than 50 contact lists
* Now displays lists in tidy columns on modern browsers
* Restored the "Refresh Lists" link to un-cache lists

= 1.0.3 =
* Renamed Constant Contact API wrapper classes for better namespacing in order to fix <a href="http://wordpress.org/support/topic/plugin-contact-form-7-constant-contact-fatal-error-when-activating">fatal errors</a> when users already have a plugin with the same class names.
* Fixed issue with first-time form setup potentially breaking sites with PHP warnings, <a href="http://wordpress.org/support/topic/plugin-contact-form-7-constant-contact-plugin-doesnt-work-with-php-version-5310">as reported here</a>
* Improved documentation: there is now inline help when setting up form integration.
* Improved plugin translation support
* Fixed issue where forms were being sent to Constant Contact, even when integration checkbox was unchecked, <a href="http://wordpress.org/support/topic/plugin-contact-form-7-constant-contact-all-forms-are-connected-to-constant-contact">as reported here</a>

= 1.0.2 =
* Fixed issue with first-time form setup potentially breaking sites with PHP warnings, <a href="http://wordpress.org/support/topic/plugin-contact-form-7-constant-contact-plugin-doesnt-work-with-php-version-5310">as reported here</a>

= 1.0.1 =
* Improved documentation: there is now inline help when setting up form integration.
* Improved plugin translation support

= 1.00 =
* Liftoff!