@extends('layouts.app')

@section('content')
    <landing-page></landing-page>
    <div class="container">
        <div class="row">
            <div class="col">
                {{ isset($user) ? $user : "No estás logueado" }}
            </div>
        </div>
    </div>
@endsection
