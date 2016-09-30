<?php
/**
 * @var string $profile_path          프로파일 경로
 * @var string $credential            인증 토큰 경로
 * @var string $database              데이터베이스 경로 (SQLite3)
 * @var string $storage               인증 토큰 저장 파일 경로
 * @var string $email                 이메일
 * @var string $label_id              레이블 아이디
 * @var string $archive_path          아카이브 경로
 * @var string $timezone              시간대
 *
 * @var int    $index_count           로컬 인덱스 수
 * @var bool   $is_db_ok              로컬 데이터베이스를 사용가능한지 확인
 *
 * @var int    $archive_count         아카이브 파일 수
 * @var array  $mappings              매핑 수. 'draft', 'publish' 두 키를 가짐.
 *
 * @var string $archive_redirect_url
 * @var string $mapping_redirect_url
 *
 * @var string $archive_update_output 아카이브 업데이트 조작의 마지막 결과 출력
 * @var string $mapping_update_output 매핑 업데이트 조작의 마지막 결과 출력
 */
?>
<div class="wrap">
    <h2>라이프모티프 관리 페이지</h2>
    <div>
        <h3>프로필 정보</h3>
        <div>
            <table class="wide widefat">
                <tbody>
                <tr>
                    <th>프로필 파일 경로</th>
                    <td><?php echo $profile_path; ?></td>
                </tr>
                <tr>
                    <th>└ credential</th>
                    <td><?php echo $credential; ?></td>
                </tr>
                <tr>
                    <th>└ 데이터베이스 경로</th>
                    <td><?php echo $database; ?></td>
                </tr>
                <tr>
                    <th>└ 인증 정보 경로</th>
                    <td><?php echo $storage; ?></td>
                </tr>
                <tr>
                    <th>└ 이메일 주소</th>
                    <td><?php echo $email; ?></td>
                </tr>
                <tr>
                    <th>└ 이메일 레이블 ID</th>
                    <td><?php echo $label_id; ?></td>
                </tr>
                <tr>
                    <th>└ 아카이브 경로</th>
                    <td><?php echo $archive_path; ?></td>
                </tr>
                <tr>
                    <th>└ 설정된 시간대</th>
                    <td><?php echo $timezone; ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <h3>시스템 상태</h3>
        <div>
            <table class="form-table">
                <tbody>
                <tr>
                    <th>로컬 인덱싱 상황</th>
                    <td><?php echo $index_count; ?></td>
                </tr>
                <tr>
                    <th>아카이브 상황</th>
                    <td><?php echo $archive_count; ?></td>
                </tr>
                <tr>
                    <th>매핑 상황</th>
                    <td>
                        <ul>
                            <li>임시 상태: <?php echo $mappings['draft']; ?></li>
                            <li>읽기 완료: <?php echo $mappings['publish']; ?></li>
                        </ul>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <h3>조작</h3>
        <div>
            <table class="form-table">
                <?php if ($is_db_ok) : ?>
                    <tr>
                        <th>아카이브 갱신</th>
                        <td>
                            <form method="post" action="<?= admin_url('admin-post.php') ?>">
                                <input type="submit" class="button button-secondary" value="클릭하여 갱신 실행"/>
                                <input type="hidden" name="action" value="update-archive"/>
                                <input type="hidden" name="wp-nonce"
                                       value="<?= wp_create_nonce('lmwp-nonce-update-archive') ?>"/>
                                <input type="hidden" name="redirect-uri" value="<?= $archive_redirect_url ?>"/>
                            </form>

                        </td>
                    </tr>
                    <tr>
                        <th>매핑 실행</th>
                        <td>
                            <form method="post" action="<?= admin_url('admin-post.php') ?>">
                                <input type="submit" class="button button-secondary" value="클릭하여 매핑 실행"/>
                                <input type="checkbox" class="checkbox" name="force-update" id="force-update" />
                                <label for="force-update">포스트를 강제로 다시 매핑</label>
                                <input type="hidden" name="action" value="update-mapping"/>
                                <input type="hidden" name="wp-nonce"
                                       value="<?= wp_create_nonce('lmwp-nonce-update-mapping') ?>"/>
                                <input type="hidden" name="redirect-uri" value="<?= $mapping_redirect_url ?>"/>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <th>포스트 모두 지우기!</th>
                        <td>
                            <form method="post" action="<?= admin_url('admin-post.php') ?>">
                                <input type="submit" class="button button-secondary" value="주의! 일기 포스트를 모두 지웁니다!"/>
                                <input type="hidden" name="action" value="purge-mapping"/>
                                <input type="hidden" name="wp-nonce"
                                       value="<?= wp_create_nonce('lmwp-nonce-purge-mapping') ?>"/>
                                <input type="hidden" name="redirect-uri" value="<?= esc_url( $_SERVER['REQUEST_URI'] ) ?>"/>
                            </form>
                        </td>
                    </tr>
                <?php else : ?>
                    <tr>
                        <p>
                            데이터베이스 경로가 올바르지 않아 조작을 할 수 없습니다.
                        </p>
                    </tr>
                <?php endif; ?>
                <?php if ($archive_update_output) : ?>
                    <tr>
                        <th>
                            아카이브 업데이트 출력
                        </th>
                        <td>
                            <?= $archive_update_output ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if ($mapping_update_output) : ?>
                    <tr>
                        <th>
                            아카이브 업데이트 출력
                        </th>
                        <td>
                            <ul>
                                <li>마지막 MID:
                                    <?= $mapping_update_output['last_mid'] ?>
                                    (<?php printf('0x%x', $mapping_update_output['last_mid']); ?>)
                                </li>
                                <li>생성된 포스트 수: <?= $mapping_update_output['created'] ?></li>
                                <li>생략한 포스트 수: <?= $mapping_update_output['skipped'] ?></li>
                            </ul>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
