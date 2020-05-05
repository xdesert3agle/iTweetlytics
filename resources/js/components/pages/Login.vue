<template>
    <div class="container-fluid login-container">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col form-right-side">
                                    <h3>Iniciar sesión</h3>

                                    <div class="form-group">
                                        <input v-model="user.email" id="email" type="email" class="form-control" placeholder="Dirección de correo electrónico" required autocomplete="email" autofocus>
                                    </div>

                                    <div class="form-group">
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

                                            <button @click="attemptLogin" class="btn btn-primary btn-block">
                                                Entrar
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
            attemptLogin: function() {
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
    .login-container {
        height: calc(100vh - 72px);

        display: flex;
        justify-content: center;
        align-items: center;

        background: url('/images/bg-login.jpg');
        background-size: cover;

        .container .row {
            justify-content: center;
        }
    }

    .card {
        margin-bottom: calc(33vh - 72px);

        .form-right-side {
            h3 {
                margin-bottom: 0.7em;
                text-align: center;
            }
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
