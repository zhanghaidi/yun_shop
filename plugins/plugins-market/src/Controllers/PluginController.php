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


class PluginController extends BaseController
{
    private $request_domain = 'https://yun.yunzmall.com';

    public function __construct()
    {
        $this->_log = app('log');
    }

    public function readyToDownload()
    {
        $keyData = \YunShop::request()->keyData;
        $msg = ['msg' => '没有这样的数据', 'code' => -2];
       // dd($keyData);
        if($keyData) {
            $pluginData = \YunShop::request()->plugin;
           // $res = $this->readyToProcessingKey($keyData, $pluginData);
            $msg = ['msg' => 'Key或密钥出错', 'code' => -3];
            $domain = rtrim(request()->getHttpHost(), '/');
            $postData = ['domain' => $domain, 'name' => $pluginData['name']];
            $res =  $this->isExists($keyData, $postData);
            //dd($res);
            if(!$res)
                return json_encode($msg);

            //key 和 密钥存在 走下载安装 或者更新
            $downloadRes = $this->processingKey($keyData, $pluginData, $domain, $postData);
            if($downloadRes['code'] !== 0) {
                $this->clearOption($domain, $pluginData['name']);
            }
            //dd($downloadRes);
            return json_encode($downloadRes);

        }

        return json_encode($msg);

    }
    /*
    * 检测是不是存在key 和secret
    */
    public function check()
    {
        $pluginData = \YunShop::request()->plugin;
        $msg = ['msg' => '没有这样的数据', 'code' => -2];
        //检测数据库有没有对应的key 和secret
        $domain = rtrim(request()->getHttpHost(), '/');
        $res =  $this->existsPlugin($domain, $pluginData['name']);

        if(!$res)
            return json_encode($msg);

        $downloadRes = $this->downloadPlugin($pluginData);

        if($downloadRes['code'] !== 0) {
            $this->clearOption($domain, $pluginData['name']);
        }

        return  json_encode($downloadRes);
    }

    /*
     * 检测是否更新
     */
    public function  checkIsUpdate()
    {
        $pluginData = \YunShop::request()->plugin;
        $msg = ['msg' => '没有这样的数据', 'code' => -2];
        //检测数据库有没有对应的key 和secret
        $domain = rtrim(request()->getHttpHost(), '/');
        $res =  $this->existsPlugin($domain, $pluginData['name']);

        if(!$res)
            return json_encode($msg);

        $downloadRes = $this->downloadPlugin($pluginData);

        if($downloadRes['code'] !== 0) {
            $this->clearOption($domain, $pluginData['name']);
        }

        return  json_encode($downloadRes);
    }

    /*
    * 检测是否存在
     * @param array $data
    */
    private function isExists($keyAndSecret, $postData)
    {
        $filename = env('PLUGIN_MARKET_CHECK', 'https://yun.yunzmall.com/plugin/check_isKey.json');

        $update = new AutoUpdate();

        $res = $update->isKeySecretExists($filename, $keyAndSecret, $postData);

        return $res['isExists'];
    }

    /*
     * 处理下载   1.保存到成group
     *
     * @param $keyData
     * @param $pluginData
     * @param string $domain
     * @return bool
     */
    private function processingKey($keyData, $pluginData, $domain, $postData)
    {
        // 1. save into option table
        $this->saveIntoOptions($domain,$keyData, $pluginData);

        // 2. go into download
        $res = $this->downloadPlugin($pluginData);
        //dd($res);
        if($res['code'] !== 0) {
           $this->clearOption($domain, $pluginData['name']);
        }
        //dd($res['code']);
        return  $res;
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
     * 清空option 相关数据段
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

    private function downloadPlugin($plugin)
    {
        $plugins = app('app\common\services\PluginManager');
        $name = $plugin['name'];

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

        $url = $url . "/" . $code . '/' . rtrim(request()->getHost(), '/') . ":" . $name;
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
        Storage::removeDir($tmp_dir);

        //Fire event of plugin was installed
        $plugin = $plugins->getPlugin($name);
       // dd($plugin);
        event(new \Yunshop\PluginsMarket\Events\PluginWasInstalled($plugin));
        return array('code' => 0, 'enable' => option('auto_enable_plugin'));
    }

    public function updateCheck()
    {
        if (empty(option('plugin_update_notification')))
            Option::set('plugin_update_notification', 'release_only');
        $notification = option('plugin_update_notification');
        if ($notification == 'none')
            return;

        $newVersionCount = array('release' => 0, 'pre' => 0);
        $installedPluginsVersionList = MarketController::loadInstalledPluginList();
        //@todo  marketPluginsVersionList  updateList
        $marketPluginsVersionList = array();
        $updateList = array();

        if (empty(option('market_source'))) {
            //A source maintained by me
            Option::set('market_source', 'https://yun.yunzmall.com/plugin.json');
        }
        $marketSourcePath = option('market_source');
        $jsonContent = '';
        try {
            $jsonContent = file_get_contents($marketSourcePath);
        } catch (\Exception $e) {
            exit(0);
        }

        $marketPluginsList = json_decode($jsonContent, true);
        foreach ($installedPluginsVersionList as $name => $currentVersion) {

            if (empty($marketPluginsList[$name]['version']))
                continue;
            if ((!empty($marketPluginsList[$name]['isPreview']) && $marketPluginsList[$name]['isPreview']) ||
                stripos($marketPluginsList[$name]['version'], 'rc') > 0 ||
                stripos($marketPluginsList[$name]['version'], 'beta') > 0 ||
                stripos($marketPluginsList[$name]['version'], 'alpha') > 0) {
                    $newVersionCount['pre']++;
            } elseif (version_compare($marketPluginsList[$name]['version'], $currentVersion) == 1)
                $newVersionCount['release']++;
        }
        if ($notification == 'release_only') {
            $newVersionCount['pre'] = 0;
        }
        //@todo change url
        $marketLink = url('admin/plugins-market');
        if (option('replace_default_market')) {
            $marketLink = url('admin/plugins/market');
        }
        return response()->json(array('url' => $marketLink, 'count' => $newVersionCount));
    }

    public function pluginToDownload()
    {
        $keyData = \YunShop::request()->keyData;
        $msg = ['msg' => '没有这样的数据', 'code' => -2];
        // dd($keyData);
        if($keyData) {
            $pluginData = \YunShop::request()->plugin;
            // $res = $this->readyToProcessingKey($keyData, $pluginData);
            $msg = ['msg' => 'Key或密钥出错', 'code' => -3];
            $domain = rtrim(request()->getHttpHost(), '/');
            $postData = ['domain' => $domain, 'name' => $pluginData['name']];
            $res =  $this->isExists($keyData, $postData);
            //dd($res);
            if(!$res)
                return json_encode($msg);

            //key 和 密钥存在 走下载安装 或者更新
            $downloadRes = $this->processingKey($keyData, $pluginData, $domain, $postData);
            if($downloadRes['code'] !== 0) {
                $this->clearOption($domain, $pluginData['name']);
            }
            //dd($downloadRes);

            $plugins = app('app\common\services\PluginManager');
            $plugin = $plugins->getPlugin($pluginData['name']);

            event(new \app\common\events\PluginWasEnabled($plugin));
            return json_encode($downloadRes);

        }

        return json_encode($msg);
    }

    public function existsPlugin($domain, $plugin)
    {
        $url = $this->request_domain . '/plugin/exists_plugin/' . $domain . ':' . $plugin;

        $update = new AutoUpdate();

        $res = $update->isPluginExists($url);

        return $res['isExists'];
    }
}
