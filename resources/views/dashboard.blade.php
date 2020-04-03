@extends('layouts.app')

@section('content')
    <dashboard :user="{{ $user }}"></dashboard>
@endsection
