<?php
namespace lmwp;

require_once 'autoloader.php';
require_once 'defines.php';
require_once 'functions.php';

use lmwp\exceptions\FileNotFoundException;
use lmwp\services\DiemProfileService;
use lmwp\models\DiemProfile;


final class Lmwp
{
    /** @var Autoloader */
    private $autoloader;

    /** @var DiemProfile */
    private $profile = null;

    private function __construct()
    {
        // first of all
        $this->initAutoloader();

        // load module
        $this->loadModules();
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function getInstance()
    {
        static $instance = null;

        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    private function initAutoloader()
    {
        $this->autoloader = new Autoloader();

        $this->autoloader->addNamespace('lmwp\\controllers\\', LMWP_CONTROLLERS);
        $this->autoloader->addNamespace('lmwp\\exceptions\\', LMWP_EXCEPTIONS);
        $this->autoloader->addNamespace('lmwp\\models\\', LMWP_MODELS);
        $this->autoloader->addNamespace('lmwp\\modules\\', LMWP_MODULES);
        $this->autoloader->addNamespace('lmwp\\services\\', LMWP_SERVICES);
        $this->autoloader->addNamespace('lmwp\\utils\\', LMWP_UTILS);

        $this->autoloader->register();
    }

    private function initProfile()
    {
        try {
            $dpService = new DiemProfileService();

            $this->profile = $dpService->loadProfile();

        } catch (FileNotFoundException $e) {

            $this->profile = new DiemProfile();
        }
    }

    private function loadModules()
    {
        if (class_exists('SQLite3') === false) {
            modules\Sqlite3NotFoundModule::init();

            return;
        }

        modules\CustomPostsModule::init();
        modules\AdminPageModule::init();
        modules\AdminSettingsPageModule::init();

        // 데이터베이스 설정은 할 수 있도록 세팅 페이지 모듈 뒤에 나와야 함.
        if ( ! $this->getProfile()->getDatabase()) {
            modules\DiemDatabaseNotFoundModule::init();

            return;
        }

        modules\AdminPostModule::init();
        modules\AdminAjaxModule::init();
        modules\ShortcodesModule::init();
    }

    public function getProfile()
    {
        if ( ! $this->profile) {
            $this->initProfile();
        }

        return $this->profile;
    }
}
