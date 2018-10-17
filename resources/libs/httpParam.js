const util = {

    build(items, parentKey = null, total, current) {
        let params = new URLSearchParams();
        this.formatParams(params, items, parentKey = null, total, current);
        return params
    },

    /**
     * 添加且格式化查询参数
     *
     * @param params
     * @param items
     * @param parentKey
     * @param total
     * @param current
     */
    formatParams(params, items, parentKey = null, total = -1, current = 0) {
        current ++;
        Object.keys(items).forEach((childKey) => {
            let value = items[childKey];
            if (value === undefined || value === null ) {
                value = '';
            }

             // 需要组装嵌套name
            let isNested = parentKey !== null;

              // 如果值是一个对象
             if (typeof value === 'object') {

                 // 递归并嵌套key
                if (current < total || total === -1) {
                    this.formatParams(params, value, this.getNestedKey(parentKey, childKey), total, current);
                }
                 // 达到最大递归数量时，结束递归
                else {
                    let newValue = '';

                    if (value instanceof Array) {
                        newValue = value.join(',');
                    }
                    //如果值是object 用','进行连接
                    else {
                        newValue = this.joinObjectKeyValue(value, ',');
                    }

                    if (newValue !== '') {
                        params.append(this.getNestedKey(parentKey, childKey), newValue)
                    }
                }
            }
             // 如果值是普通字符串
            else {
                if (isNested) {
                    params.append(this.getNestedKey(parentKey, childKey), value)
                } else {
                    params.append(childKey, value)
                }
            }
        });
    },

    /**
     * 获取嵌套的类似关联数组的key
     *
     * @param parentKey
     * @param childKey
     * @returns {string}
     */
    getNestedKey(parentKey, childKey) {
        if (parentKey === undefined || parentKey === null) return childKey;
        return parentKey + '[' + childKey + ']';
    },

    /**
     * 连接对象的key,value
     * @param object
     * @param delimiter
     * @returns {string}
     */
    joinObjectKeyValue(object, delimiter) {
        if (typeof object !== 'object') return '';
        let str = '';
        Object.keys(object).forEach((key) => {
            let value = object[key];
            if (value === undefined || value === null || value === '') return;
            str += key + delimiter + value + delimiter;
        });
        return str.length > 0 ? str.substring(0, str.length - 1) : '';
    },
};

export default util;
