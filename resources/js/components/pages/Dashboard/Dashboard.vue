<template>
    <div class="container-fluid dash-container">
        <div class="row dash-content">
            <div class="col-md-3 col-12">
                <timeline :timeline="timeline"></timeline>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'timeline'
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
            overflow: hidden
        }

        ::-webkit-scrollbar {
            width: 2px;  /* Remove scrollbar space */
            padding-left: 50px;
            //background: transparent;  /* Optional: just make scrollbar invisible */
        }

        /* Optional: show position indicator in red */
        ::-webkit-scrollbar-thumb {
            background: lighten($primaryColor, 8%);
            margin-left: 10px;
        }
    }
</style>
