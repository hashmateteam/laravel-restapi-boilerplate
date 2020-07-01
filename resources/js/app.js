/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import VueAxios from 'vue-axios';
import axios from 'axios';
Vue.use(VueAxios, axios);
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);
//Shared State Veux store
import store from "./store/index";
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    store
});

let uri = '/api/authin';
const user = {
    email: 'hashmateteam@gmail.com',
    password: 'testing123'
}
app.axios.post(uri, user).then((response) => {
    console.log(response);
    console.log(response.data.data.access.access_token);
    if (response.data.code === 200) {
        app.$store.dispatch("update_token", String(response.data.data.access.access_token));
        app.$store.dispatch("update_auth_user", Object(response.data.data.user));
        app.$cookie.set("authentication_token", response.data.data.access.access_token);
        app.$cookie.set("auth_user", JSON.stringify(response.data.data.user));
        console.log(app.$store.getters.get_token);
        console.log(app.$store.getters.get_auth_user);
        //this.$router.push({ path: '/' });
    } else {

    }
});