<!--阿里图标库-->
<link rel="stylesheet" href="//at.alicdn.com/t/font_432132_5gl1w51e2ib.css"/>
<style>


    .user_img img {
        width: 100%;
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

    .sum font {
        color:#333;
        font-size: 12px;
    }


    .order_box .state img{
        width:36px;
        height:36px;
        display: block;
        margin:0 auto;
    }
    .order_box .title, .member_market .title {
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

    .member_market .item {
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
    .member_market .item img{
        width:36px;
        height:36px;
        display: block;
        margin:0 auto;
    }
    .list li{
        height: 46px;
        line-height: 46px;
        padding-right: 14px;
        display: flex;
        display: -webkit-flex;
        align-items: center;
        -webkit-align-item:center;
        border-bottom:solid 1px #ebebeb;
    }
    .list li span{
        font-size:16px;
        color:#333;
    }
    .list li .iconfont{
        font-size:24px;
        margin-right: 6px;
        color:#f15353;
    }
    .list li .icon-member_right{
        position: absolute;
        right: 0;
        color:#c9c9c9;
        font-size: 20px;
    }

    .member_market_color{
        background-color: {{Item.params.marketbgcolor}};
    }

    .member_market_img{
        background-image: url({{Item.params.bgimg}});
        background-size:100% 100%
    }


</style>
<div class="member_market" ng-class =  "{'member_market_color': Item.params.marketbg == 1, 'member_market_img': Item.params.marketbg == 2}" ng-show = "Item.params.marketstyle == 1" >
    <div class="title">
        <div class="left">
            <span class="square"></span>
            <h2 style="color: {{Item.params.markettitlecolor}}">{{Item.params.markettitle}}</h2>
        </div>
        <i class="iconfont icon-member_right"></i>
    </div>
    <ul class="item">
        <li ng-repeat="img in Item.data.part" ng-show="img.is_open == true">
            <img src="{{img.image}}">
            <span>{{img.title}}</span>
        </li>
        <li ng-repeat="moreimg in Item.data.more">
            <img src="{{moreimg.imgurl}}">
            <span>{{moreimg.title}}</span>
        </li>
    </ul>
</div>
<div class="member_market"  ng-class =  "{'member_market_color': Item.params.marketbg == 1, 'member_market_img': Item.params.marketbg == 2}" ng-show = "Item.params.marketstyle == 2">
    <div class="title">
        <div class="left">
            <span class="square"></span>
            <h2 style="color: {{Item.params.markettitlecolor}}">{{Item.params.markettitle}}</h2>
        </div>
    </div>
    <ul class="list">
        <li ng-repeat="img in Item.data.part" ng-show="img.is_open == true">
            <i class="iconfont {{img.class}}"></i>
            <span>{{img.title}}</span>
            <i class="iconfont icon-member_right"></i>
        </li>
        <li ng-repeat="moreimg in Item.data.more">
            <i class="{{moreimg.icon}}" style="font-size:24px;margin-right: 6px;color:#f15353;"></i>
            <span>{{moreimg.title}}</span>
            <i class="iconfont icon-member_right"></i>
        </li>
    </ul>
</div>