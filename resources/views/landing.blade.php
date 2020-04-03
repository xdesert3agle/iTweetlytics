@extends('layouts.app')

@section('hero')
    <!-- hero -->
    <section class="jumbotron-two">
        <div class="container">

            <div class="row align-items-center">

                <div class="col-12 col-md-5">
                    <h1 class="display-5">Información de calidad.</h1>
                    <p class="text-muted mb-3">Holisticly syndicate unique technology after clicks and mortar growth strategies. Credibly expedite frictionless relationships after revolutionary.</p>
                    <p>
                        <a href="#signup" class="btn btn-xl btn-primary">Empieza ya tu periodo de prueba</a>
                    </p>
                    <p class="font-styled">
                        <a href="#pricing">Comparar los planes disponibles</a>
                    </p>
                </div>

                <div class="col-12 col-md-7 my-3 my-md-lg">
                    <div class="youtube cast-shadow" data-video-id="rm5sdAYCqqc" data-params="modestbranding=1&amp;showinfo=0&amp;controls=1&amp;vq=hd720">
                        <img src="images/screen2.jpg" alt="image" class="img-fluid">
                        <div class="play"><span class="pe-7s-play pe-3x"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- // end hero -->
@endsection

@section('content')
    <landing-page></landing-page>
@endsection
