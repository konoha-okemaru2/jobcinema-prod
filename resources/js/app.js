
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 *
*/

require('./bootstrap');
require('./main');
import store from './store'
window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('favourite-component', require('./components/favourite/FavouriteComponent.vue').default);
Vue.component('favourite-follow-fixed-component', require('./components/favourite/FavouriteFollowFixedComponent.vue').default);
Vue.component('favourite-count-component', require('./components/favourite/FavouriteCountComponent.vue').default);
Vue.component('slick-top', require('./components/SlickTopComponent.vue').default);
Vue.component('recent-component', require('./components/RecentJobComponent.vue').default);
Vue.component('search-component', require('./components/SearchComponent.vue').default);
Vue.component('search-history-component', require('./components/SearchHistoryComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    store
});
