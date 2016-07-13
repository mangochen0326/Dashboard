var $mp_options = {
    pattern: 'yyyy-m',
    startYear: 2015,
    finalYear: 2015,
    selectedYear:2015
};

var $max_year, $max_month, $min_year, $min_month;
var $min_disable_month = [];
var $max_disable_month = [];
var $f_selectedYear, $t_selectedYear;

$(document).ready(function(){

    $("#salesReportForm").validate({
        errorPlacement: function (error, element) {
            if(element != null) {
                $(element).tooltipster('update', $(error).text());
                $(element).tooltipster('show');
            }
            
        },
        success: function (label, element) {
            if(element != null)
                $(element).tooltipster('hide');
        },
    });

	$('#btn').click(function(){

        if($('#f_month').valid() && $('#t_month').valid()) {
            $.post('ajax.php', {
                'func':'getData', 
                'f_month':$('#f_month').val(), 
                't_month':$('#t_month').val(),
                'region':$('#region').val(),
                'si':$('#SI').val(),
                'product':$('#product').val()
                }, function(_data) {
                    eval(_data);
                    if(json.length < 1) {
                        $('#chartdiv').html('<div style="width:100%; line-height:250px; text-align:center; font-weight:bold; font-size:2em; color:#666;">No Result Found</div>');
                        return false;
                    }
            
                    var chart = AmCharts.makeChart("chartdiv", {
                        "type": "serial",
                        "theme": "light",
                        "marginRight": 80,
                        "marginTop": 17,
                        "autoMarginOffset": 20,
                        "dataProvider": json,
                        "valueAxes": [{
                            "dashLength": 1,
                            "position": "left",
                            "title": "Sales (USD)"
                        }],
                        "graphs": [{
                            "bullet": "round",
                            "id": "g1",
                            "bulletBorderAlpha": 1,
                            "bulletColor": "#FFFFFF",
                            "bulletSize": 7,
                            "lineThickness": 2,
                            "title": "Sales",
                            "useLineColorForBulletBorder": true,
                            "valueField": "sales",
                            "balloonText":"<div style='margin:10px; text-align:left;'><span style='font-size:13px'>[[category]]</span><br><span style='font-size:14px'>Sales:[[sales]] (USD)</span>"
                        }],
                        "chartScrollbar": {},
                        "chartCursor": {
                            "valueLineEnabled": true,
                            "valueLineAlpha": 0.5,
                            "fullWidth": true,
                            "cursorAlpha": 0.05
                        },
                        "dataDateFormat": "YYYY-MM",
                        "categoryField": "date",
                        "categoryAxis": {
                            "title": $("#f_month").val() + " ~ " + $("#t_month").val()
                        },
                        "export": {
                            "enabled": true
                        }
                    });
            });
        }
    });

    $.post('ajax.php', {'func':'getRegion'}, function(data){
        eval(data);
        $("#region").empty().append("<option value=''>All</option>");
        for(var i = 0; i < json.length; i++) {
            $("#region").append("<option value='" + json[i]['country_id'] + "'>" + json[i]['country_name'] + "</option>");
        }
    });

    $.post('ajax.php', {'func':'getProduct'}, function(data){
        $("#product").append("<option value=''>All</option>");
        var $json = $.parseJSON(data);
        for(var i = 0; i < $json.length; i++) {
            $("#product").append("<option value='" + $json[i]['product_id'] + "'>" + $json[i]["product_name"] + "</option>");
        }
    });

    $.post('ajax.php', {'func':'getSI'}, function(data){
        $("#SI").append("<option value=''>All</option>");
        var $json = $.parseJSON(data);
        for(var i = 0; i < $json.length; i++) {
            $("#SI").append("<option value='" + $json[i]['si_id'] + "'>" + $json[i]["si_name"] + "</option>");
        }
    });

    $.post('ajax.php', {'func':'initMonthPicker'}, function(data){
        eval(data);
        $max_year = json[0]['max_year'];
        $max_month = json[0]['max_month'];
        $min_year = json[0]['min_year'];
        $min_month = json[0]['min_month'];
        $mp_options.startYear = $min_year;
        $mp_options.finalYear = $max_year;
        for (var i = 0; i < parseInt($min_month, 10); i++) {
            $min_disable_month.push(i);
        }
        for (var i = parseInt($max_month, 10)+1; i <= 12; i++) {
            $max_disable_month.push(i);
        }
        $mp_options.selectedYear = (new Date()).getFullYear();
        $f_selectedYear = $mp_options.selectedYear;
        $t_selectedYear = $mp_options.selectedYear;
        MonthPickerInit();
    });

    $('#salesReportForm input[type="text"]').tooltipster({
        trigger: 'custom',
        onlyOne: false,
        position: 'right'
    });
});

function MonthPickerInit() {
	
    $("#f_month, #t_month").monthpicker($mp_options);

    $('#f_month, #t_month').monthpicker().bind('monthpicker-change-year', function (e, year) {
        $('#f_month').monthpicker('disableMonths', []); // (re)enables all
        $('#t_month').monthpicker('disableMonths', []);
        if (year === $min_year) {
            $('#f_month').monthpicker('disableMonths', $min_disable_month);
            $('#t_month').monthpicker('disableMonths', $min_disable_month);
        }
        if (year === $max_year) {
            $('#f_month').monthpicker('disableMonths', $max_disable_month);
            $('#t_month').monthpicker('disableMonths', $max_disable_month);
        }
        if(e.target.id == 'f_month')
            $f_selectedYear = year;
        else
            $t_selectedYear = year;        

    }).bind("monthpicker-show", function(e){
        $('#f_month').monthpicker('disableMonths', []); // (re)enables all
        $('#t_month').monthpicker('disableMonths', []);
        if ($f_selectedYear == $min_year)
            $('#f_month').monthpicker('disableMonths', $min_disable_month);
        if ($t_selectedYear == $min_year)
            $('#t_month').monthpicker('disableMonths', $min_disable_month);
        
        if ($f_selectedYear == $max_year)
            $('#f_month').monthpicker('disableMonths', $max_disable_month);
        if ($t_selectedYear == $max_year)
            $('#t_month').monthpicker('disableMonths', $max_disable_month);
        
        
    }).bind("monthpicker-click-month", function(e, month){
        
    });
}


(function (d, f, g, b) {
    var e = "tooltipster",
        c = {
            animation: "fade",
            arrow: true,
            arrowColor: "",
            content: "",
            delay: 200,
            fixedWidth: 0,
            maxWidth: 0,
            functionBefore: function (l, m) {
                m()
            },
            functionReady: function (l, m) {},
            functionAfter: function (l) {},
            icon: "(?)",
            iconDesktop: false,
            iconTouch: false,
            iconTheme: ".tooltipster-icon",
            interactive: false,
            interactiveTolerance: 350,
            offsetX: 0,
            offsetY: 0,
            onlyOne: true,
            position: "top",
            speed: 350,
            timer: 0,
            theme: ".tooltipster-default",
            touchDevices: true,
            trigger: "hover"
        };

    function h(m, l) {
        this.element = m;
        this.options = d.extend({}, c, l);
        this._defaults = c;
        this._name = e;
        this.init()
    }
    function j() {
        return !!("ontouchstart" in f)
    }
    function a() {
        var l = g.body || g.documentElement;
        var n = l.style;
        var o = "transition";
        if (typeof n[o] == "string") {
            return true
        }
        v = ["Moz", "Webkit", "Khtml", "O", "ms"], o = o.charAt(0).toUpperCase() + o.substr(1);
        for (var m = 0; m < v.length; m++) {
            if (typeof n[v[m] + o] == "string") {
                return true
            }
        }
        return false
    }
    var k = true;
    if (!a()) {
        k = false
    }
    h.prototype = {
        init: function () {
            var r = d(this.element);
            var n = this;
            var q = true;
            if ((n.options.touchDevices == false) && (j())) {
                q = false
            }
            if (g.all && !g.querySelector) {
                q = false
            }
            if (q == true) {
                if ((this.options.iconDesktop == true) && (!j()) || ((this.options.iconTouch == true) && (j()))) {
                    var m = r.attr("title");
                    r.removeAttr("title");
                    var p = n.options.iconTheme;
                    var o = d('<span class="' + p.replace(".", "") + '" title="' + m + '">' + this.options.icon + "</span>");
                    o.insertAfter(r);
                    r.data("tooltipsterIcon", o);
                    r = o
                }
                var l = d.trim(n.options.content).length > 0 ? n.options.content : r.attr("title");
                r.data("tooltipsterContent", l);
                r.removeAttr("title");
                if ((this.options.touchDevices == true) && (j())) {
                    r.bind("touchstart", function (t, s) {
                        n.showTooltip()
                    })
                } else {
                    if (this.options.trigger == "hover") {
                        r.on("mouseenter.tooltipster", function () {
                            n.showTooltip()
                        });
                        if (this.options.interactive == true) {
                            r.on("mouseleave.tooltipster", function () {
                                var t = r.data("tooltipster");
                                var u = false;
                                if ((t !== b) && (t !== "")) {
                                    t.mouseenter(function () {
                                        u = true
                                    });
                                    t.mouseleave(function () {
                                        u = false
                                    });
                                    var s = setTimeout(function () {
                                        if (u == true) {
                                            t.mouseleave(function () {
                                                n.hideTooltip()
                                            })
                                        } else {
                                            n.hideTooltip()
                                        }
                                    }, n.options.interactiveTolerance)
                                } else {
                                    n.hideTooltip()
                                }
                            })
                        } else {
                            r.on("mouseleave.tooltipster", function () {
                                n.hideTooltip()
                            })
                        }
                    }
                    if (this.options.trigger == "click") {
                        r.on("click.tooltipster", function () {
                            if ((r.data("tooltipster") == "") || (r.data("tooltipster") == b)) {
                                n.showTooltip()
                            } else {
                                n.hideTooltip()
                            }
                        })
                    }
                }
            }
        },
        showTooltip: function (m) {
            var n = d(this.element);
            var l = this;
            if (n.data("tooltipsterIcon") !== b) {
                n = n.data("tooltipsterIcon")
            }
            if ((d(".tooltipster-base").not(".tooltipster-dying").length > 0) && (l.options.onlyOne == true)) {
                d(".tooltipster-base").not(".tooltipster-dying").not(n.data("tooltipster")).each(function () {
                    d(this).addClass("tooltipster-kill");
                    var o = d(this).data("origin");
                    o.data("plugin_tooltipster").hideTooltip()
                })
            }
            n.clearQueue().delay(l.options.delay).queue(function () {
                l.options.functionBefore(n, function () {
                    if ((n.data("tooltipster") !== b) && (n.data("tooltipster") !== "")) {
                        var w = n.data("tooltipster");
                        if (!w.hasClass("tooltipster-kill")) {
                            var s = "tooltipster-" + l.options.animation;
                            w.removeClass("tooltipster-dying");
                            if (k == true) {
                                w.clearQueue().addClass(s + "-show")
                            }
                            if (l.options.timer > 0) {
                                var q = w.data("tooltipsterTimer");
                                clearTimeout(q);
                                q = setTimeout(function () {
                                    w.data("tooltipsterTimer", b);
                                    l.hideTooltip()
                                }, l.options.timer);
                                w.data("tooltipsterTimer", q)
                            }
                            if ((l.options.touchDevices == true) && (j())) {
                                d("body").bind("touchstart", function (B) {
                                    if (l.options.interactive == true) {
                                        var D = d(B.target);
                                        var C = true;
                                        D.parents().each(function () {
                                            if (d(this).hasClass("tooltipster-base")) {
                                                C = false
                                            }
                                        });
                                        if (C == true) {
                                            l.hideTooltip();
                                            d("body").unbind("touchstart")
                                        }
                                    } else {
                                        l.hideTooltip();
                                        d("body").unbind("touchstart")
                                    }
                                })
                            }
                        }
                    } else {
                        d("body").css("overflow-x", "hidden");
                        var x = n.data("tooltipsterContent");
                        var u = l.options.theme;
                        var y = u.replace(".", "");
                        var s = "tooltipster-" + l.options.animation;
                        var r = "-webkit-transition-duration: " + l.options.speed + "ms; -webkit-animation-duration: " + l.options.speed + "ms; -moz-transition-duration: " + l.options.speed + "ms; -moz-animation-duration: " + l.options.speed + "ms; -o-transition-duration: " + l.options.speed + "ms; -o-animation-duration: " + l.options.speed + "ms; -ms-transition-duration: " + l.options.speed + "ms; -ms-animation-duration: " + l.options.speed + "ms; transition-duration: " + l.options.speed + "ms; animation-duration: " + l.options.speed + "ms;";
                        var o = l.options.fixedWidth > 0 ? "width:" + l.options.fixedWidth + "px;" : "";
                        var z = l.options.maxWidth > 0 ? "max-width:" + l.options.maxWidth + "px;" : "";
                        var t = l.options.interactive == true ? "pointer-events: auto;" : "";
                        var w = d('<div class="tooltipster-base ' + y + " " + s + '" style="' + o + " " + z + " " + t + " " + r + '"><div class="tooltipster-content">' + x + "</div></div>");
                        w.appendTo("body");
                        n.data("tooltipster", w);
                        w.data("origin", n);
                        l.positionTooltip();
                        l.options.functionReady(n, w);
                        if (k == true) {
                            w.addClass(s + "-show")
                        } else {
                            w.css("display", "none").removeClass(s).fadeIn(l.options.speed)
                        }
                        var A = x;
                        var p = setInterval(function () {
                            var B = n.data("tooltipsterContent");
                            if (d("body").find(n).length == 0) {
                                w.addClass("tooltipster-dying");
                                l.hideTooltip()
                            } else {
                                if ((A !== B) && (B !== "")) {
                                    A = B;
                                    w.find(".tooltipster-content").html(B);
                                    w.css({
                                        width: "",
                                        "-webkit-transition-duration": l.options.speed + "ms",
                                        "-moz-transition-duration": l.options.speed + "ms",
                                        "-o-transition-duration": l.options.speed + "ms",
                                        "-ms-transition-duration": l.options.speed + "ms",
                                        "transition-duration": l.options.speed + "ms",
                                        "-webkit-transition-property": "-webkit-transform",
                                        "-moz-transition-property": "-moz-transform",
                                        "-o-transition-property": "-o-transform",
                                        "-ms-transition-property": "-ms-transform",
                                        "transition-property": "transform"
                                    }).addClass("tooltipster-content-changing");
                                    setTimeout(function () {
                                        w.removeClass("tooltipster-content-changing");
                                        setTimeout(function () {
                                            w.css({
                                                "-webkit-transition-property": "",
                                                "-moz-transition-property": "",
                                                "-o-transition-property": "",
                                                "-ms-transition-property": "",
                                                "transition-property": ""
                                            })
                                        }, l.options.speed)
                                    }, l.options.speed);
                                    tooltipWidth = w.outerWidth(false);
                                    tooltipInnerWidth = w.innerWidth();
                                    tooltipHeight = w.outerHeight(false);
                                    l.positionTooltip()
                                }
                            }
                            if ((d("body").find(w).length == 0) || (d("body").find(n).length == 0)) {
                                clearInterval(p)
                            }
                        }, 200);
                        if (l.options.timer > 0) {
                            var q = setTimeout(function () {
                                w.data("tooltipsterTimer", b);
                                l.hideTooltip()
                            }, l.options.timer + l.options.speed);
                            w.data("tooltipsterTimer", q)
                        }
                        if ((l.options.touchDevices == true) && (j())) {
                            d("body").bind("touchstart", function (B) {
                                if (l.options.interactive == true) {
                                    var D = d(B.target);
                                    var C = true;
                                    D.parents().each(function () {
                                        if (d(this).hasClass("tooltipster-base")) {
                                            C = false
                                        }
                                    });
                                    if (C == true) {
                                        l.hideTooltip();
                                        d("body").unbind("touchstart")
                                    }
                                } else {
                                    l.hideTooltip();
                                    d("body").unbind("touchstart")
                                }
                            })
                        }
                        w.mouseleave(function () {
                            l.hideTooltip()
                        })
                    }
                });
                n.dequeue()
            })
        },
        hideTooltip: function (m) {
            var p = d(this.element);
            var l = this;
            if (p.data("tooltipsterIcon") !== b) {
                p = p.data("tooltipsterIcon")
            }
            var o = p.data("tooltipster");
            if (o == b) {
                o = d(".tooltipster-dying")
            }
            p.clearQueue();
            if ((o !== b) && (o !== "")) {
                var q = o.data("tooltipsterTimer");
                if (q !== b) {
                    clearTimeout(q)
                }
                var n = "tooltipster-" + l.options.animation;
                if (k == true) {
                    o.clearQueue().removeClass(n + "-show").addClass("tooltipster-dying").delay(l.options.speed).queue(function () {
                        o.remove();
                        p.data("tooltipster", "");
                        d("body").css("verflow-x", "");
                        l.options.functionAfter(p)
                    })
                } else {
                    o.clearQueue().addClass("tooltipster-dying").fadeOut(l.options.speed, function () {
                        o.remove();
                        p.data("tooltipster", "");
                        d("body").css("verflow-x", "");
                        l.options.functionAfter(p)
                    })
                }
            }
        },
        positionTooltip: function (O) {
            var A = d(this.element);
            var ab = this;
            if (A.data("tooltipsterIcon") !== b) {
                A = A.data("tooltipsterIcon")
            }
            if ((A.data("tooltipster") !== b) && (A.data("tooltipster") !== "")) {
                var ah = A.data("tooltipster");
                ah.css("width", "");
                var ai = d(f).width();
                var B = A.outerWidth(false);
                var ag = A.outerHeight(false);
                var al = ah.outerWidth(false);
                var m = ah.innerWidth() + 1;
                var M = ah.outerHeight(false);
                var aa = A.offset();
                var Z = aa.top;
                var u = aa.left;
                var y = b;
                if (A.is("area")) {
                    var T = A.attr("shape");
                    var af = A.parent().attr("name");
                    var P = d('img[usemap="#' + af + '"]');
                    var n = P.offset().left;
                    var L = P.offset().top;
                    var W = A.attr("coords") !== b ? A.attr("coords").split(",") : b;
                    if (T == "circle") {
                        var N = parseInt(W[0]);
                        var r = parseInt(W[1]);
                        var D = parseInt(W[2]);
                        ag = D * 2;
                        B = D * 2;
                        Z = L + r - D;
                        u = n + N - D
                    } else {
                        if (T == "rect") {
                            var N = parseInt(W[0]);
                            var r = parseInt(W[1]);
                            var q = parseInt(W[2]);
                            var J = parseInt(W[3]);
                            ag = J - r;
                            B = q - N;
                            Z = L + r;
                            u = n + N
                        } else {
                            if (T == "poly") {
                                var x = [];
                                var ae = [];
                                var H = 0,
                                    G = 0,
                                    ad = 0,
                                    ac = 0;
                                var aj = "even";
                                for (i = 0; i < W.length; i++) {
                                    var F = parseInt(W[i]);
                                    if (aj == "even") {
                                        if (F > ad) {
                                            ad = F;
                                            if (i == 0) {
                                                H = ad
                                            }
                                        }
                                        if (F < H) {
                                            H = F
                                        }
                                        aj = "odd"
                                    } else {
                                        if (F > ac) {
                                            ac = F;
                                            if (i == 1) {
                                                G = ac
                                            }
                                        }
                                        if (F < G) {
                                            G = F
                                        }
                                        aj = "even"
                                    }
                                }
                                ag = ac - G;
                                B = ad - H;
                                Z = L + G;
                                u = n + H
                            } else {
                                ag = P.outerHeight(false);
                                B = P.outerWidth(false);
                                Z = L;
                                u = n
                            }
                        }
                    }
                }
                if (ab.options.fixedWidth == 0) {
                    ah.css({
                        width: m + "px",
                        "padding-left": "0px",
                        "padding-right": "0px"
                    })
                }
                var s = 0,
                    V = 0;
                var X = parseInt(ab.options.offsetY);
                var Y = parseInt(ab.options.offsetX);
                var p = "";

                function w() {
                    var an = d(f).scrollLeft();
                    if ((s - an) < 0) {
                        var am = s - an;
                        s = an;
                        ah.data("arrow-reposition", am)
                    }
                    if (((s + al) - an) > ai) {
                        var am = s - ((ai + an) - al);
                        s = (ai + an) - al;
                        ah.data("arrow-reposition", am)
                    }
                }
                function t(an, am) {
                    if (((Z - d(f).scrollTop() - M - X - 12) < 0) && (am.indexOf("top") > -1)) {
                        ab.options.position = an;
                        y = am
                    }
                    if (((Z + ag + M + 12 + X) > (d(f).scrollTop() + d(f).height())) && (am.indexOf("bottom") > -1)) {
                        ab.options.position = an;
                        y = am;
                        V = (Z - M) - X - 12
                    }
                }
                if (ab.options.position == "top") {
                    var Q = (u + al) - (u + B);
                    s = (u + Y) - (Q / 2);
                    V = (Z - M) - X - 12;
                    w();
                    t("bottom", "top")
                }
                if (ab.options.position == "top-left") {
                    s = u + Y;
                    V = (Z - M) - X - 12;
                    w();
                    t("bottom-left", "top-left")
                }
                if (ab.options.position == "top-right") {
                    s = (u + B + Y) - al;
                    V = (Z - M) - X - 12;
                    w();
                    t("bottom-right", "top-right")
                }
                if (ab.options.position == "bottom") {
                    var Q = (u + al) - (u + B);
                    s = u - (Q / 2) + Y;
                    V = (Z + ag) + X + 12;
                    w();
                    t("top", "bottom")
                }
                if (ab.options.position == "bottom-left") {
                    s = u + Y;
                    V = (Z + ag) + X + 12;
                    w();
                    t("top-left", "bottom-left")
                }
                if (ab.options.position == "bottom-right") {
                    s = (u + B + Y) - al;
                    V = (Z + ag) + X + 12;
                    w();
                    t("top-right", "bottom-right")
                }
                if (ab.options.position == "left") {
                    s = u - Y - al - 12;
                    myLeftMirror = u + Y + B + 12;
                    var K = (Z + M) - (Z + A.outerHeight(false));
                    V = Z - (K / 2) - X;
                    if ((s < 0) && ((myLeftMirror + al) > ai)) {
                        var o = parseFloat(ah.css("border-width")) * 2;
                        var l = (al + s) - o;
                        ah.css("width", l + "px");
                        M = ah.outerHeight(false);
                        s = u - Y - l - 12 - o;
                        K = (Z + M) - (Z + A.outerHeight(false));
                        V = Z - (K / 2) - X
                    } else {
                        if (s < 0) {
                            s = u + Y + B + 12;
                            ah.data("arrow-reposition", "left")
                        }
                    }
                }
                if (ab.options.position == "right") {
                    s = u + Y + B + 12;
                    myLeftMirror = u - Y - al - 12;
                    var K = (Z + M) - (Z + A.outerHeight(false));
                    V = Z - (K / 2) - X;
                    if (((s + al) > ai) && (myLeftMirror < 0)) {
                        var o = parseFloat(ah.css("border-width")) * 2;
                        var l = (ai - s) - o;
                        ah.css("width", l + "px");
                        M = ah.outerHeight(false);
                        K = (Z + M) - (Z + A.outerHeight(false));
                        V = Z - (K / 2) - X
                    } else {
                        if ((s + al) > ai) {
                            s = u - Y - al - 12;
                            ah.data("arrow-reposition", "right")
                        }
                    }
                }
                if (ab.options.arrow == true) {
                    var I = "tooltipster-arrow-" + ab.options.position;
                    if (ab.options.arrowColor.length < 1) {
                        var R = ah.css("background-color")
                    } else {
                        var R = ab.options.arrowColor
                    }
                    var ak = ah.data("arrow-reposition");
                    if (!ak) {
                        ak = ""
                    } else {
                        if (ak == "left") {
                            I = "tooltipster-arrow-right";
                            ak = ""
                        } else {
                            if (ak == "right") {
                                I = "tooltipster-arrow-left";
                                ak = ""
                            } else {
                                ak = "left:" + ak + "px;"
                            }
                        }
                    }
                    if ((ab.options.position == "top") || (ab.options.position == "top-left") || (ab.options.position == "top-right")) {
                        var U = parseFloat(ah.css("border-bottom-width"));
                        var z = ah.css("border-bottom-color")
                    } else {
                        if ((ab.options.position == "bottom") || (ab.options.position == "bottom-left") || (ab.options.position == "bottom-right")) {
                            var U = parseFloat(ah.css("border-top-width"));
                            var z = ah.css("border-top-color")
                        } else {
                            if (ab.options.position == "left") {
                                var U = parseFloat(ah.css("border-right-width"));
                                var z = ah.css("border-right-color")
                            } else {
                                if (ab.options.position == "right") {
                                    var U = parseFloat(ah.css("border-left-width"));
                                    var z = ah.css("border-left-color")
                                } else {
                                    var U = parseFloat(ah.css("border-bottom-width"));
                                    var z = ah.css("border-bottom-color")
                                }
                            }
                        }
                    }
                    if (U > 1) {
                        U++
                    }
                    var E = "";
                    if (U !== 0) {
                        var C = "";
                        var S = "border-color: " + z + ";";
                        if (I.indexOf("bottom") !== -1) {
                            C = "margin-top: -" + U + "px;"
                        } else {
                            if (I.indexOf("top") !== -1) {
                                C = "margin-bottom: -" + U + "px;"
                            } else {
                                if (I.indexOf("left") !== -1) {
                                    C = "margin-right: -" + U + "px;"
                                } else {
                                    if (I.indexOf("right") !== -1) {
                                        C = "margin-left: -" + U + "px;"
                                    }
                                }
                            }
                        }
                        E = '<span class="tooltipster-arrow-border" style="' + C + " " + S + ';"></span>'
                    }
                    ah.find(".tooltipster-arrow").remove();
                    p = '<div class="' + I + ' tooltipster-arrow" style="' + ak + '">' + E + '<span style="border-color:' + R + ';"></span></div>';
                    ah.append(p)
                }
                ah.css({
                    top: V + "px",
                    left: s + "px"
                });
                if (y !== b) {
                    ab.options.position = y
                }
            }
        }
    };
    d.fn[e] = function (m) {
        if (typeof m === "string") {
            var o = this;
            var l = arguments[1];
            if (o.data("plugin_tooltipster") == b) {
                var n = o.find("*");
                o = d();
                n.each(function () {
                    if (d(this).data("plugin_tooltipster") !== b) {
                        o.push(d(this))
                    }
                })
            }
            o.each(function () {
                switch (m.toLowerCase()) {
                    case "show":
                        d(this).data("plugin_tooltipster").showTooltip();
                        break;
                    case "hide":
                        d(this).data("plugin_tooltipster").hideTooltip();
                        break;
                    case "destroy":
                        d(this).data("plugin_tooltipster").hideTooltip();
                        d(this).data("plugin_tooltipster", "").attr("title", o.data("tooltipsterContent")).data("tooltipsterContent", "").data("plugin_tooltipster", "").off("mouseenter.tooltipster mouseleave.tooltipster click.tooltipster");
                        break;
                    case "update":
                        d(this).data("tooltipsterContent", l);
                        break;
                    case "reposition":
                        d(this).data("plugin_tooltipster").positionTooltip();
                        break
                }
            });
            return this
        }
        return this.each(function () {
            if (!d.data(this, "plugin_" + e)) {
                d.data(this, "plugin_" + e, new h(this, m))
            }
            var p = d(this).data("plugin_tooltipster").options;
            if ((p.iconDesktop == true) && (!j()) || ((p.iconTouch == true) && (j()))) {
                var q = d(this).data("plugin_tooltipster");
                d(this).next().data("plugin_tooltipster", q)
            }
        })
    };
    if (j()) {
        f.addEventListener("orientationchange", function () {
            if (d(".tooltipster-base").length > 0) {
                d(".tooltipster-base").each(function () {
                    var l = d(this).data("origin");
                    l.data("plugin_tooltipster").hideTooltip()
                })
            }
        }, false)
    }
    d(f).on("resize.tooltipster", function () {
        var l = d(".tooltipster-base").data("origin");
        if(l != null)
            l.tooltipster("reposition")
    })
})(jQuery, window, document);