<div class="yz-menu-header">
    <nav class="navbar navbar-transparent navbar-absolute" style="color:#fff !important;">
        <div class="container-fluid">
            {{--<div class="navbar-minimize">
                <h4>{{YunShop::app()->account['name']}}</h4>
            </div>--}}
            <div class="navbar-header">
                {{--<button type="button" class="navbar-toggle" data-toggle="collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>--}}
                <ul class="clearfix pull-left" style="">
                    {{--<li class=" active" style="">
                        <a ui-sref="shop.dashboard" href="/shop">商城</a>
                    </li>--}}
                    @foreach(\app\backend\modules\menu\Menu::current()->getItems() as $key=>$value)

                        @if(isset($value['menu']) && $value['menu'] == 1 && can($key) && $value['top_show'] == 1)

                            @if(isset($value['child']) && array_child_kv_exists($value['child'],'menu',1))

                                <li class="{{in_array($key,\app\backend\modules\menu\Menu::current()->getCurrentItems()) ? 'active' : ''}}">
                                    <a href="{{isset($value['url']) ? yzWebFullUrl($value['url']):''}}{{$value['url_params'] or ''}}">
                                        <i class="fa {{array_get($value,'icon','fa-circle-o') ?: 'fa-circle-o'}}"></i>
                                        {{--<span class="pull-right-container">--}}
                                        {{--<i class="fa fa-angle-left pull-right"></i>--}}
                                        {{--</span>--}}
                                        {{$value['name']}}
                                    </a>
                                    {{--@include('layouts.childMenu',['childs'=>$value['child'],'item'=>$key])--}}
                                </li>
                            @elseif($value['menu'] == 1)
                                <li class="{{in_array($key,\app\backend\modules\menu\Menu::current()->getCurrentItems()) ? 'active' : ''}}">
                                    <a href="{{isset($value['url']) ? yzWebFullUrl($value['url']):''}}{{$value['url_params'] or ''}}">
                                        <i class="fa {{array_get($value,'icon','fa-circle-o') ?: 'fa-circle-o'}}"></i>
                                        {{$value['name'] or ''}}
                                    </a>
                                </li>
                            @endif
                        @endif
                    @endforeach
                </ul>
            </div>
            <div class="collapse navbar-collapse" style="float:right">
                <ul class="nav navbar-nav navbar-right">
                
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="material-icons" style="float:left">person</i>
                            {{--<span class="notification">5</span>--}}
                            <p class="" style="float:left">
                                {{YunShop::app()->username}}(供应商)
                                <b class="caret"></b>
                            </p>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="about"> <i></i> <a href="?c=site&a=entry&m=yun_shop&do=4936&route=plugin.supplier.supplier.controllers.info.index"> <span class="fa fa-wechat fa-fw"></span>个人信息</a> </li>
                            @if (config('app.framework') == 'platform')
                                <li class="drop_out"> <a href="/#/login" id="sys_logout"><span class="fa fa-sign-out fa-fw"></span>退出系统</a> </li>
                            @else
                                <li class="drop_out"> <a href="?c=user&a=logout"><span class="fa fa-sign-out fa-fw"></span>退出系统</a> </li>
                            @endif
                        </ul>

                    </li>
                    {{--<li>
                        <a href="#pablo" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="material-icons">person</i>
                            <p class="hidden-lg hidden-md">Profile</p>
                        </a>
                    </li>--}}
                    <li class="separator hidden-lg hidden-md"></li>
                </ul>
                {{--<form class="navbar-form navbar-right" role="search">
                    <div class="form-group form-search is-empty">
                        <input type="text" class="form-control" placeholder="Search">
                        <span class="material-input"></span>
                    </div>
                    <button type="submit" class="btn btn-white btn-round btn-just-icon">
                        <i class="material-icons">search</i>
                        <div class="ripple-container"></div>
                    </button>
                </form>--}}
            </div>
        </div>
    </nav>
</div>