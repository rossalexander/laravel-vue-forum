<template>
    <div>
        <div v-if="notifications.length" class="dropdown">
            <button class="btn dropdown-toggle text-primary" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-bell"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a @click="markAsRead(notification)"
                   :href="notification.data.link"
                   v-for="notification in notifications"
                   v-text="notification.data.message"
                   class="dropdown-item"></a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "UserNotifications",

    data() {
        return {notifications: false}
    },

    created() {
        axios.get("/profiles/" + window.App.user.name + "/notifications")
            .then(response => this.notifications = response.data);
    },

    methods: {
// /profiles/{$user->name}/notifications/" . $user->unreadNotifications->first()->id
        markAsRead(notification) {
            axios.delete('/profiles/' + window.App.user.name + '/notifications/' + notification.id)
        }
    }
}
</script>

<style scoped>

</style>
