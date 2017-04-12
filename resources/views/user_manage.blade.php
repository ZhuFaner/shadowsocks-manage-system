@extends('layouts.app')
@section('content')

    <div class="container">
        <div class="row"> 
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        用户管理
                        <a href="{{ url('/add_user') }}" style="float: right">添加用户</a>
                    </div>
                    <div class="panel-body">
                        @if(count($userArray) > 0)
                            <ol>
                                @foreach($userArray as $user)
                                    <li class="li_user">
                                        <div class="list_item">
                                            名称：{{ empty($user->name) ? '无' : $user->name }}
                                        </div>
                                        <div class="list_item">端口：{{ $user->port }}</div>
                                        <div class="list_item">密码：{{ $user->password }}</div>
                                        <div>
                                            <button class="btn btn-default btn_common"
                                                    onclick="location.href='/user_detail?port={{ $user->port }}'">
                                                用户详情
                                            </button>
                                            <button class="btn btn-default btn_common"
                                                    onclick="location.href='/edit_user?port={{ $user->port }}'">
                                                用户编辑
                                            </button>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection