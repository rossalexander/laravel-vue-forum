<template>
    <div>
        <!--        Filter through and display each reply -->
        <div v-for="(reply, index) in items" :key="reply.id">
            <reply :data="reply" @deleted="remove(index)"></reply>
        </div>

        <!--        Bind a prop on the paginator to the dataSet values-->
        <!--        Whenever the parent's data changes, it will cascade down to any of the child components-->
        <!--        Therefore, the Paginator component should accept a prop called dataSet -->
        <paginator :dataSet="dataSet" @changed="fetch"></paginator>

        <!--        Catch the NewReply 'created' event and call add() -->
        <new-reply @created="add"></new-reply>
    </div>
</template>

<script>
import Reply from "./Reply"
import NewReply from "./NewReply"
import collection from '../mixins/collection';

export default {
    components: {Reply, NewReply},
    mixins: [collection],
    data() {
        return {dataSet: false}
    },

    created() { // When this component is created,
        this.fetch(); // immediately fetch the reply data that we need.
    },

    methods: {
        fetch(page) {
            axios.get(this.url(page)) // Get the url and have Laravel perform Eloquent query and return collection.
                .then(this.refresh); // Then refresh the data however is appropriate.
        },
        url(page) {
            if (!page) {
               // let query = location.search.match(/page=(\d+)/);
                page = (new URLSearchParams(window.location.search)).get('page');
            }
            return `${location.pathname}/replies?page=${page}`;
        },
        refresh({data}) { // Once we have a response from the fetch call, refresh the component -
            this.dataSet = data;  // by saving the dataSet on the object.
            this.items = data.data;
            window.scrollTo(0, 0);
            // console.log(data);
        }
    }
}
</script>
