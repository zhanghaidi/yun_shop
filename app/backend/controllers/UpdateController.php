<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 18/04/2017
 * Time: 11:13
 */

namespace app\backend\controllers;

use app\common\components\BaseController;
use app\common\facades\Option;
use app\common\facades\Setting;
use app\common\models\UniAccount;
use app\common\services\AutoUpdate;
use Illuminate\Filesystem\Filesystem;
use vierbergenlars\SemVer\version;

class UpdateController extends BaseController
{

    public function index()
    {
        $list = [];

        //删除非法文件
        $this->deleteFile();
        //执行迁移文件
        $this->runMigrate();

        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];

        $update = new AutoUpdate(null, null, 300);
        $update->setUpdateFile('check_app.json');

        if (is_file(base_path() . '/' . 'config/front-version.php')) {
            $update->setCurrentVersion(config('front-version'));
            $version = config('front-version');
        } else {
            $update->setCurrentVersion(config('version'));
            $version = config('version');
        }

        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory

        $update->setBasicAuth($key, $secret);

        $update->checkUpdate();
        
        if ($update->newVersionAvailable()) {
            $list = $update->getUpdates();
        }

        krsort($list);

        if (!empty($list[0]['php_version']) && !$this->checkPHPVersion($list[0]['php_version'])) {
            $list = [];
        }

        return view('update.upgrad', [
            'list' => $list,
            'version' => $version,
            'count' => count($list)
        ])->render();
    }

    /**
     * footer检测更新
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $result = ['msg' => '', 'last_version' => '', 'updated' => 0];
        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];
        if (!$key || !$secret) {
            return;
        }

        $update = new AutoUpdate(null, null, 300);
        $update->setUpdateFile('check_app.json');
        $update->setCurrentVersion(config('version'));
        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory
        $update->setBasicAuth($key, $secret);
        //$update->setBasicAuth();

        $res = $update->checkUpdate();

        if ($res === 'unknown') {
            $result = ['updated' => -1];
        }

        //Check for a new update
        if ($res === false) {
            $result['msg'] = 'Could not check for updates! See log file for details.';
            response()->json($result)->send();
            return;
        }

        if (isset($res['result']) && 0 == $res['result']) {
            $res['updated'] = 0;
            return response()->json($res)->send();
        }

        if ($update->newVersionAvailable()) {
            $result['last_version'] = $update->getLatestVersion()->getVersion();
            $result['updated'] = 1;
            $result['current_version'] = config('version');
        }
        response()->json($result)->send();
        return;
    }


    /**
     * 检测更新
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyheck()
    {
        set_time_limit(0);

        $filesystem = app(Filesystem::class);
        $update = new AutoUpdate(null, null, 300);

        $filter_file = ['composer.json', 'composer.lock', 'README.md'];
        $plugins_dir = $update->getDirsByPath('plugins', $filesystem);

        $result = ['result' => 0, 'msg' => '网络请求超时', 'last_version' => ''];
        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];
        if (!$key || !$secret) {
            return;
        }

        $update = new AutoUpdate(null, null, 300);
        $update->setUpdateFile('backcheck_app.json');
        $update->setCurrentVersion(config('version'));

        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory

        $update->setBasicAuth($key, $secret);
        //$update->setBasicAuth();

        //Check for a new update
        $ret = $update->checkBackUpdate();

        if (is_array($ret)) {
            if (!empty($ret['php-version']) && !$this->checkPHPVersion($ret['php-version'])) {
                $result = ['result' => 98, 'msg' => '服务器php版本(v' . PHP_VERSION . ')过低,不符合更新条件,建议升级到php版本>=(v' . $ret['php-version'] . ')', 'last_version' => ''];

                response()->json($result)->send();
            }

            if (1 == $ret['result']) {
                $files = [];

                if (!empty($ret['files'])) {
                    foreach ($ret['files'] as $file) {
                        //忽略指定文件
                        if (in_array($file['path'], $filter_file)) {
                            continue;
                        }

                        //忽略前端样式文件
                        if (preg_match('/^static\/app/', $file['path'])) {
                            continue;
                        }

                        //忽略没有安装的插件
                        if (preg_match('/^plugins/', $file['path'])) {
                            $sub_dir = substr($file['path'], strpos($file['path'], '/') + 1);
                            $sub_dir = substr($sub_dir, 0, strpos($sub_dir, '/'));

                            if (!in_array($sub_dir, $plugins_dir)) {
                                continue;
                            }
                        }

                        //忽略前后端\wq版本号记录文件
                        if (($file['path'] == 'config/front-version.php'
                                 || $file['path'] == 'config/backend_version.php'
                                 || $file['path'] == 'config/wq-version.php' )
                                 && is_file(base_path() . '/' . $file['path'])) {
                            continue;
                        }

                        $entry = base_path() . '/' . $file['path'];

                        //如果本地没有此文件或者文件与服务器不一致
                        if (!is_file($entry) || md5_file($entry) != $file['md5']) {
                            $files[] = array(
                                'path' => $file['path'],
                                'download' => 0
                            );
                            $difffile[] = $file['path'];
                        } else {
                            $samefile[] = $file['path'];
                        }
                    }
                }

                $tmpdir = storage_path('app/public/tmp/' . date('ymd'));
                if (!is_dir($tmpdir)) {
                    $filesystem->makeDirectory($tmpdir, 0755, true);
                }

                $ret['files'] = $files;
                file_put_contents($tmpdir . "/file.txt", json_encode($ret));

                if (empty($files)) {
                    $version = config('version');
                    //TODO 更新日志记录
                } else {
                    $version = $ret['version'];
                }

                $result = [
                    'result' => 1,
                    'version' => $version,
                    'files' => $ret['files'],
                    'filecount' => count($files),
                    'log' => $ret['log']
                ];
            } else {
                preg_match('/"[\d\.]+"/', file_get_contents(base_path('config/') . 'version.php'), $match);
                $version = $match ? trim($match[0], '"') : '1.0.0';

                $result = ['result' => 99, 'msg' => '', 'last_version' => $version];
            }
        }

        response()->json($result)->send();
    }

    public function fileDownload()
    {
        $filesystem = app(Filesystem::class);

        $tmpdir = storage_path('app/public/tmp/' . date('ymd'));
        $f = file_get_contents($tmpdir . "/file.txt");
        $upgrade = json_decode($f, true);
        $files = $upgrade['files'];
        $total = count($upgrade['files']);
        $path = "";
        $nofiles = \YunShop::request()->nofiles;
        $status = 1;

        $update = new AutoUpdate(null, null, 300);

        //找到一个没更新过的文件去更新
        foreach ($files as $f) {
            if (empty($f['download'])) {
                $path = $f['path'];
                break;
            }
        }

        if (!empty($path)) {
            if (!empty($nofiles)) {
                if (in_array($path, $nofiles)) {
                    foreach ($files as &$f) {
                        if ($f['path'] == $path) {
                            $f['download'] = 1;
                            break;
                        }
                    }
                    unset($f);
                    $upgrade['files'] = $files;
                    $tmpdir = storage_path('app/public/tmp/' . date('ymd'));
                    if (!is_dir($tmpdir)) {
                        $filesystem->makeDirectory($tmpdir, 0755, true);
                    }
                    file_put_contents($tmpdir . "/file.txt", json_encode($upgrade));

                    return response()->json(['result' => 3])->send();
                }
            }

            $key = Setting::get('shop.key')['key'];
            $secret = Setting::get('shop.key')['secret'];
            if (!$key || !$secret) {
                return;
            }

            $update->setUpdateFile('backdownload_app.json');
            $update->setCurrentVersion(config('version'));

            $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory

            $update->setBasicAuth($key, $secret);

            //Check for a new download
            $ret = $update->checkBackDownload([
                'path' => urlencode($path)
            ]);

            //预下载
            if (is_array($ret)) {
                $path = $ret['path'];
                $dirpath = dirname($path);
                $save_path = storage_path('app/auto-update/shop') . '/' . $dirpath;

                if (!is_dir($save_path)) {
                    $filesystem->makeDirectory($save_path, 0755, true);
                }

                //新建
                $content = base64_decode($ret['content']);
                file_put_contents(storage_path('app/auto-update/shop') . '/' . $path, $content);

                $success = 0;
                foreach ($files as &$f) {
                    if ($f['path'] == $path) {
                        $f['download'] = 1;
                        break;
                    }
                    if ($f['download']) {
                        $success++;
                    }
                }

                unset($f);
                $upgrade['files'] = $files;
                $tmpdir = storage_path('app/public/tmp/' . date('ymd'));

                if (!is_dir($tmpdir)) {
                    $filesystem->makeDirectory($tmpdir, 0755, true);
                }

                file_put_contents($tmpdir . "/file.txt", json_encode($upgrade));
            }
        } else {
            //覆盖
            foreach ($files as $f) {
                $path = $f['path'];
                $file_dir = dirname($path);

                if (!is_dir(base_path($file_dir))) {
                    $filesystem->makeDirectory(base_path($file_dir), 0755, true);
                }

                $content = file_get_contents(storage_path('app/auto-update/shop') . '/' . $path);

                if (!empty($content)) {
                    file_put_contents(base_path($path), $content);

                    @unlink(storage_path('app/auto-update/shop') . '/' . $path);
                }
            }

            \Log::debug('----CLI----');
            $plugins_dir = $update->getDirsByPath('plugins', $filesystem);
            if (!empty($plugins_dir)) {
                \Artisan::call('update:version', ['version' => $plugins_dir]);
            }

            //清理缓存
            \Log::debug('----Cache Flush----');

            if (!is_dir(base_path('config/shop-foundation'))) {
                \Artisan::call('config:cache');
            }

            \Cache::flush();

            \Log::debug('----Queue Restarth----');
            \Artisan::call('queue:restart');

            $status = 2;

            $success = $total;
        }

        response()->json([
            'result' => $status,
            'total' => $total,
            'success' => $success
        ])->send();
    }

    /**
     * 开始下载并更新程序
     * @return \Illuminate\Http\RedirectResponse
     */
    public function startDownload()
    {
        \Cache::flush();
        $resultArr = ['msg' => '', 'status' => 0, 'data' => []];
        set_time_limit(0);

        $key = Setting::get('shop.key')['key'];
        $secret = Setting::get('shop.key')['secret'];

        $update = new AutoUpdate(null, null, 300);
        $update->setUpdateFile('check_app.json');

        if (is_file(base_path() . '/' . 'config/front-version.php')) {
            $update->setCurrentVersion(config('front-version'));
        } else {
            $update->setCurrentVersion(config('version'));
        }

        $update->setUpdateUrl(config('auto-update.checkUrl')); //Replace with your server update directory
        Setting::get('auth.key');
        $update->setBasicAuth($key, $secret);

        //Check for a new update
        if ($update->checkUpdate() === false) {
            $resultArr['msg'] = 'Could not check for updates! See log file for details.';
            response()->json($resultArr)->send();
            return;
        }

        if ($update->newVersionAvailable()) {
            /*$update->onEachUpdateFinish(function($version){
                \Log::debug('----CLI----');
                \Artisan::call('update:version' ,['version'=>$version]);
            });*/

            $result = $update->update();

            if ($result === true) {
                $list = $update->getUpdates();
                if (!empty($list)) {
                    $this->setSystemVersion($list);
                    if (!is_dir(base_path('config/shop-foundation'))) {
                        \Artisan::call('config:cache');
                    }
                }

                $resultArr['status'] = 1;
                $resultArr['msg'] = '更新成功';
            } else {
                $resultArr['msg'] = '更新失败: ' . $result;
                if ($result = AutoUpdate::ERROR_SIMULATE) {
                    $resultArr['data'] = $update->getSimulationResults();
                }
            }
        } else {
            $resultArr['msg'] = 'Current Version is up to date';
        }
        response()->json($resultArr)->send();
        return;
    }

    /**
     * 更新本地前端版本号
     *
     * @param $updateList
     */
    private function setSystemVersion($updateList)
    {
        $version = $this->getFrontVersion($updateList);

        $str = file_get_contents(base_path('config/') . 'front-version.php');
        $str = preg_replace('/"[\d\.]+"/', '"' . $version . '"', $str);
        file_put_contents(base_path('config/') . 'front-version.php', $str);
    }

    /**
     * 获取前端版本号
     *
     * @param $updateList
     * @return mixed
     */
    private function getFrontVersion($updateList)
    {
        rsort($updateList);
        $version = $updateList[0]['version'];

        return $version;
    }

    /**
     * 删除文件
     *
     */
    private function deleteFile()
    {
        $filesystem = app(Filesystem::class);

        //file-删除指定文件，file-空 删除目录下所有文件
        $files = [
            [
                'path' => base_path('config/shop-foundation'),
            ],
            [
                'path' => base_path('config'),
                'ext' => ['php'],
                'file' => [
                    base_path('database/migrations/main-menu.php'),
                    base_path('database/migrations/notice-template.php'),
                    base_path('database/migrations/notice.php'),
                    base_path('database/migrations/observer.php'),
                    base_path('database/migrations/widget.php'),
                ]
            ],
            [
                'path' => base_path('database/migrations'),
                'ext' => ['php'],
                'file' => [
                    base_path('database/migrations/2018_10_18_150312_add_unique_to_yz_member_income.php')
                ]
            ],
            [
                'path' => storage_path('cert'),
                'ext' => ['pem']
            ],
            [
                'path' => base_path('plugins/store-cashier/migrations'),
                'ext' => ['php'],
                'file' => [
                    base_path('plugins/store-cashier/migrations/2018_11_26_174034_fix_address_store.php'),
                    base_path('plugins/store-cashier/migrations/2017_08_03_170658_create_ims_yz_cashier_goods_table.php')
                ]
            ],
            [
                'path' => base_path('plugins/supplier/migrations'),
                'ext' => ['php'],
                'file' => [
                    base_path('plugins/supplier/migrations/2018_11_26_155528_update_ims_yz_order_and_goods.php')
                ]
            ],
            [
                'path' => base_path(),
                'file' => [
                    base_path('manifest.xml'),
                    base_path('map.json')
                ]
            ]
        ];

        if (config('app.framework') == false) {
            array_push($files, [
                'path' => base_path(),
                'ext' => ['php'],
                'file' => [
                    base_path('index.php')
                ]
            ]);
        }

        foreach ($files as $rows) {
            $scan_file = $filesystem->files($rows['path']);

            if (!empty($scan_file)) {
                foreach ($scan_file as $item) {
                    if (!empty($rows['file'])) {
                        foreach ($rows['file'] as $val) {
                            if ($val == $item) {
                                @unlink($item);
                            }
                        }
                    } else {
                        $file_info = pathinfo($item);

                        if (!in_array($file_info['extension'], $rows['ext'])) {
                            @unlink($item);
                        }
                    }
                }
            }
        }
    }

    private function dataSecret()
    {
        $uniAccount = UniAccount::get();

        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;

            $pay = \Setting::get('shop.pay');

            if (!isset($pay['secret'])) {
                foreach ($pay as $key => &$val) {
                    if (!empty($val)) {
                        switch ($key) {
                            case 'alipay_app_id':
                            case 'rsa_private_key':
                            case 'rsa_public_key':
                            case 'alipay_number':
                            case 'alipay_name':
                                $val = encrypt($val);
                                break;
                        }
                    }
                }

                $pay['secret'] = 1;
                \Setting::set('shop.pay', $pay);
            }
        }
    }

    public function pirate()
    {
        return view('update.pirate', [])->render();
    }

    private function runMigrate()
    {
        $plugins = app('plugins')->getPlugins();
        foreach ($plugins as $p) {
            if($p->isEnabled()){
                \Artisan::call('migrate', ['--force' => true, '--path' => $p->getPath]);
            }
        }

        $host = request()->getHttpHost();
        $hosts = json_encode(['host' => $host]);

        file_put_contents(base_path('static/yunshop/js/host.js'), $hosts);

        \Artisan::call('migrate', ['--force' => true]);
    }

    private function checkPHPVersion($php_version)
    {
        if (version::lt($php_version, PHP_VERSION)) {
            return true;
        }

        return false;
    }
}
