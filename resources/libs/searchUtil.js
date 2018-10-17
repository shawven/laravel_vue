import http from '@/libs/http';
import httpParamUtil from '@/libs/httpParam'
import dateUtil from '@/libs/dateUtil'
import {Message} from 'iview';

const stop = (message) => {
    http.cancel(message)
};

const getList = (url, vm) => {
    let params = queriesBuilder.build(vm);
    vm.loading = true;
    return http.get(url, {params})
        .then((result) => {
            vm.loading = false;
            let resultData = result.data;
            vm.list = resultData.data.list;
            vm.pageData.total = resultData.data.total;
            vm.pageData.page = resultData.data.page;
            vm.pageData.limit = resultData.data.limit;
            return vm.list;
        })
        .catch((error) => {
            vm.loading = false;
            vm.list = [];
            vm.pageData.total = 0;
            if (http.isCancel(error) && error.message) {
                Message.warning(error.message);
            }
            if (error.response && error.response.status !== 404) {
                http.handler.handleError(error);
            }
        })
};

const queriesBuilder = {

    rangeQueryName: 'r',
    sortQueryName: 's',
    dateFormat: 'format',

    /**
     * 构建查询参数
     *
     * @param vm
     * @returns {URLSearchParams}
     */
    build(vm) {
        let obj = { ...this.buildSearchItem(vm)};
        if (!!vm.startPage)  Object.assign(obj, this.buildPage(vm));
        if (!!vm.startSort)  Object.assign(obj, this.buildSort(vm));

        return httpParamUtil.build(obj, null, 2);
    },

    /**
     * 分页查询
     *
     * @param vm
     * @returns {{page: *, limit: (*|number|limit|{type, default})}}
     */
    buildPage(vm) {
        return {page: vm.pageData.page, limit: vm.pageData.limit};
    },

    /**
     * 排序
     *
     * @param vm
     * @returns {{}}
     */
    buildSort(vm) {
        let sorts = {};

        if (!vm.sortBy) return {};

        vm.sortBy.forEach((item) => {
            for (let [k, v] of Object.entries(item)) {
                sorts[k] = v;
            }
        });

        return {[this.sortQueryName]: sorts};
    },

    /**
     * 搜索
     *
     * @param vm
     * @returns {*|any}
     */
    buildSearchItem(vm) {
        let searchItem = this.deepCopy(vm.searchItem);

        this.buildSearchRange(searchItem);

        return searchItem;
    },

    /**
     * 搜索范围项
     *
     * @param searchItem
     */
    buildSearchRange(searchItem) {
        let {keys} = Object;

        //  范围值（检测时间）
        if (keys(searchItem).includes(this.rangeQueryName)) {
            let rangeObj = searchItem[this.rangeQueryName];
            keys(rangeObj).forEach((key) => {
                if (rangeObj[key] instanceof Array) {
                    // 是否设置了format属性
                    let format = rangeObj[this.dateFormat];
                    delete rangeObj[this.dateFormat];

                    let min = rangeObj[key][0] ? '^' + this.attemptFormatTime(rangeObj[key][0], format) + '' : null;
                    let max = rangeObj[key][1] ?  this.attemptFormatTime(rangeObj[key][1], format) + '$' : null;

                    rangeObj[key] = (min && max) ? (min + ',' + max) : (min || max);
                }
            });
        }

        // 固定值（检测时间）
       for(let key of keys(searchItem)) {
            if (!searchItem[key]) continue;
            const arr = ['.*date.*', '.*time.*', '.*create.*', '.*update.*', '.*delete.*', '.*add.*', '.*edit.*'];
            arr.forEach((exp) => {
                if (new RegExp(exp).exec) {
                    searchItem[key] = this.attemptFormatTime(searchItem[key], true);
                }
            })
        }
    },

    /**
     * 格式化时间
     *
     * @param value
     * @param format
     * @returns {*}
     */
    attemptFormatTime(value, format) {
        if (this.isTimeType(value)) {
            if (format) {
                return dateUtil.formatDateTime(value)
            }
            return Math.floor((new Date(value).getTime() / 1000))
        }
        return value;
    },

    /**
     * 是否时间类型
     * @param value
     * @returns {boolean|*}
     */
    isTimeType(value){
        let jsonDataFormatRegex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{3}Z/;

        if (value instanceof Date) return true;

        if (typeof value === 'string') {
            return jsonDataFormatRegex.test(value) || value.includes('GTM+')
        }

        return false;
    },

    /**
     * 深拷贝对象
     *
     * @param obj
     * @returns {any}
     */
    deepCopy(obj) {
        return JSON.parse(JSON.stringify(obj));
    }
};


export default {
    getList, stop, queriesBuilder
};
