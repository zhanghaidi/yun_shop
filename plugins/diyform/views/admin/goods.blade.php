<div class='panel panel-default'>

    <div class='panel-heading'>自定义表单设置</div>
    <div class='panel-body'>
        <div class='panel-body'>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义表单：</label>
                <div class="col-sm-4 col-xs-6">
                    <label class="radio-inline">
                        <input type="radio" name="widgets[diyform][status]" value="1" @if ($set['status'] == 1) checked="checked" @endif />
                        开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="widgets[diyform][status]" value="0" @if (empty($set['status'])) checked="checked" @endif />
                        关闭
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">请选择</label>
                <div class="col-sm-9 col-xs-12">
                    <select name='widgets[diyform][form_id]' class='form-control diy-notice'>
                    <?php $formList = Yunshop\Diyform\models\DiyformTypeModel::getDiyformList()->get(); //dump($formList)?>
                        <option value="">
                            请选择自定义表单
                        </option>
                        @foreach ($formList as $item)
                            <option value="{{$item['id']}}"
                                   @if($set['form_id'] == $item['id'])
                                    selected
                                    @endif>{{$item['title']}}</option>
                        @endforeach
                    <select>
                </div>
            </div>
        </div>
    </div>


</div>