/**
 * Created by billge on 15-3-17.
 */
function checkTaggle(checked, name) {
    $('input[type="checkbox"][name="' + name + '"]').prop("checked", checked)
}

function reload(){
    window.location.reload();
}

function back(){
    window.history.go(-1);
}

//获取年月日
function GetDateStr(date, AddDayCount) {
    if (AddDayCount == undefined){
        AddDayCount = 0;
    }
    if (date != undefined){
        date = date.split('-');
        var dd = new Date(date[0], date[1]-1, date[2]);
    } else {
        var dd = new Date();
    }
    dd.setDate(dd.getDate()+AddDayCount);//获取AddDayCount天后的日期
    var y = dd.getFullYear();
    var m = ((dd.getMonth()+1).toString().length == 2) ? dd.getMonth()+1 : ("0"+(dd.getMonth()+1).toString());//获取当前月份的日期
    var d = (dd.getDate().toString().length == 2) ? dd.getDate() : ("0"+dd.getDate().toString());
    return y+"-"+m+"-"+d;
}

function getFormArray(dom){
    var result = {};
    var params = $(dom).serializeArray();
    for (var i in params){
        var param = params[i];
        result[param['name']] = param['value'];
    }
    return result;
}

function str_repeat(str, n){
    var list = [];
    while(list.length < n){
        list.push(str);
    }
    return list.join('');
}

//验证邮箱
function checkEmail(email) {
    var pattern = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+$/;
    if (!pattern.test(email)) {
        return false;
    }
    return true;
}

function checkValue(value, rem){
    if (rem == undefined){
        rem = /^([\w ()（）\u4e00-\u9fa5]+)$/;
    }
    if (!value.match(rem) || value.length > 20){
        return false;
    } else {
        return true;
    }
}

function getCheckBoxByName(name, isInt){
    if (isInt == undefined){
        isInt = true;
    }
    var ids = [];
    $('input[type="checkbox"][name="'+name+'"]:checked').each(function () {
        if (isInt){
            var value = parseInt($(this).val());
        } else {
            var value = $(this).val();
        }
        ids.push(value);
    });
    return ids;
}