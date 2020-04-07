@extends('layouts.app')

@section('content')
    <div class="container-fluid app-container">
        <div class="row">
            <div class="col-md-auto col-12">
                <ul class="nav flex-column nav-pills" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-stats-tab" data-toggle="pill" href="#pills-stats" role="tab" aria-controls="pills-home" aria-selected="false">Estadísticas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-profiles-tab" data-toggle="pill" href="#pills-profiles" role="tab" aria-controls="pills-profiles" aria-selected="false">Perfiles sincronizados</a>
                    </li>
                    <li class="nav-item">
                        <form action="/logout" method="POST">
                            <csrf></csrf>
                            <button type="submit" class="btn btn-danger">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="col-md col-12">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <dashboard :timeline="{{ $timeline }}"></dashboard>
                    </div>
                    <div class="tab-pane" id="pills-stats" role="tabpanel" aria-labelledby="pills-stats-tab">
                        <stats></stats>
                    </div>
                    <div class="tab-pane" id="pills-profiles" role="tabpanel" aria-labelledby="pills-profiles-tab">
                        <profiles :user="{{ $user }}"></profiles>
                    </div>
                    <div class="tab-pane" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">3</div>
                </div>
            </div>
        </div>
    </div>
@endsection
