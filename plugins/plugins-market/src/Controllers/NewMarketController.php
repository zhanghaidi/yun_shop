<?php

namespace Yunshop\PluginsMarket\Controllers;

use app\common\services\AutoUpdate;
use Option;
use app\common\components\BaseController;
use app\common\helpers\Url;
use Ixudra\Curl\Facades\Curl;

class NewMarketController extends BaseController
{
    private $request_domain = 'https://yun.yunzmall.com';

    public function getList()
    {
        $status = request()->status;
        $keyword = request()->keyword;
        $page = max(request()->page,1);
        $limit = 30;
        $plugin_list = $this->ajaxPluginList($status);
        if (!empty($keyword)) {
            foreach ($plugin_list as $k=>$v) {
                if (strpos($v['title'],$keyword) === false) {
                    unset($plugin_list[$k]);
                }
            }
            $plugin_list = array_values($plugin_list);
        }
        $count = count($plugin_list);
        //分页
        $plugin_list = $this->page($plugin_list,$page,$limit,$count);
        $plugin_list = array_values($plugin_list);
        $free_plugin = \Setting::get('free.plugin');
        foreach ($free_plugin as $plugin) {
            foreach ($plugin_list as &$list) {
                if ($list['title'] == $plugin['title']) {
                    $list['key'] = $plugin['key'];
                    $list['secret'] = $plugin['secret'];
                }
            }
        }
        $data['data'] = $plugin_list;
        $data['current_page'] = $page;
        $data['per_page'] = $limit;
        $data['total'] = $count;
        $data['last_page'] = ceil($count/$limit);
        return $this->successJson('',$data);
    }


    public function show()
    {
        return view('Yunshop\PluginsMarket::new_market',[

        ])->render();
    }

    /**
     * 插件处理，区分未安装，已安装，未授权
     *
     * @param int $status 0所有，1未授权，2未安装，3已安装
     * @return array
     */
    private function ajaxPluginList($status = 0)
    {
        $installed_plugins_version_list = self::loadInstalledPluginList();
        $raw_list = $this->getPluginList();


        $auth = $this->authPlugin();
        if (empty($raw_list)) {
            return [];
        }
        //所有
        $plugins_list = array();
        //未安装
        $not_installed_list = array();
        //已安装
        $installed_list = array();
        //未授权
        $preview_list = array();
        //
        $un_auth_list = array();
        $plugin_name_list = array_keys($raw_list);
        foreach ($plugin_name_list as $plugin_name) {
            $each_plugin = self::getSinglePluginInfo($raw_list[$plugin_name]);
            if (!$each_plugin) {
                continue;
            } else {
                $each_plugin['version_status'] = 'un_install';
                $lastesVersion = $each_plugin['versionCom'][0];
                $each_plugin['latestVersion'] = '';
                foreach ($each_plugin['versionList'] as $each_version){
                    if($each_plugin['version'] == $each_version['version']){
                        $each_plugin['versionDescription'] = $each_version['description'];
                        break;
                    }
                }
                $versionList = $each_plugin['versionList'];
                $versionCom = $each_plugin['versionCom'];
                unset($each_plugin['versionList']);
                unset($each_plugin['brief']);
                unset($each_plugin['versionCom']);
                //未授权
                if ($auth[$each_plugin['id']] === 0) {
                    $each_plugin['version_status'] = 'un_auth';
                    $un_auth_list[] = $each_plugin;
                }
                elseif (
                    (!empty($raw_list[$plugin_name]['isPreview']) && $raw_list[$plugin_name]['isPreview']) ||
                    (stripos(end($versionCom), 'rc') > 0) ||
                    (stripos(end($versionCom), 'beta') > 0) ||
                    (stripos(end($versionCom), 'alpha') > 0)) {
                    $each_plugin['version_status'] = 'preview';
                    $preview_list[] = $each_plugin;
                } elseif (!empty($installed_plugins_version_list[$each_plugin['name']])){
                    if(version_compare($lastesVersion, $installed_plugins_version_list[$each_plugin['name']]) == 1) {
                        $each_plugin['version_status'] = 'new';
                        foreach ($versionList as $each_version){
                            if($lastesVersion == $each_version['version']){
                                $each_plugin['latestVersion'] = $lastesVersion;
                                break;
                            }
                        }
                    } else {
                        $each_plugin['version_status'] = 'installed';
                    }
                    $installed_list[] = $each_plugin;
                } else {
                    $not_installed_list[] = $each_plugin;
                }
                $plugins_list[] = $each_plugin;
            }
        }
        switch ($status) {
            case 0:
                return $plugins_list;
            case 1:
                return $un_auth_list;
            case 2:
                return $not_installed_list;
            case 3:
                return $installed_list;
        }
    }

    /**
     * 获取所有本地插件
     * @return array
     */
    private static function loadInstalledPluginList()
    {
        $version_list = array();
        $resource = opendir(base_path('plugins'));
        while ($file_name = @readdir($resource)) {
            if ($file_name == '.' || $file_name == '..')
                continue;
            $plugin_path = base_path('plugins').'/'.$file_name;
            if (is_dir($plugin_path) && file_exists($plugin_path.'/package.json')) {
                $plugin_info = json_decode(file_get_contents($plugin_path.'/package.json'), true);
                $version_list[$plugin_info['name']] = $plugin_info['version'];
            }
        }
        closedir($resource);
        return $version_list;
    }

    /**
     * 获取所有源插件
     * @return mixed|null
     */
    private function getPluginList()
    {
        if (empty(option('market_source'))) {
            //A source maintained by me
            option(['market_source' => config('app.PLUGIN_MARKET_SOURCE') ?: 'https://yun.yunzmall.com/plugin.json']);

        }

        //TODO 加上不同的域名
        $domain = request()->getHttpHost();

        $market_source_path = option('market_source') . '/domain/' . $domain;

        $json_content = '';
        try {
            $json_content = Curl::to($market_source_path)
                ->get();

        } catch (\Exception $e) {
            return null;
        }
        return json_decode($json_content, true);
    }

    /**
     * @param $plugin
     * @return array|bool
     */
    private static function getSinglePluginInfo($plugin)
    {
        if (empty($plugin['name']) || empty($plugin['title']) || empty($plugin['author']) || empty($plugin['url']) || empty($plugin['version'])) {
            return false;
        } else {
            $versionList = [];
            if (!empty($plugin['old'])) {
                // $versions = array_keys($plugin['old']);
                $versionList = $plugin['old'];
            }
            $version = $plugin['version'];

            return array(
                'id'           => $plugin['id'],
                'name'         =>  $plugin['name'],

                'title'        =>  $plugin['title'],

                'description'  =>  empty($plugin['description']) ? trans('Yunshop\PluginsMarket::market.no-description') : $plugin['description'],

                'author'       =>  $plugin['author'],

                'version'      =>  $version,

                'versionList' => $versionList,

                'versionCom'  => $plugin['versionCom'],

                'size'         =>  empty($plugin['size']) ? trans('Yunshop\PluginsMarket::market.unknown') : $plugin['size'],

                'brief'        =>  empty($plugin['brief']) ? '' : $plugin['brief']);
        }
    }

    /**
     * 分页
     * @param $list
     * @param $page
     * @param $limit
     * @param $count
     * @return array
     */
    private function page($list,$page,$limit,$count)
    {
        $start = ($page - 1)*$limit;
        $end = $page*$limit;

        if ($start > $count || $end > $count) {
            $start = floor($count/$limit) * $limit;
            $end = $count;
        }

        $new_list = [];
        for ($i = $start ; $i < $end ; $i++) {
            $new_list[$i] = $list[$i];
        }
        return $new_list;
    }

    //请求接口区分插件搜未授权
    public function authPlugin()
    {
        $domain = request()->getHttpHost();
        $url = $this->request_domain . '/plugin/plugin_authorize/' . $domain;
        $content = Curl::to($url)
            ->asJsonResponse(true)
            ->get();
        $auth = [];
        if ($content['result'] == 1) {
            foreach ($content['data'] as $k=>$v) {
                $auth[$v['plugin_id']] = $v['status'];
            }

            return $auth;
        }
        return [];
    }


}
