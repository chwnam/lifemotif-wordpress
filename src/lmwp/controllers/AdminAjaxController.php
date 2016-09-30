<?php
namespace lmwp\controllers;

use lmwp\services\LifeMotifArticleService;


class AdminAjaxController
{
    public function getArticleCatalog()
    {
        \lmwp\verifyNonce(\lmwp\GET('wp-nonce'), 'lm-datepicker-nonce');

        $articleService = new LifeMotifArticleService();

        $year  = \lmwp\GET('year', 'intval', 0);
        $month = \lmwp\GET('month', 'intval', 0);

        if ($year && $month) {
            $articles = $articleService->getArticlesOfMonth(sprintf('%02d%02d', $year, $month));
            wp_send_json_success($articles);
        }

        wp_send_json_error();
    }

    public function getArticle()
    {
        \lmwp\verifyNonce(\lmwp\GET('wp-nonce'), 'lm-datepicker-nonce');

        $postId = \lmwp\GET('post-id', 'intval', 0);

        $articleService = new LifeMotifArticleService();
        $articleService->importFromDiemJson($postId, true);

        $post = \WP_Post::get_instance($postId);
        if ( ! $post) {
            wp_send_json_error();
        }

        $attachmentPosts = get_children(
            array(
                'post_parent' => $postId,
                'post_type'   => 'attachment',
                'numberposts' => -1,
                'post_status' => 'inherit',
            )
        );

        $images = array();
        $others = array();

        foreach($attachmentPosts as $attachmentPost) {
            list($majorType, $___) = explode('/', $attachmentPost->post_mime_type);
            list($__, $content_id) = explode('/', $attachmentPost->post_title);

            if($majorType == 'image') {
                $images[] = array(
                    'content_id' => $content_id,
                    'url'        => wp_get_attachment_image( $attachmentPost->ID, 'full'),
                );
            } else {
                $others[] = array(
                    'content_id' => $content_id,
                    'url'        => wp_get_attachment_url( $attachmentPost->ID )
                );
            }
        }

        wp_send_json_success(
            array(
                'post' => $post,
                'images' => $images,
                'others' => $others,
            )
        );

        die();
    }
}