<?php
namespace lmwp\controllers;

use lmwp\services\DiemWrapperFactoryService;
use lmwp\services\LifeMotifArticleService;

class AdminPostController
{
    public function updateArchive()
    {
        \lmwp\verifyNonce(\lmwp\POST('wp-nonce'), 'lmwp-nonce-update-archive');

        $wrapperService = DiemWrapperFactoryService::getDiemWrapperService();
        $result         = $wrapperService->fetchIncrementally();
        if ($result) {
            update_option('lm-diem-last-output', $result);
        }

        $this->doRedirect();
    }

    public function updateMapping()
    {
        \lmwp\verifyNonce(\lmwp\POST('wp-nonce'), 'lmwp-nonce-update-mapping');

        $articleService = new LifeMotifArticleService();
        $lastOutput     = get_option('lm-mapper-last-output', array());
        $latestMid      = \lmwp\getFromAssoc($lastOutput, 'last_mid', 'intval', 0);
        $output         = $articleService->mapPostsToMids($latestMid);
        if ($output) {
            update_option('lm-mapper-last-output', $output);
        }

        $this->updateFirstArchiveDate();
        $this->doRedirect();
    }

    public function purgeMapping()
    {
        \lmwp\verifyNonce(\lmwp\POST('wp-nonce'), 'lmwp-nonce-purge-mapping');

        $articleService = new LifeMotifArticleService();
        $articleService->purgeAllPosts();
        $this->doRedirect();
    }

    private function doRedirect()
    {
        $redirectUrl = \lmwp\POST('redirect-uri', 'esc_url_raw');
        if ($redirectUrl) {
            wp_redirect($redirectUrl);
        } else {
            die(0);
        }
    }

    private function updateFirstArchiveDate()
    {
        $query = new \WP_Query(
            array(
                'post_type'      => 'lifemotif-article',
                'order'          => 'ASC',
                'orderby'        => 'date',
                'cache_results'  => false,
                'posts_per_page' => 1,
                'offset'         => 0,
            )
        );
        if ($query->post_count == 1) {
            $post     = $query->posts[0];
            $datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $post->post_date);
            update_option('lm-first-archive-date', $datetime->format('Y-m-d'));
        }
    }
}
