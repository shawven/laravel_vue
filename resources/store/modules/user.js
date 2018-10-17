import http from '@/libs/http';

function sessionUserInfo() {
    return JSON.parse(sessionStorage.getItem('userInfo'))
}

const superAdminRole = 1;

const user = {
    state: {
        superAdminRole: superAdminRole,
        userInfo: {
            uid: null,
            username: null,
            avatar: null
        }
    },
    getters: {
        superAdminRole(state) {
            return state.superAdminRole;
        },
        isSuperAdmin(state) {
             return state.userInfo && state.userInfo.role_id ? state.userInfo.role_id.includes(superAdminRole): false
        },
        uid(state) {
           return state.userInfo.id
        },
        userInfo(state) {
            if (!state.userInfo.id && sessionUserInfo()) {
                state.userInfo = sessionUserInfo()
            }
            return state.userInfo;
        }
    },
    mutations: {
        setUserInfo(state, userInfo) {
            state.userInfo = userInfo;
        },
        logout () {
            http.post('api/logout');

            sessionStorage.clear();

            // 恢复默认样式
            document.querySelector('link[name="theme"]').setAttribute('href', '');

            // 清空打开的页面等数据，但是保存主题数据
            let theme = '';
            if (localStorage.theme) theme = localStorage.theme;

            localStorage.clear();

            if (theme) localStorage.theme = theme;

            window.location.href = '/';
        }
    }
};

export default user;
