<template>
	<nav class="navbar navbar-expand-lg main-navbar">
		<form class="form-inline mr-auto">
			<ul class="navbar-nav mr-3">
				<li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
				<li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
			</ul>
			<div class="search-element">
				<input class="form-control" type="search" placeholder="Search" aria-label="Search" data-width="250">
				<button class="btn" type="submit"><i class="fas fa-search"></i></button>
				<div class="search-backdrop"></div>
				<div class="search-result">
					<div class="search-header">
						Lịch sử tìm kiếm
					</div>
					<div class="search-item">
						<router-link :to="{name: 'buff-share'}">buff share</router-link>
						<router-link :to="{name: 'buff-share'}" class="search-close"><i class="fas fa-times"></i></router-link>
					</div>
					<div class="search-item">
						<router-link :to="{name: 'buff-comment'}">buff comment</router-link>
						<router-link :to="{name: 'buff-comment'}" class="search-close"><i class="fas fa-times"></i></router-link>
					</div>

					<div class="search-header">
						Kết quả
					</div>
					<div class="search-item">
						<router-link :to="{name: 'buff-share'}">
							<img class="mr-3 rounded" width="30" src="/template_stisla/dist/assets/img/products/product-2-50.png" alt="product">
							Buff Share Facebook
						</router-link>
					</div>
					<div class="search-item">
						<router-link :to="{name: 'buff-comment'}">
							<img class="mr-3 rounded" width="30" src="/template_stisla/dist/assets/img/products/product-2-50.png" alt="product">
							Buff Comment Facebook
						</router-link>
					</div>
					
					<div class="search-header">
						DỰ ÁN
					</div>
					<div class="search-item">
						<router-link :to="{name: 'home'}">
							<div class="search-icon bg-danger text-white mr-3">
								<i class="fas fa-code"></i>
							</div>
							SupportLive
						</router-link>
					</div>
				</div>
			</div>
		</form>

		<ul class="navbar-nav navbar-right">
			<li class="dropdown dropdown-list-toggle">
				<a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle">
					<i class="far fa-envelope"></i>
				</a>
				<!-- <div class="dropdown-menu dropdown-list dropdown-menu-right">
					<div class="dropdown-header">Messages
					<div class="float-right">
						<a href="#">Mark All As Read</a>
					</div>
					</div>
					<div class="dropdown-list-content dropdown-list-message">
					<a href="#" class="dropdown-item dropdown-item-unread">
						<div class="dropdown-item-avatar">
						<img alt="image" src="assets/img/avatar/avatar-1.png" class="rounded-circle">
						<div class="is-online"></div>
						</div>
						<div class="dropdown-item-desc">
						<b>Kusnaedi</b>
						<p>Hello, Bro!</p>
						<div class="time">10 Hours Ago</div>
						</div>
					</a>
					</div>
					<div class="dropdown-footer text-center">
					<a href="#">View All <i class="fas fa-chevron-right"></i></a>
					</div>
				</div> -->
			</li>

			<li class="dropdown dropdown-list-toggle">
				<a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg">
					<i class="far fa-bell"></i>
				</a>
				<!-- <div class="dropdown-menu dropdown-list dropdown-menu-right">
					<div class="dropdown-header">Notifications
						<div class="float-right">
							<a href="#">Mark All As Read</a>
						</div>
					</div>
					<div class="dropdown-list-content dropdown-list-icons">
						<a href="#" class="dropdown-item dropdown-item-unread">
							<div class="dropdown-item-icon bg-primary text-white">
							<i class="fas fa-code"></i>
							</div>
							<div class="dropdown-item-desc">
							Template update is available now!
							<div class="time text-primary">2 Min Ago</div>
							</div>
						</a>
					</div>
					<div class="dropdown-footer text-center">
						<a href="#">View All <i class="fas fa-chevron-right"></i></a>
					</div>
				</div> -->
			</li>

			<li class="dropdown dropdown-list-toggle">
				<a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg nav-link-user">
					<img alt="image" src="/template_stisla/dist/assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
					<div class="d-sm-none d-lg-inline-block" v-if="loggedIn">Xin chào, {{ user.name !== null ? user.name : user.username }}</div>
				</a>
				<div class="dropdown-menu dropdown-list dropdown-menu-right">
					<div class="dropdown-title">Welcome to SupportLive</div>
					<router-link :to="{name: 'setting-user', params: { username: user.username } }" class="dropdown-item has-icon d-flex align-items-center dropdown-item-text" v-if="loggedIn">
						<i class="far fa-user"></i>
						<span>Trang cá nhân</span>
					</router-link>
					<router-link :to="{name: 'logout'}"  class="dropdown-item has-icon d-flex align-items-center dropdown-item-text" v-if="loggedIn">
						<i class="fas fa-sign-out-alt"></i>
						<span>Đăng xuất</span>
					</router-link>

					<router-link :to="{name: 'register'}"  class="dropdown-item has-icon d-flex align-items-center dropdown-item-text" v-if="!loggedIn">
						<i class="fa-solid fa-user-plus"></i>
						<span>Đăng ký</span>
					</router-link>
					<router-link :to="{name: 'login'}"  class="dropdown-item has-icon d-flex align-items-center dropdown-item-text" v-if="!loggedIn">
						<i class="fa-solid fa-right-to-bracket"></i>
						<span>Đăng nhập</span>
					</router-link>
				</div>
			</li>
		</ul>
	</nav>
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
        }
    }
</script>