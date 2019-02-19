@extends('layouts.bst')
<title>用户展示</title>

@section('content')


    <table  class="table table-bordered">
        <thead>
            <tr>
              <td>uid</td>
              <td>用户名</td>
              <td>邮箱</td>
              <td>注册时间</td>
            </tr>
        </thead>
        <tbody>
            @foreach($info as $v)
                <tr>
                    <td>{{$v['u_id']}}</td>
                    <td>{{$v['name']}}</td>
                    <td>{{$v['email']}}</td>
                    <td>{{$v['reg_time']}}</td>
                </tr>
             @endforeach
        </tbody>
    </table>
@endsection
