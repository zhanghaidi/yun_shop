
<div class="form-group">
    <table class="table table-hover">
        <thead class="navbar-inner">
        <tr>
            <th class="col-sm-1">排序</th>
            <th class="col-sm-3">标题</th>
            <th class="col-sm-4">图片</th>
            <th class="col-sm-4">链接</th>
            <th class="col-sm-2">状态</th>
            <th class="col-sm-1">删除</th>
        </tr>
        </thead>
        <tbody id="param-itemscarousel">
            @foreach($package['has_many_carousel'] as $carousel)
            <tr>
                <td>
                    <input type="hidden" name="package[carousel][id][]" class="form-control" value="{{$carousel['id']}}"/>
                    <input type="text" name="package[carousel][sort][]" class="form-control carousel_sort" value="{{$carousel['carousel_sort']}}"/>
                </td>
                <td><input type="text" name="package[carousel][title][]" class="form-control" value="{{$carousel['carousel_title']}}"/></td>
                <td>
                    {!! app\common\helpers\ImageHelper::tplFormFieldImage('package[carousel][thumb][]', $carousel['carousel_thumb']) !!}
                    <span class="help-block">建议尺寸: 640 * 640 ，或正方型图片 </span>
                </td>
                <td>
                    <input class="form-control" type="text" data-id="PAL-00010" placeholder="请填写指向链接(以http://开头,不填则不显示)" value="{{$carousel['carousel_link']}}" name="package[carousel][link][]">
                    <span class="input-group-btn">
                        <button class="btn btn-default nav-link" type="button" data-id="PAL-00010" >选择链接</button>
                    </span>
                </td>
                <td>
                    <select class="form-control carousel_is_open" name="package[carousel][is_open][]">
                        <option value="1" @if($carousel['carousel_open_status'] == 1) selected="selected" @endif>显示</option>
                        <option value="0" @if($carousel['carousel_open_status'] == 0) selected="selected" @endif>隐藏</option>
                    </select>
                </td>
                <td style="text-align:left;">
                    <a href="javascript:;" class="btn btn-default btn-sm" onclick="deleteParam(this)" title="删除"><i class="fa fa-times"></i></a>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tr>
            <td colspan='6'>
                <a href="javascript:;" id='add-param_package' onclick="addParam('carousel')"
                   style="margin-top:10px;" class="btn btn-primary"  title="添加幻灯片"><i class='fa fa-plus'></i> 添加幻灯片</a>
            </td>

        </tr>
    </table>
    {{$pager}}
    @include('Yunshop\GoodsPackage::admin.carousel.mylink')
</div>
<script type="text/javascript">
    function putDataToCarouselFrom() {
        //获取用户填入的数据
        var carousel_id = $(':input[name="carousel_id"]').val();
        var carousel_title = $(':input[name="carousel_title"]').val();
        var carousel_thumb = $(':input[name="carousel_thumb"]').val();
        var carousel_link = $(':input[name="carousel[link]"]').val();
        var carousel_is_open = $(':input[name="carousel_is_open"]').val();
        //即将填入的数据
        var carousel_id = $(':input[name="carousel[id][]"]').val();
        var carousel_title = $(':input[name="carousel[title][]').val();
        var carousel_thumb = $(':input[name="carousel[thumb][]"]').val();
        var carousel_link = $(':input[name="carousel_link"][]').val();
        var carousel_is_open = $(':input[name="carousel_is_open"][]').val();
    }

</script>


