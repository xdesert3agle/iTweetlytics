<template>
    <button type="button" @click="redirect">
        <slot></slot>
    </button>
</template>

<script>
    export default {
        props: {
            'href': String,
        },
        methods: {
            redirect() {
                window.location.href = this.href;
            }
        }
    }
</script>

<style lang="scss" scoped>

</style>
