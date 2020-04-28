<template>
    <div class="button-modal-container">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal">
            <slot name="button-text"></slot>
        </button>

        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-label">{{ title }}</h5>
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
            'title': String,
        }
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    .modal-body {
        max-height: 500px;
        overflow-y: scroll;
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
</style>
