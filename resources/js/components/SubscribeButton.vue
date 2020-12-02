<template>
    <button :class="classes" @click="subscribe">Subscribe</button>
</template>

<script>
export default {
    name: "SubscribeButton",

    props: ['active'], // We use this 'active' property to (1) determine which CSS class to apply

    computed: {
        classes() {
            return ['btn', this.active ? 'btn-outline-primary' : 'btn-primary'];
        }
    },

    methods: {
        subscribe() {
            // (2) and which request type should be used
            axios[this.active ? 'delete' : 'post'](location.pathname + '/subscriptions');
            this.active = !this.active;
            // flash("Subscribed!");
        }
    }
}
</script>

<style scoped>

</style>
