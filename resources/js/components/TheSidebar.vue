<template>
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
            <li class="nav-item">
                <div class="sidebar-profile p-4" v-if="loggedIn">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="sidebar-profile-name">
                            <p class="sidebar-name">{{user.name != null ? user.name : user.username}}</p>
                            <p class="sidebar-designation">Welcome</p>
                        </div>
                        
                        <button class="navbar-toggler navbar-toggler align-self-center d-none d-lg-flex" type="button" data-toggle="minimize">
                            <i class="fa-solid fa-bars menu-icon"></i>
                        </button>
                    </div>
                </div>
            </li>
            <li class="nav-item" v-bind:class="[$route.name == 'home' ? 'active' : '']">
                <router-link :to="{name: 'home'}" class="nav-link">
                    <i class="fa-solid fa-house menu-icon"></i>
                    <span class="menu-title">Trang chủ</span>
                </router-link>
            </li>
            <li class="nav-item" v-if="loggedIn" v-bind:class="[$route.name == 'payment' ? 'active' : '']">
                <router-link :to="{name: 'payment'}" class="nav-link">
                    <i class="fa-solid fa-coins menu-icon"></i>
                    <span class="menu-title">{{ user.coin | currencyFormat }} Xu<span class="badge badge-primary ml-3">Nạp Xu</span></span>
                </router-link>
            </li>
            <li class="nav-item" v-bind:class="[$route.name == 'contact' ? 'active' : '']">
                <router-link :to="{name: 'contact'}" class="nav-link">
                    <i class="fa-solid fa-headset menu-icon"></i>
                    <span class="menu-title">Trợ giúp & hỗ trợ</span>
                </router-link>
            </li>

            <p class="sidebar-menu-title">Dịch vụ</p>
            <li class="nav-item" v-bind:class="[$route.name == 'buff-comment' ? 'active' : '']">
                <router-link :to="{name: 'buff-comment'}" class="nav-link">
                    <i class="fa-solid fa-message menu-icon"></i>
                    <span class="menu-title">Buff comment Facebook</span>
                </router-link>
            </li>
            <li class="nav-item" v-bind:class="[$route.name == 'buff-share' ? 'active' : '']">
                <router-link :to="{name: 'buff-share'}" class="nav-link">
                    <i class="fa-solid fa-share-from-square menu-icon"></i>
                    <span class="menu-title">Buff share Facebook</span>
                </router-link>
            </li>

            <p class="sidebar-menu-title" v-if="loggedIn && user.roles == '1'">Cài đặt</p>
            <li class="nav-item" v-bind:class="[$route.name == 'settings-facebook' ? 'active' : '']" v-if="loggedIn && user.roles == '1'">
                <router-link :to="{name: 'settings-facebook'}" class="nav-link">
                    <i class="fa-solid fa-gear menu-icon"></i>
                    <span class="menu-title">Facebook</span>
                </router-link>
            </li>
            <li class="nav-item" v-bind:class="[$route.name == 'settings-notification' ? 'active' : '']" v-if="loggedIn && user.roles == '1'">
                <router-link :to="{name: 'settings-notification'}" class="nav-link">
                    <i class="fa-solid fa-bullhorn menu-icon"></i>
                    <span class="menu-title">Thông báo</span>
                </router-link>
            </li>
            <li class="nav-item" v-bind:class="[$route.name == 'settings-payment' ? 'active' : '']" v-if="loggedIn && user.roles == '1'">
                <router-link :to="{name: 'settings-payment'}" class="nav-link">
                    <i class="fa-solid fa-cart-shopping menu-icon"></i>
                    <span class="menu-title">Phương thức thanh toán</span>
                </router-link>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
                tracks: this.$store.getters.getTracks == '' ? '' : this.$store.getters.getTracks,
            }
        },
        computed: {
            loggedIn() {
                return this.$store.getters.loggedIn
            },
            getUser() {
                return this.$store.getters.getUser
            }
        },
        filters: {
            currencyFormat(value) {
                return Intl.NumberFormat('en-US').format(value)
            }
        }
    }
</script>
