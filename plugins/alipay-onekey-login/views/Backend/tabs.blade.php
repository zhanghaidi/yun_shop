<div class="panel panel-info">
    <ul class="add-shopnav">
        <li @if(YunShop::request()->route == 'plugin.love.Backend.Controllers.base-set.see') class="active" @endif>
            <a href="{{yzWebUrl('plugin.love.Backend.Controllers.base-set.see')}}">{{ trans('Yunshop\Love::base_set.subtitle') }}</a>
        </li>

        @foreach(Yunshop\Love\Common\Config\SetHook::getSetMenu() as $key=>$value)
            <li @if(YunShop::request()->route == $value['route']) class="active" @endif>
                <a href="{{ yzWebUrl($value['route']) }}">{{ $value['title'] }}</a>
            </li>
        @endforeach

    </ul>

</div>