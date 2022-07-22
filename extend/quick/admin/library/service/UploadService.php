<?php
declare (strict_types=1);

namespace quick\admin\library\service;

use quick\admin\library\storage\LocalStorage;
use quick\admin\library\storage\Storage;
use quick\admin\Service;
use think\Exception;
use think\file\UploadedFile;

/**
 * Class UploadService
 * @package quick\admin\library\service
 */
class UploadService extends Service
{

    public $uptype = "local";

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     *  安全模式
     *
     * @var bool
     */
    public $safe = false;


    /**
     * 文件类型
     *
     * @var
     */
    public $extension;

    /**
     * 上传文件
     *
     * @param UploadedFile $file
     * @return $this
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        return $this;
    }


    /**
     * 文件类型
     *
     * @return string
     * @throws Exception
     */
    public function getExtension()
    {
        $this->extension = strtolower($this->file->getOriginalExtension());
//        if (!in_array($this->extension, explode(',', strtolower(sysconf('storage.allow_exts'))))) {
//            return json(['uploaded' => false, 'error' => ['message' => '文件上传类型受限，请在后台配置']]);
//        }
        if (in_array($this->extension, ['php', 'sh'])) {
            throw new Exception('可执行文件禁止上传到本地服务器');
        }
        return $this->extension;
    }

    /**
     * 文件名称
     *
     * @return string
     * @throws Exception
     */
    public function getName()
    {
        return Storage::name($this->getFile()->getPathname(), $this->getExtension(), '', 'md5_file');
    }

    public function save()
    {
        if (!($file = $this->getFile()) || empty($file)) {
            throw new Exception('文件上传异常，文件可能过大或未上传');
        }

        $fileName = $this->getName();
        $this->uptype = sysConfig('storage.storage_type');
        if ($this->uptype === 'local') {
            $local = LocalStorage::instance();

//            $info = $local->save($fileName,$file->getRealPath(),$this->safe,$file->getOriginalName());
            $realpath = dirname($realname = $local->savePath($fileName, $this->safe));
            file_exists($realpath) && is_dir($realpath) || mkdir($realpath, 0755, true);
            @rename($file->getPathname(), $realname);
            $info = $local->info($fileName, $this->safe, $file->getOriginalName());

        } else {
            $bina = file_get_contents($file->getRealPath());
            $info = Storage::instance($this->uptype)->save($fileName, $bina, $this->safe, $file->getOriginalName());
        }
        if (is_array($info) && isset($info['url'])) {
            return [
                'uploaded' => true,
                'filename' => $fileName,
                'size' => 0,
                'name' => $this->getFile()->getOriginalName(),
                'url' => $this->safe ? $fileName : $info['url']];
        } else {
            return ['uploaded' => false, 'error' => ['message' => '文件处理失败，请稍候再试！']];
        }
    }

    /**
     * 获取文件上传类型
     * @return boolean
     */
    private function getSafe(): bool
    {
        return boolval(input('safe', '0'));
    }

    /**
     * 获取文件上传方式
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private function getType(): string
    {
        $this->uptype = strtolower(input('uptype', ''));
        if (!in_array($this->uptype, ['local', 'qiniu', 'alioss', 'txcos'])) {
            $this->uptype = 'local';//strtolower(sysconf('storage.type'));
        }
        return strtolower($this->uptype);
    }

    /**
     * 获取本地文件对象
     * @return UploadedFile
     */
    private function getFile(): UploadedFile
    {
        return $this->file;
    }
}
