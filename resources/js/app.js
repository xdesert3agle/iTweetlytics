/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/HomePage.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Páginas
Vue.component('HomePage', require('./components/HomePage.vue').default);
Vue.component('LandingPage', require('./components/pages/LandingPage.vue').default);

Vue.component('Dashboard', require('./components/pages/Dashboard/Dashboard.vue').default);
    Vue.component('Timeline', require('./components/pages/Dashboard/Columns/Timeline.vue').default);
    Vue.component('Mentions', require('./components/pages/Dashboard/Columns/Mentions.vue').default);
    Vue.component('Dms', require('./components/pages/Dashboard/Columns/Chats/Dms.vue').default);
        Vue.component('Chat', require('./components/pages/Dashboard/Columns/Chats/Chat.vue').default);

    Vue.component('Tweet', require('./components/pages/Dashboard/Columns/Tweet.vue').default);

Vue.component('Profiles', require('./components/pages/Profiles.vue').default);
Vue.component('Stats', require('./components/pages/Stats.vue').default);
Vue.component('Login', require('./components/pages/Login.vue').default);

// Elements
Vue.component('TwitterProfileCard', require('./components/elements/TwitterProfileCard.vue').default);

// Utility
Vue.component('Csrf', require('./components/utility/Csrf.vue').default);

import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

const options = {
    position: "top-right",
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: false,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: true,
    closeButton: "button",
    icon: true,
    rtl: false
};

Vue.use(Toast, options);

// Google Charts wrapper
import VueGoogleCharts from 'vue-google-charts'
Vue.use(VueGoogleCharts);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
