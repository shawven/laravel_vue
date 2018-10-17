import http from '@/libs/http'
import Main from '@/views/Main'
import {loginRouter, otherRouter, pageRoutes} from '@/router/router';

const routerUtil = {
    /**
     * 由路由数据转化成真正的路由
     *
     * @param routes
     * @param isChild
     * @param total
     * @param current
     * @returns {Array}
     */
    convertRoutesDataToRouter(routes, isChild = false, total = 2, current = 0) {
        current ++ ;
        if (routes.length === 0) return [];
        routes.forEach((router) => {
            router.component = isChild ? this.lazyLoad(router.component) : Main;
            if (routerUtil.hasChildRouter(router)) {
                if (total === -1 || current < total) {
                    this.convertRoutesDataToRouter(router.children, true, total, current)
                } else {
                    delete router.children;
                }
            }
        });
    },


    /**
     * 路由数据转路由对象
     *
     * @param routesData
     * @returns {any}
     */
    routesDataToRoutes(routesData) {
        let routes = (JSON.parse(JSON.stringify(routesData)));
        this.convertRoutesDataToRouter(routes);
        return routes;
    },


    getRouterNames(routes) {
        let names = [];
        routes.forEach((router) => {
            names.push(router.name);
            if (this.hasChildRouter(router)) {
                names.push(...this.getRouterNames(router.children))
            }
            if (this.hasMetas(router)) {
                names.push(...this.getRouterNames(router.meta))
            }
        });
        return names;
    },

    /**
     * 获取子路由
     *
     * @param router
     * @returns {boolean}
     */
    hasChildRouter(router) {
        if (router.children) return router.children.length > 0;
        return false;
    },

    /**
     * 获取子路由
     *
     * @param router
     * @returns {boolean}
     */
    hasMetas(router) {
        if (router.meta) return router.meta.length > 0;
        return false;
    },

    /**
     * 异步加载路由
     *
     * @param name
     * @returns {function(): (Promise<*>|*)}
     */
    lazyLoad(name) {
        return () => import(`@/views/${name}/index.vue`)
    }
};

function sessionRoutes() {
    return JSON.parse(sessionStorage.getItem('router'))
}

const router = {
    state: {
        // 所有的路由对象（包括公共页面的路由）
        allRoutes: [],
        // APP路由数据
        routesData: [],
        // APP路由对象
        routes: [],
        // 授权的APP路由数据
        grantedRoutesData: [],
        // 授权的APP路由对象
        grantedRoutes: [],
        // 授权的APP路由名
        grantedRouteNames: [],
        // 拒绝的APP路由名
        forbiddenRouterNames: [],
    },
    getters: {
        allRoutes(state) {
            return [loginRouter, otherRouter, ...pageRoutes, ...state.routes];
        },
        routesData(state) {
            return state.routesData;
        },
        routes(state) {
            if ((!state.routes || state.routes.length === 0) && sessionRoutes()) {
                state.routes = routerUtil.routesDataToRoutes(sessionRoutes().routesData);
            }
            return state.routes;
        },
        grantedRoutesData(state) {
            return state.grantedRoutesData;
        },
        grantedRoutes(state) {
            if ((!state.grantedRoutes || state.grantedRoutes.length === 0) && sessionRoutes()) {
                state.grantedRoutes = routerUtil.routesDataToRoutes(sessionRoutes().grantedRoutesData);
            }
            return state.grantedRoutes;
        },
        grantedRouterNames(state) {
            if ((!state.grantedRouteNames || state.grantedRouteNames.length === 0) && sessionRoutes()) {
                state.grantedRouteNames = routerUtil.getRouterNames(sessionRoutes().grantedRoutesData);
            }
            return state.grantedRouteNames;
        },
        forbiddenRouterNames(state) {
            if ((!state.forbiddenRouterNames || state.forbiddenRouterNames.length === 0) && sessionRoutes()) {
                state.forbiddenRouterNames = sessionRoutes().forbiddenRouterNames;
            }
            return state.forbiddenRouterNames;
        }
    },
    mutations: {
        setRoutesData(state, routesData) {
            state.routesData = routesData;
        },
        setRoutes(state, routes) {
            state.routes = routes;
        },
        setGrantedRoutesData(state, grantedRoutesData) {
            state.grantedRoutesData = grantedRoutesData;
        },
        setGrantedRoutes(state, grantedRoutes) {
            state.grantedRoutes = grantedRoutes;
        },
        setForbiddenRouterNames(state, forbiddenRouterNames) {
            state.forbiddenRouterNames = forbiddenRouterNames
        }
    },
    actions: {
        getRoutes({commit, dispatch, state}, url) {
            return new Promise((resolve, reject) => {
                http.get(url || '/api/ucenter/authorities')
                    .then((result) => {
                        let routesData = result.data.data.routes;
                        let grantedRoutesData = result.data.data.grantedRoutes;
                        let forbiddenRouterNames = result.data.data.forbiddenRouterNames;

                        commit('setRoutesData', routesData);
                        commit('setGrantedRoutesData', grantedRoutesData);
                        commit('setRoutes', routerUtil.routesDataToRoutes(routesData));
                        commit('setGrantedRoutes', routerUtil.routesDataToRoutes(grantedRoutesData));
                        commit('setForbiddenRouterNames', forbiddenRouterNames);

                        let routerInfo = JSON.stringify({routesData, grantedRoutesData, forbiddenRouterNames});
                        sessionStorage.setItem('router', routerInfo);

                        dispatch('initTagsList', state.routes);
                        resolve()
                    }).catch((error) => {
                        reject(error)
                    })
            })
        },
        getLatestRoutes({dispatch}) {
            return dispatch('getRoutes', '/api/ucenter/authorities/latest')
        }
    }
};

export default router;
