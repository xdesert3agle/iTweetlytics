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
                                    <label for="email">Email*</label>
                                    <input v-model="user.email" id="email" type="email" class="form-control" placeholder="Dirección de correo electrónico" required autocomplete="email">
                                </div>

                                <div class="form-group">
                                    <label for="email">Contraseña*</label>
                                    <input v-model="user.password" id="password" type="password" class="form-control" placeholder="Contraseña" required autocomplete="current-password">
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="form-group form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember">

                                            <label class="form-check-label" for="remember">
                                                Mantener la sesión iniciada
                                            </label>
                                        </div>

                                        <div v-if="loginError" class="alert alert-danger animated fadeIn faster">
                                            <i class="fa fa-exclamation-circle"></i>
                                            Usuario o contraseña <span class="bold">incorrectos</span>.
                                        </div>

                                        <button @click="attemptLogin" class="btn btn-primary btn-block">Iniciar sesión</button>
                                    </div>
                                </div>
                                <span class="form-small-msg">
                                        ¿Aún no tienes una cuenta? Regístrate
                                        <a href="/#signup">aquí</a>.
                                    </span>
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
                    password: null
                },
                loginError: false
            }
        },
        methods: {
            attemptLogin: function () {
                this.loginError = false;

                axios.post('login', this.user).then((data) => {
                    window.location.href = '/app/0/';
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
            top: 72px;
            right: 0;
            left: 0;
            bottom: 0;
            background-image: url('/images/bg-login-min.jpg');
            background-position: center;
            background-color: #89c8e4;
            background-size: cover;
        }

        label {
            font-size: 12pt;
            font-weight: 500;
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
