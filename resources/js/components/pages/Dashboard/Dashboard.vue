<template>
    <div class="container-fluid dash-container">
        <div class="row no-gutters dash-content">
            <div class="col-md-3 col-12 twitter-column">
                <tl :timeline="timeline" :user="user"></tl>
            </div>
            <div class="col-md-3 col-12 twitter-column">
                <mentions :mentions="mentions" :user="user"></mentions>
            </div>
            <div class="col-md-3 col-12 twitter-column">
                <lists :lists="lists" :user="user"></lists>
            </div>
            <div class="col-md-3 col-12 twitter-column">
                <dms :chats="chats" :user="user"></dms>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'user',
            'timeline',
            'mentions',
            'chats',
            'lists',
            'loadtime'
        ]
    }
</script>

<style lang="scss" scoped>
    $primaryColor: #7642FF;

    .dash-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
        padding: 15px;

        .dash-content {
            max-height: 100% !important;
            flex: 1;
        }

        ::-webkit-scrollbar {
            width: 5px;  /* Remove scrollbar space */
            padding-left: 50px;
            background-color: lighten(black, 85%);
        }

        /* Optional: show position indicator in red */
        ::-webkit-scrollbar-thumb {
            background: lighten($primaryColor, 8%);
            margin-left: 10px;
        }
    }

    @media (max-width: 576px) {
        .dash-container {
            height: 100%;
        }

        .twitter-column:not(:first-child) {
            margin-top: 2em;
        }
    }
</style>
