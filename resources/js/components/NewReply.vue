<template>
    <div>

        <div v-if="signedIn">
            <div class="form-group">
                <textarea name="body"
                          id="body"
                          class="form-control"
                          placeholder="Have something to say?"
                          cols="30"
                          rows="5"
                          required
                          v-model="body">
                </textarea>
            </div>
            <!--             1. When we click the button-->
            <!--             2. Get the value of the text area and post it to endpoint -->
            <button class="btn btn-primary mt-2"
                    type="submit"
                    @click="addReply">Post
            </button>
        </div>

        <p v-else class="text-center">Please <a href="/login">sign in</a> to comment.</p>
    </div>
</template>

<script>
export default {
    data() {
        return {
            body: ''
        }
    },

    computed: {
        signedIn() {
            return window.App.signedIn;
        }
    },

    methods: {
        addReply() {
            axios.post(location.pathname + '/replies', {body: this.body}) // Send body to endpoint
                .then(response => { // 3. Once that has completed, accept the data
                    this.body = ''; // 4. We've added the body, so we can reset the body back to empty string
                    flash('Your reply has been posted!'); // 5. Give user flash message

                    // We need NewReply to communicate with our Replies.vue collection
                    // 6. We emit an event 'created' and send through the data response from the server
                    this.$emit('created', response.data); // could also use ES2015 shorthand {data}
                });
        }
    }
}
</script>
