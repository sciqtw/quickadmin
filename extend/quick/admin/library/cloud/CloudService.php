<?php
declare (strict_types=1);

namespace quick\admin\library\cloud;


use ErrorException;
use FilesystemIterator;
use quick\admin\library\tools\HttpTools;
use quick\admin\Service;
use Symfony\Component\Finder\Finder;
use think\Exception;

/**
 *
 */
class CloudService extends Service
{

    public $classVersion = '4.2.10';
    public $urlEncodeQueryString = true;

    private $baseUrl = 'https://www.quickadmin.cn'; // 开发

    private $token_cache_key = 'quick_admin_user_token';

    /**
     * @return mixed
     */
    protected function getToken()
    {
        return session($this->token_cache_key);
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        session($this->token_cache_key, $token);
    }

    /**
     * @param string $url
     * @return string
     */
    private function getUrl(string $url)
    {

        if (mb_stripos($url, 'http') === 0) {
            return $url;
        }
        $url = mb_stripos($url, '/') === 0 ? mb_substr($url, 1) : $url;
        $baseUrl = $this->baseUrl;
        $baseUrl = mb_stripos($baseUrl, '/') === (mb_strlen($baseUrl) - 1) ? $baseUrl : $baseUrl . '/';
        return $baseUrl . $url;
    }

    private function getHeaders(): array
    {
        return [
            'headers' => [
                'x-token:' . $this->getToken(),
                'x-version:' . $this->classVersion,
            ]
        ];
    }

    /**
     * @throws CloudNoLoginException
     */
    public function isLogin()
    {
        if (!$this->getToken()) {
            throw new CloudNoLoginException('请先登录！');
        }
    }

    /**
     * @param string $url
     * @param array $params
     * @return mixed
     * @throws CloudException
     */
    public function httpGet(string $url, array $params = [])
    {
        $url = $this->getUrl($url);
        $body = HttpTools::get($url, $params, $this->getHeaders());
        $res = json_decode($body, true);
        if (!$res) {
            throw new \Exception('Cloud response body `' . $body . '` could not be decode.');
        }
        if ($res['code'] !== 0) {
            throw new CloudException($res['msg'], $res['code'], null, $res);
        }
        return $res;
    }


    /**
     * @param string $url
     * @param array $data
     * @return mixed
     * @throws CloudException
     */
    public function httpPost(string $url, array $data = [])
    {
        $url = $this->getUrl($url);
        $body = HttpTools::post($url, $data, $this->getHeaders());
        $res = json_decode($body, true);
        if (!$res) {
            throw new \Exception('Cloud response body `' . $body . '` could not be decode.');
        }
        if ($res['code'] !== 0) {
            throw new CloudException($res['msg'], $res['code'], null, $res);
        }
        return $res;
    }


    /**
     * @param array $params
     * @return bool|string
     * @throws CloudException
     * @throws CloudNoLoginException
     */
    public function pluginDownload(array $params = [])
    {
        $this->isLogin();
        $res = $this->httpGet('plugin/api/user/getDownload',$params);
        $url = $this->getUrl('plugin/api/user/download');
        return HttpTools::get($url, [
            'token' => $res['data']['token']
        ], $this->getHeaders());
    }


    /**
     * @param array $data
     * @return mixed
     * @throws CloudException
     */
    public function login(array $data)
    {
        $res = $this->httpPost('plugin/api/passport/mobileLogin', $data);
        if (empty($res['data']['token'])) {
            throw new \Exception('登录失败');
        }
        $this->setToken($res['data']['token']);
    }

    public function logout()
    {
        $this->setToken(false);
    }


    /**
     * @throws CloudException
     * @throws CloudNoLoginException
     */
    public function userInfo()
    {
        $this->isLogin();
        return $this->httpGet('plugin/api/user/userInfo');
    }

    /**
     * @param $page
     * @param $perPage
     * @return mixed
     * @throws CloudException
     */
    public function pluginList($page, $perPage)
    {
        try {
            $res = $this->httpGet('plugin/api/index/list', [
                'page' => $page,
                'per_page' => $perPage,
            ]);
            return $res;
        }catch (\Exception $e){
            return [
                'code' => 1,
                'msg' => '请求失败',
            ];
        }

    }


}
