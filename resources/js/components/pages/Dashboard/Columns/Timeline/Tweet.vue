<template>
    <div class="row tweet-container">
        <div class="col-md-12 tweet-wrapper">
            <div class="card tweet-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-2">
                            <img :src="tweet.user.profile_image_url" :alt="'Imagen de perfil de @' + tweet.user.screen_name">
                        </div>
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <span class="name">{{ tweet.user.name }}</span>
                                    <span class="screen-name text-muted">@{{ tweet.user.screen_name }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <span class="tweet-text">{{ tweet.full_text }}</span>
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
        props: [
            'tweet'
        ]
    }
</script>

<style lang="scss" scoped>
    .tweet-container {
        .tweet-wrapper {
            .tweet-card {
                border-radius: 0;

                .card-body {
                    padding: 15px;

                    .name {
                        font-weight: bold;
                    }

                    .screen-name {
                        color: #a7a2ce;
                    }

                    .tweet-text {

                    }
                }
            }
        }

        &:not(:first-child) {
            .tweet-card {
                border-top: none;
            }
        }
    }
</style>
