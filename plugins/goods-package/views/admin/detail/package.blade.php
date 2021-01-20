<tr>
    <td>
        <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
    </td>
    <td  colspan="2">
        <input id="packageids" type="hidden" class="form-control" name="package[other_package_ids][]" data-id="{{$v}}" data-name="packageids"  value="" style="width:200px;float:left"  />
        <input id="packagenames" class="form-control" type="text" name="package[other_package_names][]" data-id="{{$v}}" data-name="packagenames" value="" style="width:200px;float:left" readonly="true">
        <span class="input-group-btn">
            <button class="btn btn-default nav-link" type="button" data-id="" onclick="$('#modal-module-menus-package').modal();$(this).parent().parent().addClass('focuspackage')" >选择套餐</button>
        </span>
    </td>
</tr>