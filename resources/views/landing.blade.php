@extends('layouts.app')

@section('content')
    <landing-page></landing-page>
    <div class="container">
        <div class="row">
            <div class="col">
                {{ Auth::user() ? "Bienvenido " . Auth::user()->name : "No estás logueado" }}
            </div>
        </div>
    </div>
@endsection
