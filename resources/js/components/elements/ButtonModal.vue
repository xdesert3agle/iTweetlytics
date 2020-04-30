<template>
    <div class="button-modal-container">
        <button v-if="button" type="button" class="btn btn-primary" data-toggle="modal" :data-target="'#' + id">
            <slot name="button"></slot>
        </button>
        <a v-else class="element" href="" data-toggle="modal" :data-target="'#' + id">
            <slot name="button"></slot>
        </a>

        <div class="modal fade" :id="id" tabindex="-1" role="dialog" :aria-labelledby="id + '-label'" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" :id="id + '-label'">{{ title }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <slot name="modal-body"></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            'id': String,
            'title': String,
            'button': Boolean
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    .button-modal-container {
        display: inline;

        a.element {
            color: inherit;

            &:hover {
                text-decoration: none;
            }
        }

        .modal {
            .modal-dialog {
                max-width: 600px;

                .modal-content {
                    .modal-body {
                        max-height: 80vh;
                        overflow-y: scroll;
                        overflow-x: hidden;
                    }

                    ::-webkit-scrollbar {
                        width: 10px; /* Remove scrollbar space */
                        padding-left: 50px;
                        background-color: lighten(black, 85%);
                        //background: transparent;  /* Optional: just make scrollbar invisible */
                    }

                    ::-webkit-scrollbar-thumb {
                        background: lighten($primaryColor, 8%);
                        margin-left: 10px;
                    }
                }
            }
        }
    }
</style>
