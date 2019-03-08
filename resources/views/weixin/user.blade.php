
<table border="1" width="1000px" >
    <tr>
        <td>openid</td>
        <td>添加时间</td>
        <td>昵称</td>
        <td>性别</td>
        <td>头像</td>
        <td>关注时间</td>
        <td>状态</td>

    </tr>
    @foreach($userinfo as $v)
        <tr>
            <td>{{$v['openid']}}</td>
            <td>{{$v['add_time']}}</td>
            <td>{{$v['nickname']}}</td>
            <td>{{$v['sex']}}</td>
            {{--<td>{{$v['headimgurl']}}</td>--}}
            {{--<td>{{$v['subscribe_time']}}</td>--}}
            <td>
                @if($v['sex']==0)
                    <a href="/admin/weixin/hei?openid={{$v['openid']}}">拉黑</a>
                @elseif($v['sex']==1)
                   已拉黑
                @endif
            </td>

        </tr>

   @endforeach
</table>


