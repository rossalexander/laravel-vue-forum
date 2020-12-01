<template>
    <ul class="pagination" v-if="shouldPaginate">
        <li v-show="prevUrl" class="page-item">
            <a href="#" class="page-link" @click.prevent="page--">&lsaquo; Previous</a>
        </li>
        <li v-show="nextUrl" class="page-item">
            <a href="#" class="page-link" @click.prevent="page++">Next &rsaquo;</a>
        </li>
    </ul>
</template>

<script>
export default {

    props: ['dataSet'],

    data() {
        return {
            page: 1,
            prevUrl: false,
            nextUrl: false,
        }
    },

    // If the Replies component updates itself and fetches new data, that change will cascade down to this Paginator
    // component, triggering the watch method, which will always keep the paginator up to date.
    watch: {
        dataSet() {
            this.page = this.dataSet.current_page;
            this.prevUrl = this.dataSet.prev_page_url;
            this.nextUrl = this.dataSet.next_page_url;
        },

        page() { // Keep an eye on the page property
            // if it ever changes,
            this.broadcast().updateUrl(); // broadcast the event that says, "the user has requested a new page, you (Replies)
            // can be responsible for that, and when you have new data, give it to me (paginator) and I will update myself.
            // Keep Replies responsible for requesting replies. It's not the responsibility of the Paginator to do that.
        }
    },

    computed: {
        shouldPaginate() {
            return !!this.prevUrl || !!this.nextUrl;
        }
    },

    methods: {
        broadcast() {
            return this.$emit('changed', this.page); // emit an event called 'updated' and send user-requested page
        },
        updateUrl() {
            history.pushState(null, null, '?page=' + this.page);
        }
    }
}
</script>
