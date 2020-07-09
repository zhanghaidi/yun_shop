<!--阿里图标库-->
<link rel="stylesheet" href="//at.alicdn.com/t/font_432132_5gl1w51e2ib.css"/>
<style>
    body, ul, li, h2 {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    body {
        background-color: #f5f5f5;
    }

    .user_order_color {
        background-color: {{Item.params.memberbgcolor}};
    }

    .user_order_img {
        background-image: url({{Item.params.bgimg}});
    }

    .user_box {
        position: relative;
    }

    .user_img {
        width: 64px;
        height: 64px;
        border-radius: 64px;
        overflow: hidden;
        background: #f2f2f2;
        border: solid 2px #ebebeb;
    }

    .user_img img {
        width: 100%;
    }

    .user_name {
        margin-left: 10px;
        margin-top: 10px;
    }

    .user_name .name {
        font-size: 16px;
        display: flex;
        display: -webkit-flex;
    }

    .name .name_a {
        max-width: 68px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: {{Item.params.membernamecolor || '#333'}};
        font-weight: bold;
        font-size: 16px;
        margin-top: 0;
    }

    .name .member_id {
        padding: 2px 10px;
        font-size: 12px;
        background-color: #f5f5f5;
        border-radius: 16px;
        margin-left: 6px;
        margin-top: 0;
    }

    .user_name .code {
        font-size: 14px;
        margin-top: 10px;
        color: #8c8c8c;
        display: flex;
        display: -webkit-flex;
    }

    .code span {
        padding: 2px 10px;
        font-size: 12px;
        font-weight: normal;
        background-color: #f5f5f5;
        border-radius: 16px;
        margin-left: 6px;
        margin-top: 0;
    }

    .user_sum {
        border-top: solid 1px #ebebeb;
        background-color: #fff;
        display: flex;
        display: -webkit-flex;
        align-items: center;
        justify-content: space-around;
        -webkit-justify-content: space-around;
        text-align: center;
        padding: 14px 0;
        margin-top: 10px;
    }

    .user_sum .sum {
        font-size: 16px;
        display: inline-block;
        margin-bottom: 6px;
    }

    .sum font {
        color: #333;
        font-size: 12px;
    }

    .user_sum .text {
        color: #8c8c8c;
        font-size: 12px;
    }

    .member_grade1 {
        display: flex;
        display: -webkit-flex;
        align-items: center;
        -webkit-align-items: center;
        padding: 4px 10px;
        background: -webkit-linear-gradient(to right, #3b3b4f, #9898a4); /* Safari 5.1 - 6.0 */
        background: -o-linear-gradient(to right, #3b3b4f, #9898a4); /* Opera 11.1 - 12.0 */
        background: -moz-linear-gradient(to right, #3b3b4f, #9898a4); /* Firefox 3.6 - 15 */
        background: linear-gradient(to right, #3b3b4f, #9898a4); /* 标准的语法 */
        border-radius: 32px;
        color: #e6c785;
        font-size: 12px;
        position: absolute;
        right: 10px;
        top: 10px;
    }

    .member_grade1 .iconfont {
        font-size: 20px;
        margin-right: 4px;
        color: #e6c785;
    }

    .member_grade2 {
        width: 325px;
        margin: 0 auto;
        height: 40px;
        line-height: 40px;
        border-radius: 8px 8px 0 0;
        display: flex;
        display: -webkit-flex;
        justify-content: space-between;
        -webkit-justify-content: space-between;
        align-items: center;
        -webkit-align-items: center;
        padding: 0 10px;
        background: -webkit-linear-gradient(to right, #3b3b4f, #9898a4); /* Safari 5.1 - 6.0 */
        background: -o-linear-gradient(to right, #3b3b4f, #9898a4); /* Opera 11.1 - 12.0 */
        background: -moz-linear-gradient(to right, #3b3b4f, #9898a4); /* Firefox 3.6 - 15 */
        background: linear-gradient(to right, #3b3b4f, #9898a4); /* 标准的语法 */
        overflow: hidden;
        position: relative;
    }

    .member_grade2 .member_name {
        color: #e6c785;
        font-size: 16px;
        font-weight: bold;
        z-index: 1
    }

    .member_grade2 .member_btn {
        background: -webkit-linear-gradient(to right, #f2e1aa, #e6c785); /* Safari 5.1 - 6.0 */
        background: -o-linear-gradient(to right, #f2e1aa, #e6c785); /* Opera 11.1 - 12.0 */
        background: -moz-linear-gradient(to right, #f2e1aa, #e6c785); /* Firefox 3.6 - 15 */
        background: linear-gradient(to right, #f2e1aa, #e6c785); /* 标准的语法 */
        color: #3c3c50;
        height: 30px;
        line-height: 30px;
        padding: 0 16px;
        border-radius: 16px;
        font-size: 12px;
    }

    .member_grade2 .member_btn .icon-member_look {
        color: #3c3c50;
        font-size: 10px;
    }

    .member_grade2 .icon-member-enter {
        position: absolute;
        top: -10px;
        left: -20px;
        font-size: 156px;
        color: #333;
        opacity: 0.4;
        z-index: 0;
        transform: rotate(30deg);
        -webkit-transform: rotate(30deg);
        -moz-transform: rotate(30deg);
    }

    .grade2_bg {
        background-color: #fff;
    }

    .member_grade3 {
        width: 100px;
        height: 74px;
        border-radius: 6px;
        padding: 2px 8px;
        background-image: url({{inits+'plugins/designer/assets/imgsrc/member/levelBg.png'}});
        background-repeat: no-repeat;
        background-position: -6px 0;
    }

    .member_grade3 .icon-member-enter {
        font-size: 20px;
        color: #e6c785;
    }

    .member_grade3 .grade {
        margin-top: 10px;
        margin-left: 16px;
        display: flex;
        display: -webkit-flex;
        align-items: center;
        -webkit-align-items: center;
        color: #e6c785;
        font-size: 12px;
        transform: rotate(10deg);
        -webkit-transform: rotate(10deg);
        -moz-transform: rotate(10deg);
    }

    .member_grade3 .member_btn {
        margin-left: 12px;
        border-radius: 16px;
        font-size: 10px;
        border: solid 1px #e6c785;
        color: #e6c785;
        text-align: center;
        width: 68px;
        background: rgba(230, 199, 133, 0.1);
        display: flex;
        display: -webkit-flex;
        justify-content: center;
        -webkit-justify-content: center;
        align-items: center;
        -webkit-align-items: center;
        transform: rotate(10deg);
        -webkit-transform: rotate(10deg);
        -moz-transform: rotate(10deg);
    }

    .member_grade3 .member_btn .icon-member_look {
        font-size: 10px;
        margin-left: 4px;
        color: #e6c785;
    }

    /*样式一居左*/
    .user_img_style1 {
        margin-left: 14px;
        margin-top: 20px;
        float: left;
    }

    .user_name_style1 {
        float: left;
        margin-left: 10px;
        margin-top: 28px;
    }

    .user_sum_style1 {
        width: 100%;
    }

    .member_grade11 {
        top: 40px;
        right: 0;
    }

    /*样式一居右*/
    .user_img_style2 {
        position: absolute;
        top: 20px;
        right: 108px;
        float: left;
    }

    .user_name_style2 {
        float: left;
        margin-left: 14px;
        margin-top: 28px;
    }

    .user_sum_style2 {
        width: 100%;
    }

    .member_grade22 {
        top: 40px;
        right: 0;
    }

    /*样式一居中*/
    .user_box3 {
        padding-top: 20px;
    }

    .user_img_style3 {
        margin: 0 auto 10px;
    }

    .name3, .code3 {
        justify-content: center;
        -webkit-justify-content: center;
    }

    .user_name_style3 {
        margin: 0 auto;
        text-align: center
    }

    .user_sum_style3 {
        width: 100%;
    }

    .member_grade33 {
        top: 20px;
        right: 0;
    }

    /*样式二居左*/
    .user_img_style4 {
        margin-left: 14px;
        margin-top: 20px;
        float: left;
    }

    .user_name_style4 {
        float: left;
        margin-left: 10px;
        margin-top: 28px;
    }

    .user_sum_style4 {
        width: 100%;
    }

    .member_grade44 {
        top: 0;
    }

    /*样式二居右*/
    .user_img_style5 {
        position: absolute;
        top: 20px;
        right: 10px;
        float: right;
    }

    .user_name_style5 {
        float: left;
        margin-left: 14px;
        margin-top: 28px;
    }

    .user_sum_style5 {
        width: 100%;
    }

    .member_grade55 {
        top: 0;
    }

    /*样式二居中*/
    .user_box6 {
        padding-top: 20px;
    }

    .user_img_style6 {
        margin: 0 auto 10px;
    }

    .name6, .code6 {
        justify-content: center;
        -webkit-justify-content: center;
    }

    .user_name_style6 {
        margin: 0 auto;
        text-align: center
    }

    .user_sum_style6 {
        width: 100%;
    }

    .member_grade66 {
        top: 0;
    }

    /*样式三居左*/
    .user_img_style7 {
        margin-left: 14px;
        margin-top: 20px;
        float: left;
    }

    .user_name_style7 {
        float: left;
        margin-left: 10px;
        margin-top: 28px;
    }

    .user_sum_style7 {
        width: 100%;
    }

    .member_grade77 {
        position: absolute;
        right: 0px;
        top: 10px;
    }

    /*样式三居右*/
    .user_img_style8 {
        position: absolute;
        top: 20px;
        left: 186px;
        float: left;
    }

    .user_name_style8 {
        float: left;
        margin-left: 14px;
        margin-top: 28px;
    }

    .user_sum_style8 {
        width: 100%;
        padding-top: 28px;
    }

    .member_grade88 {
        position: absolute;
        right: 0px;
        top: 14px;
    }

    /*样式三居中*/
    .user_box9 {
        padding-top: 20px;
    }

    .user_img_style9 {
        margin: 0 auto 10px;
    }

    .user_name_style9 {
        width: 60%;
        text-align: center;
        margin: 0 auto;
    }

    .user_sum_style9 {
        width: 100%;
    }

    .member_grade99 {
        position: absolute;
        right: 0;
        top: 89px;
    }

    /*		*/
    .order_box {
        margin-top: 10px;
    }

    .order_box_color {
        background-color: {{Item.params.memberordercolor}};
    }

    .order_box_img {
        background-image: url({{Item.params.memberorderimg}});
        background-size: 100% 100%
    }

    .order_box .state img {
        width: 36px;
        height: 36px;
        display: block;
        margin: 0 auto;
    }

    .order_box .title, .tool .title {
        padding: 0 14px;
        height: 40px;
        line-height: 40px;
        display: flex;
        display: -webkit-flex;
        justify-content: space-between;
        -webkit-justify-content: space-between;
    }

    .title .icon-member_right {
        font-size: 20px;
        color: #c9c9c9;
    }

    .title .left {
        display: flex;
        display: -webkit-flex;
        align-items: center;
        -webkit-align-items: center;
    }

    .title .left .square {
        display: inline-block;
        width: 4px;
        height: 16px;
        background-color: #f15353;
        border-radius: 2px;
        margin-right: 6px;
    }

    .title .left h2 {
        font-size: 16px;
    }

    .order_box .state {
        display: flex;
        display: -webkit-flex;
        text-align: center;
        flex-wrap: wrap;
        -webkit-flex-wrap: wrap;
    }

    .tool .item {
        display: flex;
        display: -webkit-flex;
        padding: 10px 0;
        text-align: center;
        flex-wrap: wrap;
        -webkit-flex-wrap: wrap;
    }

    .state li, .item li {
        width: 25%;
        margin-bottom: 10px;
    }

    .state li .iconfont, .item li .iconfont {
        font-size: 24px;
        color: #f15353;
        display: block;
        margin: 0 auto;
    }

    .state, li span, .item li span {
        font-size: 12px;
        color: #666;
        margin-top: 6px;
    }

</style>

<body>
<div class="user_order"
     ng-class="{'user_order_color': Item.params.memberbg == 1, 'user_order_img': Item.params.memberbg == 2}"
     ng-show="Item.params.memberlevelstyle == 1 && Item.params.memberportrait == 1">
    <div class="user_box">
        <div class="user_img user_img_style1">
            <img src="{{inits+'plugins/designer/assets/imgsrc/member/fa.png'}}" alt="">
        </div>
        <ul class="user_name user_name_style1">
            <li class="name"><span class="name_a">果冻果汁</span><span class="member_id"
                                                                   ng-show="Item.params.memberID == 1">会员ID：2590</span>
            </li>
            <li class="code">邀请码：012345<span>复制</span></li>
        </ul>
        <ul class="user_sum user_sum_style1">
            <li ng-show="Item.params.memberintegral == true && Item.params.judgeintegral == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">消费积分</span>
            </li>
            <li ng-show="Item.params.memberwhitelove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">白爱心值</span>
            </li>
            <li ng-show="Item.params.memberredlove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">爱心值</span>
            </li>
            <li ng-show="Item.params.membercredit == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">余额</span>
            </li>
            <li ng-show="Item.params.memberpoint == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">积分</span>
            </li>
            <li ng-show="Item.params.memberincome == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">提现</span>
            </li>
        </ul>
        <!--	会员等级样式一   -->
        <div ng-show="Item.params.memberlevel == 1" class="member_grade1 member_grade11">
            <i class="iconfont icon-member-enter"></i>
            <span>会员等级</span>
        </div>
    </div>
</div>
<div class="user_order"
     ng-class="{'user_order_color': Item.params.memberbg == 1, 'user_order_img': Item.params.memberbg == 2}"
     ng-show="Item.params.memberlevelstyle == 1 && Item.params.memberportrait == 3">
    <div class="user_box">
        <div class="user_img user_img_style2">
            <img src="{{inits+'plugins/designer/assets/imgsrc/member/fa.png'}}" alt="">
        </div>
        <ul class="user_name user_name_style2">
            <li class="name">
                <span class="name_a">果冻果汁</span>
                <span class="member_id" ng-show="Item.params.memberID == 1">会员ID：2590</span>
            </li>
            <li class="code">邀请码：012345<span>复制</span></li>
        </ul>
        <ul class="user_sum user_sum_style2">
            <li ng-show="Item.params.memberintegral == true && Item.params.judgeintegral == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">消费积分</span>
            </li>
            <li ng-show="Item.params.memberwhitelove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">白爱心值</span>
            </li>
            <li ng-show="Item.params.memberredlove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">爱心值</span>
            </li>
            <li ng-show="Item.params.membercredit == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br><span
                        class="text">余额</span></li>
            <li ng-show="Item.params.memberpoint == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">积分</span>
            </li>
            <li ng-show="Item.params.memberincome == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">提现</span>
            </li>
        </ul>
        <!--	会员等级样式一   -->
        <div ng-show="Item.params.memberlevel == 1" class="member_grade1 member_grade22">
            <i class="iconfont icon-member-enter"></i>
            <span class="level_font">会员等级</span>
        </div>
    </div>
</div>
<div class="user_order"
     ng-class="{'user_order_color': Item.params.memberbg == 1, 'user_order_img': Item.params.memberbg == 2}"
     ng-show="Item.params.memberlevelstyle == 1 && Item.params.memberportrait == 2">
    <div class="user_box  user_box3">
        <div class="user_img user_img_style3">
            <img src="{{inits+'plugins/designer/assets/imgsrc/member/fa.png'}}" alt="">
        </div>
        <ul class="user_name user_name_style3">
            <li class="name name3">
                <span class="name_a">果冻果汁</span>
                <span class="member_id" ng-show="Item.params.memberID == 1">会员ID：2590</span>
            </li>
            <li class="code code3">邀请码：012345<span>复制</span></li>
        </ul>
        <ul class="user_sum user_sum_style3">
            <li ng-show="Item.params.memberintegral == true && Item.params.judgeintegral == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">消费积分</span>
            </li>
            <li ng-show="Item.params.memberwhitelove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">白爱心值</span>
            </li>
            <li ng-show="Item.params.memberredlove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">爱心值</span>
            </li>
            <li ng-show="Item.params.membercredit == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">余额</span>
            </li>
            <li ng-show="Item.params.memberpoint == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">积分</span>
            </li>
            <li ng-show="Item.params.memberincome == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">提现</span>
            </li>
        </ul>
        <!--	会员等级样式一   -->
        <div ng-show="Item.params.memberlevel == 1" class="member_grade1 member_grade33">
            <i class="iconfont icon-member-enter"></i>
            <span>会员等级</span>
        </div>
    </div>
</div>
<div class="user_order"
     ng-class="{'user_order_color': Item.params.memberbg == 1, 'user_order_img': Item.params.memberbg == 2}"
     ng-show="Item.params.memberlevelstyle == 2 && Item.params.memberportrait == 1">
    <div class="user_box">
        <div class="user_img user_img_style4">
            <img src="{{inits+'plugins/designer/assets/imgsrc/member/fa.png'}}" alt="">
        </div>
        <ul class="user_name user_name_style4">
            <li class="name"><span class="name_a">果冻果汁</span><span class="member_id"
                                                                   ng-show="Item.params.memberID == 1">会员ID：2590</span>
            </li>
            <li class="code">邀请码：012345<span>复制</span></li>
        </ul>
        <ul class="user_sum user_sum_style4">
            <li ng-show="Item.params.memberintegral == true && Item.params.judgeintegral == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">消费积分</span>
            </li>
            <li ng-show="Item.params.memberwhitelove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">白爱心值</span>
            </li>
            <li ng-show="Item.params.memberredlove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">爱心值</span>
            </li>
            <li ng-show="Item.params.membercredit == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">余额</span>
            </li>
            <li ng-show="Item.params.memberpoint == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">积分</span>
            </li>
            <li ng-show="Item.params.memberincome == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">提现</span>
            </li>
        </ul>
        <div class="grade2_bg">
            <div ng-show="Item.params.memberlevel == 1" class="member_grade2 member_grade44">
                <i class="iconfont icon-member-enter"></i>
                <span class="member_name">会员等级</span>
                <div class="member_btn">
                    <span>点击查看</span>
                    <i class="iconfont icon-member_look"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="user_order"
     ng-class="{'user_order_color': Item.params.memberbg == 1, 'user_order_img': Item.params.memberbg == 2}"
     ng-show="Item.params.memberlevelstyle == 2 && Item.params.memberportrait == 3">
    <div class="user_box">
        <div class="user_img user_img_style5">
            <img src="{{inits+'plugins/designer/assets/imgsrc/member/fa.png'}}" alt="">
        </div>
        <ul class="user_name user_name_style5">
            <li class="name">
                <span class="name_a">果冻果汁</span>
                <span class="member_id" ng-show="Item.params.memberID == 1">会员ID：2590</span>
            </li>
            <li class="code">邀请码：012345<span>复制</span></li>
        </ul>
        <ul class="user_sum user_sum_style5">
            <li ng-show="Item.params.memberintegral == true && Item.params.judgeintegral == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">消费积分</span>
            </li>
            <li ng-show="Item.params.memberwhitelove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">白爱心值</span>
            </li>
            <li ng-show="Item.params.memberredlove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">爱心值</span>
            </li>
            <li ng-show="Item.params.membercredit == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">余额</span>
            </li>
            <li ng-show="Item.params.memberpoint == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">积分</span>
            </li>
            <li ng-show="Item.params.memberincome == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">提现</span>
            </li>
        </ul>
        <div class="grade2_bg">
            <div ng-show="Item.params.memberlevel == 1" class="member_grade2 member_grade55">
                <i class="iconfont icon-member-enter"></i>
                <span class="member_name">会员等级</span>
                <div class="member_btn">
                    <span>点击查看</span>
                    <i class="iconfont icon-member_look"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="user_order"
     ng-class="{'user_order_color': Item.params.memberbg == 1, 'user_order_img': Item.params.memberbg == 2}"
     ng-show="Item.params.memberlevelstyle == 2 && Item.params.memberportrait == 2">
    <div class="user_box user_box6">
        <div class="user_img user_img_style6">
            <img src="{{inits+'plugins/designer/assets/imgsrc/member/fa.png'}}" alt="">
        </div>
        <ul class="user_name user_name_style6">
            <li class="name name6">
                <span class="name_a">果冻果汁</span>
                <span class="member_id" ng-show="Item.params.memberID == 1">会员ID：2590</span>
            </li>
            <li class="code code6">邀请码：012345<span>复制</span></li>
        </ul>
        <ul class="user_sum user_sum_style6">
            <li ng-show="Item.params.memberintegral == true && Item.params.judgeintegral == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">消费积分</span>
            </li>
            <li ng-show="Item.params.memberwhitelove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">白爱心值</span>
            </li>
            <li ng-show="Item.params.memberredlove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">爱心值</span>
            </li>
            <li ng-show="Item.params.membercredit == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">余额</span></li>
            <li ng-show="Item.params.memberpoint == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">积分</span>
            </li>
            <li ng-show="Item.params.memberincome == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">提现</span>
            </li>
        </ul>
        <div class="grade2_bg">
            <div ng-show="Item.params.memberlevel == 1" class="member_grade2 member_grade66">
                <i class="iconfont icon-member-enter"></i>
                <span class="member_name">会员等级</span>
                <div class="member_btn">
                    <span>点击查看</span>
                    <i class="iconfont icon-member_look"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="user_order"
     ng-class="{'user_order_color': Item.params.memberbg == 1, 'user_order_img': Item.params.memberbg == 2}"
     ng-show="Item.params.memberlevelstyle == 3 && Item.params.memberportrait == 1">
    <div class="user_box">
        <div class="user_img user_img_style7">
            <img src="{{inits+'plugins/designer/assets/imgsrc/member/fa.png'}}" alt="">
        </div>
        <ul class="user_name user_name_style7">
            <li class="name"><span class="name_a">果冻果汁</span><span class="member_id"
                                                                   ng-show="Item.params.memberID == 1">会员ID：2590</span>
            </li>
            <li class="code">邀请码：012345<span>复制</span></li>
        </ul>
        <ul class="user_sum user_sum_style7">
            <li ng-show="Item.params.memberintegral == true && Item.params.judgeintegral == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">消费积分</span>
            </li>
            <li ng-show="Item.params.memberwhitelove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">白爱心值</span>
            </li>
            <li ng-show="Item.params.memberredlove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">爱心值</span>
            </li>
            <li ng-show="Item.params.membercredit == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">余额</span>
            </li>
            <li ng-show="Item.params.memberpoint == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">积分</span>
            </li>
            <li ng-show="Item.params.memberincome == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">提现</span>
            </li>
        </ul>
        <div ng-show="Item.params.memberlevel == 1" class="member_grade3 member_grade77">
            <div class="grade">
                <i class="iconfont icon-member-enter"></i>
                <span>会员等级</span>
            </div>
            <div class="member_btn">
                <span>点击查看</span>
                <i class="iconfont icon-member_look"></i>
            </div>
        </div>
    </div>
</div>
<div class="user_order"
     ng-class="{'user_order_color': Item.params.memberbg == 1, 'user_order_img': Item.params.memberbg == 2}"
     ng-show="Item.params.memberlevelstyle == 3 && Item.params.memberportrait == 3">
    <div class="user_box">
        <div class="user_img user_img_style8">
            <img src="{{inits+'plugins/designer/assets/imgsrc/member/fa.png'}}" alt="">
        </div>
        <ul class="user_name user_name_style8">
            <li class="name">
                <span class="name_a">果冻果汁</span>
                <span class="member_id" ng-show="Item.params.memberID == 1">会员ID：2590</span>
            </li>
            <li class="code">邀请码：012345<span>复制</span></li>
        </ul>
        <ul class="user_sum user_sum_style8">
            <li ng-show="Item.params.memberintegral == true && Item.params.judgeintegral == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">消费积分</span>
            </li>
            <li ng-show="Item.params.memberwhitelove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">白爱心值</span>
            </li>
            <li ng-show="Item.params.memberredlove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">爱心值</span>
            </li>
            <li ng-show="Item.params.membercredit == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">余额</span>
            </li>
            <li ng-show="Item.params.memberpoint == true">
                <span class="sum"><font>¥</font>66.66</span><br><span
                        class="text">积分</span></li>
            <li ng-show="Item.params.memberincome == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">提现</span>
            </li>
        </ul>
        <div ng-show="Item.params.memberlevel == 1" class="member_grade3 member_grade88">
            <div class="grade">
                <i class="iconfont icon-member-enter"></i>
                <span>会员等级</span>
            </div>
            <div class="member_btn">
                <span>点击查看</span>
                <i class="iconfont icon-member_look"></i>
            </div>
        </div>
    </div>
</div>
<div class="user_order"
     ng-class="{'user_order_color': Item.params.memberbg == 1, 'user_order_img': Item.params.memberbg == 2}"
     ng-show="Item.params.memberlevelstyle == 3 && Item.params.memberportrait == 2">
    <div class="user_box user_box9">
        <div class="user_img user_img_style9">
            <img src="{{inits+'plugins/designer/assets/imgsrc/member/fa.png'}}" alt="">
        </div>
        <ul class="user_name user_name_style9">
            <li class="name">
                <span class="name_a">果冻果汁</span>
                <span class="member_id" ng-show="Item.params.memberID == 1">会员ID：2590</span>
            </li>
            <li class="code">邀请码：012345<span>复制</span></li>
        </ul>
        <ul class="user_sum user_sum_style9">
            <li ng-show="Item.params.memberintegral == true && Item.params.judgeintegral == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">消费积分</span>
            </li>
            <li ng-show="Item.params.memberwhitelove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">白爱心值</span>
            </li>
            <li ng-show="Item.params.memberredlove == true && Item.params.judgelove == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">爱心值</span>
            </li>
            <li ng-show="Item.params.membercredit == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">余额</span>
            </li>
            <li ng-show="Item.params.memberpoint == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">积分</span>
            </li>
            <li ng-show="Item.params.memberincome == true">
                <span class="sum"><font>¥</font>66.66</span>
                <br>
                <span class="text">提现</span>
            </li>
        </ul>
        <div ng-show="Item.params.memberlevel == 1" class="member_grade3 member_grade99">
            <div class="grade">
                <i class="iconfont icon-member-enter"></i>
                <span>会员等级</span>
            </div>
            <div class="member_btn">
                <span>点击查看</span>
                <i class="iconfont icon-member_look"></i>
            </div>
        </div>
    </div>
</div>

<div class="order_box"
     ng-class="{'order_box_color': Item.params.memberorderbg == 1, 'order_box_img': Item.params.memberorderbg == 2}">
    <div class="title">
        <div class="left">
            <h2>{{Item.params.memberordername}}</h2>
        </div>
        <i class="iconfont icon-member_right"></i>
    </div>
    <ul class="state">
        <li ng-repeat="img in Item.data">
            <img src="{{img.imgurl}}">
            <span style="color: {{img.color}}">{{img.text}}</span>
        </li>
    </ul>
</div>
</body>


