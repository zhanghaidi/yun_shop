var myModel = angular.module('YunShop1', ['ng.ueditor'], function ($compileProvider) {
    $compileProvider.directive('compile', function ($compile) {
        return function (scope, element, attrs) {
            scope.$watch(
                function (scope) {
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
myModel.controller('YunShopController1', ['$scope', '$http', function ($scope, $http) {

    // $scope.menus=[
    //     {
    //         bgcolor: "#fafafa",
    //         id: 1,
    //         textcolor: "#666666",
    //         title: "购物中心",
    //         url: ""
    //     },
    // ]
    $scope.menus = jsonMenus;
    console.log(jsonMenus);
    // $scope.params = {
    //     bgcolor: "#fafafa",
    //     bgcolorhigh: "#fafafa",
    //     bordercolor: "#bfbfbf",
    //     previewbg: "#999999",
    //     showborder: 0,
    //     bgalpha:1,
    //     textcolor: "#666666",
    //     textcolorhigh: "#666666",
    //     searchword:"搜索：输入关键字在店内搜索"
    // }
    console.log(jsonParams);
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
    $scope.addTopMenu = function () {
        console.log($scope.menus);
        var mid = "menu_" + new Date().getTime();
        $scope.menus.push({
            id: mid,
            title: '',
            url: '',
        });
        defineSortable($scope);
        $scope.clear();
    };
    $scope.deleteMenu = function (menu, sub, obj) {
        if (confirm('将删除该菜单, 是否继续? ')) {
            $scope.menus = _.without($scope.menus, menu);
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
    $scope.focus = 'M0000000000000';
    $scope.save = function () {
        var menu_id = topMenuId;
        var menus = angular.toJson($scope.menus);
        var params = angular.toJson($scope.params);
        var menu_name = $.trim($(":input[name=menuname]").val());
            console.log($("input[name=menuname]").val());
        //所有菜单名称不能重复
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
                url: storeUrl,
                data: {
                    menu_id: menu_id,
                    menu_name: menu_name,
                    params: params,
                    menus: menus
                },
                success: function (data) {
                    if (data.result == '1') {
                        alert("保存成功！");
                        location.href = topMenuIndex;
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
                            if ($(this).attr('id') == menusArray[i].id) {
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
    });
}