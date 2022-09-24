<template>
    <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
			<div class="col-lg-5 mx-auto">
				<div class="auth-form-light text-left py-5 px-4 px-sm-5">
					<div class="brand-logo navbar-brand brand-logo text-img d-flex justify-content-center">Supportlive.Az</div>
					<h4>Mới ở đây?</h4>
					<h6 class="font-weight-light">Đăng ký rất dễ dàng. Nó chỉ mất một vài bước</h6>
					<form class="pt-3" @submit.prevent="register">
						<div class="form-group">
							<input type="text" class="form-control form-control-lg" placeholder="Username" v-model="username">
						</div>
						<div class="form-group">
							<input type="email" class="form-control form-control-lg" placeholder="Email" v-model="email">
						</div>
						<div class="form-group">
							<input type="password" class="form-control form-control-lg" placeholder="Password" v-model="password">
						</div>
						<div class="mb-4">
							<div class="form-check">
								<label class="form-check-label text-muted">
								<input type="checkbox" class="form-check-input">Tôi đồng ý với tất cả các Điều khoản & Điều kiện</label>
							</div>
						</div>
						<label class="form-check-label text-primary" v-if="message != ''">{{message}}</label>
						<div class="mt-3">
							<button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">ĐĂNG KÝ</button>
						</div>
						<div class="text-center mt-4 font-weight-light">
							Bạn có sẵn sàng để tạo một tài khoản? <router-link :to="{name: 'login'}" class="text-primary font-weight-bold">Đăng nhập</router-link>
						</div>
					</form>
				</div>
			</div>
        </div>
    </div>
    <!-- <div class="box-content p-5 shadow text-center form-login">
        <form @submit.prevent="register">
            <h3>Register</h3>
            <div class="d-flex align-items-center rounded-pill box-input">
                <i class="fas fa-user-circle"></i>
                <input type="text" placeholder="your username." v-model="username">
            </div>
            <div class="d-flex align-items-center rounded-pill box-input">
                <i class="fas fa-envelope-open rounded-pill"></i>
                <input type="email" placeholder="your email." v-model="email">
            </div>
            <div class="d-flex align-items-center rounded-pill box-input">
                <i class="fas fa-key rounded-pill"></i>
                <input type="password" placeholder="your password." v-model="password">
            </div>
            <p v-if="message != ''" class="text-message rounded-pill">{{message}}</p>
            <button type="submit" class="w-100 btn rounded-pill">Register</button>
            <div>
                <router-link :to="{name: 'login'}">or... Login</router-link>
            </div>
        </form>
    </div> -->
</template>

<script>
    export default {
        data() {
            return {
                username:'',
                email:'',
                password:'',
                message: '',
            }
        },
        methods: {
            register() {
                this.$store.dispatch('register', {
                    username: this.username,
                    email: this.email,
                    password: this.password,
                })
                .then(response => {
                    this.$router.push({ name: 'login' })
                })
                .catch((error) => {
                    if (error.response) {
                        if(error.response.data.message) {
                            this.message = error.response.data.message
                        } else {
                            this.message = ''
                        }
                    } else {
                        console.log(error);
                    }
                });
            }
        }
    }
</script>