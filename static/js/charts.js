(function ($) {
    var chartColors = ['#3CB878', '#B2D66D', '#EAF26A', '#FBAF5D', '#EE6B70',
        '#7cb5ec','#CC66CC','#90ed7d','#f7a35c','#8085e9','#7FB80E','#f15c80','#e4d354','#FF6666','#993366'];

    $.fn.baseCharts = function(options){
        var defaults = {
            title: "",
            type: 'column',
            backgroundColor: "#FFFFFF",
            colors: chartColors,
            categories: [],
            series: [],
            callbackfunc: null,
            callbackParam: {},
            showInLegend: false,
            legend: true,
            tickInterval: false,
            fontSize: "12px"
        };
        this.options = $.extend({}, defaults, options);
        var defaultSeries = {
            name: "",
            data: [],
            type: "column",
            dataLabels:{enabled:false}
        };
        for (var i in this.options.series){
            this.options.series[i] = $.extend({}, defaultSeries, this.options.series[i]);
        }
        if (this.options.callbackfunc){
            this.options.events = {
                legendItemClick: function () {
                    self.options.callbackfunc(this, self.options.callbackParam);
                    return false;
                }
            };
        } else {
            this.options.events = {};
        }
        var self = this;
        return this.each(function () {
            $this = $(this);
            $this.highcharts({
                chart: {
                    type: self.options.type,
                    backgroundColor: self.options.backgroundColor
                },
                title: {
                    text: self.options.title
                },
                colors: self.options.colors,
                xAxis: {
                    categories: self.options.categories,
                    tickInterval: self.options.tickInterval ? self.options.tickInterval : parseInt(self.options.categories.length / 5),
                    labels:{
                        style: {
                            fontSize: self.options.fontSize
                        }
                    }
                },
                yAxis: {
                    title: {
                        enabled: false
                    },
                    stackLabels: {
                        enabled: true,
                        style: {
                            fontWeight: 'bold'
                        }
                    }
                },
                legend: {
                    enabled: self.options.legend
                },
                tooltip: {
                    formatter: function() {
                        return '<b>'+ this.x +'</b><br/>'+
                            this.series.name +': '+ this.y ;
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        events: self.options.events,
                        dataLabels: {
                            enabled: true
                        },
                        showInLegend: true
                    },
                    spline:{
                        showInLegend: self.options.showInLegend
                    }
                },
                series: self.options.series,
                credits:{
                    enabled:false
                },
                navigation: {
                    buttonOptions: {
                        enabled: false
                    }
                }
            });
        });
    }

    $.fn.drawPraise = function(goods, bads){
        var goodCount = 0;
        for (var key in goods){
            goodCount += goods[key];
        }
        var badCount = 0;
        for (var key in bads){
            badCount += bads[key];
        }
        var sum = goodCount + badCount;
        if (!sum) {
            return false;
        }
        var goodPercent = goodCount/sum;
        return this.each(function () {
            var id = $(this)[0].id;
            var canvas = document.getElementById(id);
            if (canvas == null) {
                return false;
            }
            var context = canvas.getContext('2d');
            context.beginPath();
            context.moveTo(547, 150);
            context.arc(547, 150, 100, Math.PI*1.5, Math.PI *(1.5-goodPercent*2), false);
            //差评
            context.closePath();
            context.fillStyle = 'rgba(242,108,130,1)';
            context.fill();
            context.beginPath();
            context.moveTo(543, 150);
            context.arc(543, 150, 120, Math.PI*1.5, Math.PI*(1.5-goodPercent*2), true);
            //好评
            context.closePath();
            context.fillStyle = 'rgba(60,184,120,1)';
            context.fill();
            context.beginPath();
            context.moveTo(545, 150);
            context.arc(545, 150, 60, 0, Math.PI*2, true);
            //中间白的
            context.closePath();
            context.fillStyle = 'rgba(255,255,255,1)';
            context.fill();

            context.fillStyle = "#379BE0";
            context.font = "bold 28px 黑体";
            context.textBaseline = "top";
            context.fillText("好评率", 502, 122);
            context.fillText(parseInt(goodPercent*100)+"%", 520, 157);

            var table = '<table class="praise praise-good">' +
                '<tr><th colspan="2">好评</th></tr>';
            var i = 0;
            for (var key in goods){
                if (i++ >=5){
                    break;
                }
                table += '<tr><td>'+key+'</td><td>'+goods[key]+'</td></tr>';
            }
            table += '</table>';
            $(this).after(table);

            table = '<table class="praise praise-bad">' +
                '<tr><th colspan="2">差评</th></tr>';
            var i = 0;
            for (var key in bads){
                if (i++ >=5){
                    break;
                }
                table += '<tr><td>'+key+'</td><td>'+bads[key]+'</td></tr>';
            }
            table += '</table>';
            $(this).after(table);
        });
    }

    $.fn.lineCharts = function(stars){
        return this.each(function () {
            $this = $(this);
            var sum = 0;
            var sumStar = 0;
            for (var i in stars){
                sum += stars[i];
                sumStar += stars[i]*(parseInt(i)+1);
            }
            var html = '';
            for (var i=4; i>=0; i--){
                if (sum){
                    var percent = Math.round(stars[i]/sum*100);
                } else {
                    var percent = 0;
                }
                html += ('<div class="appline">' +
                    '<div class="applinestars">{0}</div>' +
                    '<div class="applinescolor">' +
                    '<div style="background-color:{1}; width:{2}%" class="appbg"></div>' +
                    '</div>' +
                    '<div class="applinesnumber">{3}</div>' +
                    '<div class="clear"></div>' +
                    '</div>').format(str_repeat('<i class="glyphicon glyphicon-star"></i>', i+1), chartColors[4-i], percent, stars[i]);
            }
            if (sum){
                var sumStar = (sumStar/sum).toFixed(1);
            } else {
                var sumStar = 0.0;
            }
            html += ('<div class="appintro ">' +
                '<span class="appfont1">总评分：</span>' +
                '<span class="appfont2">{0}星</span>' +
                '<span class="appfont3">({1}票)</span>' +
                '</div>').format(sumStar, sum);
            $this.html(html);


        });
    }


})(jQuery);