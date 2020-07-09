<div style='max-height:500px;overflow:auto;'>
    <table class="table table-hover">
        @if(count($goods)>0)
            <thead>
                <th style="width: 33%;text-align: center">商品ID</th>
                <th style="width: 33%;text-align: center">商品标题</th>
                <th style="width: 33%;text-align: center">选择</th>
            </thead>
        <tbody>
        @foreach($goods as $row)
            <tr>
                <td style="text-align: center">
                    {{$row['id']}}
                </td>
                <td style="text-align: center">
                    <img src='{{yz_tomedia($row['thumb'])}}'
                         style='width:30px;height:30px;padding1px;border:1px solid #ccc'/>
                    {{$row['title']}}
                </td>
                <td style="text-align: center">
                    <input type="checkbox" data-title="{{$row['title']}}" name="goods[]"  value="{{$row['id']}}">
                </td>
            </tr>

        @endforeach
        @endif
        @if(count($goods)<=0)
        <tr>
            <td colspan='4' align='center'>未找到商品</td>
        </tr>
        @endif
        </tbody>
    </table>
</div>