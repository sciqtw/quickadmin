<?php
declare (strict_types = 1);

namespace app\common\model;



/**
 * Class app\common\model\DemoComment
 *
 * @property bool $status 状态 1启用 0禁用
 * @property int $aid 关联ID
 * @property int $comments 评论数
 * @property int $id ID
 * @property int $pid 父ID
 * @property int $user_id 会员ID
 * @property string $content 内容
 * @property string $created_at
 * @property string $ip IP
 * @property string $updated_at
 * @property-read \app\common\model\DemoArticle $article
 */
class DemoComment extends BaseModel
{

    protected $name = 'demo_comment';



    public function article()
    {
        return $this->belongsTo(DemoArticle::class,"aid","id");
    }

}