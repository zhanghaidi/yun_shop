<?php


namespace Yunshop\Commission;

use Illuminate\Http\Request;
use app\common\components\BaseController;

class TestController extends BaseController
{
    public function index($name)
    {
        return view('Yunshop\ExamplePlugin::admin.test',compact('name'))->render();
    }

    protected function test()
    {
        echo 'a';
    }

}
