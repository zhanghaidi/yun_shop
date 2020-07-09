function switchtab(tag, n) {
    $("#" + tag + "-" + n).fadeIn().siblings().hide();
    $("#" + tag + "-nav-" + n).addClass("active").siblings().removeClass("active");
}

$(function () {
    require([util_name], function (util) {
        var preview_id = util.cookie.get('preview_id');
        if (preview_id) {
            preview(preview_id);
        }
    });

    $(".fe-save-submit3").click(function () {
        location.href = menuIndex;
    });

    $(".fe-save-info-type-ok").click(function () {
        var pagetype = $(this).data("type");
        if (pagetype != '2' || pagetype != '3') {
            $(this).find(".fe-save-main-radio").addClass("fe-save-main-radio2").text("√");
            $(this).siblings().find(".fe-save-main-radio").removeClass("fe-save-main-radio2").text("");
        }
        $("input[name=pagetype]").val(pagetype);
    });
});

//var myModel = angular.module('YunShop', ['ng.ueditor']);
var myModel = angular.module('YunShop', ['ng.ueditor'], function ($compileProvider) {
    //$interpolateProvider.startSymbol('@{{').endSymbol('}}');
    // configure new 'compile' directive by passing a directive
    // factory function. The factory function injects the '$compile'
    $compileProvider.directive('compile', function ($compile) {
        // directive factory creates a link function
        return function (scope, element, attrs) {
            scope.$watch(
                function (scope) {
                    // watch the 'compile' expression for changes
                    return scope.$eval(attrs.compile);
                },
                function (value) {
                    // when the 'compile' expression changes
                    // assign it into the current DOM
                    element.html(value);
                    // compile the new DOM and link it to the current
                    // scope.
                    // NOTE: we only compile .childNodes so that
                    // we don't get into infinite loop compiling ourselves
                    $compile(element.contents())(scope);
                }
            );
        };
    });
});

myModel.controller('YunShopController', ['$scope', '$http', function ($scope, $http) {
    console.log('controller');
    $scope.menus = jsonMenus;
    $scope.params = jsonParams;
    $scope.searchGoods = [];

    $scope.underscore = null;
    require(['underscore'], function (underscore) {
        $scope.underscore = underscore;
    });
    $scope.clear = function (m) {
        angular.forEach($scope.menus, function (m, index) {
            m.textcolor = index == 0 ? $scope.params.textcolorhigh : $scope.params.textcolor;
            m.bgcolor = index == 0 ? $scope.params.bgcolorhigh : $scope.params.bgcolor;
            m.iconcolor = index == 0 ? $scope.params.iconcolorhigh : $scope.params.iconcolor;
            m.bordercolor = index == 0 ? $scope.params.bordercolorhigh : $scope.params.bordercolor;
        });
    }

    $scope.searchgood = function (Mid) {
        console.log(Mid);
        kw = $("#secect-kw").val();
        //var data = $.param({kw:kw});

        $http.post(myLinkGoods,
            {kw:kw}
        ).success(function (data) {
            console.log(data);
            $scope.searchGoods = [];
            angular.forEach(data, function (d, i) {
                Sid = 'S' + new Date().getTime();
                $scope.searchGoods.push({
                    id: Sid + i,
                    title: data[i].title,
                    thumb: data[i].thumb,
                    //goodid: data[i].id,
                    price: data[i].price,
                    market_price: data[i].market_price,
                    url: data[i].url

                    //sales: data[i].real_sales,
                    //unit: data[i].sku
                });
            });
            //$("div[mid=" + Mid + "]").mouseover();
        }).error(function () {
            alert('查询商品信息失败！请刷新页面。');
        });
    }

    // 1.1 选择链接
    $scope.chooseUrl = function () {
        $('#modal-mylink').attr({"menid":Mid,"Cid":Cid,T:T});
        console.log('***' + Mid);
        $('#modal-mylink').modal();
    }
    $scope.selectUrl = function (menu, event) {
        $('.popovermenu').hide();
        $(event.currentTarget).next().toggle();
    }
    $scope.confirmUrl = function (menu, event) {

        $(event.currentTarget).closest('.popovermenu').toggle();
    }
    $scope.clearUrl = function (menu, event) {

        menu.url = '';

    }

    $scope.clearMiniUrl = function (menu, event) {

        menu.appID = '';
        menu.miniUrl = '';
    }

    $scope.selectIcon = function (menu, event) {
        require.config({
            paths:{
                util:inits+"static/resource/js/app/"+'util',
                utils:inits+"static/resource/js/app/"+'utils',
            }
        });
        require([inits+"static/resource/js/app/"+util_name+'.js', "jquery"], function (u, $) {
            var btn = $(event.currentTarget);
            var spview = btn.parent();
            var ipt = spview.find('.icon');
            u.iconBrowser(function (ico) {
                ipt.val(ico);
                menu.icon = ico;
                $scope.$apply();

            });
        });

    }
    $scope.addMenu = function () {
        console.log($scope.menus);
        console.log($scope.params);
        if ($scope.menus.length >= 5) {
            return;
        }
        var mid = "menu_" + new Date().getTime();
        $scope.menus.push({
            id: mid,
            title: '',
            icon: '',
            url: '',
            hrefChoice : '1',
            subMenus: []
        });
        defineSortable($scope);
        $scope.clear();
    };

    $scope.addSubMenu = function (menu, obj) {
        $('.parentmenu').eq(obj.$index).find('a').eq(2).hide();
        if (menu.subMenus.length >= 5) {
            return;
        }
        var mid = "menu_" + new Date().getTime();
        menu.subMenus.push({
            id:mid,
            title: '',
            type: 'url',
            url: '',
            forward: '',
            hrefChoice : '1'
        });
        defineSortable($scope);
    };

    $scope.deleteMenu = function (menu, sub, obj) {
        if (sub) {
            if (typeof obj == 'object') {
                var text = $('.sonmenu').eq(obj.$parent.$index).find('input[type="text"]').eq(obj.$index);

                if (text.val() != '') {
                    if (confirm('将删除该菜单, 是否继续? ')) {
                        if (menu.subMenus.length == 1) {
                            $('.parentmenu').eq(obj.$parent.$index).find('a').eq(2).show();
                        }
                        menu.subMenus = _.without(menu.subMenus, sub);
                    }
                } else {
                    if (menu.subMenus.length == 1) {
                        $('.parentmenu').eq(obj.$parent.$index).find('a').eq(2).show();
                    }
                    menu.subMenus = _.without(menu.subMenus, sub);
                }
            }
        } else {
            if (menu.subMenus.length > 0 && !confirm('将同时删除所有子菜单, 是否继续? ')) {
                return;
            }
            $scope.menus = _.without($scope.menus, menu);
            $scope.clear();
        }
    };

    //选择链接
    $scope.currentMenu = null;
    $scope.chooseUrl = function (menu) {
        $scope.currentMenu = menu;
        $('#modal-mylink').modal();
    }

    $scope.chooseLink = function (type, hid) {
        console.log("hid:" + hid);
        var href = $(" #fe-tab-link-li-" + hid).attr("nhref");
        if (href == undefined) {
            href = $(" #" + hid).attr("nhref");
            console.log("hrefuuuu:" + href);
        }
        if (hid == 'other-1') {
            href = $("textarea[name=mylink_href]").val();
        }
        console.log("href:" + href);

        $scope.currentMenu.url = href;
        //$scope.currentMenu.url = $("#fe-tab-link-" + type + " #fe-tab-link-li-" + hid).data("href");
        $('#modal-mylink .close').click();
    }
    $scope.temp = {
        notcie: []
    };
    /*$scope.ajaxselect = function (type) {
        val = $("#select-" + type + "-kw").val();

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: "{php echo $this->createPluginWebUrl('designer',array('op'=>'api','apido'=>'selectlink'))}",
            data: {kw: val, type: type},
            success: function (data) {
                $scope.temp[type] = data;
                $scope.$apply();
            },
            error: function () {
                alert('查询失败！请刷新页面。');
            }
        });
    }*/

    $scope.focus = 'M0000000000000';

    $scope.save = function () {
        var menu_id = menuId;
        var menus = angular.toJson($scope.menus);
        var params = angular.toJson($scope.params);
        var menu_name = $.trim($(":input[name=menuname]").val());

        //所有菜单名称不能重
        var tmpMenusTitle = new Array();
        $('.ui-sortable').find(".fe-panel-editor-input1").each(function (index, e) {
            //console.dir($(this).val());
            tmpMenusTitle.push($(this).val());
        });
        var sortTmpMenusTitle = tmpMenusTitle.sort();
        for (var i = 0; i < sortTmpMenusTitle.length - 1; i++) {
            if (sortTmpMenusTitle[i] == sortTmpMenusTitle[i + 1]) {
                alert('菜单名称重复!');
                return;
            }
        }

        if (!menu_name) {
            alert('请填写菜单名称!');
            $(":input[name=menuname]").focus();
            return;
        }
        $(".fe-save-submit").text('保存中...').addClass("fe-save-disabled").data('saving', '1');
        $(".fe-save-submit2").css("color", "#bbb");
        if ($(".fe-save-submit").data('saving') == 1) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: menuStore,
                data: {
                    menu_id: menu_id,
                    menu_name: menu_name,
                    params: params,
                    menus: menus
                },
                success: function (data) {
                    if (data.result == '1') {
                        alert("保存成功！");
                        location.href = menuIndex;
                    } else {
                        alert(data.message);
                        $(".fe-save-submit").text('保存').removeClass("fe-save-disabled").data('saving', '0');
                        $(".fe-save-submit2").css("color", "#4bb5fb")
                    }
                }
                , error: function () {
                    alert('保存失败请重试');
                    $(".fe-save-submit").text('保存').removeClass("fe-save-disabled").data('saving', '0');
                    $(".fe-save-submit2").css("color", "#4bb5fb")
                }
            });
        }
    }

    defineSortable($scope);

    $scope.openMenu = function (menu, event) {
        if (menu.subMenus.length <= 0) {
            return;
        }
        $('.sub').hide();
        var li = $(event.currentTarget).closest('li');
        li.find('.sub').toggle().css('width', li.width() - 10).css('opacity', 1);
        angular.forEach($scope.menus, function (m, index) {
            m.textcolor = m == menu ? $scope.params.textcolorhigh : $scope.params.textcolor;
            m.bgcolor = m == menu ? $scope.params.bgcolorhigh : $scope.params.bgcolor;
            m.iconcolor = m == menu ? $scope.params.iconcolorhigh : $scope.params.iconcolor;
            m.bordercolor = m == menu ? $scope.params.bordercolorhigh : $scope.params.bordercolor;
        });

    }

}]);

function defineSortable($scope) {
    require(['jquery.ui'], function () {
        //主菜单可拖拽初始化
        $('.ui-sortable').sortable(
            {handle: '.btn-move'},
            {
                update: function (e, ui) {
                    var menusArray = $scope.menus;
                    var tmpArray = new Array();
                    var tmpIndex = 0;

                    //拖拽后重新构建menus数组中各主菜单顺序
                    $(this).find(".fe-panel-editor-input1").each(function () {
                        for (var i in menusArray) {
                            if ($(this).attr("id") == menusArray[i].id) {
                                tmpArray[tmpIndex] = menusArray[i];
                                tmpIndex++;
                                break;
                            }
                        }
                    });
                    $scope.menus = tmpArray;
                }
            }
        );

        //子菜单可拖拽初始化
        $('.ui-sortable-sub').sortable(
            {handle: '.btn-move'},
            {
                update: function (e, ui) {
                    var menusArray = $scope.menus;
                    var tmpArray = new Array();
                    $(this).find(".fe-panel-editor-input1").each(function () {
                        tmpArray.push($(this).attr("id"));
                    });
                    //拖拽后重新构建menus数组中各子菜单顺序
                    for (var i in menusArray) {
                        var tmpSubMenus = menusArray[i].subMenus;
                        var aSubMenus = new Array();
                        for (var j in tmpArray) {
                            for (var k in tmpSubMenus) {
                                if (tmpArray[j] == tmpSubMenus[k].id) {
                                    aSubMenus[j] = tmpSubMenus[k];
                                    break;
                                }
                            }
                        }
                        if (aSubMenus.length > 0) {
                            menusArray[i].subMenus = aSubMenus;
                        }
                    }
                    $scope.menus = menusArray;
                    //console.dir(menusArray);
                }
            }
        );

    });
}

