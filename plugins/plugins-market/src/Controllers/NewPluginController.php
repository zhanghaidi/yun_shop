<?php

namespace Yunshop\PluginsMarket\Controllers;


use app\common\services\AutoUpdate;
use app\frontend\modules\update\models\authModel;
use Utils;
use Option;
use App\Events;
use ZipArchive;
use Ixudra\Curl\Facades\Curl;
use app\common\services\Storage;
use Illuminate\Http\Request;
use app\common\services\PluginManager;
use app\common\components\BaseController;


class NewPluginController extends BaseController
{
    private $request_domain = 'https://yun.yunzmall.com';

    public function __construct()
    {
        $this->_log = app('log');
    }

    /**
     * 授权插件
     * @return false|mixed|string
     */
    public function authorize()
    {
        $keyData = request()->keyData;
        $pluginData = request()->plugin;
        $domain = request()->getHttpHost();
        $postData = ['domain' => $domain, 'name' => $pluginData['name']];

        //检测密钥
        $res =  $this->isExists($keyData, $postData);
        if ($res) {
            $this->saveIntoOptions($domain,$keyData, $pluginData);
            return $this->successJson('授权成功');
        } else {
            return $this->errorJson('密钥错误，授权失败');
        }


    }

    /**
     * 安装或者升级插件
     * @return false|mixed|string
     */
    public function install()
    {
        $pluginData = request()->plugin;
        //检测数据库有没有对应的key 和secret
        $domain = request()->getHttpHost();
        $res =  $this->existsPlugin($domain, $pluginData['name']);
        \Log::info('------插件安装升级------',$res);
        if ($res) {
            $downloadRes = $this->downloadPlugin($pluginData);
            if ($downloadRes['code'] !== 0) {
                $this->clearOption($domain, $pluginData['name']);
                return $this->errorJson('安装失败');
            }
        } else {
            return $this->errorJson('安装失败');
        }
        return $this->successJson('安装成功');

    }

    /**
     * 批量安装插件
     * @return false|mixed|string
     */
    public function batchInstall()
    {
        $pluginData = request()->plugin;
        //检测数据库有没有对应的key 和secret
        $domain = request()->getHttpHost();
        //批量安装
        $success = 0;
        $fail = 0;
        foreach ($pluginData as $k=>$v) {
            $res =  $this->existsPlugin($domain, $v['name']);
            if ($res) {
                $downloadRes = $this->downloadPlugin($v);
                \Log::info('------插件安装升级------',$res);
                if ($downloadRes['code'] !== 0) {
                    $this->clearOption($domain, $v['name']);
                    $fail++;
                } else {
                    $success++;
                }
            } else {
                $fail++;
            }
        }
        return $this->successJson('安装成功:'.$success.'个，安装失败:'.$fail.'个');
    }

    /*
    * 检测密钥是否存在
    * @param array $data
    */
    private function isExists($keyAndSecret, $postData)
    {
        $filename = env('PLUGIN_MARKET_CHECK', $this->request_domain.'/plugin/check_isKey.json');

        $update = new AutoUpdate();

        $res = $update->isKeySecretExists($filename, $keyAndSecret, $postData);

        \Log::info('------插件授权结果------',$res);

        return $res['isExists'];
    }

    /*
     * 存 key 和 secret 到 option 表
     * @param string $domain
     * @param array $data
     *
     * @return bool
     */
    private function saveIntoOptions($domain, $keyData, $data)
    {
        $keys = json_decode(option('key'), true);
        //dd($keys);
        if($keys[$domain]){
            $keys[$domain][$data['name']] = $keyData;

            Option::set('key', json_encode([$domain => $keys[$domain]]));

        } else {
            Option::set('key', json_encode([$domain =>
                [$data['name'] => $keyData]
            ]));
        }

        return true;
    }

    /*
     * 密钥错误，清除数据库密钥
     * @param string $domain
     * @param string $key
     * @return void
     */
    private function clearOption($domain, $key)
    {
        $keys = json_decode(option('key'), true);
        if($keys[$domain][$key])
            unset($keys[$domain][$key]);
        Option::set('key', json_encode($keys));
    }

    /**
     * 下载文件
     * @param $plugin
     * @return array|false|mixed|string
     */
    private function downloadPlugin($plugin)
    {
        $plugins = app('app\common\services\PluginManager');
        $name = $plugin['name'];
        $code = '';

        if (!$name)
            return ['code' => -1, 'msg' => '名字不存在！'];

        //Prepare download
        $tmp_dir = storage_path('plugin-download-temp');
        $tmp_path = $tmp_dir.'/tmp_'.$name.'.zip';
        if (!is_dir($tmp_dir)) {
            if (false === mkdir($tmp_dir)) {
                return ['code' => 1];
            }
        }
        //Gather URL
        $marketSourcePath = option('market_source') . '/' . request()->getHost() . '/' . $name;

        $ctx = stream_context_create([
            "ssl"=>[
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ]
        ]);

        $json_content = '';
        try {
            $json_content = file_get_contents($marketSourcePath, false, $ctx);
            $code = authModel::orderBy('id', 'desc')->value('code');
        } catch (\Exception $e) {

            return json_encode(['code' => 2, 'msg' => '访问路径不对']);
        }
        $url = json_decode($json_content,true)['url'];


        $url = $url . "/" . $code . '/' . request()->getHost() . ":" . $name;
        //Connection check
        if (!$fp = @fopen($url, 'rb', false, $ctx)) {
            return ['code' => 5, 'msg' => 'url 打不开！'];
        }
        // TODO check
        //Start to download
        try {
            Utils::download($url, $tmp_path);

        } catch (\Exception $e) {
            Storage::removeDir($tmp_dir);
            return ['code' => 3, 'msg' => '下载失败！'];
        }


        $zip = new ZipArchive();
        $res = $zip->open($tmp_path);
        if ($res === true) {
            try {
                $zip->extractTo(base_path('plugins'));
            } catch (\Exception $e) {
                $zip->close();
                Storage::removeDir($tmp_dir);
                return ['code' => 4, 'msg' => '解压失败'];
            }
        } else {
            $zip->close();
            Storage::removeDir($tmp_dir);
            return ['code' => 4 , '解压失败！'];
        }
        $zip->close();

        //Clean temporary working dir
        //Storage::removeDir($tmp_dir);

        //Fire event of plugin was installed
        $plugin = $plugins->getPlugin($name);
        // dd($plugin);
        event(new \Yunshop\PluginsMarket\Events\PluginWasInstalled($plugin));
        return array('code' => 0, 'enable' => option('auto_enable_plugin'));
    }

    /**
     * 检测站点插件是否存在
     * @param $domain
     * @param $plugin
     * @return mixed
     */
    public function existsPlugin($domain, $plugin)
    {
        $url = $this->request_domain . '/plugin/exists_plugin/' . $domain . ':' . $plugin;

        $update = new AutoUpdate();

        $res = $update->isPluginExists($url);

        return $res['isExists'];
    }
}
