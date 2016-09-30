<?php
namespace lmwp\models;

use lmwp\exceptions\FileNotFoundException;


/**
 * Class DiemProfileManager
 *
 * DAO
 *
 * @package lmwp\models
 */
class DiemProfileManager
{
    public function updateProfile(DiemProfile $profile)
    {
        $profileArray = array(
            'credential'   => $profile->getCredential(),
            'database'     => $profile->getDatabase(),
            'storage'      => $profile->getStorage(),
            'email'        => $profile->getEmail(),
            'label-id'     => $profile->getLabelId(),
            'archive-path' => $profile->getArchivePath(),
            'timezone'     => $profile->getTimezone(),
        );

        try {
            file_put_contents($profile->getProfilePath(), json_encode($profileArray));
        } catch (\Exception $e) {
            error_log($e->getTraceAsString());
        }
    }

    public function loadProfile(string $path) : DiemProfile
    {
        if (file_exists($path) == false) {
            throw new FileNotFoundException(null, 0, null, $path);
        }

        $profileArray = json_decode(file_get_contents($path), true);

        $profile = new DiemProfile();

        $profile->setCredential($profileArray['credential']);
        $profile->setDatabase($profileArray['database']);
        $profile->setStorage($profileArray['storage']);
        $profile->setEmail($profileArray['email']);
        $profile->setLabelId($profileArray['label-id']);
        $profile->setArchivePath($profileArray['archive-path']);
        $profile->setTimezone($profileArray['timezone']);

        $profile->setProfilePath($path);

        return $profile;
    }
}