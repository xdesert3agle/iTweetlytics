@extends('layouts.app_nofooter_noheader')

@section('content')
    <app :user="{{ $user }}" :timeline="{{ $timeline ?? '' }}" :mentions="{{ $mentions ?? '' }}"  :lists="{{ $lists ?? '' }}" :chats="{{ $chats ?? '' }}" :loadtime="{{ $loadTime }}"></app>
@endsection
