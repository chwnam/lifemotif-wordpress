<?php

namespace lmwp\services;

use lmwp\models\DiemProfile;


interface DiemProfileServiceInterface
{
    public function loadProfile();

    public function updateProfile(DiemProfile $profile);
}