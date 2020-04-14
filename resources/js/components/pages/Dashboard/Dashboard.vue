<template>
    <div class="container-fluid dash-container">
        <div class="row no-gutters dash-content">
            <div class="col-md-3 col-12 twitter-column">
                <timeline :timeline="timeline"></timeline>
            </div>
            <div class="col-md-3 col-12 twitter-column">
                <mentions :mentions="mentions"></mentions>
            </div>
            <div class="col-md-3 col-12 twitter-column">
                <lists :lists="lists"></lists>
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
        height: calc(100vh - 72px - 15px - 15px); // 72px (header) + 15px (padding header) + 15px (padding-bottom propio)
        padding: 15px;

        .dash-content {
            max-height: 100% !important;
            flex: 1;
            overflow: hidden;

            .twitter-column {
                overflow: hidden;
            }
        }

        ::-webkit-scrollbar {
            width: 5px;  /* Remove scrollbar space */
            padding-left: 50px;
            background-color: lighten(black, 85%);
            //background: transparent;  /* Optional: just make scrollbar invisible */
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
