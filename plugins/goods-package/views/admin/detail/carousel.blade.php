<tr>
    <td><input type="text" name="package[carousel][sort][]" class="form-control carousel_sort" value=""/></td>
    <td><input type="text" name="package[carousel][title][]" class="form-control" value=""/></td>
    <td>
        {!! app\common\helpers\ImageHelper::tplFormFieldImage('package[carousel][thumb][]', $row['thumb']) !!}
        <span class="help-block">建议尺寸:640 * 350 , 请将所有图片轮播图片尺寸保持一致</span>
            <a href='' target='_blank'>
                <img src="" style='width:100px;border:1px solid #ccc;padding:1px' />
            </a>
        </td>
    <td>
        <input class="form-control" type="text" data-id="PAL-00010" placeholder="请填写指向的链接(以http://开头,不填则不显示)" value="" name="package[carousel][link][]">
        <span class="input-group-btn">
            <button class="btn btn-default nav-link" type="button" data-id="PAL-00010" >选择链接</button>
        </span>
    </td>
    <td>
        <select class="form-control carousel_is_open" id="category_parent" name="package[carousel][is_open][]">
            <option value="1">显示</option>
            <option value="0">隐藏</option>
        </select>
    </td>
    <td style="text-align:left;">
        <a href="javascript:;" class="btn btn-default btn-sm" onclick="deleteParam(this)" title="删除"><i class="fa fa-times"></i></a>
    </td>
</tr>