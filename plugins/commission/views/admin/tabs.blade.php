<div class="panel panel-info">
    <ul class="add-shopnav">
        <li @if(YunShop::request()->route == 'plugin.commission.admin.set') class="active" @endif><a
                    href="{{yzWebUrl('plugin.commission.admin.set')}}">分销设置</a></li>
        {{--<li @if(YunShop::request()->route == 'plugin.commission.admin.set.manage') class="active" @endif><a--}}
                    {{--href="{{yzWebUrl('plugin.commission.admin.set.manage')}}">管理奖设置</a></li>--}}
        <li @if(YunShop::request()->route == 'plugin.commission.admin.set.notice') class="active" @endif><a
                    href="{{yzWebUrl('plugin.commission.admin.set.notice')}}">通知设置</a></li>
        <li @if(YunShop::request()->route == 'plugin.commission.admin.set.expand') class="active" @endif><a
                    href="{{yzWebUrl('plugin.commission.admin.set.expand')}}">定制设置</a></li>
    </ul>
</div>