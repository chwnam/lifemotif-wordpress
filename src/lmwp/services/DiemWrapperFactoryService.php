<?php
/**
 * Created by PhpStorm.
 * User: changwoo
 * Date: 16. 9. 25
 * Time: 오후 8:50
 */

namespace lmwp\services;


class DiemWrapperFactoryService
{
    public static function getDiemWrapperService()
    {
        $profile_path = get_option('lm-profile-path');
        $python_path  = get_option('lm-python-path');
        $diem_path    = get_option('lm-diem-path');
        $log_level    = get_option('lm-log-level');
        $log_file     = get_option('lm-log-file');

        return new DiemWrapperService(
            $python_path,
            $diem_path,
            $profile_path,
            $log_level,
            $log_file
        );
    }
}