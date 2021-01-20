<div class='panel panel-default' id="app">
    <div class='panel-heading'>
        课程设置
    </div>
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启课程点播</label>
            <div class="col-sm-9 col-xs-12">

                <label class="radio-inline">
                    <input type="radio" name="widgets[video_demand][is_course]" value="1"
                           @if($item['is_course'] == '1') checked="checked" @endif /> 开启</label>
                <label class="radio-inline">
                    <input type="radio" name="widgets[video_demand][is_course]" value="0"
                           @if($item['is_course'] == '0') checked="checked" @endif /> 关闭</label>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">请选择讲师</label>
            <div class="col-sm-9 col-xs-12">
                <select name='widgets[video_demand][lecturer_id]' class='form-control diy-lecturer'>
                    <option value="" @if(!$item['lecturer_id']) selected @endif >
                        请选择讲师
                    </option>
                    @foreach ($lecturers as $lecturer)
                        <option value="{{$lecturer['id']}}"
                                @if($item['lecturer_id'] == $lecturer['id'])
                                selected
                                @endif>{{$lecturer['real_name']}}({{$lecturer['mobile']}})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启讲师打赏</label>
            <div class="col-sm-9 col-xs-12">
                <label class="radio-inline">
                    <input type="radio" name="widgets[video_demand][is_reward]" value="1"
                           @if($item['is_reward'] == '1') checked="checked" @endif /> 开启</label>
                <label class="radio-inline">
                    <input type="radio" name="widgets[video_demand][is_reward]" value="0"
                           @if($item['is_reward'] == '0') checked="checked" @endif /> 关闭</label>
            </div>
        </div>


        <div class="form-group"></div>

    </div>

    <div class='panel-heading'>
        权限设置
    </div>
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级观看权限</label>
        <div class="col-sm-9 col-xs-12 chks" >

            <label class="checkbox-inline">
                <input type="checkbox" class='chkall' name="" value="" @if ( $item['see_levels'] == '' ) checked="true" @endif  /> 全部会员等级
            </label>
            <label class="checkbox-inline">
                <input type="checkbox" class='chksingle'  name="widgets[video_demand][see_levels][]" value="0" @if ( $item['see_levels'] != '' && is_array($item['see_levels'])  && in_array('0', $item['see_levels'])) checked="true" @endif  /> 普通等级
            </label>
            @foreach ($levels as $level)
                <label class="checkbox-inline">
                    <input type="checkbox" class='chksingle'  name="widgets[video_demand][see_levels][]" value="{{ $level['id'] }}" @if ( $item['see_levels'] != '' && is_array($item['see_levels']) && in_array($level['id'], $item['see_levels']) ) checked="true" @endif  /> {{ $level['level_name'] }}
                </label>
            @endforeach
        </div>
    </div>

    <div class='panel-heading'>
        课程章节管理
    </div>

    <div class="form-group">
        <div class="col-sm-9 col-xs-12">
            <div class='panel-body'>

            </div>
        </div>
        <div class="col-sm-9 col-xs-12">
            <div class='panel-body'>


                <div class="table-responsive ">
                    <div v-for="(item, index) in chapter">
                        <input type="hidden" name="widgets[video_demand][chapter][chapter_id][]" v-model="item.chapter_id" value="">
                        <div class="input-group">
                            <div class="input-group-addon" >章节名称</div>
                            <input type="text" name="widgets[video_demand][chapter][chapter_name][]"
                                   v-model="item.chapter_name"
                                   class="form-control" value=""/>

                            <div class="input-group-addon" >视频地址</div>
                            <input type="text" name="widgets[video_demand][chapter][video_address][]"
                                   v-model="item.video_address"
                                   class="form-control" value=""/>

                            <div class="input-group-addon" >试听权限</div>

                            <label class="checkbox-inline" style="height: 34px;display: table-cell; padding-left: 25px;" v-if="item.is_audition==1">
                                <input type="checkbox" class='chkall' name="" value="1" checked /> 免费试听
                                <input type="hidden" name="widgets[video_demand][chapter][is_audition][]" value="1">
                            </label>

                            <label class="checkbox-inline" style="height: 34px;display: table-cell; padding-left: 25px;" v-else>
                                <input type="checkbox" class='chkall' name="" value="0" /> 免费试听
                                <input type="hidden" name="widgets[video_demand][chapter][is_audition][]" value="0">
                            </label>


                            <div class="input-group-addon del-task" @click="delTask(index)" title="删除"><i
                                    class="fa fa-trash"></i></div>
                    </div>

                </div>

                <span class='help-block'><input id="chapter_button" type="button" @click="addTask" value="增加任务"
                                                            class="btn btn-success add-task"/></span>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    require(['select2'], function () {
        $('.diy-lecturer').select2();
    });

    $(document).on('click','.chkall',function (e) {
        if($(this).is(':checked')) {
            $(this).next().val(1);
        }else{
            $(this).next().val(0);
        }
    });

    var app = new Vue({
        el: '#app',
        delimiters: ['[[', ']]'],
        data: {
            course_chapter: [],
            chapter: [
                {
                    chapter_id: '',
                    chapter_name: '',
                    video_address: '',
                    is_audition: 0,
                }
            ],
        },
        mounted() {

        },

        created: function () {
            this.course_chapter = this.getChapter();
            if (this.course_chapter) {
                this.chapter = [];
            }
            _this = this;
            _this.course_chapter.forEach(function (item) {
                _this.chapter.push({
                    chapter_id: item.id,
                    chapter_name: item.chapter_name,
                    video_address: item.video_address,
                    is_audition: item.is_audition,
                });
            });
        },

        methods: {
            addTask: function () {
                this.chapter.push({
                    chapter_id: '',
                    chapter_name: '',
                    video_address: '',
                    is_audition: 0,
                });
            },
            delTask: function (index) {
                this.chapter.splice(index, 1);
            },
            getChapter: function () {
                return {!! $course_chapter !!}
            }
        },

    })

</script>



