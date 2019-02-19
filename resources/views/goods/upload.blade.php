@extends('layouts.bst');

@section('content')

    <form action="/goods/upload/do"  method="post"  enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="file" name="goods_file">

        <input type="submit" value="upload">
    </form>

@endsection