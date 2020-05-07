@extends('layouts.app_nofooter_noheader')

@section('content')
    <sync :user="{{ $user }}"></sync>
@endsection
