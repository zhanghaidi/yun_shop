<?php


namespace app\backend\modules\setting\controllers;


use app\common\components\BaseController;
use Illuminate\Filesystem\Filesystem;

class TrojanController extends BaseController
{
    public function check()
    {
        $show = false;
        $del_files = [];

        if (config('app.framework') != 'platform') {
            $path = base_path() . '/../../attachment/image';
        } else {
            $path = base_path('static/upload');
        }

        if (\YunShop::request()->trojan == 'check') {
            $show = true;
            $filesystem = app(Filesystem::class);

            $files = $filesystem->allFiles($path);

            foreach ($files as $item) {
                if ($item->getExtension() == 'php') {
                    $del_files[] = $item->getPathname();
                }
            }
        }

        return view('setting.trojan.check', [
            'show' => $show,
            'files' => $del_files,
            'del_file' => implode('|', $del_files)
        ])->render();
    }

    public function del()
    {
        $files = \YunShop::request()->files;

        if (!empty($files)) {
            $filesystem = app(Filesystem::class);
            $files = explode('|', $files);

            foreach ($files as $file) {
                if (!$filesystem->delete($file)) {
                    return json_encode(['status' => 2]);
                }
            }

            return json_encode(['status' => 1]);
        }

        return json_encode(['status' => 0]);
    }
}