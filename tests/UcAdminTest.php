<?php declare(strict_types=1);

namespace Tests;

use Uploadcare\Interfaces\Api\FileApiInterface;
use Uploadcare\Interfaces\File\FileInfoInterface;

class UcAdminTest extends \WP_UnitTestCase
{
    public function testClassExists(): void
    {
        require_once \dirname(__DIR__) . '/uploadcare.php';
        new \Uploadcare_Wordpress_Plugin();
        self::assertTrue(\class_exists(\UcAdmin::class));
        self::assertTrue(\class_exists(\WP_Dependencies::class));
    }

    public function testConfigLoaded(): void
    {
        require_once \dirname(__DIR__) . '/uploadcare.php';
        new \Uploadcare_Wordpress_Plugin();
        $admin = new \UcAdmin('uploadcare-test', \UPLOADCARE_VERSION);
        $reflection = (new \ReflectionObject($admin));
        $plugin_name = $reflection->getProperty('plugin_name');
        $plugin_name->setAccessible(true);
        self::assertEquals('uploadcare-test', $plugin_name->getValue($admin));

        $version = $reflection->getProperty('version');
        $version->setAccessible(true);
        self::assertEquals(\UPLOADCARE_VERSION, $version->getValue($admin));

        $config = $reflection->getProperty('ucConfig');
        $config->setAccessible(true);
        self::assertInstanceOf(\Uploadcare\Configuration::class, $config->getValue($admin));

        $api = $reflection->getProperty('api');
        $api->setAccessible(true);
        self::assertInstanceOf(\Uploadcare\Api::class, $api->getValue($admin));
    }

    public function testSettingsLink(): void
    {
        require_once \dirname(__DIR__) . '/admin/UcAdmin.php';
        require_once \dirname(__DIR__) . '/uploadcare.php';
        $admin = new \UcAdmin('uploadcare-test', 'TEST_VERSION');
        $links = $admin->plugin_action_links([]);
        self::assertNotEmpty($links);
        self::assertStringContainsString(__('Settings', 'uploadcare-test'), $links[0]);
    }

    public function provideRegisteredScripts(): array
    {
        return [
            ['uploadcare-elements'],
            ['uploadcare-widget'],
            ['uploadcare-config'],
            ['uploadcare-main'],
            ['image-block'],
        ];
    }

    /**
     * @dataProvider provideRegisteredScripts
     *
     * @param string $handle
     */
    public function testInitAction(string $handle): void
    {
        require_once \dirname(__DIR__) . '/admin/UcAdmin.php';
        global $wp_scripts;
        if (!$wp_scripts instanceof \WP_Scripts) {
            $wp_scripts = new \WP_Scripts();
        }

        $admin = new \UcAdmin('uploadcare-test', 'TEST_VERSION');
        $admin->uploadcare_plugin_init();

        $registered = $wp_scripts->query($handle);
        self::assertIsObject($registered);
        self::assertEquals($handle, $registered->handle);
        self::assertFalse($wp_scripts->query($handle, 'enqueued'));
    }

    public function provideInPostScripts(): array
    {
        return [
            ['uploadcare-main'],
            ['uc-config'],
            ['uploadcare-elements'],
            ['image-block'],
        ];
    }

    public function testAddUploadcareJsToAdmin(): void
    {
        require_once \dirname(__DIR__) . '/uploadcare.php';
        require_once \dirname(__DIR__) . '/admin/UcAdmin.php';
        $admin = new \UcAdmin('uploadcare-test', 'TEST_VERSION');

        global $wp_scripts;
        if (!$wp_scripts instanceof \WP_Scripts) {
            $wp_scripts = new \WP_Scripts();
        }
        $admin->uploadcare_plugin_init();

        $admin->add_uploadcare_js_to_admin('post.php');

        self::assertArrayHasKey('uploadcare-main', $wp_scripts->registered);
    }

    protected function mockFileInfo(): FileInfoInterface
    {
        $info = $this->getMockBuilder(FileInfoInterface::class)
            ->getMock();
        $info->expects(self::atLeastOnce())->method('getMimeType')->willReturn('image/jpeg');
        $info->expects(self::atLeastOnce())->method('getOriginalFilename')->willReturn('test');
        $info->expects(self::atLeastOnce())->method('getUuid')->willReturn('abcdef-1452-44778');

        return $info;
    }

    public function testUploadcareHandleMethod(): void
    {
        require_once \dirname(__DIR__) . '/uploadcare.php';
        require_once \dirname(__DIR__) . '/admin/UcAdmin.php';
        $admin = new \UcAdmin('uploadcare-test', 'TEST_VERSION');
        $fileUrl = 'https://ucarecdn.com/c9f36f67-6f45-4b6d-a876-699f4dc60730/-/preview/2048x2048/-/quality/lightest/-/format/auto/';

        $_POST['file_url'] = $fileUrl;
        $fileApi = $this->getMockBuilder(FileApiInterface::class)
            ->getMock();
        $fileApi->expects(self::once())->method('fileInfo')->willReturn($this->mockFileInfo());

        $api = $this->getMockBuilder(\Uploadcare\Api::class)
            ->disableOriginalConstructor()
            ->getMock();
        $api->expects(self::once())->method('file')->willReturn($fileApi);

        $reflectionApi = (new \ReflectionObject($admin))->getProperty('api');
        $reflectionApi->setAccessible(true);
        $reflectionApi->setValue($admin, $api);

        $this->expectException(\WPDieException::class);
        $admin->uploadcare_handle();

        $this->expectOutputRegex('attach_id');
    }

    public function testUploadcareMediaUpload(): void
    {
        require_once \dirname(__DIR__) . '/uploadcare.php';
        require_once \dirname(__DIR__) . '/admin/UcAdmin.php';
        $admin = new \UcAdmin('uploadcare-test', 'TEST_VERSION');

        $admin->uploadcare_media_upload();
        $this->expectOutputRegex('/uploadcare/');
    }
}
