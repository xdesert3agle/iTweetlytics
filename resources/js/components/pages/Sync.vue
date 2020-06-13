<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card animated fadeInRight fast">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <div class="col">
                                        <h1>¡Bienvenid@ a iTweetlytics, {{ user.name }}!</h1>
                                        <h3>
                                            Sincroniza tu perfil de Twitter para comenzar
                                            <i class="fab fa-twitter"></i>
                                        </h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <span>
                                            <small>
                                                ¿No eres {{ user.name }}? Haz click
                                                <form action="logout" method="POST">
                                                    <csrf></csrf>
                                                    <button type="submit" class="link">aquí</button>
                                                </form>
                                                para cerrar sesión.
                                            </small>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4 button-sync-now-container">
                                <button-href href="/twitter/login" class="btn btn-primary btn-big">
                                    <i class="fa fa-xs fa-sync"></i>
                                    Sincronizar ahora
                                </button-href>
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
            'user'
        ],
        methods: {
            logout() {
                this.$el.submit();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .container {
        height: 100vh;

        .row {
            height: 100%;
            align-items: center;

            .col {

                .card {
                    box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.07), 0 3px 1px -2px rgba(0, 0, 0, 0.02), 0 1px 5px 0 rgba(0, 0, 0, 0.02);

                    h1 {
                        font-size: 27pt;
                    }

                    h3 {
                        color: #34393d;
                    }

                    .fa-twitter {
                        color: #00acee;
                        margin-left: 4px;
                    }

                    button.link {
                        padding: 0!important;
                        border: 0!important;
                        background: unset;
                        color: #7642FF;
                        font-size: 10pt;
                    }

                    form {
                        display: inline;
                    }

                    .button-sync-now-container {
                        display: flex;
                        justify-content: center;

                        .btn-big {
                            display: flex;
                            align-items: center;
                            font-size: 16pt;

                            i {
                                margin-right: 10px;
                            }
                        }
                    }
                }
            }
        }
    }

</style>
