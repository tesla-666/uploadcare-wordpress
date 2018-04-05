# Uploadcare superpowers for WordPress

This is a plugin that lets you add any media to your [WordPress][5] library or
post via [Uploadcare][1]. `uploadcare-wordpress` also helps you accelerate media
content, check out this [article][ext-wparena-article] for details.

The plugin is based on the [uploadcare-php][4] library.

## Requirements

- Wordpress 3.5+
- PHP 5.3+
- php-curl

## Install

### In Wordpress

<a href="https://wordpress.org/plugins/uploadcare/" title="Navigate to the plugin page">
  <img src="https://ucarecdn.com/a6ed4f07-46d4-45f1-9a2e-1bef04d9f21a/InstallFromWP.gif"
       width="888" alt="Installing WP plugin">
</a>

### Manual install

1. Download the [latest release][3]. The download is a ZIP archive holding the
   plugin together with all of its dependencies.
2. Unzip the archive to your `wp-content/plugins` directory.
3. Activate the plugin on your "Plugins" page in a WordPress admin area.
4. Go to "Settings" -> "Uploadcare settings" and set your public and secret API.
   The keys are used to identify an Uploadcare project your uploaded media will
   go to. Please note, you can use `demopublickey` and `demoprivatekey` for
   testing purposes. The keys point to the demo Uploadcare account where all the
   files are wiped out every 24 hours.
   To acquire your own keys, you will need to create
   an [Uploadcare account](https://uploadcare.com/accounts/create/free/). The
   provided link will navigate you to creating a FREE one.
5. Make a new post. That is it!

Feel free to provide [your feedback](mailto:hello@uploadcare.com).

## Usage

1. Start adding a new post.
2. Press "Add Media" to insert any media with
   [Uploadcare Widget](https://uploadcare.com/features/widget/).
3. Upload a file using the widget.
4. When uploading images, you can edit them right in your mobile or desktop
   browser. This adds the modified image to your post. You can learn more about
   in-browser image editing [here](https://uploadcare.com/features/image_processing/).
5. Press "Store and Insert." You are there: an image gets added to your post.

[1]: https://uploadcare.com/
[2]: https://uploadcare.com/documentation/reference/basic/cdn.html
[3]: https://github.com/uploadcare/uploadcare-wordpress/releases
[4]: https://github.com/uploadcare/uploadcare-php
[5]: http://wordpress.org/
[ext-wparena-article]: https://wparena.com/3-must-have-wordpress-plugins-to-start-a-photoblog/
