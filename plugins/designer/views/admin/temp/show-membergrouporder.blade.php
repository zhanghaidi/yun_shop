<style>
    /*		*/
    .order_box{
        margin-top: 10px;
    }
    .gorder_box_color{
        background-color: {{Item.params.memberordercolor}};
    }
    .gorder_box_img{
        background-image: url({{Item.params.memberorderimg}});
        background-size:100% 100%
    }
    .order_box .state img{
        width:36px;
        height:36px;
        display: block;
        margin:0 auto;
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
    .order_box .state{
        display: flex;
        display: -webkit-flex;
        text-align: center;
        flex-wrap: wrap;
        -webkit-flex-wrap: wrap;
    }

    .state li, .item li {
        width: 25%;
        margin-bottom: 10px;
    }

    .state, li span, .item li span {
        font-size: 12px;
        color: #666;
        margin-top: 6px;
    }
</style>
<div class="order_box" ng-class =  "{'gorder_box_color': Item.params.memberorderbg == 1, 'gorder_box_img': Item.params.memberorderbg == 2}">
    <div class="title">
        <div class="left">
            <h2>{{Item.params.memberordername}}</h2>
        </div>
        <i class="iconfont icon-member_right"></i>
    </div>
    <ul class="state">
        <li ng-repeat="img in Item.data" >
            <img src="{{img.imgurl}}">
            <span style="color: {{img.color}}">{{img.text}}</span>
        </li>
    </ul>
</div>