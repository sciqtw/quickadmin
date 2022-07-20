<?php
declare (strict_types=1);

namespace app\common\service\common;

use app\common\model\SystemAdminInfo;
use app\common\model\SystemUser;
use app\common\model\SystemUserIdentity;
use app\common\service\CommonService;
use PDO;
use quick\admin\Exception;
use quick\admin\library\tools\CodeTools;
use think\Config;
use think\Db;
use think\db\connector\Mysql;
use think\db\Query;
use think\DbManager;

/**
 * Class CommonAdminUserService
 * @package app\common\service\common
 */
class InstallService extends CommonService
{


    public $db_host;
    public $db_port;
    public $db_username;
    public $db_password;
    public $db_prefix;
    public $db_name;
    public $admin_username;
    public $admin_password;

    /**
     * 验证规则
     * @return array
     */
    protected function rules(): array
    {
        return [
            'db_host' => 'require|max:50',
            'db_port' => 'require|integer',
            'db_username' => 'require',
            'db_password' => 'require',
            'db_prefix' => 'require',
            'db_name' => 'require',
            'admin_username' => 'require',
            'admin_password' => 'require',
        ];
    }


    /**
     * @return array
     */
    protected function attrLabels(): array
    {
        return [
            'db_host' => '数据库服务器',
            'db_port' => '数据库端口',
            'db_username' => '数据库用户',
            'db_password' => '数据库密码',
            'db_prefix' => '数据表前缀',
            'db_name' => '数据库名称',
            'admin_username' => '管理员账号',
            'admin_password' => '管理员密码',
        ];
    }


    public function install()
    {
        if (!$this->validate()) {
            return $this->error($this->getErrorMsg());
        }

        $sql = app()->getRootPath() . 'quick-admin-install.sql';
        if (!file_exists($sql)) {
            return $this->error('缺少安装sql文件：' . $sql);
        }

        $sql = file_get_contents($sql);

        $sql = str_replace("`qk_", "`{$this->db_prefix}", $sql);

        try {
            $pdo = new PDO("mysql:host={$this->db_host}" . ($this->db_port ? ";port={$this->db_port}" : ''), $this->db_username, $this->db_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//            $res =  $pdo->query("SHOW DATABASES LIKE '{$this->db_name}'");
//            return $this->success('安装成功',[$res]);
            $pdo->query("CREATE DATABASE IF NOT EXISTS `{$this->db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");



            $instance = new DbManager();
            $instance->setConfig([
                'default' => 'install',
                'connections' => [
                    'install' => [
                        'type' => 'mysql',
                        'hostname' => $this->db_host,
                        'hostport' => $this->db_port,
                        'database' => $this->db_name,
                        'username' => $this->db_username,
                        'password' => $this->db_password,
                        'prefix' => $this->db_prefix,
                        'charset' => 'utf8mb4',
                    ]
                ]
            ]);

            $instance = $instance->connect('install');
            $instance->execute("SELECT 1");
            $instance->getPdo()->exec($sql);

            // 管理员
            $adminUsername = $this->admin_username;
            $newPassword = $this->admin_password;
            $data = [
                'name' => $adminUsername,
                'nickname' => $adminUsername,
                'status' => 1,
            ];


            $user = SystemUser::where(['username' => $data['name']])->find();
            if($user){
                SystemUserIdentity::where(['user_id' => $user->id])->delete();
                SystemAdminInfo::where(['user_id' => $user->id])->delete();
                $user->delete();
            }

            $data['plugin_name'] = 'admin';
            $user = SystemUser::createAdminUser($data['name'],[
                'password' => $newPassword
            ],[],1);
            $data['user_id'] = $user->id;
            $data['auth_set'] = '';

            $res = $instance->name('system_admin_info')->save($data);



            // 数据库配置文件
            $db_hostname = $this->db_host;
            $db_hostport = $this->db_port;
            $db_username = $this->db_username;
            $db_password = $this->db_password;
            $db_database = $this->db_name;
            $db_prefix = $this->db_prefix;

            $dbConfigFile = app()->getRootPath() . '/config/database.php';
            $config = @file_get_contents($dbConfigFile);
            $callback = function ($matches) use ($db_hostname, $db_hostport, $db_username, $db_password, $db_database, $db_prefix) {
                $field = "db_" . $matches[1];
                $replace = $$field;
                if ($matches[1] == 'hostport' && $db_hostport == 3306) {
                    $replace = 3306;
                }
                return "'{$matches[1]}'{$matches[2]}=>{$matches[3]}env('database.{$matches[1]}', '{$replace}'),";
            };
            $config = preg_replace_callback("/'(hostname|database|username|password|hostport|prefix)'(\s+)=>(\s+)env\((.*)\)\,/", $callback, $config);

            // 检测能否成功写入数据库配置
            $result = @file_put_contents($dbConfigFile, $config);
            if (!$result) {
                throw new Exception('The current permissions are insufficient to write the file config/database.php');
            }


            $this->installLock();
            return $this->success('安装成功',[$res]);
        } catch (\PDOException $e) {
            return $this->error( $e->getMessage());
        } catch (\Throwable $e) {
            return $this->error( $e->getMessage(),['file' => $e->getFile(),'line' => $e->getCode(),'trace' => $e->getTrace()]);
        }

    }

    private function installLock()
    {
        $content = 'install at ' . date('Y-m-d H:i:s') . ' ' . time() . ', ' . app()->request->host();
        file_put_contents(app()->getRootPath() . '/install.lock', base64_encode($content));
    }

}
