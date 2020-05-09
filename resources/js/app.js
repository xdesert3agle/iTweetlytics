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
Vue.component('Sync', require('./components/pages/Sync.vue').default);

Vue.component('App', require('./components/pages/App.vue').default);
    Vue.component('Dashboard', require('./components/pages/Dashboard/Dashboard.vue').default);
        Vue.component('Tl', require('./components/pages/Dashboard/Columns/Timeline/Tl.vue').default);
            Vue.component('ButtonNewTweet', require('./components/pages/Dashboard/Columns/Timeline/ButtonNewTweet.vue').default);
        Vue.component('Mentions', require('./components/pages/Dashboard/Columns/Mentions.vue').default);
        Vue.component('Lists', require('./components/pages/Dashboard/Columns/Lists.vue').default);
        Vue.component('Dms', require('./components/pages/Dashboard/Columns/Chats/Dms.vue').default);
            Vue.component('Chat', require('./components/pages/Dashboard/Columns/Chats/Chat.vue').default);

    Vue.component('Tweet', require('./components/pages/Dashboard/Columns/Tweet.vue').default);

Vue.component('ScheduledTweets', require('./components/pages/ScheduledTweets.vue').default);
Vue.component('Profiles', require('./components/pages/Profiles.vue').default);
Vue.component('Stats', require('./components/pages/Stats/Stats.vue').default);
    Vue.component('GeneralStats', require('./components/pages/Stats/GeneralStats.vue').default);
    Vue.component('FollowersStats', require('./components/pages/Stats/FollowersStats.vue').default);
    Vue.component('FriendsStats', require('./components/pages/Stats/FriendsStats.vue').default);
    Vue.component('GraphCard', require('./components/pages/Stats/GraphCard.vue').default);
Vue.component('Login', require('./components/pages/Login.vue').default);

// Elements
Vue.component('TwitterProfileCard', require('./components/elements/TwitterProfileCard.vue').default);
Vue.component('ButtonModal', require('./components/elements/ButtonModal.vue').default);
Vue.component('ButtonHref', require('./components/elements/ButtonHref.vue').default);

// Utility
Vue.component('Csrf', require('./components/utility/Csrf.vue').default);

import VueToastr from "vue-toastr";
Vue.use(VueToastr);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
