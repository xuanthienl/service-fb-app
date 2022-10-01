<template>
    <v-app>
        <router-view v-if="$route.name == 'login' || $route.name == 'register'" :key="getUser"></router-view>
        <div v-else class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <my-navbar :key="getUser"></my-navbar>
            <my-sidebar :key="getUser"></my-sidebar>
            <div class="main-content">
                <router-view :key="getUser"></router-view>
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