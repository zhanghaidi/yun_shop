<div class="form-group key_item">
    <label class="col-sm-2 control-label">打印内容</label>
    <div class="col-sm-9">
        <div class="input-group">
            <input type="text" name="temp[print_data][]" class="form-control" value="{{$row}}"/>
            <div class="input-group-btn"><button class="btn btn-danger" type="button" onclick="$(this).parents('.key_item').remove()">删除</button></div>
        </div>
        <span class='help-block'> 输入要打印的内容</span>
    </div>
</div>

<script>
    function len(s) {
        s = String(s);
        if (s.indexOf('|') != -1){
            return 32;
        }
        return s.length + (s.match(/[^\x00-\xff]/g) || "").length;// 加上匹配到的全角字符长度
    }
    function limit(obj, limit) {
        var val = obj.value;
        if (len(val) > limit) {
            val=val.substring(0,limit);
            while (len(val) > limit){
                val = val.substring(0, val.length - 1);
            };
            obj.value = val;
        }
    }
    $("input[name='temp[print_data][]']").keyup(function(){
        limit(this,32);//20字节内
    })
</script>

