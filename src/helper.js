import axios from 'axios'

let helper = {
    requestPost(params) {
        return axios.post(params.url ? params.url : window.location,
            params.data + '&_csrf=' + window.yii.getCsrfToken())
    },
    dateFormatPoint(dateObj, twoDigYear = false) {
        let time = '';
        if (dateObj && typeof dateObj === "string" && dateObj.indexOf('.') > -1) {
            return dateObj;
        }
        if (dateObj && typeof dateObj === "string") {
            time = dateObj.split(' ')[1];
            dateObj = new Date(dateObj);
        }
        if (dateObj && typeof dateObj.getDate === "function") {
            let day = dateObj.getDate();
            if(day < 10) day = '0' + day;
            let m = dateObj.getMonth() + 1;
            if(m < 10) m = '0' + m;
            let y = 0;
            if (!twoDigYear) y = dateObj.getFullYear();
            else y = dateObj.getFullYear().toString().substr(-2);
            if(isNaN(day)) day = '00';
            if(isNaN(m)) m = '00';
            if(isNaN(y)) y = '0000';
            return day + '.' + m + '.' + y + (time ? ' ' + time : '');
        }
        return '';
    },
    dateFormatMySQL(dateObj) {
        let time = '';
        if (dateObj && typeof dateObj === "string" && dateObj.indexOf('-') > -1) {
            return dateObj;
        }
        if (dateObj && typeof dateObj === "string") {
            time = dateObj.split(' ')[1];
            let data = dateObj.split('.');
            return data[2] + '-' + data[1] + '-' + data[0] + (time ? ' ' + time : '');
        }
        if (dateObj && typeof dateObj.getDate === "function") {
            let day = dateObj.getDate();
            if(day < 10) day = '0' + day;
            let m = dateObj.getMonth() + 1;
            if(m < 10) m = '0' + m;
            let y = dateObj.getFullYear();
            if(isNaN(day)) day = '00';
            if(isNaN(m)) m = '00';
            if(isNaN(y)) y = '0000';
            let h = dateObj.getHours()
            if (h < 10) h = '0' + h
            let min = dateObj.getMinutes()
            if (min < 10) min = '0' + min
            return y + '-' + m + '-' + day + ' ' + h + ':' + min
        }
        return '';
    },
    dateFormatMonthName(dateObj) {
        const date = new Date(this.dateFormatMySQL(dateObj))
        const m = [
            '',
            'января',
            'февраля',
            'марта',
            'апреля',
            'мая',
            'июня',
            'июля',
            'августа',
            'сентября',
            'октября',
            'ноября',
            'декабря'
        ]
        return date.getDate() + ' ' + m[date.getMonth() + 1] + ' ' + date.getFullYear()
    },

};

export default helper