<template>
    <div id="errorModal" class="modal fade in" style="display: block;">
        <div class="modal-dialog modal-confirm">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="icon-box">
                        <i class="far fa-4x fa-times-circle"></i>
                    </div>
                </div>
                <div class="modal-body text-center">
                    <h4>Ha ocurrido un error al sincronizar el perfil</h4>
                    <p>El perfil ya se encuentra sincronizado por otro usuario</p>
                    <button class="btn btn-neutral" data-dismiss="modal" @click="redirect">Volver a la aplicación</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            $('#errorModal').modal('show');
        },
        methods: {
            redirect() {
                return window.location.href = "/app";
            }
        }
    }
</script>

<style lang="scss" scoped>
    .container {
        height: 100vh !important;

        .row {
            height: 100% !important;

            .col {
                height: 100% !important;
                display: flex;
                justify-content: center;
                align-items: center;

                h1 {
                    font-size: 24pt;
                    margin: 0!important;
                }
            }
        }
    }

    .modal {
        .modal-dialog {
            margin-top: 0!important;
            top: 25%;

            .modal-content {
                box-shadow: 0 6px 6px 0 rgba(0, 0, 0, 0.07), 0 7px 5px -6px rgba(0, 0, 0, 0.02), 0 5px 9px 0 rgba(0, 0, 0, 0.02);

                .modal-header {
                    background: #e85e6c;

                    .icon-box {
                        width: 100%;
                        display: flex;
                        justify-content: center;
                        color: white;
                    }
                }
            }
        }
    }
</style>
