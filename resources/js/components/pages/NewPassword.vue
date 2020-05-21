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
                                <form action="/password/reset" method="POST">
                                    <input type="hidden" name="_token" :value="csrf">
                                    <input type="hidden" name="token" :value="token">
                                    <div class="form-group">
                                        <label for="email" class="required">Email</label>
                                        <input id="email" name="email" type="email" class="form-control" placeholder="Dirección de correo electrónico" :value="email" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="required">Nueva contraseña</label>
                                        <input id="password" type="password" class="form-control" name="password" placeholder="Introduce tu nueva contraseña" required autocomplete="new-password">
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="required">Confirmar nueva contraseña</label>
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirma tu nueva contraseña" required autocomplete="new-password">
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <button type="submit" class="btn btn-neutral btn-block">
                                                Cambiar contraseña
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
        props: [
            'email',
            'token'
        ],
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            background-image: url('/images/backgrounds/bg-password-recover.jpg');
            background-position: center;
            background-color: #dd6451;
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
