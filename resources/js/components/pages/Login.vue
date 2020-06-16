<template>
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-12">
                <div class="background"></div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3>Iniciar sesión</h3>

                                <div class="form-group">
                                    <label for="email" class="required">Email</label>
                                    <input v-model="user.email" id="email" type="email" class="form-control" placeholder="Dirección de correo electrónico" required autocomplete="email">
                                </div>

                                <div class="form-group">
                                    <label for="password" class="required">Contraseña</label>
                                    <input v-model="user.password" id="password" type="password" class="form-control" placeholder="Contraseña" required autocomplete="current-password">
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group form-check">
                                            <input v-model="user.remember" class="form-check-input" type="checkbox" name="remember" id="remember">

                                            <label class="form-check-label" for="remember">
                                                Mantener la sesión iniciada
                                            </label>
                                        </div>

                                        <div v-if="loginError" class="alert alert-danger animated fadeIn faster">
                                            <i class="fa fa-exclamation-circle"></i>
                                            Usuario o contraseña <span class="bold">incorrectos</span>.
                                        </div>

                                        <button @click="attemptLogin" class="btn btn-primary btn-block">Iniciar sesión
                                        </button>
                                    </div>
                                </div>
                                <a href="/password/reset" class="form-small-msg">
                                    ¿Has olvidado tu contraseña?
                                </a>
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
                user: {
                    email: null,
                    password: null,
                    remember: null
                },
                loginError: false
            }
        },
        methods: {
            attemptLogin: function () {
                this.loginError = false;

                axios.post('login', this.user).then((response) => {
                    this.$swal({
                        html: '<span class="welcome-message">¡Bienvenido, ' + response.data + '!</h1>',
                        icon: 'success',
                        timer: 1600,
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        onClose: function () {
                            window.location.href = '/app/';
                        }
                    });
                }).catch((error) => {
                    this.loginError = true;
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    .container .row {
        justify-content: center;
        .background {
            position: fixed;
            top: 64px;
            right: 0;
            left: 0;
            bottom: 0;
            background-image: url('/images/backgrounds/bg-login-min.jpg');
            background-position: center;
            background-color: #376cc8;
            background-size: cover;
        }
    }
    .card {
        margin-top: 10vh;
        position: relative;
        z-index: 1;
        box-shadow: rgba(0, 0, 0, 0.2) 0 0.25em 0.25em;
        h3 {
            margin-bottom: 0.7em;
            font-size: 1.5rem;
        }
        .form-small-msg {
            font-size: 11pt;
        }
        button {
            margin-bottom: 5px;
        }
    }
    .alert {
        border: none;
        border-left: 8px solid #cd2b2b;
        i {
            margin-right: 0.5em;
        }
    }
</style>
