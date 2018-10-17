let util = {
};

util.formatDate = (date) => {
    let time = getDate(date);

    let y = time.getFullYear();//年
    let m = time.getMonth() + 1;//月
    if (m >= 0 && m <= 9) {
        m = "0" + m;
    }
    let d = time.getDate();//日
    if (d >= 0 && d <= 9) {
        d = "0" + d;
    }

    return (y + "-" + m + "-" + d)
};

util.formatDateTime = (date) => {
    let time = getDate(date);

    let y = time.getFullYear();//年
    let m = time.getMonth() + 1;//月
    if (m >= 0 && m <= 9) {
        m = "0" + m;
    }
    let d = time.getDate();//日
    if (d >= 0 && d <= 9) {
        d = "0" + d;
    }
    let h = time.getHours();//时
    if (h >= 0 && h <= 9) {
        h = "0" + h;
    }
    let mm = time.getMinutes();//分
    if (mm >= 0 && mm <= 9) {
        mm = "0" + mm;
    }
    let ss = time.getSeconds();
    if (ss >= 0 && ss <= 9) {
        ss = "0" + ss;
    }

    return (y + "-" + m + "-" + d + " " + h + ":" + mm + ":" + ss);
};

function getDate(date) {
    if (date == null || date === '') {
        throw new Error('Invalid date format, input can not be null');
    }
    return date instanceof Date ? date : new Date(date);
}

export default util;
