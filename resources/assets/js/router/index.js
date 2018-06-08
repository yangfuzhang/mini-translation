import Vue from 'vue';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

export default new VueRouter({
    saveScrollPosition: true,
    routes: [
        {
            name: '首页',
            path: '/',
            component: resolve => void(require(['../components/Home.vue'], resolve))
        },
		{
            name: '发布',
            path: '/publish',
            component: resolve => void(require(['../components/Publish.vue'], resolve))
        },
		{
            name: '租房动态',
            path: '/list',
            component: resolve => void(require(['../components/List.vue'], resolve))
        },
		{
            name: '租友动态',
            path: '/users',
            component: resolve => void(require(['../components/User.vue'], resolve))
        }
    ]
});