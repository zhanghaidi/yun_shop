<tr>
    <td>
        <input class="form-control" name="package[category][sort][]" data-id="" value="" style="width:50px;float:left"  />
    </td>
    <td>
        <input class="form-control" name="package[category][cate_name][]" data-id=""  value="" style="width:120px;float:left"  />
    </td>
    <td>
        <input id="goodids" type="hidden" class="form-control" name="package[category][goods_ids][]" data-id="" data-name="goodsids"  value="" style="width:200px;float:left"  />
        <input id="goodnames" class="form-control" type="text" name="package[category][goods_names][]" data-id="" data-name="goodsnames" value="" style="width:450px;float:left" readonly="true">
        <span class="input-group-btn">
            <button class="btn btn-default nav-link-goods" type="button" data-id="" onclick="$('#modal-module-menus-goods').modal();$(this).parent().parent().addClass('focusgood')">选择商品</button>
        </span>
    </td>
    <td>
        <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
    </td>
</tr>