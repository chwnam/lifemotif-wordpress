<?php

namespace lmwp\services;

use lmwp\models\DiemProfileManager;
use lmwp\models\DiemProfile;


class DiemProfileService implements DiemProfileServiceInterface
{
    private $diemProfileManager;

    public function __construct()
    {
        $this->diemProfileManager = new DiemProfileManager();
    }

    public static function getProfilePathSetting()
    {
        return get_option( 'lm-profile-path' );
    }

    public static function updateProfilePathSetting($path)
    {
        update_option('lm-profile-path', $path);
    }

    public function loadProfile($path = '')
    {
        if (empty($path)) {
            $path = $this->getProfilePathSetting();
        }

        return $this->diemProfileManager->loadProfile($path);
    }

    public function updateProfile(DiemProfile $profile)
    {
        $this->diemProfileManager->updateProfile($profile);
    }

}