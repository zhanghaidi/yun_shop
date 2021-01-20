<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">奖励通知</label>
    <div class="col-sm-8 col-xs-12">
        <select name='setdata[nominate_award_message]' class='form-control diy-notice'>
            <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['nominate_award_message'])) value="{{$set['nominate_award_message']}}"
                    selected @else value="" @endif>
                默认消息模版
            </option>
            @foreach ($tempList as $item)
                <option value="{{$item['id']}}"
                        @if($set['nominate_award_message'] == $item['id'])
                        selected
                        @endif>{{$item['title']}}</option>
            @endforeach
        </select>
    </div>
</div>