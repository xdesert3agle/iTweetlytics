@extends('layouts.app_nofooter_noheader')

@section('content')
    <app :user="{{ $user }}" :timeline="{{ $timeline }}" :mentions="{{ $mentions }}" :chats="{{ $chats }}" :lists="{{ $lists }}" :loadtime="{{ $loadTime }}"></app>
@endsection
