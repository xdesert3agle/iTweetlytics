<template>
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-12">
                <div class="background"></div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h3>Restablecer contraseña</h3>
                                <form action="/password/email" method="POST">
                                    <input type="hidden" name="_token" :value="csrf">
                                    <div class="form-group">
                                        <label for="email" class="required">Email</label>
                                        <input id="email" name="email" type="email" class="form-control" placeholder="Dirección de correo electrónico" required autocomplete="email">
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                Solicitar recuperación de contraseña
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                            window.location.href = '/app/0/';
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
            top: 72px;
            right: 0;
            left: 0;
            bottom: 0;
            background-image: url('/images/bg-login-min.jpg');
            background-position: center;
            background-color: #89c8e4;
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
