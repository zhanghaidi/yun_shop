<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2020-02-04
 * Time: 10:10
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace app\backend\modules\upload\controllers;


use app\backend\modules\upload\models\CoreAttach;
use app\common\components\BaseController;
use app\common\services\ImageZip;
use app\platform\modules\system\models\SystemSetting;

class UploadController extends BaseController
{
    protected $uniacid;
    protected $common;

    public function __construct()
    {
        $this->uniacid = \YunShop::app()->uniacid ?: 0;
        $this->common = $this->common();
    }

    public function upload()
    {
        $file = request()->file('file');

        if (!$file) {
            return $this->errorJson('请传入正确参数.');
        }

        if (!$file->isValid()) {
            return $this->errorJson('上传失败.');
        }

        $type = request()->upload_type;

        if ($type == 'image') {
            if ($file->getClientSize() > 30 * 1024 * 1024) {
                return $this->errorJson('图片过大.');
            }

            $defaultImgType = [
                'jpg', 'bmp', 'eps', 'gif', 'mif', 'miff', 'png', 'tif',
                'tiff', 'svg', 'wmf', 'jpe', 'jpeg', 'dib', 'ico', 'tga', 'cut', 'pic'
            ];

            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $ext = $file->getClientOriginalExtension(); //文件后缀

            if (!in_array($ext, $defaultImgType)) {
                return $this->errorJson('非规定类型的文件格式');
            }

            if (!$ext) {
                $ext = 'jpg';
            }
            $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;

            if (config('app.framework') == 'platform') {
                $setting = SystemSetting::settingLoad('global', 'system_global');
                $remote = SystemSetting::settingLoad('remote', 'system_remote');

                if (in_array($ext, $defaultImgType)) {
                    if ($setting['image_extentions'] && !in_array($ext, array_filter($setting['image_extentions']))) {
                        return $this->errorJson('非规定类型的文件格式');
                    }
                    $defaultImgSize = $setting['img_size'] ? $setting['img_size'] * 1024 : 1024 * 1024 * 5; //默认大小为5M
                    if ($file->getClientSize() > $defaultImgSize) {
                        return $this->errorJson('文件大小超出规定值');
                    }
                }

                if ($setting['image']['zip_percentage']) {
                    //执行图片压缩
                    $imagezip = new ImageZip();
                    $imagezip->makeThumb(
                        yz_tomedia($newOriginalName),
                        yz_tomedia($newOriginalName),
                        $setting['image']['zip_percentage']
                    );
                }

                if ($setting['thumb_width'] == 1 && $setting['thumb_width']) {
                    $imagezip = new ImageZip();
                    $imagezip->makeThumb(
                        yz_tomedia($newOriginalName),
                        yz_tomedia($newOriginalName),
                        $setting['thumb_width']
                    );
                }
            } else {
                //全局配置
                global $_W;

                //公众号独立配置信息 优先使用公众号独立配置
                $uni_setting = app('WqUniSetting')->get()->toArray();
                if (!empty($uni_setting['remote']) && iunserializer($uni_setting['remote'])['type'] != 0) {
                    $setting['remote'] = iunserializer($uni_setting['remote']);
                    $remote = $setting['remote'];
                    $upload = $_W['setting']['upload'];
                } else {
                    $remote = $_W['setting']['remote'];
                    $upload = $_W['setting']['upload'];
                }

                if (in_array($ext, $defaultImgType)) {
                    if ($upload['image']['extentions'] && !in_array($ext, $upload['image']['extentions'])) {
                        return $this->errorJson('非规定类型的文件格式');
                    }
                    $defaultImgSize = $upload['image']['limit'] ? $upload['image']['limit'] * 1024 : 5 * 1024 * 1024;
                    if ($file->getClientSize() > $defaultImgSize) {
                        return $this->errorJson('文件大小超出规定值');
                    }
                }

                if ($upload['image']['zip_percentage']) {
                    //执行图片压缩
                    $imagezip = new ImageZip();
                    $imagezip->makeThumb(
                        yz_tomedia($newOriginalName),
                        yz_tomedia($newOriginalName),
                        $upload['image']['zip_percentage']
                    );
                }

                if ($upload['image']['thumb'] == 1 && $upload['image']['width']) {
                    $imagezip = new ImageZip();
                    $imagezip->makeThumb(
                        yz_tomedia($newOriginalName),
                        yz_tomedia($newOriginalName),
                        $upload['image']['width']
                    );
                }
            }

            if (config('app.framework') == 'platform') {
                //本地上传
                $result = \Storage::disk('newimages')->put($newOriginalName, file_get_contents($realPath));
                if (!$result) {
                    return $this->successJson('上传失败');
                }

                $url = \Storage::disk('newimages')->url($newOriginalName);

                $core_attach_result = \app\platform\modules\application\models\CoreAttach::create([
                    'uniacid' => $this->uniacid,
                    'uid' => \Auth::guard('admin')->user()->uid,
                    'filename' => safe_gpc_html(htmlspecialchars_decode($originalName, ENT_QUOTES)),
                    'attachment' => $url,
                    'type' => 1,
                    'module_upload_dir' => '',
                    'group_id' => intval($this->uniacid),
                    'upload_type' => $remote['type']
                ]);

                //远程上传
                if ($remote['type'] != 0) {
                    file_remote_upload_new($newOriginalName, true, $remote);
                }

                return $this->successJson('上传成功', [
                    'name' => $originalName,
                    'ext' => $ext,
                    'filename' => $newOriginalName,
                    'attachment' => $url,
                    'url' => yz_tomedia($url),
                    'is_image' => 1,
                    'filesize' => 'null',
                    'group_id' => intval($this->uniacid)
                ]);
            } else {
                global $_W;

                //本地上传
                $result = \Storage::disk('image')->put($newOriginalName, file_get_contents($realPath));
                if (!$result) {
                    return $this->successJson('上传失败');
                }

                $url = \Storage::disk('image')->url($newOriginalName);

                $core_attach_result = CoreAttach::create([
                    'uniacid' => $this->uniacid,
                    'uid' => $_W['uid'],
                    'filename' => safe_gpc_html(htmlspecialchars_decode($originalName, ENT_QUOTES)),
                    'attachment' => $url,
                    'type' => 1,
                    'createtime' => TIMESTAMP,
                    'module_upload_dir' => '',
                    'group_id' => 0,
                ]);

                //远程上传
                if ($remote['type'] != 0) {
                    file_remote_upload_wq($newOriginalName, true, $remote, true);
                }

                $info = array(
                    'name' => $originalName,
                    'ext' => $ext,
                    'filename' => $newOriginalName,
                    'attachment' => $url,
                    'url' => yz_tomedia($url),
                    'is_image' => 1,
                    'filesize' => 'null',
                );

                $info['state'] = 'SUCCESS';
                die(json_encode($info));
            }
        } elseif ($type == 'video') {
            if ($file->getClientSize() > 50 * 1024 * 1024) {
                return $this->errorJson('资源过大.');
            }

            $defaultAudioType = ['avi', 'asf', 'wmv', 'avs', 'flv', 'mkv', 'mov', '3gp', 'mp4',
                'mpg', 'mpeg', 'dat', 'ogm', 'vob', 'rm', 'rmvb', 'ts', 'tp', 'ifo', 'nsv'
            ];

            $defaultVideoType = [
                'mp3', 'aac', 'wav', 'wma', 'cda', 'flac', 'm4a', 'mid', 'mka', 'mp2',
                'mpa', 'mpc', 'ape', 'ofr', 'ogg', 'ra', 'wv', 'tta', 'ac3', 'dts'
            ];

            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $ext = $file->getClientOriginalExtension(); //文件后缀

            $merge_ext = array_merge($defaultAudioType, $defaultVideoType);
            if (!in_array($ext, $merge_ext)) {
                return $this->errorJson('非规定类型的文件格式');
            }

            $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;

            if (config('app.framework') == 'platform') {
                $setting = SystemSetting::settingLoad('global', 'system_global');
                $remote = SystemSetting::settingLoad('remote', 'system_remote');

//            if (in_array($ext, $defaultType)) {
//                if ($setting['image_extentions'] && !in_array($ext, array_filter($setting['image_extentions']))) {
//                    return $this->errorJson('非规定类型的文件格式');
//                }
//                $defaultImgSize = $setting['img_size'] ? $setting['img_size'] * 1024 : 1024 * 1024 * 5; //默认大小为5M
//                if ($file->getClientSize() > $defaultImgSize) {
//                    return $this->errorJson('文件大小超出规定值');
//                }
//            }

                //本地上传
                $result = \Storage::disk('videos')->put($newOriginalName, file_get_contents($realPath));
                if (!$result) {
                    return $this->successJson('上传失败');
                }

                $url = \Storage::disk('videos')->url($newOriginalName);

                $core_attach_result = \app\platform\modules\application\models\CoreAttach::create([
                    'uniacid' => $this->uniacid,
                    'uid' => \Auth::guard('admin')->user()->uid,
                    'filename' => safe_gpc_html(htmlspecialchars_decode($originalName, ENT_QUOTES)),
                    'attachment' => $url,
                    'type' => 3,
                    'module_upload_dir' => '',
                    'group_id' => intval($this->uniacid),
                    'upload_type' => $remote['type']
                ]);

                //远程上传
                if ($remote['type'] != 0) {
                    file_video_remote_upload($url, true, $remote);
                }

                return $this->successJson('上传成功', [
                    'name' => $originalName,
                    'ext' => $ext,
                    'filename' => $newOriginalName,
                    'attachment' => $url,
                    'url' => yz_tomedia($url),
                    'is_image' => 0,
                    'filesize' => 'null',
                    'group_id' => intval($this->uniacid)
                ]);

            } else {
                //全局配置
                global $_W;

                //公众号独立配置信息 优先使用公众号独立配置
                $uni_setting = app('WqUniSetting')->get()->toArray();
                if (!empty($uni_setting['remote']) && iunserializer($uni_setting['remote'])['type'] != 0) {
                    $setting['remote'] = iunserializer($uni_setting['remote']);
                    $remote = $setting['remote'];
                    $upload = $_W['setting']['upload'];
                } else {
                    $remote = $_W['setting']['remote'];
                    $upload = $_W['setting']['upload'];
                }

//            if (in_array($ext, $defaultType)) {
//                if ($upload['image']['extentions'] && !in_array($ext, $upload['image']['extentions'])) {
//                    return $this->errorJson('非规定类型的文件格式');
//                }
//                $defaultImgSize = $upload['image']['limit'] ? $upload['image']['limit'] * 1024 : 5 * 1024 * 1024;
//                if ($file->getClientSize() > $defaultImgSize) {
//                    return $this->errorJson('文件大小超出规定值');
//                }
//            }

                //本地上传
                $result = \Storage::disk('wq_videos')->put($newOriginalName, file_get_contents($realPath));
                if (!$result) {
                    return $this->successJson('上传失败');
                }

                $url = \Storage::disk('wq_videos')->url($newOriginalName);

                $core_attach_result = CoreAttach::create([
                    'uniacid' => $this->uniacid,
                    'uid' => $_W['uid'],
                    'filename' => safe_gpc_html(htmlspecialchars_decode($originalName, ENT_QUOTES)),
                    'attachment' => $url,
                    'type' => 3,
                    'createtime' => TIMESTAMP,
                    'module_upload_dir' => '',
                    'group_id' => 0,
                ]);

                //远程上传
                if ($remote['type'] != 0) {
                    file_video_remote_upload_wq($url, true, $remote, true);
                }

                $info = array(
                    'name' => $originalName,
                    'ext' => $ext,
                    'filename' => $newOriginalName,
                    'attachment' => $url,
                    'url' => yz_tomedia($url),
                    'is_image' => 0,
                    'filesize' => 'null',
                );

                $info['state'] = 'SUCCESS';
                die(json_encode($info));
            }
        }
    }

    public function fetch()
    {
        $url = trim(request()->url);
        $resp = ihttp_get($url);
        if (!$resp) {
            return $this->errorJson('提取文件失败');
        }

        if (strexists($resp['headers']['Content-Type'], 'image')) {
            switch ($resp['headers']['Content-Type']) {
                case 'application/x-jpg':
                case 'image/jpeg':
                    $ext = 'jpg';
                    break;
                case 'image/png':
                    $ext = 'png';
                    break;
                case 'image/gif':
                    $ext = 'gif';
                    break;
                default:
                    return $this->errorJson('提取资源失败, 资源文件类型错误.');
                    break;
            }
        } else {
            return $this->errorJson('提取资源失败, 仅支持图片提取.');
        }

        $originName = pathinfo($url, PATHINFO_BASENAME);
        $newOriginalName = md5($originName . str_random(6)) . '.' . $ext;

        if (config('app.framework') == 'platform') {
            $setting = SystemSetting::settingLoad('global', 'system_global');
            $remote = SystemSetting::settingLoad('remote', 'system_remote');

            if ($setting['image']['zip_percentage']) {
                //执行图片压缩
                $imagezip = new ImageZip();
                $imagezip->makeThumb(
                    yz_tomedia($originName),
                    yz_tomedia($originName),
                    $setting['image']['zip_percentage']
                );
            }

            if ($setting['thumb_width'] == 1 && $setting['thumb_width']) {
                $imagezip = new ImageZip();
                $imagezip->makeThumb(
                    yz_tomedia($originName),
                    yz_tomedia($originName),
                    $setting['thumb_width']
                );
            }

            //本地上传
            $result = \Storage::disk('newimages')->put($newOriginalName, $resp['content']);
            if (!$result) {
                return $this->successJson('上传失败');
            }

            $core_attach_result = \app\platform\modules\application\models\CoreAttach::create([
                'uniacid' => $this->uniacid,
                'uid' => \Auth::guard('admin')->user()->uid,
                'filename' => $newOriginalName,
                'attachment' => $url,
                'type' => 1,
                'module_upload_dir' => '',
                'group_id' => intval($this->uniacid),
                'upload_type' => $remote['type']
            ]);

            //远程上传
            if ($remote['type'] != 0) {
                file_remote_upload_new($newOriginalName, true, $remote);
            }

            $url = \Storage::disk('newimages')->url($newOriginalName);

            return $this->successJson('上传成功', [
                'img' => $url,
                'img_url' => yz_tomedia($url),
            ]);
        } else {
            //全局配置
            global $_W;

            //公众号独立配置信息 优先使用公众号独立配置
            $uni_setting = app('WqUniSetting')->get()->toArray();
            if (!empty($uni_setting['remote']) && iunserializer($uni_setting['remote'])['type'] != 0) {
                $setting['remote'] = iunserializer($uni_setting['remote']);
                $remote = $setting['remote'];
                $upload = $_W['setting']['upload'];
            } else {
                $remote = $_W['setting']['remote'];
                $upload = $_W['setting']['upload'];
            }

            if ($upload['image']['zip_percentage']) {
                //执行图片压缩
                $imagezip = new ImageZip();
                $imagezip->makeThumb(
                    yz_tomedia($originName),
                    yz_tomedia($originName),
                    $upload['image']['zip_percentage']
                );
            }

            if ($upload['image']['thumb'] == 1 && $upload['image']['width']) {
                $imagezip = new ImageZip();
                $imagezip->makeThumb(
                    yz_tomedia($originName),
                    yz_tomedia($originName),
                    $upload['image']['width']
                );
            }

            //本地上传
            $result = \Storage::disk('image')->put($newOriginalName, $resp['content']);
            if (!$result) {
                return $this->successJson('上传失败');
            }

            $core_attach_result = CoreAttach::create([
                'uniacid' => $this->uniacid,
                'uid' => $_W['uid'],
                'filename' => $newOriginalName,
                'attachment' => $url,
                'type' => 1,
                'createtime' => TIMESTAMP,
                'module_upload_dir' => '',
                'group_id' => 0,
            ]);

            //远程上传
            if ($remote['type'] != 0) {
                file_remote_upload_wq($newOriginalName, true, $remote, true);
            }

            $url = \Storage::disk('image')->url($newOriginalName);

            return $this->successJson('上传成功', [
                'img' => $url,
                'img_url' => yz_tomedia($url),
            ]);
        }
    }

    public function getImage()
    {
        if (config('app.framework') == 'platform') {
            $result = $this->getNewImage();
        } else {
            $result = $this->getWqImage();
        }

        return $this->successJson('ok', $result);
    }

    public function getWqImage()
    {
        $year = request()->year;
        $month = intval(request()->month);
        $page = max(1, intval(request()->page));
        $groupid = intval(request()->group_id);
        $page_size = 33;
        $is_local_image = $this->common['islocal'] == 'local' ? true : false;
        if ($page<=1) {
            $page = 0;
            $offset = ($page)*$page_size;
        } else {
            $offset = ($page-1)*$page_size;
        }

//        if(!$is_local_image) {
//            $core_attach =  new WechatAttachment;
//        } else {
            $core_attach = new CoreAttach;
//        }
        $core_attach = $core_attach->where('uniacid', $this->uniacid)->where('module_upload_dir', $this->common['module_upload_dir']);

        if (!$this->uniacid) {
            $core_attach = $core_attach->where('uid', \Auth::guard('admin')->user()->uid);
        }
        if ($groupid > 0) {
            $core_attach = $core_attach->where('group_id', $groupid);
        }
        if ($groupid == 0) {
            $core_attach = $core_attach->where('group_id', -1);
        }
        if ($year || $month) {
            $start_time = $month ? strtotime("{$year}-{$month}-01") : strtotime("{$year}-1-01");
            $end_time = $month ? strtotime('+1 month', $start_time) : strtotime('+12 month', $start_time);
            $core_attach = $core_attach->where('createtime', '>=', $start_time)->where('createtime', '<=', $end_time);
        }
//        if ($this->common['islocal']) {
            $core_attach = $core_attach->where('type', 1);
//        } else {
//            $core_attach = $core_attach->where('type', 'image');
//        }

        $core_attach = $core_attach->orderby('createtime', 'desc');
        $count = $core_attach->count();
        $core_attach = $core_attach->offset($offset)->limit($page_size)->get();

        foreach ($core_attach as &$meterial) {
            if ($this->common['islocal']) {
                $meterial['url'] = yz_tomedia($meterial['attachment']);
                unset($meterial['uid']);
            } else {
                $meterial['attach'] = yz_tomedia($meterial['attachment'], true);
                $meterial['url'] = $meterial['attach'];
            }
        }

        $pager = pagination($count, $page, $page_size,'',$context = array('before' => 5, 'after' => 4, 'isajax' => '1'));
        $result = array('items' => $core_attach, 'pager' => $pager);

        iajax(0, $result);
    }

    public function getNewImage()
    {
        $core_attach = new \app\platform\modules\application\models\CoreAttach();
        if (request()->year != '不限') {
            $search['year'] = request()->year;
        }

        if(request()->month != '不限') {
            $search['month'] = request()->month;
        }

        $core_attach = $core_attach->search($search);
        $core_attach = $core_attach->where('uniacid', $this->uniacid)->where('module_upload_dir', $this->common['module_upload_dir']);

        if (!$this->uniacid) {
            $core_attach = $core_attach->where('uid', \Auth::guard('admin')->user()->uid);
        }

        //type = 1 图片
        $core_attach = $core_attach->where('type', 1);

        $core_attach = $core_attach->orderby('created_at', 'desc')->paginate(33);

        foreach ($core_attach as &$meterial) {
            $meterial['url'] = yz_tomedia($meterial['attachment']);
            unset($meterial['uid']);
        }

        return $core_attach->toArray();
    }

    public function getVideo()
    {
        if (config('app.framework') == 'platform') {
            $core_attach = new \app\platform\modules\application\models\CoreAttach();
            if (request()->year != '不限') {
                $search['year'] = request()->year;
            }

            if(request()->month != '不限') {
                $search['month'] = request()->month;
            }

            $core_attach = $core_attach->search($search);
            $core_attach = $core_attach->where('uniacid', $this->uniacid)->where('module_upload_dir', $this->common['module_upload_dir']);

            if (!$this->uniacid) {
                $core_attach = $core_attach->where('uid', \Auth::guard('admin')->user()->uid);
            }

            //type = 3 视频
            $core_attach = $core_attach->where('type', 3);

            $core_attach = $core_attach->orderby('created_at', 'desc')->paginate(33);

            foreach ($core_attach as &$meterial) {
                $meterial['url'] = yz_tomedia($meterial['attachment']);
                unset($meterial['uid']);
            }

            return $this->successJson('ok', $core_attach);
        } else {
            $core_attach = new CoreAttach();

            $page_index = max(1, request()->page);
            $page_size = 5;
            if ($page_index<=1) {
                $page_index = 0;
                $offset = ($page_index)*$page_size;
            } else {
                $offset = ($page_index-1)*$page_size;
            }

            if (!$this->uniacid) {
                $core_attach = $core_attach->where('uid', \Auth::guard('admin')->user()->uid);
            }

            $total = $core_attach->count();
            $core_attach = $core_attach
                ->where('type', 3)
                ->where('uniacid', $this->uniacid)
                ->where('module_upload_dir', $this->common['module_upload_dir'])
                ->orderby('createtime', 'desc')
                ->offset($offset)
                ->limit(24)
                ->get();

            foreach ($core_attach as &$meterial) {
                $meterial['url'] = yz_tomedia($meterial['attachment']);
                unset($meterial['uid']);
            }

            $pager = pagination($total, 1, 24, '', $context = array('before' => 5, 'after' => 4, 'isajax' => '1'));

            $result = array('items' => $core_attach, 'pager' => $pager);
            iajax(0, $result);
        }
    }

    public function delete()
    {
        $uid = \Auth::guard('admin')->user()->uid;
        $id = request()->id;
        if (!is_array($id)) {
            $id = array(intval($id));
        }
        $id = safe_gpc_array($id);

        if (config('app.framework') == 'platform') {
            $setting = SystemSetting::settingLoad('global', 'system_global');
            $remote = SystemSetting::settingLoad('remote', 'system_remote');
            $core_attach = \app\platform\modules\application\models\CoreAttach::where('id', $id);
            if (!$this->uniacid) {
                $core_attach = $core_attach->where('uid', $uid);
            } else {
                $core_attach = $core_attach->where('uniacid', $this->uniacid);
            }
            $core_attach = $core_attach->first();
            if ($core_attach['upload_type']) {
                $status = file_remote_delete($core_attach['attachment'], $core_attach['upload_type'], $remote);
            } else {
                $status = file_delete($core_attach['attachment']);
            }
            if (is_error($status)) {
                return $this->errorJson($status['message']);
            }

            if ($core_attach->delete()) {
                return $this->successJson('删除成功');
            } else {
                return $this->errorJson('删除数据表数据失败');
            }
        } else {
            $core_attach = CoreAttach::where('id', $id);
            if (!$this->uniacid) {
                $core_attach = $core_attach->where('uid', $uid);
            } else {
                $core_attach = $core_attach->where('uniacid', $this->uniacid);
            }
            $core_attach = $core_attach->first();
            if ($core_attach['upload_type']) {
                $status = file_remote_delete($core_attach['attachment']);
            } else {
                $status = file_delete($core_attach['attachment']);
            }
            if (is_error($status)) {
                return $this->errorJson($status['message']);
            }

            if ($core_attach->delete()) {
                return $this->successJson('删除成功');
            } else {
                return $this->errorJson('删除数据表数据失败');
            }
        }
    }

    public function common()
    {
        $dest_dir = request()->dest_dir;
        $type = in_array(request()->upload_type, array('image','audio','video')) ? request()->upload_type : 'image';
        $option = array_elements(array('uploadtype', 'global', 'dest_dir'), $_POST);
        $option['width'] = intval($option['width']);
        $option['global'] = request()->global;

        if (preg_match('/^[a-zA-Z0-9_\/]{0,50}$/', $dest_dir, $out)) {
            $dest_dir = trim($dest_dir, '/');
            $pieces = explode('/', $dest_dir);
            if(count($pieces) > 3){
                $dest_dir = '';
            }
        } else {
            $dest_dir = '';
        }

        $module_upload_dir = '';
        if($dest_dir != '') {
            $module_upload_dir = sha1($dest_dir);
        }

        if ($option['global']) {
            $folder = "{$type}s/global/";
            if ($dest_dir) {
                $folder .= '' . $dest_dir . '/';
            }
        } else {
            $folder = "{$type}s/{$this->uniacid}";
            if (!$dest_dir) {
                $folder .= '/' . date('Y/m/');
            } else {
                $folder .= '/' . $dest_dir . '/';
            }
        }

        return [
            'dest_dir' => $dest_dir,
            'module_upload_dir' => $module_upload_dir,
            'type' => $type,
            'options' => $option,
            'folder' => $folder,
        ];
    }
}