=== Pco Image Widget Field ===
Contributors: PeytzCo, Compute, jamesbonham
Tags: image, upload, widget
Requires at least: 3.5
Tested up to: 3.9
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add image fields to your custom widgets.

== Description ==

= Features =
Pco Image Widget Field allows developers to add multiple image fields to custom widgets.

By calling `pco_image_field()` inside your widget's form() function, you will be able to give your users a way to add images via the WordPress Media Frame.

= Translations =
* Standard English by [compute](http://profiles.wordpress.org/compute)
* Danish by [compute](http://profiles.wordpress.org/compute)

There are just a few strings to translate. However, it would be great to have more languages available. Please [contact us](mailto:wordpress@peytz.dk) to add your translation to the plugin!

Contribute to [this project](https://github.com/Peytz-WordPress/pco-image-widget-field) on [github](https://github.com/Peytz-WordPress) or find [all of our favorite and custom made plugins](http://profiles.wordpress.org/peytzco/) 

== Installation ==

1. Add the plugin by either downloading the folder and uploading it to the wp-content/plugins directory or install it from the Control Panel using Plugins->Add New.
1. Activate Pco Image Widget Field from the Plugins menu using Plugins->Installed Plugins.
1. Put `pco_image_field( $this, $instance );` inside your Widget's form() method.

= Configuration =

It's possible to add settings to the function by adding a settings array as a third argument: `array( 'title' => 'your-title', 'update' => 'your-update-text', 'field' => 'your-image-field' )`. Here are what each setting does…

* **title** - The title text in the upper left corner of the Media Frame. Defaults to ´Image´
* **update-text** - The text on the update button in the lower right corner of the Media Frame. Defaults to ´Update Image´
* **field** - The field name. Change this if you need multiple images in one widget. Defaults to ´image_id´

Note that this plugin will not save your widget data or show your image anywhere on the front-end. It will simply store the field inside the $new_instance array and wait for you to save the widget. Once the widget instance is saved, you will be able to retrieve the image id from the widget data.

== Frequently Asked Questions ==

= Will this work for WordPress versions earlier than 3.5.0? =
No. This plugin was build to make the Media Frame more useful.
Older versions of WordPress will have to use thickbox instead. You can use the [Widget Image Field](http://wordpress.org/plugins/widget-image-field/) plugin instead, but it's recommended that you update your WordPress installation instead.

= Will this automatically add an image field to my widget when I install it? =

No. This plugin just creates the building blocks.
You will have to manually add the function `pco_image_field( $this, $instance )` inside your widget.

= But I cannot code =
Sad. Try these alternatives instead: [Image Widget](http://wordpress.org/plugins/image-widget/) or [Simple Image Widget](http://wordpress.org/plugins/simple-image-widget/)

This plugin was build to let developers easily add an image field to their widgets. If you have a developer available this will be an easy task.

= I have added the function but it returns warnings and kills my script. Eh? =
Make sure the plugin has been activated. Adding a function that doesn't exists will produce a PHP warning and kill the script. To prevent these errors you can use a function_exists before calling `pco_image_field()`.

= What if I don't declare any fields to the settings array? =
You don't have to define the settings array. The default field is image_id, and is recommended for simplicity.

= What if I want to create multiple image fields inside my widget? =
Define the field in the setting array. For example:
`pco_image_field( $this, $instance, array( 'field' => 'my_image_id' ) );
pco_image_field( $this, $instance, array( 'field' => 'my_next_image_id' ) );
pco_image_field( $this, $instance, array( 'field' => 'my_last_image_id' ) );`

= The image will not save =
Save your field inside your widgets `update()`.

= None of my data is saved =
There is a problem with your widget.

= It seems like it's saving my data but I don't get anything on the frontend? =
Then you will have to output the image in your theme.
Get the image id from `$instance['image_id'];` and use a function like `wp get attachment image();`.

= Well your plugin conflicts with... =
Let us know!
Please add a new ticket inside our support forum!

= Too much hard coding :( . I need a hook/functionality for... =
Please add a new ticket inside our support forum and tell us about the feature request you need!

== Screenshots ==

1. Select your image.
2. Media frame opens. Pick your image and click update.
3. Save your stuff.
4. And output the image in your widget.

== Changelog ==

= 1.0.2 =
* Make sure this plugin works together with the customizer

= 1.0.1 =
* Better handling of styles
* Responsiveness in regards to mp6
* Global: `$pco_iwf` - Globalization of the plugin object
* Filter: `pcoiwf_preview_size` - Change the preview size if you're using wider widgets

= 1.0 =
* First release
