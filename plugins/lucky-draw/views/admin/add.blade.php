@extends('layouts.base')
@section('title', "活动详情")
@section('content')

<style>
    #re_content{
        margin-top:20px;
    }
    .el-form .el-form-item{
    margin-right: 60px;   
            }
    .list_total_num{
        margin-top:20px;
    }
    .el-table__header-wrapper{
        margin-top:20px;
    }
    .el-tag{font-weight:700;font-size:15px;}
    .rightlist-head{padding:15px 0;line-height:50px;}
    .rightlist-head-con{float:left;padding-right:20px;font-size:16px;color:#888;}

    .el-button+.el-button {margin-left: 0px;}
    .el-breadcrumb{padding:30px 0;font-size:16px;}
    .el-breadcrumb__inner a{font-weight: 500;}

    .tip{color:#333;font-size:12px;}
    /* 上传图片 */
    .avatar-uploader .el-upload {margin-top:15px;border: 1px dashed #d9d9d9;border-radius: 6px;cursor: pointer;position: relative;overflow: hidden;}
    .avatar-uploader .el-upload:hover {border-color: #409EFF;}
    .avatar-uploader-icon {font-size: 28px;color: #8c939d;width: 48px;height: 48px;line-height: 48px;text-align: center;}
    .avatar {width: 48px;height: 48px;display: block;}
    .el-upload-tip{width:178px;margin:0;padding:0;color:#999;text-align:center;}
    input[type=file] {display: none;}
    .avatar-uploader-box{position:relative;width:60px;}
    .avatar-tip-text{position:absolute;width:48px;height:20px;line-height:28px;bottom:0;background:#000;opacity:0.7;color:#fff;font-size:12px;}
    .el-icon-circle-close{position:absolute;top:10px;right:0;color:#999;}
    /* 添加奖品弹出框 */
    .addAwrad .tag{
        word-wrap:break-word;
        padding:7px;
    }
    .addAwrad .tag span{
        padding:5px;
        margin-bottom:5px;
        display:inline-block;
        border-radius:4px;
        margin-right:5px;
    }
    /* 编辑奖品弹出框 */
    .editAwrad .tag{
        word-wrap:break-word;
        padding:7px;
    }
    .editAwrad .tag span{
        padding:5px;
        margin-bottom:5px;
        display:inline-block;
        border-radius:4px;
        margin-right:5px;
    }
    
    .Coupon .tag{
        word-wrap:break-word;
        padding:7px;
    }
    .Coupon .tag span{
        padding:5px;
        margin-bottom:5px;
        display:inline-block;
        border-radius:4px;
        margin-right:5px;
    }
    .classNormal{
        border:solid 1px #eee;
    }
    .classred{
        border:solid 1px red;
    }
    .el-step.is-simple .el-step__head{
        display: flex;
        align-items: center;
        margin-bottom: 2px;
    }
    </style>
<!-- tab -->

<div id='re_content' v-loading="all_loading">
    <div class="rightlist-head">
        <el-breadcrumb separator-class="el-icon-arrow-right">
                <el-breadcrumb-item><a href="{{ yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.index') }}">活动列表</a></el-breadcrumb-item>
                <el-breadcrumb-item>添加活动</el-breadcrumb-item>
        </el-breadcrumb>
    </div>
    <el-steps :active="active" simple style="width:90%;margin:0 auto">
        <el-step title="1、创建活动" icon="el-icon-edit"></el-step>
        <el-step title="2、奖项设置" icon="el-icon-setting"></el-step>
        <el-step title="3、页面装修" icon="el-icon-goods"></el-step>
        <el-step title="4、完成" icon="el-icon-success" :label="5"></el-step>
    </el-steps>
    <!-- content start -->
    <div style="width:1100px;margin:50px auto">
        <!-- left -->
        <div class="left-con" v-if="active==0||active==1||active==2">
        
        </div>
        <!-- right -->
        <!-- step 1 -->
        <el-form ref="form" :model="form" :rules="rules" label-width="20%">
            <div v-if="active==0" class="right-con" style="width:600px;border:1px solid #e1e1e1;background:#fafafa;float:left;">
                <div style="line-height:40px;padding-left:15px;font-weight:900;">基本信息</div>
                <hr style="padding:0;margin:0"/>
                <template>
                    <el-form-item label="活动名称" prop="name">
                        <el-input v-model="form.basic_name" style="width:70%"></el-input>
                    </el-form-item>
                    <el-form-item label="活动时间" prop="time">
                        <el-row>
                            <el-col :span="12" style="float:none;width:80%;">
                            <el-date-picker v-model="form.basic_start_time" type="datetime" format="yyyy-MM-dd HH:mm:ss"
                                value-format="timestamp" placeholder="选择开始时间" ></el-date-picker>
                            </el-col>
                            <el-col :span="12" style="float:none;margin-top:10px;width:80%;">
                            <el-date-picker v-model="form.basic_end_time" type="datetime" format="yyyy-MM-dd HH:mm:ss"
                                value-format="timestamp" placeholder="选择结束时间"></el-date-picker>
                            </el-col>
                        </el-row>
                    </el-form-item>
                    <el-form-item label="参与用户身份" prop="user_role">
                        <el-radio v-model.munber="form.basic_user_role" :label="0" >所有用户</el-radio>
                        <el-radio v-model.munber="form.basic_user_role" :label="1" >部分用户</el-radio>
                        <div v-if="form.basic_user_role">
                            <el-radio v-model.munber="form.basic_user_type" :label="0" >会员等级</el-radio>
                            <el-radio v-model.munber="form.basic_user_type" :label="1" >推广员</el-radio>
                            <el-select v-model="level" placeholder="请选择会员等级" clearable v-if="!form.basic_user_type" style="width:70%">
                                <el-option v-for="item in level_list" :key="item.id" :label="item.level_name" :value="item.id"></el-option>
                            </el-select>
                        </div>
                    </el-form-item>

                    <el-form-item label="购买商品" prop="goods_id">
                        <el-select
                                v-model="form.goods_id"
                                placeholder="请输入关键词"
                                filterable
                                remote
                                reserve-keyword
                                @change="getImgUrl"
                                placeholder="请输入关键词"
                                :remote-method="loadGoods"
                                :loading="loading"
                                style="width: 100%">
                            <el-option
                                    v-for="item in goods"
                                    :key="item.id"
                                    :label="'[GID:'+item.id+'][商品名称:'+item.title+']'"
                                    :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="" v-if="good">
                        <el-input disabled v-model="good" placeholder=""></el-input>
                    </el-form-item>
                    <el-form-item label="" v-if="imageUrl">
                        <img width="30%" :src="imageUrl" alt="">
                    </el-form-item>

                    <el-form-item label="活动类型" prop="type">
                        <el-radio v-model.munber="form.basic_user_ticket" :label="0" >无限制抽签</el-radio><br>
                        <el-radio v-model.munber="form.basic_user_ticket" :label="1" >积分抽奖</el-radio>
                        <span style="font-weight:500;padding-left:20px">消耗<input type="text" style="height:25px;width:100px;" v-model="form.basic_coin"/>积分</span><br>
                        <el-radio v-model.munber="form.basic_user_ticket" :label="2" v-if="love_status == 1">[[love_name]]抽奖</el-radio>
                        <span style="font-weight:500;padding-left:20px" v-if="love_status == 1">消耗<input type="text" style="height:25px;width:100px;" v-model="form.basic_love"/>[[love_name]]</span><br>
                    </el-form-item>
                    
                    <el-form-item label="参与次数" prop="number">
                        <el-radio v-model.munber="form.basic_number" :label="0" >一天N次</el-radio><br>
                        <div v-if="form.basic_number==0">
                            <div style="font-weight:500;padding-left:20px">每人每天抽<input type="text" v-model="form.basic_dayTime" style="height:25px;width:100px;"/>次</div>
                            <div style="font-weight:500;padding-left:20px">每天分享额外抽<input type="text"  v-model="form.basic_dayExtra" style="height:25px;width:100px;"/>次</div>
                        </div>
                        <el-radio v-model.munber="form.basic_number" :label="1" >一人N次</el-radio>
                        <div v-if="form.basic_number==1">
                            <div style="font-weight:500;padding-left:20px">每人一共可抽<input type="text" v-model="form.basic_peoTime" style="height:25px;width:100px;"/>次</div>
                            <div style="font-weight:500;padding-left:20px">每人分享额外抽<input type="text" v-model="form.basic_peoExtra" style="height:25px;width:100px;"/>次</div>
                        </div>
                    </el-form-item>
                   
                    
                </template>
            </div>
            <!-- step 2 -->
            <div v-if="active==1" class="right-con" style="width:600px;border:1px solid #e1e1e1;background:#fafafa;float:left;">
            <el-form ref="prizeSet" :model="prizeSet" :rules="rules" label-width="20%">
                <div style="line-height:40px;padding-left:15px;font-weight:900;">奖品设置</div>
                <hr style="padding:0;margin:0"/>
                <el-button type="primary" size="mini" style="margin:5px" @click="addAward()" v-if="award_list.length<6">添加奖品</el-button>
                <!-- 表格start -->
                <template>
                    <el-table :data="award_list" style="width: 100%" v-loading="search_loading">
                        <el-table-column prop="name" label="名称" align="center"></el-table-column>
                        <el-table-column prop="award" label="奖品" align="center" ></el-table-column>
                        <el-table-column label="图片" width="100" align="center">
                            <template slot-scope="scope">
                                <img :src="[[scope.row.thumb_url]]" style="width:50px;height:50px;">
                            </template>
                        </el-table-column>
                        <el-table-column prop="prize_num" label="数量"  align="center"></el-table-column>
                        <el-table-column prop="chance" label="中奖概率"  align="center"> </el-table-column>
                        <el-table-column label="操作" align="center">
                            <template slot-scope="scope">
                                <a @click="editAward(scope, award_list)">编辑</a>
                                <a @click="open(scope, award_list)">删除</a>
                            </template>
                        </el-table-column>
                    </el-table>
                </template>
                <span style="margin:10px;" class="help-block">注：所有奖品中奖概率总和不能超过100%</span>
                <div style="line-height:40px;padding-left:15px;font-weight:900;">未中奖设置</div>
                <hr style="padding:0;"/>
                <template>
                    <el-form-item label="名称" prop="name">
                        <el-input v-model="prizeSet.name" style="width:70%"></el-input>
                    </el-form-item>
                    <el-form-item label="图片" prop="name">
                        <div class="avatar-uploader-box">
                            <el-upload class="avatar-uploader" action="{{ yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.upload') }}" accept="image/*" :show-file-list="false" :on-success="prizeSuccess" :before-upload="prizeUpload">
                                <img v-if="prizeSet.prize_thumb" :src="prizeSet.prize_thumb" class="avatar">
                                <div v-if="prizeSet.prize_thumb" class="avatar-tip-text">修改</div>
                                <i v-if="!prizeSet.prize_thumb" class="el-icon-plus avatar-uploader-icon"></i>
                            </el-upload>
                            <i v-show="prizeSet.prize_thumb" class="el-icon-circle-close" @click="clearPrize" title="点击清除图片"></i>
                        </div>
                        <div class="tip">建议尺寸96*96像素，支持JPG、PNG、JPEG格式</div>
                    </el-form-item>
                    <el-form-item label="提示语" prop="tip">
                        <el-input v-model="prizeSet.tip" style="width:70%"></el-input>
                    </el-form-item>
                    <el-form-item label="提示跳转" prop="is_jump">
                        <el-radio v-model.munber="prizeSet.is_jump" :label="0" >无跳转</el-radio>
                        <el-radio v-model.munber="prizeSet.is_jump" :label="1" >有跳转</el-radio>
                            <el-form-item label="" v-if="prizeSet.is_jump">
                                <el-input type="textarea" placeholder="请输入跳转链接，以https://开头" v-model="prizeSet.url"></el-input>
                            </el-form-item>
                        <div class="tip">如果开启有跳转，小程序默认跳转首页</div>
                    </el-form-item>
                    
                    <div style="line-height:40px;padding-left:15px;font-weight:900;">参与者奖励</div>
                    <hr style="padding:0;margin:0"/>
                    <div style="margin:10px 0">
                        <span style="font-weight:500;padding-left:20px">送<el-input type="text" style="width:100px;" v-model="prizeSet.coin"></el-input>积分</span>
                        <span style="font-weight:500;padding-left:20px" v-if="love_status == 1">送<el-input type="text" style="width:100px;" v-model="prizeSet.love"></el-input>[[love_name]]</span>
                        <span style="font-weight:500;padding-left:20px">送<el-input type="text" style="width:100px;" v-model="prizeSet.rest"></el-input>余额</span>
                    </div>
                    <el-form-item label="送优惠券">
                        <el-input style="width:300px;" v-model="couponName"></el-input>
                        <el-button @click="couponShow()">选择优惠券</el-button>
                    </el-form-item>
                    <el-form-item label="" prop="is_not_win">
                        <el-checkbox v-model="prizeSet.is_not_win">仅送给未中奖者</el-checkbox>
                    </el-form-item>
                </template>
                </el-form-item>
            </div>
            <!-- step 3 -->
            <div v-if="active==2" class="right-con" style="width:600px;border:1px solid #e1e1e1;background:#fafafa;float:left;">
            <el-form ref="diy_form" :model="diy_form" :rules="rules" label-width="20%">
                <div style="line-height:40px;padding-left:15px;font-weight:900;">页面装修</div>
                <hr style="padding:0;"/>
                 <template>
                    <el-form-item label="图片" prop="name">
                        <div class="avatar-uploader-box">
                            <el-upload class="avatar-uploader" action="{{ yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.upload') }}" accept="image/*" :show-file-list="false" :on-success="diySuccess" :before-upload="diyUpload">
                                <img v-if="diy_form.thumb_url" :src="diy_form.thumb_url" class="avatar">
                                <div v-if="diy_form.thumb_url" class="avatar-tip-text">修改</div>
                                <i v-if="!diy_form.thumb_url" class="el-icon-plus avatar-uploader-icon"></i>
                            </el-upload>
                            <i v-show="diy_form.thumb_url" class="el-icon-circle-close" @click="clearDiy" title="点击清除图片"></i>
                        </div>
                        <div class="tip">建议尺寸375x160px，支持JPG、PNG、JPEG格式</div>
                    </el-form-item>
                    <el-form-item label="商家LOGO" prop="is_logo">
                        <el-radio v-model.munber="diy_form.is_logo" :label="0" >不展示</el-radio>
                        <el-radio v-model.munber="diy_form.is_logo" :label="1" >展示</el-radio>
                    </el-form-item>
                    <el-form-item label="中奖名单" prop="is_award_list">
                        <el-radio v-model.munber="diy_form.is_award_list" :label="0" >不展示</el-radio>
                        <el-radio v-model.munber="diy_form.is_award_list" :label="1" >展示</el-radio>
                    </el-form-item>
                    <el-form-item label="背景色" prop="color">
                        <el-color-picker v-model="diy_form.color"></el-color-picker>
                    </el-form-item>
                    <div style="line-height:40px;padding-left:15px;font-weight:900;">活动说明</div>
                    <hr style="padding:0;margin-top:0"/>
                    <el-input type="textarea" rows="5" placeholder="请输入活动说明" style="width:90%;margin-left:5%;padding-bottom:20px;" v-model="diy_form.notice"></el-input>
                </template>
            </div>
            
        </el-form>

        
    </div>
    <!-- content end -->
    <!-- button -->
    <div style="width:100%;display:block;float:left;text-align:center;margin:30px 0;" v-if="active==0||active==1||active==2">
    <a href="{{ yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.index') }}"><el-button  v-if="active==0">取消</el-button></a>
        <el-button @click="last()" v-if="active==1||active==2">
            上一步
        </el-button>
        <el-button type="success" @click="next()" v-if="active==1||active==0">
            下一步
        </el-button>
        <el-button type="success" @click="submit()" v-if="active==2">
            完成
        </el-button>
    
    </div>
    <!-- 完成 -->
    <!-- step 4 -->
    <div v-if="active==4">
        <div style="text-align:center">
            <div style="line-height:100px;">
                <i class="el-icon-success" style="color:#67C23A;font-size:80px;"></i>
            </div>
            <div style="font-weight:900;font-size:30px;line-height:48px;">幸运大转盘创建成功</div>
            <div style="font-weight:500;font-size:24px;line-height:48px;">立即推广活动，获得更多客户与订单</div>
            <div>
              
            </div>
                <img :src="qrCode" alt='二维码'>
            <div>
                <el-button style="margin:10px 0" @click="copy()">复制链接</el-button>
                <el-input style="opacity:0;position: absolute;" v-model="activity_url" ref="activity">
            </div>
            <a href="{{ yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.index') }}"><el-button style="margin:10px 0">返回列表</el-button></a>
            <a href="{{ yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.add') }}"><el-button style="margin:10px 0">创建活动</el-button></a>
        </div>
    </div>
    <!-- 添加奖品弹出框 -->
    <el-dialog title="添加奖品" :visible.sync="is_add_dialog" @close="closePop" class="addAwrad" :lock-scroll="true">
        <div >
            <el-form ref="award_form" :model="award_form" :rules="rules" label-width="20%">
                <template>
                    <el-form-item label="名称" prop="name">
                        <el-input v-model="award_form.name" style="width:70%" placeholder="最多可填6个字"></el-input>
                    </el-form-item>
                    <el-form-item label="奖品" prop="award_type">
                        <div>
                            <el-radio v-model.munber="award_form.award_type" :label="1" >优惠券</el-radio>
                    <el-select
                            v-model="award_form.coupon"
                            v-if="award_form.award_type==1"
                            filterable
                            remote
                            style="width:70%"
                            reserve-keyword
                            placeholder="请输入关键词"
                            :remote-method="loadAward"
                            :loading="loading"
                            style="width: 100%">
                                <el-option
                                    v-for="item in AwardList"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id">
                                </el-option>
                    </el-select>
                        </div>
                        <div style="margin:5px 0">
                            <el-radio v-model.munber="award_form.award_type" :label="2" >积分</el-radio>
                            <el-input style="width:60%;padding-left:20px" v-model="award_form.coin">
                                 <template slot="append">积分</template>
                            </el-input>
                        </div>
                        <div style="margin:5px 0" v-if="love_status == 1">
                            <el-radio v-model.munber="award_form.award_type" :label="3" >[[love_name]]</el-radio>
                            <el-input style="width:60%;padding-left:20px" v-model="award_form.love">
                                <template slot="append">[[love_name]]</template>
                            </el-input>
                        </div>
                        <div style="margin:5px 0">
                            <el-radio v-model.munber="award_form.award_type" :label="4" >余额</el-radio>
                            <el-input style="width:60%;padding-left:20px" v-model="award_form.rest">
                                <template slot="append">余额</template>
                            </el-input>
                        </div>
                    </el-form-item>
                    <el-form-item label="图片" prop="name">
                        <div class="avatar-uploader-box">
                            <el-upload class="avatar-uploader" action="{{ yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.upload') }}" accept="image/*" :show-file-list="false" :on-success="uploadSuccess" :before-upload="beforeUpload">
                                <img v-if="award_form.thumb_url" :src="award_form.thumb_url" class="avatar">
                                <div v-if="award_form.thumb_url" class="avatar-tip-text">修改</div>
                                <i v-if="!award_form.thumb_url" class="el-icon-plus avatar-uploader-icon"></i>
                            </el-upload>
                            <i v-show="award_form.thumb_url" class="el-icon-circle-close" @click="clearImg" title="点击清除图片"></i>
                        </div>
                        <div class="tip">建议尺寸96*96像素，支持JPG、PNG、JPEG格式</div>
                    </el-form-item>
                    <el-form-item label="数量" prop="name">
                        <el-input v-model="award_form.number" style="width:70%">
                            <template slot="append">份</template>
                        </el-input>
                    </el-form-item>
                    
                    <el-form-item label="中奖概率" prop="rent">
                        <el-input v-model="award_form.rent" style="width:70%">
                            <template slot="append">%</template>
                        </el-input>
                    </el-form-item>
                </template>
            </el-form>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="is_add_dialog = false">取 消</el-button>
            <el-button type="primary" @click="choose()">确 定</el-button>
        </span>
    </el-dialog>
    <!-- 编辑奖品弹出框 -->
    <el-dialog title="编辑奖品" :visible.sync="is_edit_dialog" @close="closeEdit" class="editAwrad" :lock-scroll="true">
        <div>
            <el-form ref="edit_form" :model="edit_form" :rules="rules" label-width="20%">
                <template>
                    <el-form-item label="名称" prop="name">
                        <el-input v-model="edit_form.name" style="width:70%" placeholder="最多可填6个字" v-model="edit_form.name" :value="edit_form.type===1?edit_form.name:''"></el-input>
                    </el-form-item>
                    <el-form-item label="奖品" prop="award_type">
                        <div>
                            <el-radio v-model.munber="edit_form.type" :label="1" >优惠券</el-radio>
                        <el-select
                            v-model="edit_form.coupon_id"
                            v-if="edit_form.type==1"
                            filterable
                            remote
                            style="width:70%"
                            reserve-keyword
                            placeholder="请输入关键词"
                            :remote-method="loadEdit"
                            :loading="loading"
                            style="width: 100%">
                                <el-option
                                    v-for="item in EditList"
                                    :key="item.id"
                                    :label="item.name"
                                    :value="item.id">
                                </el-option>
                        </el-select>
                        </div>
                        <div style="margin:5px 0">
                            <el-radio v-model.munber="edit_form.type" :label="2" >积分</el-radio>
                            <el-input style="width:60%;padding-left:20px" v-model="edit_form.point" :value="edit_form.type===2?edit_form.point:''">
                                 <template slot="append">积分</template>
                            </el-input>
                        </div>
                        <div style="margin:5px 0" v-if="love_status == 1">
                            <el-radio v-model.munber="edit_form.type" :label="3">[[love_name]]</el-radio>
                            <el-input style="width:60%;padding-left:20px" v-model="edit_form.love" :value="edit_form.type===3?edit_form.love:''">
                                <template slot="append">[[love_name]]</template>
                            </el-input>
                        </div>
                        <div style="margin:5px 0">
                            <el-radio v-model.munber="edit_form.type" :label="4" >余额</el-radio>
                            <el-input style="width:60%;padding-left:20px" v-model="edit_form.amount" :value="edit_form.type===4?edit_form.rest:''">
                                <template slot="append">余额</template>
                            </el-input>
                        </div>
                    </el-form-item>
                    <el-form-item label="图片" prop="name">
                        <div class="avatar-uploader-box">
                            <el-upload class="avatar-uploader" action="{{ yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.upload') }}" accept="image/*" :show-file-list="false" :on-success="editSuccess" :before-upload="editUpload">
                                <img v-if="edit_form.thumb_url" :src="edit_form.thumb_url" class="avatar">
                                <div v-if="edit_form.thumb_url" class="avatar-tip-text">修改</div>
                                <i v-if="!edit_form.thumb_url" class="el-icon-plus avatar-uploader-icon"></i>
                            </el-upload>
                            <i v-show="edit_form.thumb_url" class="el-icon-circle-close" @click="clearEdit" title="点击清除图片"></i>
                        </div>
                        <div class="tip">建议尺寸96*96像素，支持JPG、PNG、JPEG格式</div>
                    </el-form-item>
                    <el-form-item label="数量" prop="name">
                        <el-input v-model="edit_form.prize_num" style="width:70%">
                            <template slot="append">份</template>
                        </el-input>
                    </el-form-item>
                    
                    <el-form-item label="中奖概率" prop="rent">
                        <el-input v-model="edit_form.chance" style="width:70%">
                            <template slot="append">%</template>
                        </el-input>
                    </el-form-item>
                </template>
            </el-form>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="is_edit_dialog = false">取 消</el-button>
            <el-button type="primary" @click="editChoose()">确 定</el-button>
        </span>
    </el-dialog>
    <el-dialog title="选择优惠券" :visible.sync="coupon_show" @close="closeCoupon" class="Coupon">
        <el-select
                v-model="prizeSet.coupon_id"
                filterable
                remote
                style="width:100%"
                reserve-keyword
                placeholder="请输入关键词"
                :remote-method="loadCoupon"
                value-key="id"
                @change="choice"
                :loading="loading"
                style="width: 100%">
                <el-option
                        v-for="item in CouponList"
                        :key="item.id"
                        :label="item.name"
                        :value="item">
                </el-option>
        </el-select>
        <span slot="footer" class="dialog-footer">
            <el-button @click="coupon_show = false">取 消</el-button>
            <el-button type="primary" @click="couponChoose()">确 定</el-button>
        </span>
    </el-dialog>
    
</div>

<script>
    var vm = new Vue({
        el: "#re_content",
        delimiters: ['[[', ']]'],
        data() {
            let data = {!! $page_list?:'{}' !!}
            let love_name = {!! $love_name ?: '爱心值' !!}
            let love_status = {!! $love_status !!}
            let category_list = {!! $category_list?:'{}' !!}
            let activate_list = {!! $type_list?:'{}' !!};
            let lang = {!! $lang?:'{}' !!};
            return {
                love_name:love_name,
                love_status:love_status,
                arr:[],
                AwardList:[],
                couponName:'',
                couponSend:{},
                EditList:[],
                CouponList:[],
                activity_url:'',
                qrCode:'',
                all_loading:false,
                search_loading:false,
                search_form:{},
                real_search_form:"",
                activate_list:activate_list,
                category_list: category_list,
                level:'',
                coupon:'',
                coupon_show:false,
                level_list:[
                ],
                coupon_list:[],
                award_list:[
                ],
                active:0,
                old_edit_form_coupon:'',
                form:{
                    basic_name:'',
                    basic_start_time:null,
                    basic_end_time:null,
                    basic_user_role:0,
                    basic_number:0,
                    basic_user_ticket:0,
                    basic_love:'',
                    basic_coin:'',
                    basic_dayTime:'',
                    basic_dayExtra:'',
                    basic_peoTime:'',
                    basic_peoExtra:'',
                    basic_user_type:0,
                    goods_id:'',
                },
                prizeSet:{
                    name:'',
                    prize_thumb:'',
                    tip:'',
                    is_jump:0,
                    coin:'',
                    love:'',
                    rest:'',
                    coupon:'',
                    url:'',
                    is_not_win:true,
                    coupon_id:{}
                },
                award_form:{
                    name:'',
                    award_type:1,
                    coupon:'',
                    thumb_url:'',
                    number:'',
                    rent:'',
                    coin:'',
                    love:'',
                    rest:''
                },
                edit_form:{
                amount: '',
                chance: '',
                coupon_id: '',
                id: '',
                love: '',
                name: "",
                point: '',
                prize_num: 11,
                thumb_url: "",
                type:1,
                },
                diy_form:{
                    thumb_url:'',
                    is_logo:1,
                    is_award_list:1,
                    color:'',
                    notice:''
                },
                is_add_dialog:false,
                is_edit_dialog:false,
                page_total:1,
                data:'',
                list:[
                ],
                goods : [],
                good : '',
                imageUrl : '',
                loading:false,
                rules:{},
            }
        },
        created () {
            this.getLevel();
        },
        methods: {
            closePop(){
                this.is_add_dialog=false;
            },
            closeEdit(){
                this.is_edit_dialog=false;
            },
            closeCoupon(){
                this.coupon_show=false;
               
            },
            open(scope, rows) {
            this.$confirm('此操作将永久删除该奖品, 是否继续?', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
            }).then(() => {
          this.$message({
            type: 'success',
            message: '删除成功!',
            callback:this.deleteAward(scope, rows)
          });
            }).catch(() => {
            this.$message({
            type: 'info',
            message: '已取消删除'
          });          
            });
            },
            loadGoods(query) {
                console.log(query)
                if (query !== '') {
                    this.loading = true;
                    this.$http.get("{!! yzWebUrl('plugin.lucky-draw.admin.controllers.activity.get-goods', ['keyword' => '']) !!}" + query).then(response => {
                        this.goods = response.data.data,
                            this.loading = false;
                    }, response => {
                        console.log(response);
                    });
                } else {
                    this.goods = [];
                }
            },
            getImgUrl(id) {
                let good = this.goods.find(function (good) {
                    return good.id == id;
                });
                this.imageUrl = good.thumb;
                console.log(this.imageUrl)
            },

            deleteAward(scope, rows){
                rows.splice(scope.$index, 1);
                this.arr.map((item,index,key)=>{
                    if(item===scope.row.id){
                        this.arr.splice(index,1);
                    }
                })
                let json={
                    id:scope.row.id
                }
                this.$http.post("{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.delPrize') !!}",json).then(function (response){
                  
                },function (response) {
                    console.log(response);
                }
                );
            },
            getLevel(){
                this.$http.get('{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.getMemberLevels') !!}').then(function (response){
                    
                    this.level_list=response.data.data.memberLevels;
                },function (response) {
                    console.log(response);
                }
                );
            },
            next() {
                if(this.active===0){
                    if(this.form.basic_name===''){
                        this.$message.error("请输入活动名称");
                        return
                    }
                    if(this.form.basic_start_time===null){
                        this.$message.error("请选择开始时间");
                        return
                    }
                    if(this.form.basic_end_time===null){
                        this.$message.error("请选择结束时间");
                        return
                    }
                    if(this.form.basic_user_type==0&&this.form.basic_user_role===1){
                        if(this.level===''){
                            this.$message.error("请选择会员等级");
                            return
                        }
                    }
                    switch(this.form.basic_user_ticket) {
                          case 0:
                          break;
                          case 1:
                          if(this.form.basic_coin===''){
                            this.$message.error("请填写消耗积分");
                            return
                          }
                          if(isNaN(this.form.basic_coin)){
                            this.$message.error("消耗积分需为数字");
                            return
                          }
                          break;
                          case 2:
                          if(this.form.basic_love===''){
                            this.$message.error(`请填写消耗${this.love_name}`);
                            return
                          }
                          if(isNaN(this.form.basic_love)){
                            this.$message.error(`消耗${this.love_name}需为数字`);
                            return
                          }
                          break;
                    } 
                    switch(this.form.basic_number){
                          case 0:
                          if(this.form.basic_dayTime===''){
                              console.log(this.level);
                            this.$message.error("请填写每人每天抽取的次数");
                            return
                          }
                          if(isNaN(this.form.basic_dayTime)){
                            this.$message.error("每人每天抽取的次数需为数字");
                            return
                          }
                          if(this.form.basic_dayExtra===''){
                            this.$message.error("请填写每天分享额外抽取的次数");
                            return
                          }
                          if(isNaN(this.form.basic_dayExtra)){
                            this.$message.error("每天分享额外抽取的次数需为数字");
                            return
                          }
                          break;
                          case 1:
                          if(this.form.basic_peoTime===''){
                            this.$message.error("请填写每人一共可抽取的次数");
                            return
                          }
                          if(isNaN(this.form.basic_peoTime)){
                            this.$message.error("每人一共可抽取的次数需为数字");
                            return
                          }
                          if(this.form.basic_peoExtra===''){
                            this.$message.error("请填写每人分享额外抽取的次数");
                            return
                          }
                          if(isNaN(this.form.basic_peoExtra)){
                            this.$message.error("每人分享额外抽取的次数需为数字");
                            return
                          }
                          break;
                    }
                }
                if(this.active===1){
                    if(this.award_list.length<=0){
                        this.$message.error("请先添加奖品");
                        return 
                    }
                    if(this.prizeSet.name===''){
                        this.$message.error("请先填写未中奖设置名称");
                        return 
                    }
                    if(this.prizeSet.prize_thumb===''){
                        this.$message.error("请先传未中奖设置图片");
                        return 
                    }
                    if(this.prizeSet.coin==''&&this.prizeSet.love==''&&this.prizeSet.rest==''&&this.couponName==''){
                        this.$message.error("请先填写参与者奖励");
                        return
                    }
                    let changeAmount=this.award_list.map((item,index,key)=>{return Number(item.chance)})
                    let  chanceAmount=changeAmount.reduce(function(a,b){
                        return a+b
                    },0)
                   if(chanceAmount>=100){
                    this.$message.error("所有奖品中奖概率总和不能大于等于100%");
                    return 
                   }
                }
                if(this.active<=2){
                    this.active++;
                }
            },
            last() {
                this.active--;
            },
            addAward() {
                this.award_form={
                    name:'',
                    award_type:1,
                    coupon:'',
                    thumb_url:'',
                    number:'',
                    rent:'',
                    coin:'',
                    love:'',
                    rest:''

                },
                this.is_add_dialog = true;
            },
            editAward(scope, rows){
                let json={
                    id:scope.row.id
                }
                this.$http.post("{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.editPrize') !!}",json).then(response => {
                                if (response.data.result) {
                                    this.edit_form=response.data.data.prizeModel;
                                    if(response.data.data.prizeModel.type===1){
                                        this.old_edit_form_coupon=response.data.data.prizeModel.coupon_id;
                                        this.edit_form.coupon_id=response.data.data.prizeModel.has_one_coupon.name;
                                    }
                                    if(this.edit_form.type==2){
                                        this.edit_form.love='';
                                        this.edit_form.amount='';
                                        this.edit_form.coupon_id='';
                                    }
                                    if(this.edit_form.type==3){
                                        this.edit_form.point='';
                                        this.edit_form.amount='';
                                        this.edit_form.coupon_id='';
                                    }
                                    if(this.edit_form.type==4){
                                        this.edit_form.love='';
                                        this.edit_form.point='';
                                        this.edit_form.coupon_id='';
                                    }
                              
                                }
                                else {
                                  this.$message({message: response.data.msg,type: 'error'});
                                }
                });
                this.is_edit_dialog = true;
            },
            couponChoose(){
                this.coupon_show=false;
                this.couponName=this.couponSend.name;
            },
            choice(value){
                this.couponSend=value;
            },
            // 确认选中奖品
            choose() {
                if(this.award_form.name===''){
                    this.$message.error('请填写正确的奖品名称');
                    return ;
                }
                switch (this.award_form.award_type) {
                    case 1:
                    if(this.award_form.coupon===''||this.award_form.coupon===null||this.award_form.coupon===undefined){
                            this.$message.error("请选择优惠券");
                            return
                    }
                    break;
                    case 2:
                    if(this.award_form.coin===''){
                            this.$message.error("请填写积分");
                            return
                    }
                    if(isNaN(this.award_form.coin)){
                            this.$message.error("积分需为数字");
                            return
                    }
                    break;
                    case 3:
                    if(this.award_form.love===''){
                            this.$message.error(`请填写${this.love_name}`);
                            return
                    }
                    if(isNaN(this.award_form.love)){
                            this.$message.error(`${this.love_name}需为数字`);
                            return
                    }
                    break;
                    case 4:
                    if(this.award_form.rest===''){
                            this.$message.error("请填写余额");
                            return
                    }
                    if(isNaN(this.award_form.rest)){
                            this.$message.error("余额需为数字");
                            return
                    }
                    break;
                }
                if(this.award_form.thumb_url===''){
                    this.$message.error("请添加图片");
                    return
                }
                if(this.award_form.number===''){
                    this.$message.error("请填写数量");
                    return
                }
                if(isNaN(this.award_form.number)){
                            this.$message.error("数量需为数字");
                            return
                }
                if(this.award_form.rent===''){
                    this.$message.error("请填写中奖概率");
                    return
                }
                if(0>Number(this.award_form.rent)||Number(this.award_form.rent)>=100){
                    this.$message.error("中奖概率需在0-100之间");
                    return
                }
                let json={
                    name:this.award_form.name,
                    type:this.award_form.award_type,
                    coupon_id:this.award_form.award_type===1?this.award_form.coupon:'',
                    thumb_url:this.award_form.thumb_url,
                    prize_num:this.award_form.number,
                    chance:this.award_form.rent,
                    point:this.award_form.award_type===2?this.award_form.coin:'',
                    love:this.award_form.award_type===3?this.award_form.love:'',
                    amount:this.award_form.award_type===4?this.award_form.rest:'',
                    
                }
                this.$http.post("{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.addPrize') !!}",{'form_data':json}).then(response => {
                         
                                if (response.data.result) {
                                    this.$message({message: "添加奖品成功",type: 'success'});
                                    json.id=response.data.data.id;
                                    this.arr.push(json.id);
                                    this.award_list.push(json);
                                    this.award_list.map((item,index,key)=>{
                                        if(item.type==1){
                                            item.award='优惠券'
                                        }
                                        if(item.type==2){
                                            item.award='积分'
                                        }
                                        if(item.type==3){
                                            item.award=love_name
                                        }
                                        if(item.type==4){
                                            item.award='余额'
                                        }

                                    })
                                }
                                else {
                                    this.$message({message: response.data.msg,type: 'error'});
                                }
                });
                this.is_add_dialog=false;
            },
            editChoose(){
                if(this.edit_form.name===''){
                    this.$message.error('请填写正确的奖品名称');
                    return ;
                }
                switch (this.edit_form.type) {
                    case 1:
                    if(this.edit_form.coupon_id===''||this.edit_form.coupon_id===null||this.edit_form.coupon_id===undefined){
                            this.$message.error("请选择优惠券");
                            return
                    }
                    break;
                    case 2:
                    if(this.edit_form.point===''){
                            this.$message.error("请填写积分");
                            return
                    }
                    if(isNaN(this.edit_form.point)){
                            this.$message.error("积分需为数字");
                            return
                    }
                    break;
                    case 3:
                    if(this.edit_form.love===''){
                            this.$message.error(`请填写${this.love_name}`);
                            return
                    }
                    if(isNaN(this.edit_form.love)){
                            this.$message.error(`${this.love_name}需为数字`);
                            return
                    }
                    break;
                    case 4:
                    if(this.edit_form.amount===''){
                            this.$message.error("请填写余额");
                            return
                    }
                    if(isNaN(this.edit_form.amount)){
                            this.$message.error("余额需为数字");
                            return
                    }
                    break;
                }
                if(this.edit_form.thumb_url===''){
                    this.$message.error("请添加图片");
                    return
                }
                if(this.edit_form.prize_num===''){
                    this.$message.error("请填写数量");
                    return
                }
                if(isNaN(this.edit_form.prize_num)){
                            this.$message.error("数量需为数字");
                            return
                }
                if(this.edit_form.chance===''){
                    this.$message.error("请填写中奖概率");
                    return
                }
                if(0>Number(this.edit_form.chance)||Number(this.edit_form.chance)>=100){
                    this.$message.error("中奖概率需在0-100之间");
                    return
                }
                if(typeof(this.edit_form.coupon_id)==="string"){
                    this.edit_form.coupon_id=this.old_edit_form_coupon;
                }
                let json={
                    amount:this.edit_form.type===4?this.edit_form.amount:'',
                    chance:this.edit_form.chance,
                    coupon_id:this.edit_form.type===1?this.edit_form.coupon_id:'',
                    created_at:this.edit_form.created_at,
                    give_out_num:this.edit_form.give_out_num,
                    id:this.edit_form.id,
                    love:this.edit_form.type===3?this.edit_form.love:'',
                    name:this.edit_form.name,
                    point:this.edit_form.type===2?this.edit_form.point:'',
                    prize_num:this.edit_form.prize_num,
                    thumb:this.edit_form.thumb,
                    thumb_url:this.edit_form.thumb_url,
                    type:this.edit_form.type,
                    uniacid:this.edit_form.uniacid,
                    updated_at:this.edit_form.updated_at
                }
                this.$http.post("{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.editPrize') !!}",{'form_data':json}).then(response => {
                                if (response.data.result) {
                                    this.award_list.map((item,index,key)=>{
                                        if(item.id===this.edit_form.id){
                                            this.award_list.splice(index,1,this.edit_form)
                                            this.award_list.map((item,index,key)=>{
                                        if(item.type==1){
                                            item.award='优惠券'
                                        }
                                        if(item.type==2){
                                            item.award='积分'
                                        }
                                        if(item.type==3){
                                            item.award=love_name
                                        }
                                        if(item.type==4){
                                            item.award='余额'
                                        }

                                    })
                                        }
                                    })
                                    this.$message({message: "编辑奖品成功",type: 'success'});
                                }
                                else {
                                    this.$message({message: response.data.msg,type: 'error'});
                                }
                    });
                this.is_edit_dialog=false;
            },
            diyUpload(file) {
                const isImg = file.type === 'image/jpeg' || file.type==="image/png";
                const isLt2M = file.size / 1024 / 1024 < 2;
                if (!isLt2M) {
                    this.$message.error('上传图片大小不能超过 2MB!');
                    return false
                }
                if (!isImg) {
                     this.$message.error('上传图片的格式只能是 JPG或PNG 格式!');
                     return false;
                 }
            },
            diySuccess (res, file){
                if (res.result == 1) {
                    this.diy_form.thumb_url = res.data.thumb_url;
                    this.$message.success("上传成功！");
                } else {
                    this.$message.error(res.msg);
                }
            },
            prizeUpload(file) {
                const isImg = file.type === 'image/jpeg' || file.type==="image/png";
                const isLt2M = file.size / 1024 / 1024 < 2;
                if (!isLt2M) {
                    this.$message.error('上传图片大小不能超过 2MB!');
                    return false
                }
                if (!isImg) {
                     this.$message.error('上传图片的格式只能是 JPG或PNG 格式!');
                     return false;
                 }
            },
            prizeSuccess (res, file){
                if (res.result == 1) {
                    
                    this.prizeSet.prize_thumb = res.data.thumb_url;
                    this.$message.success("上传成功！");
                } else {
                    this.$message.error(res.msg);
                }
            },
            editUpload(file) {
                const isImg = file.type === 'image/jpeg' || file.type==="image/png";
                const isLt2M = file.size / 1024 / 1024 < 2;
                if (!isLt2M) {
                    this.$message.error('上传图片大小不能超过 2MB!');
                    return false
                }
                if (!isImg) {
                     this.$message.error('上传图片的格式只能是 JPG或PNG 格式!');
                     return false;
                 }
            },
            editSuccess (res, file){
                if (res.result == 1) {
                    
                    this.edit_form.thumb_url = res.data.thumb_url;
                    this.$message.success("上传成功！");
                } else {
                    this.$message.error(res.msg);
                }
            },
            beforeUpload(file) {
                const isImg = file.type === 'image/jpeg' || file.type==="image/png";
                const isLt2M = file.size / 1024 / 1024 < 2;
                if (!isLt2M) {
                    this.$message.error('上传图片大小不能超过 2MB!');
                    return false
                }
                if (!isImg) {
                            this.$message.error('上传图片的格式只能是 JPG或PNG 格式!');
                            return false;
                 }
            },
            uploadSuccess (res, file){
                if (res.result == 1) {
                    this.award_form.thumb_url = res.data.thumb_url;
                    this.$message.success("上传成功！");
                    
                } else {
                    this.$message.error(res.msg);
                }
                
            },
            
            clearImg() {
                    this.award_form.thumb_url = "";
            },
            clearEdit() {
                    this.edit_form.thumb_url = "";
            },
            clearPrize(){
                this.prizeSet.prize_thumb=""
            },
            clearDiy() {
                    this.diy_form.thumb_url = "";
            },
            submit() {
                if(this.active===2){
                    if(this.diy_form.thumb_url===''){
                        this.$message.error("请先添加页面装修图片");
                        return 
                    }
                    if(this.diy_form.color===''){
                        this.$message.error("请先选择背景颜色");
                        return 
                    }
                    if(this.diy_form.notice===''){
                        this.$message.error("请先添加活动说明");
                        return 
                    }
                  
                   let json={
                    name:this.form.basic_name,
                    countdown_time:[Number(String(this.form.basic_start_time).substring(0,10)),Number(String(this.form.basic_end_time).substring(0,10))],
                    role_type:this.form.basic_user_role,
                    member_type:this.form.basic_user_type,
                    level_id:this.level,
                    draw_type:this.form.basic_user_ticket,
                    use_point:this.form.basic_user_ticket===1?this.form.basic_coin:'',
                    use_love:this.form.basic_user_ticket===2?this.form.basic_love:'',
                    partake_times:this.form.basic_number,
                    days_times:this.form.basic_number===0?this.form.basic_dayTime:'',
                    days_share_times:this.form.basic_number===0?this.form.basic_dayExtra:'',
                    somebody_times:this.form.basic_number===1?this.form.basic_peoTime:'',
                    somebody_share_times:this.form.basic_number===1?this.form.basic_peoExtra:'',
                    prize_id:this.arr,
                    empty_prize_name:this.prizeSet.name,
                    empty_prize_thumb:this.prizeSet.prize_thumb,
                    jump_type:this.prizeSet.is_jump,
                    jump_link:this.prizeSet.is_jump===1?this.prizeSet.url:'',
                    partake_point:this.prizeSet.coin,
                    partake_love:this.prizeSet.love,
                    partake_amount:this.prizeSet.rest,
                    partake_coupon_id:this.couponName.length>0?this.prizeSet.coupon_id.id:'',
                    limit:this.prizeSet.is_not_win===true?1:0,
                    background:this.diy_form.thumb_url,
                    background_colour:this.diy_form.color,
                    is_logo:this.diy_form.is_logo,
                    is_roster:this.diy_form.is_award_list,
                    content:this.diy_form.notice,   
                    empty_prize_prompt:this.prizeSet.tip
                    }
                    this.$http.post('{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.add') !!}',{'form_data':json}).then(function (response){
                        this.$message({message: "创建活动成功",type: 'success'});
                            this.$http.post('{!! yzWebFullUrl('plugin.lucky-draw.admin.controllers.activity.getActivityById') !!}',{'id':response.data.data.id}).then(function (response){
                                this.qrCode=response.data.data.activityModel.qrCode;
                                this.activity_url=response.data.data.activityModel.activity_url;
                     },function (response) {
                        this.$message({message: response.data.msg,type: 'error'});
                    })
                        this.active += 2;
                },function (response) {
                    console.log(response);
                    this.$message({message: response.data.msg,type: 'error'});
                })
        
                }
            },
            couponShow(){
                this.coupon_show=true;
            },
            loadAward(query) {
                if (query !== '') {
                    this.loading = true;
                    this.AwardList = [];
                    this.$http.post("{!! yzWebUrl('plugin.lucky-draw.admin.controllers.activity.getCoupons') !!}",{kwd:query}).then(response => {
                        this.AwardList = response.data.data.coupons;
                        this.loading = false;
                    }, response => {
                        this.$message({type: 'error',message: response.data.msg});
                        console.log(response);
                    });
                } else {
                    this.AwardList = [];
                }
            },
            loadEdit(query) {
                if (query !== '') {
                    this.loading = true;
                    this.EditList=[];
                    this.$http.post("{!! yzWebUrl('plugin.lucky-draw.admin.controllers.activity.getCoupons') !!}",{kwd:query}).then(response => {
                        this.EditList = response.data.data.coupons;
                        this.loading = false;
                    }, response => {
                        this.$message({type: 'error',message: response.data.msg});
                        console.log(response);
                    });
                } else {
                    this.EditList = [];
                }
            },
            loadCoupon(query) {
                if (query !== '') {
                    this.loading = true;
                    this.CouponList =[];
                    this.$http.post("{!! yzWebUrl('plugin.lucky-draw.admin.controllers.activity.getCoupons') !!}",{kwd:query}).then(response => {
                        this.CouponList = response.data.data.coupons;
                        this.loading = false;
                    }, response => {
                        this.$message({type: 'error',message: response.data.msg});
                        console.log(response);
                    });
                } else {
                    this.EditList = [];
                }
            },
            copy() {
                that = this;
                let Url = that.$refs['activity'];
                Url.select(); // 选择对象
                document.execCommand("Copy",false);
                that.$message({message:"复制成功！",type:"success"});
            },

        },
    });
</script>
<style>
.left-con{  
    float:left;
    margin-right:2%;
    width:375px;
    height:667px;
    background: url({{ plugin_assets('lucky-draw', 'views/admin/v2.png') }}) 0px -18px no-repeat;
}
</style>
@endsection

