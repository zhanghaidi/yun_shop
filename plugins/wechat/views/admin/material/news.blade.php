@extends('layouts.base')
@section('title', "公众号设置")
@section('content')
<style>
    .rightlist #app .rightlist-head{line-height:50px;padding:15px 0;}
    .rightlist #app{margin-left:30px;}
    .el-form-item__label{padding-right:30px;}
    .tip{font-size:12px;color:#999;}
    .rightlist-head-con{float:left;padding-right:20px;font-size:16px;color:#888;}
    .el-tag{font-weight:700;font-size:15px;margin-bottom:30px;}
    .el-icon-edit{font-size:16px;padding:0 15px;color:#409EFF;cursor: pointer;}
    /* 滑块选择小白点 */
    .el-switch.is-checked .el-switch__core::after {left: 100%;margin-left: -17px;}
    .el-switch__core::after {content: "";position: absolute;top: 1px;left: 1px;border-radius: 100%;transition: all .3s;width: 16px;height: 16px;background-color: #fff;}
    /* 选择图文 */
    .image_text_head{border:1px solid #dadada;}
    .image_text_head:hover{border:1px #428bca solid;cursor: pointer;color:#428bca; background:#f4f6f9;}
	  input[type=file] {display: none;}
    /* 添加图文样式  */
    .news-left{float:left;width:220px;border:1px #c7c7c7 solid;background:#f8f8f8}
    .news-left-title{padding:20px 0 20px 20px;font-size:18px;font-weight:900;}
    
    .news-left-list{background:#fff;border:1px solid #c7c7c7;width:95%;margin:5px auto;height:120px;position:relative}
    .news-left-list:hover{border:1px solid #079200;cursor: pointer;}
    .news-left-list-bottom{position:absolute;bottom:0;width:100%;line-height:30px;background:#000;opacity:0.7;display:none;color:#fff;}
    .news-left-list-img{float:right;width:78px;height:78px;background:#ececec;margin-right:5px;}
    .news-left-list:hover>.news-left-list-bottom{display:inline;}
    .news-left-list-title{line-height:20px;height:20px;overflow:hidden;margin:5px;}
    .news-left-list-bottom-icon{width:30px;display:inline-block;text-align:right}

    .news-left-plus{text-align:center;line-height:100px;border:1px dotted #c7c7c7;width:95%;margin:10px auto;}
    .news-left-plus:hover{border:1px solid #079200;cursor: pointer;}

      /* 选中该图文 */
    .current{border:2px solid #079200;}
    [v-cloak]{
        display:none;
    }
</style>

<style >
	.tinymce-container {
		position: relative;
		line-height: normal;
	}
	.tinymce-container>>>.mce-fullscreen {
		z-index: 10000;
	}
	.tinymce-textarea {
		visibility: hidden;
		z-index: -1;
	}
	.editor-custom-btn-container {
		position: absolute;
		right: 4px;
		top: 4px;
		/*z-index: 2005;*/
	}
	.fullscreen .editor-custom-btn-container {
		z-index: 10000;
		position: fixed;
	}
	.editor-upload-btn {
		display: inline-block;
	}
	.editor-slide-upload {
		margin-bottom: 20px;
		/deep/ .el-upload--picture-card {
		width: 100%;
		}
	}
</style>
<div class="rightlist">
    <div id="app" v-loading="submit_loading" v-cloak>
		<link rel="stylesheet" href="//at.alicdn.com/t/font_913727_gt395lrelsk.css">
		<div class="rightlist-head">
            <div class="rightlist-head-con">新建/编辑图文</div>
        </div>
        <div style="width:1120px;margin:50px auto">
          
          <!-- left -->
          <div class="news-left">
            <div class="news-left-title">图文列表</div>
            <div class="news-left-list" v-for="(item,index) in menu" :class="{current:selectIndex===index}" @click="chooseMenu(index)">
              <div class="news-left-list-title">[[item.title]]</div>
              <div class="news-left-list-img">
                <img v-show="item.thumb_url" :src="item.thumb_url" style="width:78px;height:78px;">
              </div>
              <div class="news-left-list-bottom">
                <div class="news-left-list-bottom-icon">
                  <i class="iconfont icon-arrowsupline" style="display:block;font-size:20px;width:100%" title="向上移动" @click.stop="topMenu(index)"></i>
                </div>
                <div class="news-left-list-bottom-icon">
                  <i class="iconfont icon-arrowsdownline" style="display:block;font-size:20px;width:100%" @click.stop="bottomMenu(index)"></i>
                </div>
                <div style="width:70px;display:inline-block;text-align:right">
                  <!-- 导入文章 -->
                </div>
                <div class="news-left-list-bottom-icon" @click.stop="delMenu(index)">
                  <i class="iconfont icon-shanchu" style="display:block;font-size:20px;width:100%"></i>
                </div>
                
              </div>
            </div>
            <div class="news-left-plus" @click="addMenu" v-if="menu.length<8">
              <i class="iconfont icon-plus" style="display:block;font-size:50px;width:100%"></i>
            </div>
          </div >
          <!-- left -->
          <!-- center -->
          <div style="float:left;border:1px #c7c7c7 solid;">
		  	<el-form label-width="20%"  ref="search_form">
				<el-form-item label="标题">
					<el-input v-model="menu[selectIndex].title" placeholder="请输入标题" style="width:100%;"></el-input>
				</el-form-item>
				<el-form-item label="作者">
					<el-input v-model="menu[selectIndex].author" placeholder="请输入作者" style="width:100%;"></el-input>
				</el-form-item>
				<el-form-item label="链接地址">
					<el-input v-model="menu[selectIndex].content_source_url" placeholder="请输入链接地址" style="width:100%;"></el-input>
				</el-form-item>
			</el-form>
			<div style="text-align:right;">
				<el-button type="primary" size="mini" @click="openDia(0)">插入图片</el-button>
				<!-- <el-button type="primary" size="mini" @click="ceshi()">插入视频</el-button> -->
			</div>
			<tinymce v-model="menu[selectIndex].content"></tinymce>
          </div>
          <!-- center -->
          <!-- right -->
          <div style="float:left;width:300px;padding:0 10px;border:1px #c7c7c7 solid">
            <div style="padding:20px 0 20px 20px;font-size:18px;font-weight:900;">发布样式编辑</div>
            <div>
              <span style="font-weight:900">封面：<span class="tip">(小图片建议尺寸：200像素 * 200像素)</span></span>
              <div class="tip" style="color:red;font-weight:600">封面必选</div>
              <el-button size="mini" @click="openDia(1)">选择图片</el-button>
              <div style="padding:20px;">
                <img :src="menu[selectIndex].thumb_url" v-show="menu[selectIndex].thumb_url" style="width:107px;107px;">
              </div>
              <el-checkbox v-model.number="menu[selectIndex].show_cover_pic" :true-label="1" :false-label="0" style="margin-left:30px;">
                在正文顶部显示封面图
              </el-checkbox>
              <!-- <el-checkbox v-model.number="menu[selectIndex].need_open_comment" :true-label="1" :false-label="0">
                是否打开评论
              </el-checkbox>
              <el-checkbox v-model.number="menu[selectIndex].only_fans_can_comment" :true-label="1" :false-label="0">
                是否粉丝才可评论
              </el-checkbox> -->
            </div>
            <div style="margin-top:30px;">
              <span style="font-weight:900">摘要：<span class="tip">(选填，如果不填写会默认抓取正文前54个字)</span></span>
              <div style="padding:20px;">
                <el-input type="textarea" v-model="menu[selectIndex].digest" rows="5"></el-input>
              </div>
            </div>
          </div>
          <!-- right -->
		  <!-- submit -->
			<div style="display:block;float:left;width:100%;text-align:center">
				<el-button type="primary" @click.prevent="submit('local')">保存到本地</el-button>
				<el-button type="primary" @click.prevent="submit('perm')">保存并上传到微信</el-button>
			</div>
			<!-- submit -->
        </div>
		
        <!-- 图片选择弹出框 -->
        <el-dialog title="图片" :visible.sync="img_url" width="60%" v-loading="dialog_loading">
            <div>
                <el-tabs v-model="img_url0" @tab-click="handleClickImg">
                    <el-tab-pane label="微信" name="img_url1">
                        <el-row>
                            <el-col :span="24" align="right" style="margin:20px 0;">
                                <el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.upload',['type' => 'wechat']) !!}" accept="image/*" :show-file-list="false" :on-success="uploadSuccess" :before-upload="beforeUpload">
                                    <el-button type="primary">上传图片</el-button>
                                </el-upload>
                            </el-col>
                        </el-row>
                        <el-row style="overflow-y: scroll;max-height:400px;">
                            <el-col :span="5" v-for="(item,index) in img_list" :key="index"  style="margin:10px 10px;width:230px;" @click.native="chooseImgUrl(index)">
                                <div class="image_text_head">
                                    <div style="padding:10px 30px">
                                        <div class="image_text_con" style="min-width:180px;height:150px;overflow:hidden;position:relative;">
                                            <img :src="item.attachment"  style="min-width:180px;height:150px;overflow:hidden;" alt="">
                                            <div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;height:32px;overflow:hidden;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
                                                [[item.filename]]
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </el-col>
                        </el-row>
                         <!-- 分页 -->
                        <el-row>
                            <el-col :span="24" align="right" style="padding:15px 5% 15px 0">
                                <el-pagination layout="prev, pager, next" @current-change="currentChangeWechatImg" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
                            </el-col>
                        </el-row>
                    </el-tab-pane>
                    <el-tab-pane label="本地服务器" name="img_url2">
						<el-row>
							<el-col :span="24" align="right" style="margin:20px 0;">
								<el-upload  action="{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.upload',['type' => 'local']) !!}" accept="image/*" :show-file-list="false" :on-success="uploadSuccess" :before-upload="beforeUpload">
									<el-button type="primary">上传图片</el-button>
								</el-upload>
							</el-col>
						</el-row>
						<el-row style="overflow-y: scroll;max-height:400px;">
							<el-col :span="5" v-for="(item,index) in img_list" :key="index"  style="margin:10px 10px;width:230px;" @click.native="chooseImgUrl(index)">
								<div class="image_text_head">
									<div style="padding:10px 30px">
										<div class="image_text_con" style="min-width:180px;height:150px;overflow:hidden;position:relative;">
											<img :src="item.attachment"  style="min-width:180px;height:150px;overflow:hidden;" alt="">
											<div class="image_text_con_title" style="position:absolute;bottom:0;width:100%;overflow:hidden;height:32px;line-height:32px;background:#000;opacity:0.5;color:#fff;padding:0 15px;">
												[[item.filename]]
											</div>
										</div>
									</div>
								</div>
							</el-col>
						</el-row>
						<!-- 分页 -->
						<el-row>
							<el-col :span="24" align="right" style="padding:15px 5% 15px 0">
								<el-pagination layout="prev, pager, next" @current-change="currentChangeLocalImg" :total="total" :page-size="per_size" background v-loading="loading"></el-pagination>
							</el-col>
						</el-row>
                    </el-tab-pane>
                    <el-tab-pane label="提取网络地址" name="img_url3">
                        <el-col :span="24" align="right" style="margin:20px 0;">
                           
                        </el-col>
                    </el-row>
                        <div style="color:98999a;text-align:center;font-size:26px">
                            <div style="margin:20px 0">输入图片链接</div>
                            <div>
                                <el-input style="width:60%" v-model="network_img_url" placeholder="图片链接"></el-input>
                            </div>
                            <div>
                                <el-button style="padding:10px 60px;margin:20px 0" @click="transform">转换</el-button>
                            </div>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="img_url = false">取 消</el-button>
                <!-- <el-button type="primary" @click="img_text_url = false">确 定</el-button> -->
            </span>
        </el-dialog>

    </div>
</div>
    
<script src="{{resource_get('static/yunshop/tinymce4.7.5/tinymce.min.js')}}"></script> 
<script src="{{resource_get('static/yunshop/tinymceTemplate.js')}}"></script>    

<script>
    var app = new Vue({
        el:"#app",     
        delimiters: ['[[', ']]'],
        data() {
            let data = {!! $data?:'{}' !!}
			console.log(data)
			let form ={
				has_many_wechat_news:[
					{title:"",show_cover_pic:0,content:"",digest:"",thumb_media_id:"1",need_open_comment:0,only_fans_can_comment:0},
				],
				...data.data
			}
			console.log(form);
            return{
				        form:form,
              	dialog_loading:false,
                img_url:false,
                img_url0:'img_url1',
                submit_loading:"",
				        network_img_url:"",
                content:'',
                selectIndex:0,//选中哪篇图文进行编辑
				        verifica:0,//表单验证状态
				        img_index:0,//判断点击哪个选择图片按钮
                menu:form.has_many_wechat_news,
                img_list:[],
                // 分页
                loading:false,
                table_loading:false,
                total:0,
                per_size:0,
                current_page:0,
                rules:{
                    
                },
                
            }
        },
        created() {
          window.addEventListener('beforeunload', e => {
            window.onbeforeunload =null
          }); 
        },
        methods: {
          addMenu() {
			      //   console.log(this.img_index)
            var that = this;
            if(that.menu.length>=8){
              that.$message.error("最多添加八篇图文信息！")
              return 0;
            }
            else{
              that.menu.push(
                {title:"",show_cover_pic:0,content:"",digest:"",thumb_media_id:"1",need_open_comment:0,only_fans_can_comment:0}
              );
            }
          },
          topMenu(index){
            if(index===0){
              this.$message.error("已经在最前了，不能再往上移动！")
              return 0;
            }
            var top_item = this.menu[index-1];
            var bottom_item = this.menu[index];
            this.menu[index-1] = bottom_item;
            this.menu[index] = top_item;
            this.chooseMenu(index-1);//触发数组重新渲染
          },
          bottomMenu(index){
            if(index===this.menu.length-1){
              this.$message.error("已经在最后了，不能再往下移动！")
              return 0;
            }
            var top_item = this.menu[index];
            var bottom_item = this.menu[index+1];
            this.menu[index] = bottom_item;
            this.menu[index+1] = top_item;
            this.chooseMenu(index+1);//触发数组重新渲染
          },
          delMenu(index){
            this.selectIndex = 0;
            if(this.menu.length<=1){
              this.$message.error("不能少于一篇图文信息！")
              return 0;
            }
            this.$confirm('确定删除吗', '提示', {confirmButtonText: '确定',cancelButtonText: '取消',type: 'warning'}).then(() => {
              this.menu.splice(index,1);
              }).catch(() => {
                  
              });
          },
          chooseMenu(index){
            //触发数组重新渲染
            if(index === this.selectIndex ){
              this.selectIndex = "";
            }
            this.selectIndex = index;
          },
          //打开图片弹出框
          openDia(x){
            this.img_url = true;
            this.img_index=x;
            this.handleClickImg();
          },
          // 图片弹出框里的tabs
          handleClickImg() {
            var that = this;
            console.log(that.per_page);
            if(that.img_url0 == "img_url1"){
                that.dialog_loading=true,
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.get-wechat-image-v2') !!}",{}).then(response => {
                console.log(response);
                if(response.data.result==1){
                    console.log("hahahahah")
                    that.img_list = response.data.data.data;
                    that.per_size = response.data.data.per_page;
                    that.total = response.data.data.total;
                    that.current_page = response.data.data.current_page;
                    that.dialog_loading = false;
                }
                that.dialog_loading = false;
                }),function(res){
                    console.log(res);
                    that.dialog_loading = false;
                };
            }
            else if(that.img_url0 == "img_url2"){
                that.dialog_loading=true,
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.get-local-image') !!}",{}).then(response => {
                console.log(response);
                if(response.data.result==1){
                    that.img_list = response.data.data.data;
                    that.per_size = response.data.data.per_page;
                    that.total = response.data.data.total;
                    that.current_page = response.data.data.current_page;
                    that.dialog_loading = false;
                }
                that.dialog_loading = false;
                }),function(res){
                    console.log(res);
                    that.dialog_loading = false;
                };
            }
          },
          // 本地图片分页
          currentChangeLocalImg(val){
              this.loading = true;
              this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.get-local-image') !!}',{page:val}).then(function (response){
                  console.log(response);
                  this.img_list = response.data.data.data;
                  this.per_size = response.data.data.per_page;
                  this.total = response.data.data.total;
                  this.current_page = response.data.data.current_page;
                  this.loading = false;
              },function (response) {
                  console.log(response);
                  this.loading = false;
              }
              );
          },
          // 微信图片分页
          currentChangeWechatImg(val){
              this.loading = true;
              this.$http.post('{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.get-wechat-image-v2') !!}',{page:val}).then(function (response){
                  console.log(response);
                  this.img_list = response.data.data.data;
                  this.per_size = response.data.data.per_page;
                  this.total = response.data.data.total;
                  this.current_page = response.data.data.current_page;
                  this.loading = false;
              },function (response) {
                  console.log(response);
                  this.loading = false;
              }
              );
          },
		
          // 选择图片
          chooseImgUrl(index){
            var that = this;
            //选择富文本图片的时候
            if(that.img_index === 0){
                  //选择微信图片
              if(that.img_url0 == "img_url1"){
                that.submit_loading=true,
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.upload-image') !!}",{type:"wechat",media_id:that.img_list[index].media_id}).then(response => {
                console.log(response);
                if(response.data.result==1){
                      let img_url_2 = response.data.data.url
                  that.menu[that.selectIndex].content = that.menu[that.selectIndex].content+"<img src="+img_url_2+"></img>";
                }
                else{
                  that.$message.error(response.data.msg);
                  that.submit_loading = false;
                  return false;
                }
                that.submit_loading = false;
                }),function(res){
                  console.log(res);
                  that.submit_loading = false;
                  return false;
                };
                      that.img_url = false;
              }
              //选择本地图片
              if(that.img_url0 == "img_url2"){
                that.submit_loading=true,
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.upload-image') !!}",{type:"local",id:that.img_list[index].id}).then(response => {
                console.log(response);
                if(response.data.result==1){
                        let img_url_2 = response.data.data.url
                  that.menu[that.selectIndex].content = that.menu[that.selectIndex].content+"<img src="+img_url_2+"></img>";
                }
                else{
                  that.$message.error(response.data.msg);
                  that.submit_loading = false;
                  return false;
                }
                that.submit_loading = false;
                }),function(res){
                  console.log(res);
                  that.submit_loading = false;
                  return false;
                };
                    that.img_url = false;
              }
            }
            //选择封面图片的时候
            else if(that.img_index === 1){
              // 如果是本地图片，先转换成微信图片
              if(!that.img_list[index].media_id){
                that.submit_loading=true,
                that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.local-to-wechat') !!}",{id:that.img_list[index].id}).then(response => {
                console.log(response);
                if(response.data.result==1){
                  that.menu[this.selectIndex].thumb_url = response.data.data.attachment;
                  that.menu[this.selectIndex].thumb_media_id = response.data.data.media_id;
                }
                else{
                  that.$message.error(response.data.msg);
                  that.submit_loading = false;
                  return false;
                }
                that.submit_loading = false;
              }),function(res){
                console.log(res);
                that.submit_loading = false;
                return false;
              };
              that.img_url = false;
              return false;
              }
              that.menu[this.selectIndex].thumb_url = that.img_list[index].attachment;
              that.menu[this.selectIndex].thumb_media_id = that.img_list[index].media_id;
              that.img_url = false;

            }
          },
          // 转化图片网络地址
          transform() {
            var that = this;
            that.dialog_loading = true;
                that.submit_loading = true;
            if(that.img_index === 1){
              that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.image.fetch') !!}",{url:that.network_img_url}).then(response => {
                console.log(response);
                if(response.data.result==1){
                  that.$message.success("转换成功！");
                  that.network_img_url="";
                  that.img_url = false;
                }
                else{
                  that.$message.error(response.data.msg);
                }
                that.dialog_loading = false;
                      that.submit_loading = false;
              }),function(res){
                console.log(res);
                that.dialog_loading = false;
                      that.submit_loading = false;
                return false;
              };
            }
            else if(that.img_index === 0){
              that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.upload-image') !!}",{type:'fetch',url:that.network_img_url}).then(response => {
                console.log(response);
                if(response.data.result==1){
                  console.log("hahahahah")
                  that.$message.success("转换成功！");
                  that.network_img_url="";
                  that.img_url = false;
                        let img_url_2 = response.data.data.url
                  that.menu[that.selectIndex].content = that.menu[that.selectIndex].content+"<img src="+img_url_2+"></img>";
                }
                else{
                  that.$message.error(response.data.msg);
                }
                that.dialog_loading = false;
                      that.submit_loading = false;
              }),function(res){
                console.log(res);
                that.dialog_loading = false;
                      that.submit_loading = false;
                return false;
              };
            }

          },
          // 上传图片之前
          beforeUpload(){
                  this.dialog_loading=true;
                  },
          // 上传图片成功之后
          uploadSuccess(response,file,fileList){
            if(response.result==1){
              this.$message.success("上传成功！")
              this.handleClickImg();
            }
            else{
              this.$message.error(response.msg);
            }
            this.dialog_loading=false;
            this.$message.success("上传成功！")
          },
          submit(type){
            var that = this;
            console.log(that.menu)
            that.verification1();
            if(that.verifica==0){
              return 0;
            }
            console.log('表单验证通过');
            that.submit_loading = true;
            that.form.has_many_wechat_news = that.menu;
            that.$http.post("{!! yzWebFullUrl('plugin.wechat.admin.material.controller.news.save') !!}",{model:type,form_data:that.form}).then(response => {
              console.log(response);
              if(response.data.result==1){
                that.$message.success(response.data.msg);
                  window.location.href='{!! yzWebFullUrl('plugin.wechat.admin.material.controller.material.index') !!}';
              }
              else{
                that.$message.error(response.data.msg);
                that.submit_loading = false;
                return false;
              }
            }),function(res){
              console.log(res);
              that.submit_loading = false;
              return false;
            };
          },
          //表单验证
          verification1(){
            this.verifica = 1;
            this.menu.forEach((value,index,item) => {
              if(value.title==''){
                this.$message.error("第"+(index+1)+'篇图文标题不能为空！');
                this.verifica = 0;
                return 0;
              }
              if(value.content==''){
                this.$message.error("第"+(index+1)+'篇图文内容不能为空！');
                this.verifica = 0;
                return 0;
              }
              if(value.thumb_url=='' || !value.thumb_url){
                this.$message.error("第"+(index+1)+'篇封面不能为空！');
                this.verifica = 0;
                return 0;
              }
            });
            
          },
          ceshi(){
            this.menu[this.selectIndex].content = this.menu[this.selectIndex].content+"<img src='https://dev5.yunzshop.com/attachment/images/2/2019/01/U9ORRzzrOBRtIQV15rHkZHvmRWOaK3.jpg'></img>";
            this.menu[this.selectIndex].content = this.menu[this.selectIndex].content+"<video controls='controls' width='300' height='150'>↵<source src='https://dev8.yunzshop.com/static/upload/videos/12/2019/04/iY44JyBSc4i540JyF4BOB7CK0YX4sJ.mp4' type='video/mp4' /></video>"
            console.log("ceshi")
          },
            
        },
    })
</script>
<script type="text/javascript">
   window.onbeforeunload = function() { 
	   console.log("hahahah")
       return "确认离开当前页面吗？未保存的数据将会丢失";
   }
</script>
<style>
    

</style>
@endsection
