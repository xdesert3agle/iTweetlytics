<template>
    <div class="landing-content">
        <section class="jumbotron-two">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6">
                        <h1>Analíticas de calidad</h1>
                        <h4 class="text-muted mb-3">Gestiona tus perfiles de Twitter y recibe información
                            imprescindible.</h4>
                        <p class="font-styled">
                            <a href="#pricing">Comparar los planes disponibles</a>
                        </p>
                    </div>

                    <div class="col-12 col-md-6 my-3 my-md-lg">
                        <div class="card form-register-card">
                            <div class="card-body">
                                <h3 class="card-title">Únete ahora</h3>
                                <div class="row justify-content-md-center">
                                    <div class="col form-register-container">
                                        <input type="hidden" name="_token" :value="csrf">
                                        <div class="form-row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="name" class="required">Nombre</label>
                                                    <input id="name" type="text" class="form-control" :class="{ 'animated shake has-errors': checkForErrors('name') }" v-model="newUser.name" placeholder="Nombre" required autocomplete="name">
                                                    <div v-if="errors && errors.name && errors.name.length > 0" class="field-error-container animated fadeIn faster">
                                                        <small class="form-error" v-for="(error, i) in errors.name">{{
                                                            error }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="surname" class="required">Apellidos</label>
                                                    <input id="surname" type="text" class="form-control" :class="{ 'animated shake has-errors': checkForErrors('surname') }" v-model="newUser.surname" placeholder="Apellidos" required autocomplete="surname">
                                                    <div v-if="errors && errors.surname && errors.surname.length > 0" class="field-error-container animated fadeIn faster">
                                                        <small class="form-error" v-for="(error, i) in errors.surname">{{
                                                            error
                                                            }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="email" class="required">Email</label>
                                            <input id="email" type="email" class="form-control" :class="{ 'animated shake has-errors': checkForErrors('email') }" v-model="newUser.email" placeholder="Dirección de correo electrónico" required autocomplete="email">
                                            <div v-if="errors && errors.email && errors.email.length > 0" class="field-error-container animated fadeIn faster">
                                                <small class="form-error" v-for="(error, i) in errors.email">{{ error
                                                    }}</small>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="password" class="required">Contraseña</label>
                                                    <input id="password" type="password" class="form-control" :class="{ 'animated shake has-errors': checkForErrors('password') }" v-model="newUser.password" placeholder="Contraseña" required autocomplete="new-password">
                                                    <div v-if="errors && errors.password && errors.password.length > 0" class="field-error-container animated fadeIn faster">
                                                        <small class="form-error" v-for="(error, i) in errors.password">{{
                                                            error }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="password-confirm" class="required">Confirmar
                                                        contraseña</label>
                                                    <input id="password-confirm" type="password" class="form-control" v-model="newUser.password_confirmation" placeholder="Confirmar contraseña" required autocomplete="new-password">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button @click="attemptRegister" type="button" class="btn btn-block btn-primary">
                                                Registrarme
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-shape"></div>
            <div class="bg-circle"></div>
            <div class="bg-circle-two"></div>
        </section>
        <div class="section" id="intro">
            <div class="container">
                <div class="client-logos text-center">
                    <p class="text-muted">TRUSTED BY MOST POPULAR BRANDS</p>
                    <img src="images/client_logo_1.png" alt="client logo"/>
                    <img src="images/client_logo_2.png" alt="client logo"/>
                    <img src="images/client_logo_3.png" alt="client logo"/>
                    <img src="images/client_logo_4.png" alt="client logo"/>
                    <img src="images/client_logo_5.png" alt="client logo"/>
                    <img src="images/client_logo_6.png" alt="client logo"/>
                    <img src="images/client_logo_7.png" alt="client logo"/>
                    <img src="images/client_logo_8.png" alt="client logo"/>
                    <img src="images/client_logo_9.png" alt="client logo"/>
                    <img src="images/client_logo_10.png" alt="client logo"/>
                </div>
                <!-- // end .client-logos -->

            </div>
            <!-- // end .container -->
        </div>
        <!-- // end #services.section -->

        <div class="section bg-light pt-lg">
            <div class="container">

                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="media-icon d-flex mr-3"><i class="pe-7s-paint-bucket pe-3x"></i></div>
                        <!-- // end .di -->
                        <div class="media-body">
                            <h5 class="mt-0">Easy Customization</h5> Cras sit amet nibh libero, in gravida nulla. Nulla
                            vel
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="media mb-5">
                            <div class="media-icon d-flex mr-3"><i class="pe-7s-rocket pe-3x"></i></div>
                            <!-- // end .di -->
                            <div class="media-body">
                                <h5 class="mt-0">Super Fast</h5> Cras sit amet nibh libero, in gravida nulla. Nulla vel
                                metus scelerisque ante.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="media mb-5">
                            <div class="media-icon d-flex mr-3"><i class="pe-7s-piggy pe-3x"></i></div>
                            <div class="media mb-5">
                                <!-- // end .di -->
                                <div class="media-body">
                                    <h5 class="mt-0">Save Money</h5>in gravida nulla. Nulla vel metus scelerisque ante
                                    sollicitudin.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="media mb-5">
                            <div class="media-icon d-flex mr-3"><i class="pe-7s-cloud-upload pe-3x"></i></div>
                            <!-- // end .di -->
                            <div class="media-body">
                                <h5 class="mt-0">Cloud Upload</h5> sit amet nibh libero, in gravida nulla. vel metus
                                scelerisque ante sollicitudin.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="media mb-5">
                            <div class="media-icon d-flex mr-3"><i class="pe-7s-science pe-3x"></i></div>
                            <!-- // end .di -->
                            <div class="media-body">
                                <h5 class="mt-0">Proven Technology</h5> Cras sit amet nibh libero, in gravida nulla.
                                Nulla vel metus ante .
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="media mb-5">
                            <div class="media-icon d-flex mr-3"><i class="pe-7s-like2 pe-3x"></i></div>
                            <!-- // end .di -->
                            <div class="media-body">
                                <h5 class="mt-0">100% Satisfaction</h5>Amet nibh libero, in gravida nulla. Nulla vel
                                metus ante sollicitudin.
                            </div>
                        </div>
                    </div>
                    <!-- // end .col -->
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="section" id="features">

            <div class="container">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <div class="browser-window limit-height my-5 mr-0 mr-sm-5">
                            <div class="top-bar">
                                <div class="circles">
                                    <div class="circle circle-red"></div>
                                    <div class="circle circle-yellow"></div>
                                    <div class="circle circle-blue"></div>
                                </div>
                            </div>
                            <div class="content">
                                <img src="images/dashboard.png" alt="image">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="media">
                            <div class="media-body">
                                <div class="media-icon mb-3"><i class="pe-7s-like2 pe-3x"></i></div>
                                <h3 class="mt-0">Perfect Dashboard</h3>
                                <p> Reprehenderit nemo quod tempore doloribus ratione distinctio quis quidem vitae sunt
                                    reiciendis, molestias omnis soluta?</p>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="row align-items-center mt-5">

                    <div class="col-sm-4">
                        <div class="media">
                            <div class="media-body">
                                <div class="media-icon mb-3"><i class="pe-7s-graph1 pe-3x"></i></div>
                                <h3 class="mt-0">Charts, Diagrams & more</h3>
                                <p> Reprehenderit nemo quod tempore doloribus ratione distinctio quis quidem vitae sunt
                                    reiciendis, molestias omnis soluta?</p>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-8">
                        <img src="images/screen.jpg" alt="image" class="img-fluid cast-shadow my-5">
                    </div>


                </div>
            </div>
        </div>
        <!-- features -->

        <div class="section bg-light py-lg">
            <div class="container">

                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="media">

                            <!-- // end .di -->
                            <div class="media-body text-center">
                                <div class="color-icon mb-3"><i class="pe-7s-medal pe-3x"></i></div>
                                <h5 class="mt-0">Award Winning Design</h5> Monotonectally envisioneer e-business niche
                                markets vis-a-vis cost effective information. objectively promote worldwide.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="media">
                            <!-- // end .di -->
                            <div class="media-body text-center">
                                <div class="color-icon mb-3"><i class="pe-7s-diamond pe-3x"></i></div>
                                <h5 class="mt-0">Carefully Crafted</h5> Authoritatively streamline strategic markets
                                without user-centric potentialities. Credibly integrate progressive technologies
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="media">
                            <!-- // end .di -->
                            <div class="media-body text-center">
                                <div class="color-icon mb-3"><i class="pe-7s-cloud-upload pe-3x"></i></div>
                                <h5 class="mt-0">Cloud Sync</h5>Objectively underwhelm e-business leadership skills
                                after cross-unit best practices. Continually innovate robust action items
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- // end features -->


        <!-- Testimonials -->
        <div class="section">
            <div class="container">
                <div class="section-title text-center">
                    <h3>What our users say</h3>
                    <p>They love it. Read what our customers had to say!</p>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="embed-tweet-item">
                            <blockquote class="twitter-tweet" data-cards="hidden" lang="en" data-width="550" data-link-color="#7642FF" data-align="center">
                                <a href="https://twitter.com/kmin/status/943914224329347072"></a>
                            </blockquote>
                        </div>
                        <!-- end .embed-tweet-item -->
                    </div>
                    <!-- end .col -->
                    <div class="col-md-4">
                        <div class="embed-tweet-item">
                            <blockquote class="twitter-tweet" data-cards="hidden" lang="en" data-width="550" data-link-color="#7642FF" data-align="center">
                                <a href="https://twitter.com/halarewich/status/954224121784688640"></a>
                            </blockquote>
                        </div>
                        <!-- end .embed-tweet-item -->
                    </div>
                    <!-- end .col -->
                    <div class="col-md-4">
                        <div class="embed-tweet-item">
                            <blockquote class="twitter-tweet" data-cards="hidden" lang="en" data-width="550" data-link-color="#7642FF" data-align="center">
                                <a href="https://twitter.com/scottbelsky/status/921417663859052544"></a>
                            </blockquote>
                        </div>
                        <!-- end .embed-tweet-item -->
                    </div>
                    <!-- end .col -->
                </div>
                <!-- end .row -->
            </div>
        </div>
        <!-- // end Testimonials -->


        <!-- Pricing -->
        <div class="section bg-light py-lg" id="pricing">
            <div class="container">

                <div class="section-title text-center mt-0 mb-5">
                    <h3>Elige tu plan</h3>
                    <p>Pago sencillos. Sin tasas ocultas. Escoge el plan que mejor se adapte a tus necesidades</p>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card pricing">
                            <div class="card-body">
                                <small class="text-muted">PERSONAL</small>
                                <h5 class="card-title">$9</h5>
                                <p class="card-text">
                                <ul class="list-unstyled">
                                    <li>3 Projects</li>
                                    <li class="plan-muted">Team Collaboration</li>
                                    <li class="plan-muted">Analytics &amp; Reports</li>
                                    <li>One user</li>
                                </ul>
                                </p>
                                <a href="#" class="btn btn-round btn-outline-primary">Choose this plan</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card pricing">
                            <div class="card-body">
                                <small class="text-muted">STARTUP</small>
                                <h5 class="card-title">$29</h5>
                                <p class="card-text">
                                <ul class="list-unstyled">
                                    <li>20 Projects</li>
                                    <li>Team Collaboration</li>
                                    <li>Analytics &amp; Reports</li>
                                    <li>10 Active users</li>
                                </ul>
                                </p>
                                <a href="#" class="btn btn-round btn-primary">Choose this plan</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card pricing">
                            <div class="card-body">
                                <small class="text-muted">ENTERPRISE</small>
                                <h5 class="card-title">$149</h5>
                                <p class="card-text">
                                <ul class="list-unstyled">
                                    <li>Unlimited Projects</li>
                                    <li>Team Collaboration</li>
                                    <li>Analytics &amp; Reports</li>
                                    <li>Priority Support</li>
                                </ul>
                                </p>
                                <a href="#" class="btn btn-round btn-outline-primary">Choose this plan</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- // end Pricing -->
    </div>
</template>

<script>
    export default {
        props: [
            'reports'
        ],
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                newUser: {
                    name: "",
                    surname: "",
                    email: "",
                    password: "",
                    password_confirmation: "",
                },
                errors: {
                    name: [],
                    surname: [],
                    email: [],
                    password: [],
                }
            }
        },
        computed: {},
        methods: {
            attemptRegister() {
                this.errors = {
                    name: [],
                    surname: [],
                    email: [],
                    password: [],
                };

                axios.post('register', this.newUser)
                    .then((data) => {
                        this.user = data.data;
                        window.location.href = "/sync";
                    })
                    .catch((error) => {
                        this.errors = error.response.data.errors;
                    });
            },
            checkForErrors(field) {
                return this.errors[field] && this.errors[field].length > 0;
            }
        }
    }
</script>

<style lang="scss" scoped>
    section.jumbotron-two {
        h1 {
            font-size: 3rem;
        }
    }

    .form-register-card {
        box-shadow: rgba(0, 0, 0, 0.2) 0 0.25em 0.25em;

        h3 {
            text-align: center;
            margin-top: 0.4em;
            margin-bottom: 0.9em;
            font-weight: bold;
            font-size: 2rem;
            color: #1e1f4b;
        }

        .form-register-container {

            input.has-errors {
                border-color: red !important;
            }

            .field-error-container {

                small.form-error {
                    display: block;
                    color: red;
                }
            }
        }
    }
</style>
