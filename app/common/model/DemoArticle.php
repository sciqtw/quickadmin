<?php
declare (strict_types = 1);

namespace app\common\model;


/**
 * Class app\common\model\DemoArticle
 *
 * @property bool $is_delete
 * @property int $comment_count 评论次数
 * @property int $dislikes 点踩数
 * @property int $id 自增id
 * @property int $likes 点赞数
 * @property int $user_id 用户id
 * @property int $views 浏览次数
 * @property int $weigh 权重
 * @property string $content 内容
 * @property string $created_at
 * @property string $description 描述
 * @property string $image 缩略图
 * @property string $json_key key_value josn
 * @property string $json_list json_list
 * @property string $json_text jsontest
 * @property string $keywords 关键字
 * @property string $tags TAG
 * @property string $title 文章标题
 * @property string $updated_at
 * @property-read \app\common\model\DemoComment[] $comments
 * @property-read \app\common\model\SystemUser $user
 */
class DemoArticle extends BaseModel
{




    protected $name = 'demo_article';

    // 设置json类型字段
    protected $json = ['json_text','json_key','json_list'];


    /**
     * 验证规则
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'require|max:25|min:2',
            'description' => 'require',
        ];
    }


    /**
     * @return array
     */
    public function message(): array
    {
        return [
            'title.require' => '名称必须',
            'title.max' => '名称最多不能超过25个字符',
            'time.checkName' => 'checkName个字符',
            'description'     => 'description必须',
        ];
    }



    /**
     * @return array
     */
    public function attrLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'description' => '解析',
        ];
    }




    public function comments()
    {
        return $this->hasMany(DemoComment::class,"aid","id");
    }


    public function user()
    {
        return $this->belongsTo(SystemUser::class,"user_id","id");
    }
}