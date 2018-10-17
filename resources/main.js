import Vue from 'vue';
import iView from 'iview';
import router from './router';
import store from './store';
import App from './app';
import './locale';
import 'iview/dist/styles/iview.css';
import './styles/user.css';
import VueI18n from 'vue-i18n';
import 'url-search-params-polyfill';
import http, {messageHttp, modalHttp, noticeHttp} from '@/libs/http';
import modal from '@/libs/modal';
import authButton from '@/components/auth-button'
import listTable from '@/components/list/list-table'
import loading from '@/components/loading'
import userInfo from '@/views/user/user-info'

Vue.prototype.$Modal = modal;
Vue.prototype.$http = http;
Vue.prototype.$messageHttp = messageHttp;
Vue.prototype.$noticeHttp = noticeHttp;
Vue.prototype.$modalHttp = modalHttp;

Vue.component('auth-button', authButton);
Vue.component('list-table', listTable);
Vue.component('user-info', userInfo);
Vue.component('loading', loading);

Vue.prototype.$can = (name) => {
    return store.getters.grantedRouterNames.includes(name);
};
Vue.prototype.$authButtonColumn = (buttons) => {
    let canButtons = buttons.filter((button) => Vue.prototype.$can(button.name));
    let length = canButtons.length;
    if (length === 0) return null;
    return {
        title: '操作',
        align: 'center',
        width: length === 1 ? 100 : length * 85,
        render: (h, params) => {
            let renderAuthButtons = [];
            canButtons.forEach((button) => {
                let disabled = button.disabled
                    ? (typeof button.disabled === "function" ? button.disabled(params) : button.disabled)
                    : false;
                renderAuthButtons.push(
                    h('auth-button', {
                        props: {
                            name: button.name,
                            disabled: disabled,
                            size: 'small'
                        },
                        style: {
                            marginLeft: '3px',
                            marginRight: '3px'
                        },
                        on: {
                            click: () => button.click(params)
                        }
                    })
                )
            });
            return h('div', renderAuthButtons);
        }
    };
};
Vue.directive('can', {
    bind: (el, binding) => {
        if (!Vue.prototype.$can(binding.value)) {
            el.parentNode.removeChild(el);
        }
    }
});

Vue.use(VueI18n);
Vue.use(iView);

new Vue({
    el: '#app',
    router: router,
    store: store,
    render: h => h(App)
});
