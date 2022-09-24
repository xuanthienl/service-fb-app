<template>
    <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-5 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                <div class="brand-logo navbar-brand brand-logo text-img d-flex justify-content-center">Supportlive.Az</div>
                <h4 v-if="!forgotPassword">Xin chào! Bắt đầu nào</h4>
                <h6 class="font-weight-light" v-if="!forgotPassword">Đăng nhập để tiếp tục.</h6>
                <h4 v-if="forgotPassword">Xin chào! Hành động này sẽ gửi một mật khẩu mới đến địa chỉ Email của bạn</h4>
                <h6 class="font-weight-light" v-if="forgotPassword">Nhập địa chỉ Email để tiếp tục.</h6>
                <form class="pt-3" @submit.prevent="login" v-if="!forgotPassword">
                    <div class="form-group">
                        <input type="email" class="form-control form-control-lg" placeholder="Email" v-model="email">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control form-control-lg" placeholder="Password" v-model="password">
                    </div>
                    <label class="form-check-label text-primary" v-if="message != ''">{{message}}</label>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">ĐĂNG NHẬP</button>
                    </div>
                    <div class="my-2 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <label class="form-check-label text-muted">
                            <input type="checkbox" class="form-check-input" v-model="remember_me">Giữ cho tôi đăng nhập</label>
                        </div>
                        <a class="auth-link text-black" v-on:click="forgotPassword = true, message = ''">Quên mật khẩu?</a>
                    </div>
                    <div class="text-center mt-4 font-weight-light">
                        Không có tài khoản? <router-link :to="{name: 'register'}" class="text-primary font-weight-bold">Đăng ký</router-link>
                    </div>
                </form>

                <form class="pt-3" @submit.prevent="resetPassword" v-else>
                    <div class="form-group">
                        <input type="email" class="form-control form-control-lg" placeholder="Email" v-model="email">
                    </div>
                    <label class="form-check-label text-primary" v-if="message != ''">{{message}}</label>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">CẤP LẠI MẬT KHẨU</button>
                    </div>
                    <div class="text-center mt-4 font-weight-light">
                        Bạn có sẵn một tài khoản? <a v-on:click="forgotPassword = false, message = ''" class="text-primary font-weight-bold">Đăng nhập</a>
                    </div>
                </form>
            </div>
          </div>
        </div>
    </div>
    <!-- <div class="box-content p-5 shadow text-center form-login">
        <form @submit.prevent="login" v-if="!forgotPassword">
            <h3 >Login</h3>
            <div class="d-flex align-items-center rounded-pill box-input">
                <i class="fas fa-envelope-open"></i>
                <input type="email" placeholder="your email." v-model="email">
            </div>
            <div class="d-flex align-items-center rounded-pill box-input">
                <i class="fas fa-key"></i>
                <input type="password" placeholder="your password." v-model="password">
            </div>

            <div class="d-flex justify-content-between">
                <label class="text-left box-checkbox pb-3">
                    <p>Remember Me</p>
                    <input type="checkbox" v-model="remember_me">
                    <span class="checkmark"></span>
                </label>
                <p class="forgot-pw" v-on:click="forgotPassword = true">forgot password.</p>
            </div>

            <p v-if="message != ''" class="text-message rounded-pill">{{message}}</p>
            <button type="submit" class="w-100 btn rounded-pill">Login</button>
            <div>
                <router-link :to="{name: 'register'}">or... Create new account</router-link>
            </div>
        </form>

        <form @submit.prevent="resetPassword" v-else>
            <h3>Forgot Password</h3>
            <div class="d-flex align-items-center rounded-pill box-input">
                <i class="fas fa-envelope-open"></i>
                <input type="email" placeholder="your email." v-model="email">
            </div>
            <p v-if="message != ''" class="text-message rounded-pill">{{message}}</p>
            <button type="submit" class="w-100 btn rounded-pill">Send password reset email</button>
        </form>
    </div> -->
</template>

<script>
    export default {
        data() {
            return {
                email:'',
                password:'',
                remember_me:'',
                message: '',
                forgotPassword: false,
            }
        },
        methods: {
            login() {
                this.$store.dispatch('retrieveToken', {
                    email: this.email,
                    password: this.password,
                })
                .then(response => {
                    this.$router.push({ name: 'home' }).catch(()=>{});
                })
                .catch((error) => {
                    if (error.response) {
                        if(error.response.data.message) {
                            this.message = error.response.data.message
                        } else {
                            this.message = ''
                        }
                    } else {
                        return
                    }
                });
            },
            resetPassword() {
                axios.post('/api/reset-pasword', {
                    email: this.email,
                })
                .then((response) => {
                    this.forgotPassword = false
                    this.message = ''
                })
                .catch((error) => {
                    if (error.response) {
                        if(error.response.data.message) {
                            this.message = error.response.data.message
                        } else {
                            this.message = ''
                        }
                    } else {
                        return
                    }
                });
            },
        }
    }
</script>