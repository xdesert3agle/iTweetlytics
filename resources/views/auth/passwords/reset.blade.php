@extends('layouts.app_nofooter')

@section('content')
    <new-password :token="'{{ $token }}'" :email="'{{ $email }}'"></new-password>
@endsection
