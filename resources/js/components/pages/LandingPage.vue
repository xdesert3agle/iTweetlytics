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
                                            <div class="col-md col-12">
                                                <div class="form-group">
                                                    <label for="name" class="required">Nombre</label>
                                                    <input id="name" type="text" class="form-control" :class="{ 'animated shake has-errors': checkForErrors('name') }"
                                                           v-model="newUser.name" placeholder="Nombre" required autocomplete="name">
                                                    <div v-if="errors && errors.name && errors.name.length > 0" class="field-error-container animated fadeIn faster">
                                                        <small class="form-error" v-for="(error, i) in errors.name">{{
                                                            error }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md col-12">
                                                <div class="form-group">
                                                    <label for="surname" class="required">Apellidos</label>
                                                    <input id="surname" type="text" class="form-control" :class="{ 'animated shake has-errors': checkForErrors('surname') }"
                                                           v-model="newUser.surname" placeholder="Apellidos" required autocomplete="surname">
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
                                            <input id="email" type="email" class="form-control" :class="{ 'animated shake has-errors': checkForErrors('email') }"
                                                   v-model="newUser.email" placeholder="Dirección de correo electrónico" required autocomplete="email">
                                            <div v-if="errors && errors.email && errors.email.length > 0" class="field-error-container animated fadeIn faster">
                                                <small class="form-error" v-for="(error, i) in errors.email">{{ error
                                                    }}</small>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md col-12">
                                                <div class="form-group">
                                                    <label for="password" class="required">Contraseña</label>
                                                    <input id="password" type="password" class="form-control" :class="{ 'animated shake has-errors': checkForErrors('password') }"
                                                           v-model="newUser.password" placeholder="Contraseña" required autocomplete="new-password">
                                                    <div v-if="errors && errors.password && errors.password.length > 0" class="field-error-container animated fadeIn faster">
                                                        <small class="form-error" v-for="(error, i) in errors.password">{{
                                                            error }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md col-12">
                                                <div class="form-group">
                                                    <label for="password-confirm" class="required">Confirmar
                                                        contraseña</label>
                                                    <input id="password-confirm" type="password" class="form-control" v-model="newUser.password_confirmation"
                                                           placeholder="Confirmar contraseña" required autocomplete="new-password">
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

        <div class="section bg-light pt-lg">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="media">
                            <div class="media-icon d-flex mr-3"><i class="pe-7s-rocket pe-3x"></i></div>
                            <!-- // end .di -->
                            <div class="media-body">
                                <h5 class="mt-0">Información rápida</h5> Recibe la información del perfil al momento
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="media">
                            <div class="media-icon d-flex mr-3"><i class="pe-7s-piggy pe-3x"></i></div>
                            <div class="media mb-5">
                                <!-- // end .di -->
                                <div class="media-body">
                                    <h5 class="mt-0">Totalmente gratuita</h5> Ahorra grandes cantidades de dinero en otras
                                    aplicaciones de pago
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="media">
                            <div class="media-icon d-flex mr-3"><i class="pe-7s-door-lock pe-3x"></i></div>
                            <!-- // end .di -->
                            <div class="media-body">
                                <h5 class="mt-0">Información segura</h5> La información sensible se almacena de una forma
                                totalmente segura
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
                            <div class="content">
                                <img src="images/dashboard.png" alt="image">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="media">
                            <div class="media-body">
                                <div class="media-icon mb-3"><i class="pe-7s-like2 pe-3x"></i></div>
                                <h3 class="mt-0">Cliente de Twitter integrado</h3>
                                <p>Administra tu timeline, menciones, listas y mensajes privados en una cómoda disposición en columnas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center mt-5">
                    <div class="col-sm-4">
                        <div class="media">
                            <div class="media-body">
                                <div class="media-icon mb-3"><i class="pe-7s-graph1 pe-3x"></i></div>
                                <h3 class="mt-0">Estadísticas, gráficas y más</h3>
                                <p>Recibe información de utilidad sobre tus perfiles de Twitter y compara los resultados en múltiples intervalos de tiempo</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <img src="images/screen.jpg" alt="image" class="img-fluid cast-shadow my-5">
                    </div>
                </div>

                <div class="row align-items-center mt-5">
                    <div class="col-sm-8">
                        <div class="browser-window limit-height my-5 mr-0 mr-sm-5">
                            <div class="content">
                                <img src="images/dashboard.png" alt="image">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="media">
                            <div class="media-body">
                                <div class="media-icon mb-3"><i class="pe-7s-edit pe-3x"></i></div>
                                <h3 class="mt-0">Etiquetado de perfiles</h3>
                                <p>Clasifica tus seguidores y seguidos con tus propios filtros personalizados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
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

    @media (max-width: 576px) {
        section.jumbotron-two {
            h1 {
                font-size: 26pt;
                text-align: center;
            }

            h4 {
                text-align: center;
                font-size: 16pt;
                line-height: 1.6em!important;
            }

            p {
                text-align: center;
            }
        }
    }
</style>
