<template>
    <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
            <div class="sidebar-brand">
                <a href="/" class="text-img">SupportLive</a>
            </div>
            <div class="sidebar-brand sidebar-brand-sm">
                <a href="/" class="text-img">Az.Me</a>
            </div>

            <ul class="sidebar-menu">
                <li class="menu-header">Bảng điều khiển</li>
                <li v-bind:class="[$route.name == 'home' ? 'active' : '']">
                    <router-link :to="{name: 'home'}" class="nav-link">
                        <i class="fa fa-solid fa-house"></i>
                        <span>Trang chủ</span>
                    </router-link>
                </li>
                <li v-bind:class="[$route.name == 'contact' ? 'active' : '']">
                    <router-link :to="{name: 'contact'}" class="nav-link">
                        <i class="fa fa-solid fa-headset"></i>
                        <span>Trợ giúp & hỗ trợ</span>
                    </router-link>
                </li>

                <li class="menu-header">Ví</li>
                <li>
                    <a class="nav-link">
                        <i class="fa fa-solid fa-coins"></i>
                        <span>{{ loggedIn === true ? user.coin : 0 | currencyFormat }} Xu</span>
                    </a>
                </li>
                <li v-bind:class="[$route.name == 'payment' ? 'active' : '']">
                    <router-link :to="{name: 'payment'}" class="nav-link">
                        <i class="fa fa-solid fa-wallet"></i>
                        <span>Nạp Xu</span>
                    </router-link>
                </li>

                <li class="menu-header">DỊCH VỤ</li>
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown">
                        <i class="fa fa-brands fa-square-facebook"></i>
                        <span>Facebook</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li v-bind:class="[$route.name == 'buff-share' ? 'active' : '']">
                            <router-link :to="{name: 'buff-share', query: { type: 'share' }}" class="nav-link">
                                <i class="fa fa-solid fa-share-from-square"></i>
                                <span>Buff Share</span>
                            </router-link>
                        </li> 
                        <li v-bind:class="[$route.name == 'buff-comment' ? 'active' : '']">
                            <router-link :to="{name: 'buff-comment', query: { type: 'comment' }}" class="nav-link">
                                <i class="fa fa-solid fa-message"></i>
                                <span>Buff Comment</span>
                            </router-link>
                        </li>
                        <li v-bind:class="[$route.name == 'buff' && $route.query.type == 'view' ? 'active' : '']">
                            <router-link :to="{name: 'buff', query: { type: 'view' }}" class="nav-link">
                                <i class="fa fa-solid fa-users-viewfinder"></i>
                                <span>Buff View</span>
                            </router-link>
                        </li>
                        <li v-bind:class="[$route.name == 'buff' && $route.query.type == 'like' ? 'active' : '']">
                            <router-link :to="{name: 'buff', query: { type: 'like' }}" class="nav-link">
                                <i class="fa fa-regular fa-thumbs-up"></i>
                                <span>Buff Like</span>
                            </router-link>
                        </li>
                        <li v-bind:class="[$route.name == 'buff' && $route.query.type == 'live' ? 'active' : '']">
                            <router-link :to="{name: 'buff', query: { type: 'live' }}" class="nav-link">
                                <i class="fa fa-solid fa-eye"></i>
                                <span>Buff Mắt</span>
                            </router-link>
                        </li>
                    </ul>
                </li>

                <li class="menu-header" v-if="loggedIn && user.roles == '1'">Quản lý</li>
                <li v-bind:class="[$route.name == 'manage-user' ? 'active' : '']" v-if="loggedIn && user.roles == '1'">
                    <router-link :to="{name: 'manage-user'}" class="nav-link">
                        <i class="fa fa-solid fa-house-user"></i>
                        <span>Users</span>
                    </router-link>
                </li>

                <li class="menu-header" v-if="loggedIn && user.roles == '1'">Cài đặt</li>
                <li v-bind:class="[$route.name == 'settings-facebook' ? 'active' : '']" v-if="loggedIn && user.roles == '1'">
                    <router-link :to="{name: 'settings-facebook'}" class="nav-link">
                        <i class="fa fa-brands fa-square-facebook"></i>
                        <span>Facebook</span>
                    </router-link>
                </li>
                <li v-bind:class="[$route.name == 'settings-notification' ? 'active' : '']" v-if="loggedIn && user.roles == '1'">
                    <router-link :to="{name: 'settings-notification'}" class="nav-link">
                        <i class="fa fa-solid fa-bell"></i>
                        <span>Thông báo</span>
                    </router-link>
                </li>
                <li v-bind:class="[$route.name == 'settings-payment' ? 'active' : '']" v-if="loggedIn && user.roles == '1'">
                    <router-link :to="{name: 'settings-payment'}" class="nav-link">
                        <i class="fa fa-solid fa-cart-shopping"></i>
                        <span>Thanh toán</span>
                    </router-link>
                </li>
            </ul>    
        </aside>
      </div>
</template>

<script>
    export default {
        data() {
            return {
                user:this.$store.getters.getUser,
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
