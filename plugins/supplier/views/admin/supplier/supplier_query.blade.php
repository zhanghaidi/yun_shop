<div style='max-height:500px;overflow:auto;min-width:850px;'>
    <table class="table table-hover" style="min-width:850px;">
        <tbody>
        @foreach($supplier as $row)
            <tr>
                <td><img src='{{tomedia($row['avatar'])}}' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /> {{$row['username']}}</td>
                <td style="width:80px;"><a href="javascript:;" onclick='select_supplier({{json_encode($row)}})'>选择</a></td>
            </tr>
        @endforeach
        @if (count($supplier) <= 0)
            <tr>
                <td colspan='4' align='center'>未找到供应商</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
