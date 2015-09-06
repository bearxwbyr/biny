/**
 * Created by billge on 14-10-27.
 */
//ajax.js
//jQuery(document).ajaxSend(function(event, xhr, settings) {
//    function getUrlParams(param){
//        var reg = new RegExp("(^|&)"+ param +"=([^&]*)(&|$)");
//        var r = window.location.search.substr(1).match(reg);
//        if (r!=null) return unescape(r[2]); return null;
//    }
//    function setUrlParam(params, key, value){
//        if (params instanceof Object){
//            return params;
//        } else if (params){
//            return params + "&" + key + "=" + encodeURIComponent(value);
//        } else if (typeof xhr.setRequestHeader == "function") {
//            xhr.setRequestHeader("Content-Type", 'application/x-www-form-urlencoded; charset=UTF-8');
//            return key + "=" + encodeURIComponent(value);
//        }
//    }
//    if (typeof xhr.setRequestHeader == "function"){
//        xhr.setRequestHeader('X-CSRF-TOKEN', getCookie('wetest_token'));
//    }
//});

jQuery(document).ajaxSuccess(function(event,xhr,options){
    try {
        var ret = $.parseJSON(xhr.responseText);
        if (ret.__logs){
            var logs = ret.__logs;
            for (var i in logs){
                var log = logs[i];
                if (log instanceof Function){
                    continue;
                }
                switch (log['type']){
                    case 'log':
                        console.log("{0} => ".format(log['key']), log['value']);
                        break;

                    case 'info':
                        console.info("{0} => ".format(log['key']), log['value']);
                        break;

                    case 'error':
                        console.error("{0} => ".format(log['key']), log['value']);
                        break;

                    case 'warn':
                        console.warn("{0} => ".format(log['key']), log['value']);
                        break;

                    default :
                        console.log("{0} => ".format(log['key']), log['value']);
                        break;

                }
            }
        }
    } catch (e){}
});

(function ($) {
    $.fn.pagination = function (options) {
        var self = this;
        this.Pagination = new Pagination();
        this.Pagination.init(options);
        return this.each(function () {
            $this = $(this);
//            self.Pagination.debug($this);
            $this.html(self.Pagination.buildPagination());
            self.Pagination.bindCheck($this);
        });

    };

    var Pagination = (function(){
        var extras = {
            init: function(options){
                var defaults = {
                    "pages": 0,
                    "page": 0,
                    "callbackfunc": null,
                    "callbackParams": {},
                    "showpages": 6
                };
                this.options = $.extend({}, defaults, options);
            },
            debug: function ($this) {

            },
            buildPagination: function(){
                if (this.options.pages <= 1) {
                    return '';
                }
                var prev = 3;
                var next = 5;
                if (this.options.showpages) {
                    prev = parseInt(this.options.showpages / 2) - 1;
                    next = parseInt(this.options.showpages / 2) + 1;
                }
                var div = [];
                div.push('<ul class="pagination">');
                if (this.options.page > 0) {
                    div.push('<li><a data-page={0} class="checkPage"><<</a></li>'.format(this.options.page - 1));
                }
                for (var i = this.options.page - prev; i <= this.options.page + next; i++) {
                    if (i <= 0 || i > this.options.pages) {
                        continue;
                    }
                    if (i - 1 == this.options.page) {
                        div.push('<li class="active disabled"><a>{0}</a></li>'.format(i));
                    }
                    else {
                        div.push('<li><a data-page={0} class="checkPage">{1}</a></li>'.format(i - 1, i));
                    }
                }

                if (this.options.page + 1 < this.options.pages) {
                    div.push('<li><a data-page={0} class="checkPage">>></a></li>'.format(this.options.page + 1, i));
                }
                div.push('</ul>');
                return div.join("\n");
            },
            bindCheck: function($this){
                var self = this;
                $this.find('.checkPage').bind("click", function(){
                    var page = $(this).data("page");
                    self.options.callbackfunc(self.options.callbackParams, page);
                })
            }
        };
        return extras;
    });

})(jQuery);

if (!String.prototype.format) {
    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined'
                ? args[number] : match;
        });
    };
}

function in_array(id, array){
    for (var i in array){
        if (id == array[i]){
            return true;
        }
    }
    return false;
}

function array_key_exists(key, array){
    for (var i in array){
        if (key == i){
            return true;
        }
    }
    return false;
}

function urlencode(array){
    var url = [];
    for (var key in array){
        url.push("{0}={1}".format(key, array[key]));
    }
    return url.join("&");
}

$(function(){
    $('ul.subtitle>li>ul>li>div').each(function () {
        $(this).css("left", $(this).parent().parent().width())
        $(this).parent().children('a').append(' <span class="caret-right"></span>');
    })

});