<?php
namespace lmwp\controllers;

use lmwp\Lmwp as Lmwp;
use lmwp\services\DiemLocalDatabaseService;
use lmwp\services\DiemDirectoryService;


class AdminPageController
{
    public function lifeMotifSummary()
    {
        $lmwp    = Lmwp::getInstance();
        $profile = $lmwp->getProfile();

        $dbService  = new DiemLocalDatabaseService($profile->getDatabase());
        $dirService = new DiemDirectoryService($profile);

        $isDbOk      = $dbService->isDatabaseOk();
        $isArchiveOk = $dirService->isArchiveOk();

        $profileContext = array(
            // 프로파일 경로와 그 내용들 덤프
            'profile_path' => $profile->getProfilePath(),
            'credential'   => $profile->getCredential(),
            'database'     => $profile->getDatabase() . ($isDbOk ? '' : '&nbsp;<span>(경로 올바르지 않음)</span>'),
            'storage'      => $profile->getStorage(),
            'email'        => $profile->getEmail(),
            'label_id'     => $profile->getLabelId(),
            'archive_path' => $profile->getArchivePath() . ($isArchiveOk ? '' : '&nbsp;<span>(경로 올바르지 않음)</span>'),
            'timezone'     => $profile->getTimezone(),
        );

        $localDatabaseContext = array(
            'index_count' => $isDbOk ? $dbService->getIndexCount() : '데이터베이스 경로가 올바르지 않아 인덱스 정보를 알아낼 수 없습니다.',
            'is_db_ok'    => $isDbOk,
        );

        $postCounts = wp_count_posts('lifemotif-article', 'readable');

        $repositoryContext = array(
            'archive_count' => $isArchiveOk ? $dirService->getArchiveCount() : '경로가 올바르지 않아 정보를 알아낼 수 없습니다.',
            'mappings'      => array(
                'draft'   => $postCounts->draft,
                'publish' => $postCounts->publish,
            ),
        );

        // manipulation context
        $archiveRedirectUrl = add_query_arg('a', 1, $_SERVER['REQUEST_URI']);
        $archiveRedirectUrl = remove_query_arg('m', $archiveRedirectUrl);
        $mappingRedirectUrl = add_query_arg('m', 1, $_SERVER['REQUEST_URI']);
        $mappingRedirectUrl = remove_query_arg('a', $mappingRedirectUrl);

        // 조작 결과 파라미터 'a', 'm'
        $archiveUpdateOutput = \lmwp\GET('a', 'intval', 0) ? nl2br(get_option('lm-diem-last-output')) : '';
        $mappingUpdateOutput = \lmwp\GET('m', 'intval', 0) ? get_option('lm-mapper-last-output', array()) : '';

        $manipulationContext = array(
            // 라디이렉트 지시
            'archive_redirect_url'  => $archiveRedirectUrl,
            'mapping_redirect_url'  => $mappingRedirectUrl,
            // 조작 후 결과 출력
            'archive_update_output' => $archiveUpdateOutput,
            'mapping_update_output' => $mappingUpdateOutput,
        );

        \lmwp\getTemplate(
            'admin-pages/lifemotif-summary',
            array_merge(
                $profileContext,
                $localDatabaseContext,
                $repositoryContext,
                $manipulationContext
            )
        );
    }
}

