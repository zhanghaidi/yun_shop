<?php

namespace Yunshop\PluginsMarket\Controllers;

use Option;
use app\common\components\BaseController;
use app\common\helpers\Url;
use Ixudra\Curl\Facades\Curl;
use vierbergenlars\SemVer\version;

class MarketController extends BaseController
{

    public static $php_version = '5.6.0';

    public function show()
    {
        $plugin_list = $this->ajaxPluginList();

        $free_plugin = \Setting::get('free.plugin');

        foreach ($free_plugin as $plugin) {
            foreach ($plugin_list as &$list) {
                if ($list['title'] == $plugin['title']) {
                    $list['key'] = $plugin['key'];
                    $list['secret'] = $plugin['secret'];
                }
            }
        }

        return view('Yunshop\PluginsMarket::market', [
            'data' => $plugin_list,
            'uninstall' => !$this->checkPHPVersion(self::$php_version)
        ])->render();
    }


    public function ajaxPluginList()
    {
        $installed_plugins_version_list = self::loadInstalledPluginList();
        $raw_list = self::getPluginList();

        if (!empty($raw_list['php_version'])) {
            self::$php_version = $raw_list['php_version'];
        }

        //print_r($installed_plugins_version_list);
        if (empty($raw_list)) {
            return [];
            //return response()->json(array('recordsTotal' => 0, 'data' => array()));
        }
        $plugins_list = array();
        $plugin_name_list = array_keys($raw_list);
        foreach ($plugin_name_list as $plugin_name) {
            $each_plugin = self::getSinglePluginInfo($raw_list[$plugin_name]);
            if (!$each_plugin) {
                continue;
            } else {
                $version_status = '';

                $lastesVersion = $each_plugin['versionCom'][0];
                // echo $each_plugin['name'].' -> ' .$lastesVersion . '------' . $installed_plugins_version_list[$each_plugin['name']];
                // echo "=== " . version_compare($lastesVersion, $installed_plugins_version_list[$each_plugin['name']]) . '<hr/>';

                $updateVersion = '';
                if (
                    (!empty($raw_list[$plugin_name]['isPreview']) && $raw_list[$plugin_name]['isPreview']) ||
                    (stripos(end($each_plugin['versionCom']), 'rc') > 0) ||
                    (stripos(end($each_plugin['versionCom']), 'beta') > 0) ||
                    (stripos(end($each_plugin['versionCom']), 'alpha') > 0)) {
                    $version_status = 'preview';
                } elseif (!empty($installed_plugins_version_list[$each_plugin['name']])) {
                    if (version_compare($lastesVersion, $installed_plugins_version_list[$each_plugin['name']]) == 1) {

                        $version_status = 'new';
                        foreach ($each_plugin['versionList'] as $each_version) {
                            if ($lastesVersion == $each_version['version']) {
                                $updateVersion = $lastesVersion;
                                break;
                            }
                        }
                    } else {
                        $version_status = 'installed';
                    }
                }
                $each_plugin['versionStatus'] = $version_status;
                $each_plugin['latestVersion'] = $updateVersion;
                $plugins_list[] = $each_plugin;
            }
        }
        //exit();

        return $plugins_list;
        //return response()->json($plugins_list);
    }

    public static function loadInstalledPluginList()
    {
        $version_list = array();
        $resource = opendir(base_path('plugins'));
        while ($file_name = @readdir($resource)) {
            if ($file_name == '.' || $file_name == '..')
                continue;
            $plugin_path = base_path('plugins') . '/' . $file_name;
            if (is_dir($plugin_path) && file_exists($plugin_path . '/package.json')) {
                $plugin_info = json_decode(file_get_contents($plugin_path . '/package.json'), true);
                $version_list[$plugin_info['name']] = $plugin_info['version'];
            }
        }
        closedir($resource);
        return $version_list;
    }


    private static function getPluginList()
    {
        if (empty(option('market_source'))) {
            //A source maintained by me
            option(['market_source' => config('app.PLUGIN_MARKET_SOURCE') ?: 'https://yun.yunzmall.com/plugin.json']);

        }

        //TODO 加上不同的域名
        $domain = rtrim(request()->getHttpHost(), '/');
        $market_source_path = option('market_source') . '/domain/' .$domain;

        $json_content = '';
        try {
            $json_content = Curl::to($market_source_path)
                ->get();
            //$json_content = file_get_contents($market_source_path);

        } catch (\Exception $e) {
            return null;
        }

        return json_decode($json_content, true);
    }

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
                'id' => $plugin['id'],
                'name' => $plugin['name'],

                'title' => $plugin['title'],

                'description' => empty($plugin['description']) ? trans('Yunshop\PluginsMarket::market.no-description') : $plugin['description'],

                'author' => $plugin['author'],

                'version' => $version,

                'versionList' => $versionList,

                'versionCom' => $plugin['versionCom'],

                'size' => empty($plugin['size']) ? trans('Yunshop\PluginsMarket::market.unknown') : $plugin['size'],

                'brief' => empty($plugin['brief']) ? '' : $plugin['brief']);
        }
    }

    private function checkPHPVersion($php_version)
    {
        if (version::lt($php_version, PHP_VERSION)) {
            return true;
        }

        return false;
    }


}
