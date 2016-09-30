<?php
namespace lmwp\services;

use lmwp\Lmwp;
use lmwp\models\DiemLocalDatabase;


class LifeMotifArticleService
{
    /**
     * @param string $m This text should be like 'YYYYMM' (e.g. 201609)
     *                  Leave it blank to query this month.
     *
     * @return array key: day, value: post ID
     */
    public function getArticlesOfMonth($m = '')
    {
        if (empty($m)) {
            $timezone = get_option('timezone_string');
            $now      = date_create(null, new \DateTimeZone($timezone));
            $m        = $now->format('Ym');
        }

        // 이번 달의 포스트 목록
        $query = new \WP_Query(
            array(
                'post_type'   => 'lifemotif-article',
                'post_status' => array('draft', 'publish'),
                'order'       => 'ASC',
                'orderby'     => 'date',
                'm'           => $m,
                'nopaging'    => true,
            )
        );

        $articles = array();
        foreach ($query->posts as $post) {
            $date = date_create_from_format('Y-m-d H:i:s', $post->post_date);

            $articles[intval($date->format('d'))] = $post->ID;
        }

        return $articles;
    }

    /**
     * Crate posts and map them to message ids in the local (SQLITE3) database.
     *
     * @param $latestMid
     *
     * @return array
     */
    public function mapPostsToMids($latestMid)
    {
        $lmwp    = Lmwp::getInstance();
        $profile = $lmwp->getProfile();

        $localDb = new DiemLocalDatabase($profile->getDatabase());
        $fetched = $localDb->queryIndexList($latestMid);

        $created = $skipped = 0;
        $last    = $latestMid; // last mid

        while ($row = $fetched->fetchArray(SQLITE3_ASSOC)) {

            $last = $mid = intval($row['mid']);

            if ( ! $mid) {
                error_log(__METHOD__ . ": \$mid value '$mid' is invalid. ");
                continue;
            }

            $tid = intval($row['tid']);

            if ( ! $tid) {
                error_log(__METHOD__ . ": \$tid value '$tid' is invalid. ");
                continue;
            }

            $diaryDate = $row['diary_date'];

            if ( ! preg_match('/\d{4}-\d{2}-\d{2}/', $diaryDate)) {
                error_log(__METHOD__ . ": \$diary_date value '$diaryDate' is invalid. ");
                continue;
            }

            if ( ! $this->isPostAlreadyCreated($mid, $tid, $diaryDate)) {
                $this->createEmptyMappedPost($mid, $tid, $diaryDate);
                ++$created;
            } else {
                ++$skipped;
            }
        }

        error_log(__METHOD__ . " finished. $created post(s) created, $skipped mid(s) skipped.");

        return array(
            'last_mid' => $last,
            'created'  => $created,
            'skipped'  => $skipped,
        );
    }

    /**
     * @param $mid
     * @param $tid
     * @param $diaryDate
     *
     * @used-by mapPostsToMids
     *
     * @return bool
     */
    private function isPostAlreadyCreated($mid, $tid, $diaryDate)
    {
        $postName = static::createPostName($mid, $tid, $diaryDate);
        $post     = get_page_by_path($postName, OBJECT, 'lifemotif-article');

        return $post instanceof \WP_Post;
    }

    /**
     * @param $mid
     * @param $tid
     * @param $diaryDate
     *
     * @used-by isPostAlreadyCreated
     *
     * @return string
     */
    public static function createPostName($mid, $tid, $diaryDate)
    {
        return '0x' . dechex($mid) . '-' . '0x' . dechex($tid) . '-' . $diaryDate;
    }

    /**
     * @param $mid
     * @param $tid
     * @param $diaryDate
     *
     * @used-by mapPostsToMids
     */
    private function createEmptyMappedPost($mid, $tid, $diaryDate)
    {
        $dateGmt = date_create($diaryDate, new \DateTimeZone(get_option('timezone_string')));
        $dateGmt->setTimezone(new \DateTimeZone('UTC'));

        $result = wp_insert_post(
            array(
                'post_date'      => "$diaryDate 00:00:00",
                'post_date_gmt'  => $dateGmt->format('Y-m-d H:i:s'),
                'post_title'     => $diaryDate,
                'post_name'      => static::createPostName($mid, $tid, $diaryDate),
                'post_status'    => 'draft',
                'post_type'      => 'lifemotif-article',
                'comment_status' => 'open',
                'ping_status'    => 'closed',
            )
        );

        if (is_wp_error($result)) {
            error_log(__METHOD__ . ': creating a post failed. ' . $result->get_error_message());

            return;
        }

        error_log(__METHOD__ . ": finished. $mid mapped to post ID $result");
    }

    public function importFromDiemJson($postId, $forceUpdate = false)
    {
        $diemWrapperService = DiemWrapperFactoryService::getDiemWrapperService();

        $post = \WP_Post::get_instance($postId);
        if ( ! $post) {
            return false;
        }

        // skip this process if the 'post_status' is 'publish' and not forcing update.
        if ($post->post_status == 'publish' && ! $forceUpdate) {
            return $postId;
        }

        // extract mid from the post
        $post_name_fields = explode('-', $post->post_name);

        $mid = $post_name_fields[0];
        // $tid = $post_name_fields[1];
        $diary_date = "{$post_name_fields[2]}-{$post_name_fields[3]}-{$post_name_fields[4]}";

        // diem extract
        $extracted = json_decode($diemWrapperService->export($mid), true);

        if (is_array($extracted)) {

            // confirm diary-date
            if ($extracted['diary-date'] != $diary_date) {
                throw new \Exception('diary date mismatch!');
            }

            $args = array(
                'ID'            => $postId,
                'post_status'   => 'publish',
                'post_date'     => $post->post_date,
                'post_date_gmt' => $post->post_date_gmt,
                'edit_date'     => 'do_not_edit', // do not edit post date!
            );

            $email_date     = new \DateTime($extracted['email-date']);
            $email_date_gmt = clone $email_date;
            $email_date_gmt->setTimezone(new \DateTimeZone('UTC'));

            $args['post_modified']     = $email_date->format('Y-m-d H:i:s');
            $args['post_modified_gmt'] = $email_date_gmt->format('Y-m-d H:i:s');

            // content
            $args['post_content'] = $extracted['content'];

            // attachments?
            if (isset($extracted['attachments'])) {
                $this->exportAttachments($diemWrapperService, $extracted['attachments'], $mid, $postId);
            }

            // modify post status
            return wp_update_post($args);
        }

        return false;
    }

    /**
     * @param DiemWrapperService       $diemWrapperService
     * @param array                    $attachments
     * @param                          $mid
     * @param                          $postId
     */
    private function exportAttachments($diemWrapperService, array &$attachments, $mid, $postId)
    {
        if (empty($attachments)) {
            return;
        }

        if ( ! function_exists('wp_update_attachment_metadata')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }

        if (strlen($mid) > 2 && substr($mid, 0, 2) == '0x') {
            $_m = substr($mid, 2);
        } else {
            $_m = $mid;
        }

        $uploadDir = \lmwp\getFromAssoc(wp_upload_dir(), 'basedir') . '/' . $_m;
        if ( ! file_exists($uploadDir)) {
            wp_mkdir_p($uploadDir);
        }

        $diemWrapperService->extractAttachments($mid, $uploadDir, true);

        foreach ($attachments as $attachment) {

            $attachmentFileName  = $uploadDir . '/' . $attachment['file-name'];
            $attachmentPostTitle = "{$mid}/{$attachment['content-id']}";

            assert(file_exists($attachmentFileName),
                "Oops! The attachment '$attachmentFileName' is not created!");

            $existingAttachmentPost = get_page_by_title($attachmentPostTitle, OBJECT, 'attachment');

            if ( ! $existingAttachmentPost) {

                $attachmentId = wp_insert_attachment(
                    array(
                        'post_title'     => $attachmentPostTitle,
                        'post_content'   => "",
                        'post_status'    => "inherit",
                        'post_mime_type' => $attachment['content-type'],
                    ),
                    $attachmentFileName,
                    $postId
                );

                $attachmentMetadata = wp_generate_attachment_metadata($attachmentId, $attachmentFileName);
                wp_update_attachment_metadata($postId, $attachmentMetadata);
            }
        }
    }

    public function purgeAllPosts()
    {
        $query = new \WP_Query(
            array(
                'post_type' => 'lifemotif-article',
                'nopaging'  => true,
                'fields'    => 'ids',
            )
        );

        foreach ($query->posts as $post) {
            wp_delete_post($post, true);
        }

        delete_option('lm-mapper-last-output');
    }
}