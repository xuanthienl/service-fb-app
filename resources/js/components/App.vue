<template>
    <v-app>
        <my-navbar :key="getUser"></my-navbar>
        <div class="container-fluid page-body-wrapper">
            <!-- <my-settings-panel></my-settings-panel> -->
            <my-sidebar :key="getUser"></my-sidebar>
            <div class="main-panel">
                <router-view></router-view>
                <!-- <my-footer></my-footer> -->
            </div>
            <notifications class="notifications" group="foo"/>
        </div>
    </v-app>
</template>
<script>
    export default {
        data() {
            return {
                user: this.$store.getters.getUser
            }
        },
        mounted() {
            this.$store.dispatch('loadCurrency')
        },
        computed: {
            loggedIn() {
                return this.$store.getters.loggedIn
            },
            getUser() {
                return Object.keys(this.$store.getters.getUser).length;
            }
        },
    }
</script>