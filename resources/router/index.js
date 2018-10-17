import Vue from 'vue';
import iView from 'iview';
import Util from '@/libs/util';
import VueRouter from 'vue-router';
import Cookies from 'js-cookie';
import store from '@/store/index';
import {loginRouter, otherRouter, pageRoutes} from './router';

Vue.use(VueRouter);

// 路由配置
const commonRoutes = [loginRouter, otherRouter];

const RouterConfig = {
    // mode: 'history',
    routes: commonRoutes
};

const router = new VueRouter(RouterConfig);

// 刷新页面从新添加路由
if (sessionStorage.getItem('router')) {
    router.addRoutes([...store.getters.routes, ...pageRoutes])
}

router.beforeEach((to, from, next) => {
    iView.LoadingBar.start();
    Util.title(to.meta.title);

    let noLoginToOtherPage = !sessionStorage.getItem('userInfo') && to.name !== 'login';
    let loggedToLoginPage = sessionStorage.getItem('userInfo') && to.name === 'login';

    if (noLoginToOtherPage) {
        next({name: 'login'});
        return
    }
    if (loggedToLoginPage) {
        Util.title();
        next({name: 'home_index'});
        return
    }

    // 访问拒绝路由
    let forbiddenRouterNames = store.getters.forbiddenRouterNames;
    if (forbiddenRouterNames.some((name) => name === to.name)) {
        next({name: 'error-403', replace: true});
        return
    }

    // 未配置跳转时访问父路由则跳转默认第一个子路由
    Util.toDefaultPage(store.getters.allRoutes, to.name, router, next)

});

router.afterEach((to) => {
    Util.openNewPage(router.app, to.name, to.params, to.query);
    iView.LoadingBar.finish();
    window.scrollTo(0, 0);
});

export default router
