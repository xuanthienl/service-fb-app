import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate"; //Lưu thông tin lại, vuex chỉ lưu thông tin tạm thời, F5 sẽ bị mất
import axios from 'axios';
Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        token: localStorage.getItem('access_token') || null,
        user: {}
    },
    getters: {
        loggedIn(state) {
            return state.token !== null
        },
        getUser(state) {
            return state.user
        },
        getCurrencyCount(state) {
            return state.user.coin
        },
        token(state) {
            return state.token
        }
    },
    mutations: {
        retrieveToken(state, token) {
            state.token = token
        },
        addUser(state, user) {
            state.user = user
        },
        destroyToken(state) {
            state.token = null
            state.user = {}
        },
        loadCurrency(state, currency) {
            state.user.coin = currency
        },
        servicePayment(state, total) {
            state.user.coin = state.user.coin - total
        }
    },
    actions: {
        //Register
        register(context, data) {
            return new Promise((resolve, reject) => {
                axios
                .post('/api/register', {
                    username: data.username,
                    email: data.email,
                    password: data.password,
                })
                .then(response => {
                    resolve(response)
                })
                .catch(error => {
                    reject(error)
                })
            })
        },
        //Login
        retrieveToken(context, credentials) {
            return new Promise((resolve, reject) => {
                axios
                .post('/api/login', {
                    email: credentials.email,
                    password: credentials.password,
                })
                .then(response => {
                    const token = response.data.access_token
                    localStorage.setItem('access_token', token)
                    context.commit('retrieveToken', token)
                    context.commit('addUser', response.data.user)
                    resolve(response) //trả về response
                })
                .catch(error => {
                    reject(error) //trả về error
                })
            })
        },
        //Delete token (Logout)
        destroyToken(context) {
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + context.state.token
      
            if (context.getters.loggedIn) {
                return new Promise((resolve, reject) => {
                    axios.post('/api/logout')
                    .then(response => {
                        localStorage.removeItem('access_token')
                        context.commit('destroyToken')
                        resolve(response)
                    })
                    .catch(error => {
                        localStorage.removeItem('access_token')
                        context.commit('destroyToken')
                        reject(error)
                    })
                })
            }
        },
        loadCurrency(context) {
            if (context.getters.loggedIn) {
                return new Promise((resolve, reject) => {
                    axios.get('/api/user/' + context.getters.getUser.username)
                    .then(response => {
                        context.commit('loadCurrency', response.data.user.coin)
                        resolve(response)
                    })
                    .catch(error => {
                        reject(error)
                    })
                })
            }
        },
        reloadUser({commit}, user) {
            commit('addUser', user)
        },
        servicePayment({commit}, total) {
            commit('servicePayment', total)
        }
    },
    plugins: [createPersistedState()],
});

export default store;