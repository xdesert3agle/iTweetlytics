<template>
    <div class="row">
        <div class="col-12">
            <div class="row no-gutters card-row">
                <div class="col-md-4 col-12">
                    <graph-card id="friends"
                                :user="user" :stat_endpoint="'/ajax/profile/' + user.current_twitter_profile[0].id + '/reports/friends/'"
                                card_title="Seguidos"
                                modal_title="Seguidos">

                        <template slot="modal-trigger">
                            <button class="btn-text">Gestionar seguidos</button>
                        </template>
                    </graph-card>
                </div>
                <div class="col-md-4 col-12">
                    <graph-card id="befriends"
                                :user="user" :stat_endpoint="'/ajax/profile/' + user.current_twitter_profile[0].id + '/reports/befriends/'"
                                card_title="Seguidos"
                                modal_title="Seguidos">

                        <template slot="modal-trigger">
                            <button class="btn-text">Detalles</button>
                        </template>
                    </graph-card>
                </div>
                <div class="col-md-4 col-12">
                    <graph-card id="unfriends"
                                :user="user" :stat_endpoint="'/ajax/profile/' + user.current_twitter_profile[0].id + '/reports/unfriends/'"
                                card_title="Dejados de seguir"
                                modal_title="Dejados de seguir">

                        <template slot="modal-trigger">
                            <button class="btn-text">Detalles</button>
                        </template>
                    </graph-card>
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
    }
</script>


<style lang="scss" scoped>
    $primaryColor: #7642FF;
    $textColor: #3E396B;

    .card-row {
        div[class*="col"] {
            display: flex;
            flex-direction: column;

            &:not(:first-child) {
                padding-left: 10px;
            }
        }

        &:not(:first-child) {
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            div[class*="col"]:not(:first-child) {
                margin-top: 10px!important;
                padding-left: 0!important;
            }
        }
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
</style>
