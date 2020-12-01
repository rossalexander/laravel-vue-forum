<template>
    <button @click="toggle" type="submit" :class="classes">
        <i class="fas fa-heart"></i>
        <span v-text="count"></span>
    </button>
</template>

<script>
export default {
    props: ['reply'],
    data() {
        return {
            count: this.reply.favorites_count,
            active: this.reply.is_favorited // 'active' used to be is_favorited', but renamed for simplicity
        }
    },

    computed: {
        classes() {
            return ['btn', this.active ? 'btn-outline-primary' : 'btn-outline-info'];
        },
        endpoint() {
            return '/replies/' + this.reply.id + '/favorites/';
        }
    },

    methods: {
        toggle() {
            return this.active ? this.destroy() : this.create();
        },

        create() {
            axios.post(this.endpoint)
            this.active = true;
            this.count++;
        },

        destroy() {
            axios.delete(this.endpoint) // create PHP endpoint
            this.active = false;
            this.count--;
        }
    }
}
</script>
