import {Message, Notice} from 'iview';
import store from '@/store'
import Axios from 'axios'
import httpParamUtil from '@/libs/httpParam'
// 官方modal bug：非点击取消按钮关闭时默认不会销毁当前实例
import Modal from './modal'

const successTitle = '操作成功';
const errorTitle = '发生错误';
const cancelTitle = '取消成功';
const errorTimeOut = 30;

const MESSAGE = 'message';
const NOTICE = 'notice';
const MODAL_MESSAGE = 'modal_message';
const MODAL_NOTICE = 'modal_notice';

const handler = {
    canceler:{},

    cancel(message) {
        this.canceler(message);
    },

    getCancelToken() {
        return new Axios.CancelToken((c) => {this.canceler = c;});
    },

    isCancel(error) {
        return Axios.isCancel(error)
    },

    handelHttp(promise, show, success, error, end){
        return promise
            .then(result => {
                handler.handleSuccess(result, show, success);
                handler.handleFinally(end);
            })
            .catch(result => {
                handler.handleError(result, show, error);
                handler.handleFinally(end);
            })
    },

    handleParams(params) {
        if (params === undefined || params === null) {
            return {}
        }

        if (params instanceof URLSearchParams) {
            return params
        }

        return httpParamUtil.build(params);
    },

    handleSuccess(result, show, success) {
        let successConfig = {};
        let successMsg =  result.data.message;

        if (typeof success === 'function') {
            success(result.data);
        } else if(success) {
            successMsg = success;
        }

        switch (show) {
            case MESSAGE:
                Message.destroy();
                if (successMsg) Message.success(Object.assign({content: successMsg}, successConfig));
                break;
            case NOTICE:
                Notice.destroy();
                let noticeData = {title: successTitle};
                if (successMsg) noticeData.desc = successMsg;
                Notice.success(Object.assign(noticeData, successConfig));
                break;
            case MODAL_MESSAGE:
                Modal.remove();
                if (successMsg) Message.success(Object.assign({content: successMsg}, successConfig));
                break;
            case MODAL_NOTICE:
                Modal.remove();
                let modalNoticeData = {title: successTitle};
                if (successMsg) modalNoticeData.desc = successMsg;
                Notice.success(Object.assign(modalNoticeData, successConfig));
                break;
            default:
                Message.destroy();
                Notice.destroy();
                Modal.remove();
        }
    },

    handleError(result, show, error) {
        let errorConfig = {duration: errorTimeOut, closable: true};

        if (this.isCancel(error)) {
            Message.warning(error.message ? error.message : cancelTitle)
        }

        let errorMsg;

        if (typeof error === 'function') {
            error(result.data);
        } else if (error) {
            errorMsg = error;
        }

        if (!errorMsg) {
            errorMsg = this.getError(result)
        }

        switch (show) {
            case MESSAGE:
                Message.error(Object.assign({content: errorMsg}, errorConfig));
                break;
            case NOTICE:
                Notice.error(Object.assign({title: errorTitle, desc: errorMsg}, errorConfig));
                break;
            case MODAL_MESSAGE:
                Modal.stop();
                Message.error(Object.assign({content: errorMsg}, errorConfig));
                break;
            case MODAL_NOTICE:
                Modal.stop();
                Notice.error(Object.assign({title: errorTitle, desc: errorMsg}, errorConfig));
                break;
            default:
                Modal.stop();
                Message.error(Object.assign({content: errorMsg}, errorConfig));
        }
    },

    handleFinally(end) {
        if (typeof end === 'function') {
            end();
        }
    },

    getError(result) {
        // 详细异常 状态码 + 状态描述文档 + 系统异常信息
        let detailMessage;
        if (result.response) {
            if (typeof result.response.data === 'object') {
                detailMessage = result.response.data.message ? result.response.data.message : '';
            } else {
                detailMessage = result.response.status + ' ' + result.response.statusText + '<br>'
                    +  result.response.data;
            }
        } else {
            detailMessage = result.message;
        }

        return detailMessage ;
    }
};

const interceptors = {
    request: {
        success: config => {
            return config
        },
        error: error => {
            return Promise.reject(error)
        }
    },
    response: {
        success: response => {
            return response
        },
        error: error => {
            if (error.response && error.response.status === 401) {
                store.commit('logout');
                return;
            }
            if (error.message.startsWith('timeout')) {
                let matchArray = new RegExp('.*\\s{1}(\\d+)ms.*').exec(error.message);
                error = new Error('连接已超时超过' + matchArray[1] / 1000 + '秒');
            }
            return Promise.reject(error)
        }
    }
};

const getInstance = (options = {}) => {
    //默认配置项
    const defaultOptions = {
        // timeout: 10000,

        // baseURL: 'http://www.mylaravel.com',

        cancelToken: handler.getCancelToken(),

        paramsSerializer: (params) => {
            return handler.handleParams(params)
        },

        transformRequest: [(data) => {
            // 对 data 进行任意转换处理
            return handler.handleParams(data);
        }],
    };

    if (options.method) {
        Axios.interceptors.request.use(interceptors.request.success, interceptors.request.error);
        Axios.interceptors.response.use(interceptors.response.success, interceptors.response.error);
        return  Axios({ ...defaultOptions, ...options})
    }

    let instance = Axios.create({ ...defaultOptions, ...options});
    instance.interceptors.request.use(interceptors.request.success, interceptors.request.error);
    instance.interceptors.response.use(interceptors.response.success, interceptors.response.error);
    return instance
};

const http = {
    handler,
    create(options) {
        return getInstance(options)
    },
    get(url, config) {
        return getInstance().get(url, config)
    },
    post(url, data, config) {
        return getInstance().post(url, data, config)
    },
    put(url, data, config) {
        return getInstance().put(url, data, config)
    },
    delete (url, config) {
        return getInstance().delete(url, config)
    },
    cancel(message) {
        handler.cancel(message);
    },
    isCancel(error) {
        return handler.isCancel(error)
    }
};

export default http;

export const messageHttp = {
    create(options) {
        return getInstance(options)
    },
    get(url, success, error, end) {
        return handler.handelHttp(http.get(url), MESSAGE, success, error, end);
    },
    post(url, data, success, error, end) {
        return handler.handelHttp(http.post(url, data), MESSAGE, success, error, end);
    },
    put(url, data, success, error, end) {
        return handler.handelHttp(http.put(url, data), MESSAGE, success, error, end);
    },
    delete(url, success, error, end) {
        return handler.handelHttp(http.delete(url), MESSAGE, success, error, end);
    },
    cancel(message) {
        handler.cancel(message);
    },
    isCancel(error) {
        return handler.isCancel(error)
    },
};

export const noticeHttp = {
    create(options) {
        return getInstance(options)
    },
    get(url, success, error, end) {
        return handler.handelHttp(http.get(url), NOTICE, success, error, end);
    },
    post(url, data, success, error, end) {
        return handler.handelHttp(http.post(url, data), NOTICE, success, error, end);
    },
    put(url, data, success, error, end) {
        return handler.handelHttp(http.put(url, data), NOTICE, success, error, end);
    },
    delete(url, success, error, end) {
        return handler.handelHttp(http.delete(url), NOTICE, success, error, end);
    },
    cancel(message) {
        handler.cancel(message);
    },
    isCancel(error) {
        return handler.isCancel(error)
    },
};

export const modalHttp = {
    delete (url, title, content, success, error, end) {
        let tempArray = url.split('/');
        let hasDot =  (url.endsWith('/') && String(tempArray[tempArray.length - 2]).includes(','))
            || (!url.endsWith('/') && String(tempArray[tempArray.length - 1]).includes(','));

        let option = {
            width: 300,
            title: title || hasDot ? '批量删除确认' : '删除确认',
            content: content || hasDot ? '确定要批量删除' +  url.split(',').length + '条数据吗？' : '确定要删除此数据吗？',
            loading: true,
            closable: true
        };

        option.onOk = () => {
            handler.handelHttp(http.delete(url), MODAL_MESSAGE, success, error, end);
        };

        Modal.confirm(option);
    },
    prompt(url, params, paramName, title, content, success, error, end) {
        let render = h => {
            return h('div', {
                    props: {
                        label: title,
                        labelWidth: 100,
                        labelPosition: "top"
                    },
                },
                [
                    h('label', {
                        attrs: {
                            for: 'prompt-input'
                        },
                        style: {
                            display: 'inline-block',
                            padding: '20px 0'
                        }
                    }, content),
                    h('Input', {
                        props: {
                            elementId: 'prompt-input',
                            autofocus: true
                        },
                        on: {
                            input: (val) => {
                                params[paramName] = val;
                            }
                        }
                    })
                ]
            );
        };

        let option = {
            width: 350,
            title: title,
            render: render,
            loading: true,
            closable: true
        };

        option.onOk = () => {
            if (!params[paramName] || params[paramName].trim() === '') {
                Modal.stop();
                Message.warning('输入无效');
                return;
            }
            handler.handelHttp(http.put(url, params), MODAL_MESSAGE, success, error, end);
        };

        Modal.confirm(option);
    },
    confirm(config, title, content, success, error, end) {
        let option = {
            width: 300,
            title: title,
            content: content,
            loading: true,
            closable: true
        };

        option.onOk = () => {
            handler.handelHttp(http.create(config), null, success, error, end);
        };

        Modal.confirm(option);
    }
};
