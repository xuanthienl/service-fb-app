require('./bootstrap');
window.Vue = require('vue').default;

import App from './components/App';

import axios from 'axios';
import VueAxios from 'vue-axios';
Vue.use(VueAxios, axios);

import store from "./store";
Vue.use(store)

import Vuetify from '../plugins/vuetify';
Vue.use(Vuetify);

import MetaInfo from 'vue-meta-info'
Vue.use(MetaInfo)

import Notifications from 'vue-notification'
Vue.use(Notifications)

import router from './router';
Vue.use(router)

Vue.component('my-settings-panel', require('./components/TheSettingsPanel.vue').default);
Vue.component('my-sidebar', require('./components/TheSidebar.vue').default);
Vue.component('my-navbar', require('./components/TheNavbar.vue').default);
Vue.component('my-footer', require('./components/TheFooter.vue').default);

const app = new Vue({
    el: '#app',
    store,
    router,
    render: h => h(App)
});

// - Install project Laravel
// - Install laravel/ui (laravel 7.0 thì chọn laravel/ui 2.0)
// - Run php artisan ui vue
// - Run npm install
// - Run npm run dev (lệnh này tạo ra 2 forder css and js trong /public của Laravel)
// - Chú ý: use npm i vuetify-loader@1.4.3
// Key path "file:///app/storage/oauth-public.key" does not exist or is not readable: Run heroku ps:exec -a your_app_name -> Run php artisan passport:keys

// RUN SOURCE (PHP 8.0.18 - NODEJS 16.17.0)
// - composer update
// - php artisan passport:keys
// - php artisan migrate
// - php artisan passport:install
// - php artisan db:seed
// - npm install
// - npm run dev


// - Để phản ánh khi Input có sử thay đổi -> có thể dùng watch