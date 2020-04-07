<template>
    <div class="container-fluid dash-container">
        <div class="row">
            <div class="col">
                <h3 class="tab-content-title">Dashboard</h3>
            </div>
        </div>

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
    .dash-container {
        height: calc(100vh - 72px - 15px - 15px); // 72px (header) + 15px (padding header) + 15px (padding-bottom propio)
        display: flex;
        flex-direction: column;

        .dash-content {
            max-height: 100% !important;
            margin-bottom: 15px;
            flex: 1;
            overflow: hidden
        }
    }
</style>
