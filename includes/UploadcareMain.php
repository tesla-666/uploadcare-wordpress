<?php

class UploadcareMain
{
    public const SCALE_CROP_TEMPLATE = '%s-/stretch/off/-/scale_crop/%s/center/';
    public const RESIZE_TEMPLATE = '%s-/preview/%s/-/quality/lightest/-/format/auto/';
    public const PREVIEW_TEMPLATE = '%s-/preview/160x160/-/resize/160x/-/scale_crop/160x160/';
    public const UUID_REGEX = '/\b[0-9a-f]{8}\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\b[0-9a-f]{12}\b/';

    /**
     * @var UcLoader
     */
    protected $loader;

    /**
     * @var string
     */
    protected $plugin_name;

    /**
     * @var string
     */
    protected $version;

    public function __construct()
    {
        if (defined('UPLOADCARE_VERSION')) {
            $this->version = UPLOADCARE_VERSION;
        } else {
            $this->version = '3.0.0';
        }
        $this->plugin_name = 'uploadcare';
        $this->loader = new UcLoader();

        $this->set_locale();
        $this->define_admin_hooks();
        $this->defineFrontHooks();
    }

    public static function getUuid(string $data = null): ?string
    {
        if ($data === null) {
            return null;
        }

        $matches = [];
        \preg_match(self::UUID_REGEX, $data, $matches);

        return $matches[0] ?? null;
    }

    private function set_locale()
    {
        $this->loader->add_action('plugins_loaded', new UcI18n($this->plugin_name), 'load_plugin_textdomain');
    }

    /**
     * Add hooks and actions for frontend.
     *
     * @return void
     */
    private function defineFrontHooks()
    {
        $ucFront = new UcFront($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('init', $ucFront, 'editorPostMeta');
        $this->loader->add_filter('wp_prepare_attachment_for_js', $ucFront, 'prepareAttachment', 0, 3);
        $this->loader->add_action('wp_enqueue_scripts', $ucFront, 'frontendScripts');
        $this->loader->add_filter('render_block', $ucFront, 'renderBlock', 0, 2);
        $this->loader->add_filter('post_thumbnail_html', $ucFront, 'postFeaturedImage', 10, 5);
    }

    /**
     * Add hooks and actions for backend.
     *
     * @return void
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new UcAdmin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_head', $plugin_admin, 'loadAdminCss');
//        $this->loader->add_action('admin_bar_menu', $this, 'adminBar', 100, 1);
        $this->loader->add_action('plugins_loaded', $this, 'runUploadTask');
        $this->loader->add_action('plugins_loaded', $this, 'runDownloadTask');
        $this->loader->add_action('init', $plugin_admin, 'uploadcare_plugin_init');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'add_uploadcare_js_to_admin');
        $this->loader->add_action('wp_ajax_uploadcare_handle', $plugin_admin, 'uploadcare_handle');
        $this->loader->add_action('wp_ajax_uploadcare_transfer', $plugin_admin, 'transferUp');
        $this->loader->add_action('wp_ajax_uploadcare_down', $plugin_admin, 'transferDown');
        $this->loader->add_action('post-upload-ui', $plugin_admin, 'uploadcare_media_upload');
        $this->loader->add_action('admin_menu', $plugin_admin, 'uploadcare_settings_actions');
        $this->loader->add_action('delete_attachment', $plugin_admin, 'attachmentDelete', 10, 2);

        $this->loader->add_filter('plugin_action_links_uploadcare/uploadcare.php', $plugin_admin, 'plugin_action_links');
        $this->loader->add_filter('load_image_to_edit_attachmenturl', $plugin_admin, 'uc_load', 10, 2);
        $this->loader->add_filter('wp_get_attachment_url', $plugin_admin, 'uc_get_attachment_url', 8, 2);
        $this->loader->add_filter('image_downsize', $plugin_admin, 'uploadcare_image_downsize', 9, 3);
        $this->loader->add_filter('post_thumbnail_html', $plugin_admin, 'uploadcare_post_thumbnail_html', 10, 5);
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * @return string
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * @return string
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * @return UcLoader
     */
    public function get_loader()
    {
        return $this->loader;
    }
}
