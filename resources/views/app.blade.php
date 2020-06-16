@extends('layouts.app_nofooter_noheader')

@section('title', 'Dashboard - iTweetlytics')

@section('content')
    <app :user="{{ $user }}" :timeline="{{ $timeline ?? '' }}" :mentions="{{ $mentions ?? '' }}"  :lists="{{ $lists ?? '' }}" :chats="{{ $chats ?? '' }}" :loadtime="{{ $loadTime }}"></app>
@endsection
