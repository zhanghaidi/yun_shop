@extends('goods.index')

@section('foreach')
    @foreach($list as $item)

        <tr>
            <td width="3%"><input type="checkbox" name="check1" value="{{$item['id']}}"></td>
            <td width="6%">{{$item['id']}}</td>
            <td width="6%">
                <input type="text" class="form-control"
                       name="display_order[{{$item['id']}}]"
                       value="{{$item['display_order']}}">
            </td>
            <td width="6%" title="{{$item['title']}}">
                <img src="{{tomedia($item['thumb'])}}"
                     style="width:40px;height:40px;padding:1px;border:1px solid #ccc;"/>
            </td>
            <td title="{{$item['title']}}" class='tdedit' width="26%" style="white-space:normal">
                                            <span class=' fa-edit-item' style='cursor:pointer'><i class='fa fa-pencil'
                                                                                                  style="display:none"></i> <span
                                                        class="title">{{$item['title']}}</span> </span>
                <div class="input-group goodstitle" style="display:none"
                     data-goodsid="{{$item['id']}}">
                    <input type='text' class='form-control' value="{{$item['title']}}"/>
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-info"
                                data-goodsid='{{$item['id']}}' data-type="title"><i
                                    class="fa fa-check"></i></button>
                    </div>
                </div>
            </td>
            <td class='tdedit' width="16%">
                @if($item['has_option']==1)
                    <span class='tip' title='多规格不支持快速修改'>{{$item['price']}}</span>
                @else
                    <span class=' fa-edit-item' style='cursor:pointer'><i
                                class='fa fa-pencil' style="display:none"></i> <span
                                class="title">{{$item['price']}}</span> </span>
                    <div class="input-group" style="display:none"
                         data-goodsid="{{$item['id']}}">
                        <input type='text' class='form-control' value="{{$item['price']}}"/>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-info"
                                    data-goodsid='{{$item['id']}}' data-type="price"><i
                                        class="fa fa-check"></i></button>
                        </div>
                    </div>
                @endif
                <br/>
                @if($item['has_option']==1)
                    <span class='tip' title='多规格不支持快速修改'>{{$item['stock']}}</span>
                @else
                    <span class=' fa-edit-item' style='cursor:pointer'><i
                                class='fa fa-pencil' style="display:none"></i> <span
                                class="title">{{$item['stock']}}</span> </span>
                    <div class="input-group" style="display:none"
                         data-goodsid="{{$item['id']}}">
                        <input type='text' class='form-control' value="{{$item['stock']}}"/>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-info"
                                    data-goodsid='{{$item['id']}}' data-type="stock"><i
                                        class="fa fa-check"></i></button>
                        </div>
                    </div>
                @endif


            </td>

            <td>{{$item['real_sales']}}</td>
            <td>

                <label data='{{$item['status']}}'
                       class='@if($item['status']==1) btn btn-info @else  btn btn-default  @endif'
                       onclick="setProperty(this, {{$item['id']}},'status')">
                    @if($item['status']==1)
                        {{$lang['putaway']}}
                    @else
                        {{$lang['soldout']}}
                    @endif
                </label>

            </td>

            <td style="position:relative; overflow:visible;" width="25%">
                <div class="btn-group">
                    <a class="umphp" title="商品二维码"
                       data-url="{{yzAppFullUrl('goods/'.$item['id'])}}"
                       data-goodsid="{{$item['id']}}">
                        <div class="img">
                            {!! QrCode::size(120)->generate(yzAppFullUrl('goods/'.$item['id'])) !!}
                        </div>
                        <span>推广链接</span>
                    </a>

                    <a href="javascript:;"
                       data-clipboard-text="{{yzAppFullUrl('goods/'.$item['id'])}}"
                       data-url="{{yzAppFullUrl('goods/'.$item['id'])}}"
                       title="复制连接" class="js-clip">复制链接</a>


                    <a href="{{$yz_url($edit_url, array('id' => $item['id']))}}"
                       class="" title="编辑">编辑</a>
                    &nbsp;
                    <a href="{{$yz_url($delete_url, array('id' => $item['id']))}}"
                       onclick="return confirm('{{$delete_msg}}');
                               return false;" class="" title="删除">删除</a>
                    &nbsp;
                    <a href="{{yzWebUrl('plugin.jd-supply.admin.shop-goods.update-jd-goods', array('id' => $item['id']))}}"
                       title="同步跟新商品" class="js-clip">同步跟新商品</a>

                </div>
                <div>
                    <label data='{{$item['is_new']}}'
                           class='btn btn-sm @if($item['is_new']==1) btn-info @else btn-default @endif'
                           onclick="setProperty(this,{{$item['id']}},'is_new')">新品</label>

                    <label data='{{$item['is_hot']}}'
                           class='btn btn-sm @if($item['is_hot']==1) btn-info @else btn-default @endif'
                           onclick="setProperty(this,{{$item['id']}},'is_hot')">热卖</label>

                    <label data='{{$item['is_recommand']}}'
                           class='btn btn-sm @if($item['is_recommand']==1) btn-info @else btn-default @endif'
                           onclick="setProperty(this,{{$item['id']}},'is_recommand')">推荐</label>

                    <label data='{{$item['is_discount']}}'
                           class='btn btn-sm @if($item['is_discount']==1) btn-info @else btn-default @endif'
                           onclick="setProperty(this,{{$item['id']}},'is_discount')">促销</label>
                </div>
                <!-- yitian_add::商品链接二维码 2017-02-07 qq:751818588 -->
            </td>
        </tr>
    @endforeach

@endsection
@section('add_goods')
    {{--<a class='btn btn-success '--}}
       {{--href="@if($add_url){{yzWebUrl($add_url)}}@else{{yzWebUrl('goods.goods.create')}}@endif"><i--}}
                {{--class='fa fa-plus'></i> 发布{{$lang['good']}}</a>--}}
@endsection
