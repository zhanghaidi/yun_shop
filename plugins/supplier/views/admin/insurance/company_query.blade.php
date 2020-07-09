<div style='max-height:500px;overflow:auto;min-width:850px;'>
    <table class="table table-hover" style="min-width:850px;">
        <tbody>
        <span class="help-block">保险公司名称</span>
        @foreach($company as $row)
            <tr>
                <td>
                    <!--<img src='{{yz_tomedia($row['thumb'])}}' style='width:30px;height:30px;padding1px;border:1px solid #ccc'/>-->
                    {{$row['name']}}&nbsp;&nbsp;&nbsp;[ID:{{$row['id']}}]
                </td>
                <td style="width:80px;"><a href="javascript:;" onclick='select_company({{json_encode($row)}})'>选择</a></td>
            </tr>
        @endforeach
        @if(count($company)<=0)
        <tr>
            <td colspan='4' align='center'>未找到保险公司</td>
        </tr>
        @endif
        </tbody>
    </table>
</div>