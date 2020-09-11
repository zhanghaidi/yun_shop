<div class="panel panel-info">
    <ul class="add-shopnav">

        @foreach(Config::get('plugins.sign.set_tabs') as $key=>$value)
            <li @if(YunShop::request()->route == $value['route']) class="active" @endif>
                <a href="{{ yzWebUrl($value['route']) }}">{{ $value['title'] }}</a>
            </li>
        @endforeach

    </ul>

</div>