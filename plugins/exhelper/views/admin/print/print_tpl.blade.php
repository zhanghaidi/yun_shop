<table class="table" style="border-top: 0;">
    <tr>
        <td style="width:100%;vertical-align: top; border: 0; padding: 0; padding-right: 20px;white-space:normal;">
            <div class="panel panel-default" style="margin-bottom: 0;">
                <div class="panel-heading">
                    搜索结果 <span id="buyercount" style="color:#ff6600"></span>买家/<span id="ordercount" style="color:#ff6600"></span>订单
                </div>
                <div class="panel-body" style="min-height:100px; max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover" style="width: auto; min-width: 100%; margin: 0;">
                        @if(!empty($list))
                            @foreach($list as $row)
                                <tr style="cursor: pointer;">
                                    <td class='order_item' data-orderids="{!! implode(',',$row['orderids']) !!}">{{$row['realname']}}</td>
                                </tr>
                            @endforeach
                        @else
                            抱歉！未查找到相关数据。
                        @endif
                    </table>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top; border: 0; padding: 0;">
            <div class="panel panel-default" id="orders">
                <div class="panel-heading">订单信息</div>
                <div class="panel-body">
                    @if(!empty($list)) 请先选择左侧搜索结果 @else 抱歉！未查到相关数据。 @endif
                </div>
            </div>
        </td>
    </tr>
</table>