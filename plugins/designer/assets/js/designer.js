// 百度编辑器初始化
var opts = {
    type: 'image',
    direct: false,
    multi: true,
    tabs: {
        'upload': 'active',
        'browser': '',
        'crawler': ''
    },
    path: '',
    dest_dir: '',
    global: false,
    thumb: false,
    width: 0
};
UE.registerUI('myinsertimage', function (editor, uiName) {
        editor.registerCommand(uiName, {
            execCommand: function () {
                require(['fileUploader'],
                    function (uploader) {
                        uploader.show(function (imgs) {
                                if (imgs.length == 0) {
                                    return;
                                } else if (imgs.length == 1) {
                                    editor.execCommand('insertimage', {
                                        'src': imgs[0]['url'],
                                        '_src': imgs[0]['attachment'],
                                        'width': '100%',
                                        'alt': imgs[0].filename
                                    });
                                } else {
                                    var imglist = [];
                                    for (i in imgs) {
                                        imglist.push({
                                            'src': imgs[i]['url'],
                                            '_src': imgs[i]['attachment'],
                                            'width': '100%',
                                            'alt': imgs[i].filename
                                        });
                                    }
                                    editor.execCommand('insertimage', imglist);
                                }
                            },
                            opts);
                    });
            }
        });
        var btn = new UE.ui.Button({
            name: '插入图片',
            title: '插入图片',
            cssRules: 'background-position: -726px -77px',
            onclick: function () {
                editor.execCommand(uiName);
            }
        });
        editor.addListener('selectionchange',
            function () {
                var state = editor.queryCommandState(uiName);
                if (state == -1) {
                    btn.setDisabled(true);
                    btn.setChecked(false);
                } else {
                    btn.setDisabled(false);
                    btn.setChecked(state);
                }
            });
        return btn;
    },
    19);


$(function () {
    require(['util'], function (util) {
        var preview_id = util.cookie.get('preview_id');
        if (preview_id) {
            preview(preview_id);
        }
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

function switchtab(tag, n) {
    $("#" + tag + "-" + n).fadeIn().siblings().hide();
    $("#" + tag + "-nav-" + n).addClass("active").siblings().removeClass("active");
}

function fold() {
    width = $(".fe-save").width();
    left = $(".fe-save").css("left");
    left = left.replace("px", "");
    if (left >= 0) {
        $(".fe-save").animate({
            left: 0 - width + 40 + "px"
        }, 1000);
        $(".fe-save-fold").addClass("fe-save-fold2");
    } else {
        $(".fe-save").animate({
            left: "0px"
        }, 1000);
        $(".fe-save-fold").removeClass("fe-save-fold2");
    }
}

function setcookie(id) {
    require(['util'], function (util) {
        util.cookie.set('preview_id', id);
    });
}

function clone(myObj) {
    if (typeof (myObj) != 'object' || myObj == null) return myObj;
    var newObj = new Object();
    for (var i in myObj) {
        newObj[i] = clone(myObj[i]);
    }
    return newObj;
}

function cloneArr(arr) {
    var newArr = [];
    $(arr).each(function (i, val) {
        newArr.push(clone(val));
    });
    return newArr;
}

function initswipe(jobj) {
    var bullets = jobj.next().get(0).getElementsByTagName('a');
    var banner = Swipe(jobj.get(0), {
        auto: 2000,
        continuous: true,
        disableScroll: false,
        callback: function (pos) {
            var i = bullets.length;
            while (i--) {
                bullets[i].className = '';
            }
            bullets[pos].className = 'cur';
        }
    })
}

var myModel = angular.module('SZ_YIEditor', ['ng.ueditor', 'as.sortable'], function ($compileProvider) {
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

myModel.controller('MainCtrl', function ($scope) {
    $scope.dragControlListeners = {
        orderChanged: function (event) {
            // console.log(event, 'orderChanged');
        }, //Do what you want
        containment: '#board', //optional param.
    };

});

myModel.controller('SZ_YIController', ['$scope', '$http', function ($scope, $http) {
    $scope.navs = [{
            id: 'notice',
            icon: 'fa-bullhorn',
            name: '公告',
            params: {
                color: '',
                bgcolor: '',
                notice: '',
                noticehref: '',
                scroll: '0'
            }
        },
        {
            id: 'banner',
            icon: 'fa-bullhorn',
            name: '轮播',
            params: {
                shape: '',
                align: 'center',
                scroll: '2',
                bgcolor: ''
            },
            data: [{
                    id: 'B0000000000001',
                    imgurl: imgUrl1,
                    hrefurl: 'http://www.baidu.com',
                    sysurl: 'url',
                },
                {
                    id: 'B0000000000002',
                    imgurl: imgUrl2,
                    hrefurl: 'http://www.qq.com',
                    sysurl: 'url'
                },
                {
                    id: 'B0000000000003',
                    imgurl: imgUrl6,
                    hrefurl: 'http://www.sina.com',
                    sysurl: 'url'
                }
            ]
        },
        {
            id: 'title',
            icon: 'fa-bold',
            name: '标题',
            params: {
                title1: '',
                title2: '',
                showtitle2: '1',
                fontsize1: '18px',
                fontsize2: '14px',
                align: 'left',
                color: '#000',
            }
        },
        {
            id: 'search',
            icon: 'fa-search',
            name: '搜索框',
            params: {
                placeholder: '搜索：输入关键字在店内搜索',
                style: 'style1',
                'color': '',
                'bgcolor': '',
                'bordercolor': '',
                searchurl: searchGoodsUrl, //商品搜索
                uniacid: yz_uniacid
            }
        },
        {
            id: 'line',
            icon: 'fa-bullhorn',
            name: '辅助线',
            params: {
                height: '2px',
                style: 'dashed',
                color: '#000'
            }
        },
        {
            id: 'blank',
            icon: 'fa-square-o',
            name: '辅助空白',
            params: {
                height: '100px',
                bgcolor: ''
            }
        },
        {
            id: 'shop',
            icon: 'fa-bookmark',
            name: '店招',
            params: {
                style: '1',
                bgimg: imgUrl3,
                logo: '1',
                name: '1',
                menu: '1',
                navcolor: ''
            },
            data: []
        },
        {
            id: 'goods',
            icon: 'fa-tasks',
            name: '商品组',
            params: {
                style: '50%',
                showtitle: '0',
                titlecolor: '',
                bgcolor: '',
                showname: '1',
                title: '',
                option: 'sale-rx',
                buysub: 'buy-3',
                price: '1',
                lowershelf: '1',
                goodhref: goodsUrl
            },
            data: []
        },
        {
            id: 'richtext',
            icon: 'fa-font',
            name: '富文本',
            params: {
                bgcolor: '',
            },
            content: ''
        },
        {
            id: 'menu',
            icon: 'fa-bullhorn',
            name: '按钮组',
            params: {
                num: '20%',
                style: '0',
                bgcolor: '#fff',
            },
            data: [{
                id: 'F0000000000001',
                imgurl: imgUrlIcon,
                text: '',
                hrefurl: '',
                color: ''
            }, {
                id: 'F0000000000002',
                imgurl: imgUrlIcon,
                text: '',
                hrefurl: '',
                color: ''
            }, {
                id: 'F0000000000003',
                imgurl: imgUrlIcon,
                text: '',
                hrefurl: '',
                color: ''
            }, {
                id: 'F0000000000004',
                imgurl: imgUrlIcon,
                text: '',
                hrefurl: '',
                color: ''
            }, {
                id: 'F0000000000005',
                imgurl: imgUrlIcon,
                text: '',
                hrefurl: '',
                color: ''
            }]
        },
        {
            id: 'picture',
            icon: 'fa-file-image-o',
            name: '单图',
            params: {},
            data: [{
                id: 'P0000000000001',
                imgurl: imgUrl4,
                hrefurl: '',
                option: '0'
            }]
        },
        {
            id: "cube",
            icon: 'fa-picture-o',
            name: "图片组合",
            params: {
                bgcolor: '',
                layout: {},
                showIndex: 0,
                selection: {},
                currentPos: {},
                currentLayout: {
                    isempty: !0
                }
            },
            data: []
        }
    ];
    $scope.shop = {
        uniacid: yz_uniacid
    };
    $scope.system = [system];
    $scope.pages = [pageinfo];
    $scope.Items = yzItems;
    $scope.menuList = yzMenuList;

    $scope.underscore = null;
    require(['underscore'], function (underscore) {
        $scope.underscore = underscore;
        $scope.hasCube = function (Item) {

            var has = false;
            var row = 0,
                col = 0;
            for (var i = row; i < 4; i++) {
                for (var j = col; j < 4; j++) {
                    if (!$scope.underscore.isUndefined(Item.params.layout[i][j]) && !Item.params.layout[i][j].isempty) {
                        has = true;
                        break;
                    }
                }
            }
            return has;


        }
    });


    $scope.showSelection = function (Edit, row, col) {

        Edit.params.currentPos = {
            row: row,
            col: col
        };
        Edit.params.selection = {};
        var maxrow = 4,
            maxcol = 4,
            end = false;

        for (var i = row; i <= 3; i++) {

            if ($scope.underscore.isUndefined(Edit.params.layout[i][col]) || !$scope.underscore.isUndefined(Edit.params.layout[i][col]) && !Edit.params.layout[i][col].isempty) {
                maxrow = i;
                end = true;
            }
            if (end) {
                break;
            }
        }

        end = false;
        for (var j = col; j <= 3; j++) {
            if ($scope.underscore.isUndefined(Edit.params.layout[row][j]) || !$scope.underscore.isUndefined(Edit.params.layout[row][j]) && !Edit.params.layout[row][j].isempty) {
                maxcol = j;
                end = true;
            }
            if (end) {
                break;
            }
        }

        var f = -1,
            g = 1;

        for (var i = row; i < maxrow; i++) {

            var y = 1;
            Edit.params.selection[g] = {};
            for (var j = col; j < maxcol; j++) {
                if (f >= 0 && f < j || (!$scope.underscore.isUndefined(Edit.params.layout[i][j]) && Edit.params.layout[i][j].isempty)) {
                    Edit.params.selection[g][y] = {
                        rows: g,
                        cols: y
                    };
                    y++;
                } else {
                    f = j - 1
                }
            }
            g++;
        }

        $(".layout-table li").removeClass("selected");
        $scope.modalobj = $("#" + Edit.id + "-modal-cube-layout").modal({
            show: true
        });
        $('#' + Edit.id + '-modal-cube-layout').find(".layout-table").unbind('mouseover').mouseover(function (a) {
            if ("LI" == a.target.tagName) {
                $(".layout-table li").removeClass("selected");
                var c = $(a.target).attr("data-rows"),
                    d = $(a.target).attr("data-cols");
                $(".layout-table li").filter(function (a, e) {
                    return $(e).attr("data-rows") <= c && $(e).attr("data-cols") <= d
                }).addClass("selected")
            }
        });

        return true;
    }
    $scope.selectLayout = function (Edit, currentRow, currentCol, rows, cols) {
        if ($scope.underscore.isUndefined(rows)) {
            rows = 0;
        }
        if ($scope.underscore.isUndefined(cols)) {
            cols = 0;
        }
        Edit.params.layout[currentRow][currentCol] = {
            cols: cols,
            rows: rows,
            isempty: false,
            imgurl: "",
            classname: "index-" + Edit.params.showIndex
        };
        for (var i = parseInt(currentRow); i < parseInt(currentRow) + parseInt(rows); i++) {
            for (var j = parseInt(currentCol); j < parseInt(currentCol) + parseInt(cols); j++) {
                if (currentRow != i || currentCol != j) {
                    delete Edit.params.layout[i][j];
                }
            }
        }
        Edit.params.showIndex++;
        $scope.modalobj.modal('hide');
        $scope.changeItem(Edit, currentRow, currentCol);
        return true;
    }
    $scope.changeItem = function (Edit, row, col) {
        $("#cube-editor td").removeClass("current").filter(function (a, e) {
            return $(e).attr("x") == row && $(e).attr("y") == col
        }).addClass("current");
        $("#cube_thumb").attr("src", "");
        Edit.params.currentLayout = Edit.params.layout[row][col];
    }
    $scope.delCube = function (Edit, Cid, cols, rows) {
        if (Edit && Cid && cols && rows) {
            var len = Edit.params.layout.length;
            $.each(Edit.params.layout, function (row, a) {
                $.each(Edit.params.layout[row], function (col, b) {
                    if (col != '$$hashKey') {
                        row = parseInt(row);
                        col = parseInt(col);
                        rows = parseInt(rows);
                        cols = parseInt(cols);
                        if (!b) {} else if (b.classname == Edit.params.currentLayout.classname) {
                            for (var i = row; i < row + rows; i++) {
                                for (var j = col; j < col + cols; j++) {
                                    Edit.params.layout[i][j] = {
                                        cols: 1,
                                        rows: 1,
                                        isempty: true,
                                        imgurl: "",
                                        classname: ""
                                    };
                                }
                            }
                        }
                    }
                });

            });
        }
    }


    // 1.1 添加一条子级(good,picture,banner)
    $scope.addItemChild = function (type, Mid) {
        if (type && Mid) {
            t = '';
            if (type == 'good') {
                t = 'G';
            } else if (type == 'picture') {
                t = 'P';
            } else if (type == 'banner') {
                t = 'B';
            }
            var var_id = t + new Date().getTime();
            var push = {
                banner: {
                    id: var_id,
                    imgurl: '',
                    hrefurl: '',
                    sysurl: 'url'
                },
                picture: {
                    id: var_id,
                    imgurl: '',
                    hrefurl: '',
                    option: '0'
                },
                good: {}
            };
            var Items = $scope.Items;
            angular.forEach(Items, function (m, index) {
                if (m.id == Mid) {
                    m.data.push(push[type]);
                    //console.log(push[type]);
                }
            });
        }
    }

    // 1.1 删除一条子级
    $scope.delItemChild = function (Mid, Cid) {
        if (confirm("此操作不可逆，确认移除？")) {
            var Items = $scope.Items;
            angular.forEach(Items, function (m, index1) {
                if (m.id == Mid) {
                    angular.forEach(m.data, function (c, index2) {
                        if (c.id == Cid) {
                            m.data.splice(index2, 1);
                        }
                    });
                }
            });
        }
    }

    // 1.1 上传图片
    $scope.uploadImgChild = function (Mid, Cid, Type) {
        require(['jquery', 'util'], function ($, util) {
            util.image('', function (data) {
                var Items = $scope.Items;
                angular.forEach(Items, function (m, index1) {
                    if (m.id == Mid) {
                        if (Type == 'cube') {
                            m.params.currentLayout.imgurl = data['url'];
                            $("div[mid=" + Mid + "]").mouseover();

                        } else {
                            angular.forEach(m.data, function (c, index2) {
                                if (c.id == Cid) {
                                    c.imgurl = data['url'];
                                    $("div[mid=" + Mid + "]").mouseover();
                                    //console.log(Items);
                                }
                            });
                        }
                    }
                });
            });
        });
    }

    $scope.chooseUrlCube = function (Mid, Cid) {
        var Items = $scope.Items;
        angular.forEach(Items, function (m) {
            if (m.id == Mid) {
                m.params.currentLayout.url = 'http://www.qq.com';
                $("div[mid=" + Mid + "]").mouseover();
            }
        });
    }
    // 1.1 选择链接
    $scope.chooseUrl = function (Mid, Cid, T) {
        $('#modal-mylink').attr({
            "Mid": Mid,
            "Cid": Cid,
            T: T
        });
        $('#modal-mylink').modal();
    }
    $scope.chooseLink = function (type, hid) {
        var Mid = $('#modal-mylink').attr("Mid");
        var Cid = $('#modal-mylink').attr("Cid");
        var T = $('#modal-mylink').attr("T");
        var url = $("#fe-tab-link-li-" + hid).attr("nhref");
        if (url == undefined) {
            url = $(" #" + hid).attr("nhref");
        }
        if (hid == 'other-1') {
            url = $("textarea[name=mylink_href]").val();
        }
        //console.log(url);
        if (url && Mid && Cid) {
            angular.forEach($scope.Items, function (m, index1) {
                if (m.id == Mid) {
                    if (T == 'cube') {
                        m.params.currentLayout.url = url;
                        $("div[mid=" + Mid + "]").mouseover();
                    } else {
                        angular.forEach(m.data, function (c, index2) {
                            if (c.id == Cid) {
                                c.hrefurl = url;
                            }
                        });
                    }
                }
            });

            var id = "menu-" + Cid;
            $("input[data-id=" + id + "]").val(url);
            $('#modal-mylink').attr({
                "Mid": '',
                "Cid": '',
                T: ''
            });
            console.log(hid);
            $('#modal-mylink .close').click();
        }

    }
    $scope.temp = {
        notcie: []
    };


    $scope.focus = 'M0000000000000';
    //$scope.selectGoods = [];
    $scope.keyword = function (val, Eid) {
        $.ajax({
            type: 'post',
            url: keyWordUrl,
            data: {
                keyword: val,
                page_id: designerId
            },
            success: function (data) {
                if (data != 'ok') {
                    alert('关键字已存在');
                    window.dosave = '1';
                    $("div[Editid=" + Eid + "]").find(".keyword").css('border', "#f01 solid 1px");
                } else {
                    window.dosave = '0';
                    $("div[Editid=" + Eid + "]").find(".keyword").css('border', "#ddd solid 1px");
                }
            },
            error: function () {
                alert('获取关键字信息失败！请刷新页面。');
            }
        });
    }


    $scope.selectgood = function (Mid) {
        console.log(Mid);
        kw = $("#secect-kw").val();
        $http.post(selectGoodsUrl, {
            'keyword': kw
        }).success(function (data) {
            console.log(data);
            $scope.selectGoods = [];
            angular.forEach(data, function (d, i) {
                Sid = 'S' + new Date().getTime();
                $scope.selectGoods.push({
                    id: Sid + i,
                    name: data[i].title,
                    img: data[i].thumb,
                    goodid: data[i].id,
                    pricenow: data[i].price,
                    priceold: data[i].market_price,
                    sales: data[i].real_sales,
                    unit: data[i].sku
                });
            });
            $("div[mid=" + Mid + "]").mouseover();
            //console.log($scope.selectGoods);
        }).error(function () {
            alert('查询商品信息失败！请刷新页面。');
        });
    }

    $scope.selectarticle = function (Mid) {
        console.log(Mid);
        kw = $("#secect-kw").val();
        $http.post(selectArticleUrl, {
            'keyword': kw
        }).success(function (data) {
            console.log(data);
            $scope.selectArticle = [];
            angular.forEach(data, function (d, i) {
                Sid = 'S' + new Date().getTime();
                //var article_pay_meoney = (data[i].has_one_article_pay === null)  ? 0 : data[i].has_one_article_pay.money;
               // var article_pay_record = (data[i].has_one_record === null) ? 0 : data[i].has_one_record.pay_status;
                $scope.selectArticle.push({
                    id: Sid + i,
                    name: data[i].title,
                    img: data[i].thumb,
                    goodid: data[i].id,
                    pricenow: data[i].price,
                    priceold: data[i].market_price,
                    sales: data[i].real_sales,
                    unit: data[i].sku,
                    has_one_article_pay: data[i].has_one_article_pay,
                    has_one_record: data[i].has_one_record
                });
            });
            $("div[mid=" + Mid + "]").mouseover();
            //console.log($scope.selectGoods);
        }).error(function () {
            alert('查询商品信息失败！请刷新页面。');
        });
    }

    $scope.searchgood = function (Mid) {
        console.log(Mid);
        kw = $("#secect-kw").val();
        //var data = $.param({kw:kw});

        $http.post(myLinkGoodsUrl, {
            kw: kw
        }).success(function (data) {
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

    $scope.pushGood = function (Mid, Sid) {
        var repaction = $('#floating-good').attr("action");
        var repGid = $('#floating-good').attr("Gid");
        angular.forEach($scope.Items, function (m, index1) {
            if (m.id == Mid) {
                angular.forEach($scope.selectGoods, function (s, index2) {
                    if (s.id == Sid) {
                        if (repaction == 'replace' && repGid) {
                            // 执行替换
                            angular.forEach(m.data, function (r, index3) {
                                if (r.id == repGid) {
                                    var Gid = 'G' + new Date().getTime();
                                    r.id = Gid;
                                    r.img = s.img;
                                    r.goodid = s.goodid;
                                    r.name = s.name;
                                    r.priceold = s.priceold;
                                    r.pricenow = s.pricenow;
                                    r.sales = s.sales;
                                    r.unit = s.unit;
                                    $('#floating-good .close').click();
                                }
                            });
                        } else if (!repaction) {
                            var Gid = 'G' + new Date().getTime();
                            // 执行添加
                            m.data.push({
                                id: Gid,
                                img: s.img,
                                goodid: s.goodid,
                                name: s.name,
                                priceold: s.priceold,
                                pricenow: s.pricenow
                            });
                        }
                    }
                });
            }
        });
    }


    $scope.load = function () {}
    $scope.changeImg = function (Mid, n) {
        width = parseInt($(".fe-mod-move").width());
        height = (width * parseInt(n.replace("%", "")) / 100) - 16;
        $("div[mid=" + Mid + "] .fe-mod-8-main-img img").height(height);
    };
    $scope.setimg = function (Mid, n) {
        width = $(".fe-mod-move").width();
        n = n.replace("%", "");
        n = n / 100;
        $("div[mid=" + Mid + "] .fe-mod-12 img").height(width * n - 30);
    }
    $scope.setfocus = function (Mid, e) {
        $scope.focus = Mid;
        ccc = $("div[id=" + Mid + "]").offset().top;
        ddd = (ccc - 280) >= 0 ? (ccc - 280) : 0;
        $(".fe-panel-editor").css("margin-top", ddd + "px");
        $(document.body).animate({
            scrollTop: ccc - 100
        }, 500);
    }
    $scope.drag = function (Mid) {
        var container = $("#editor");
        var del = container.find(".fe-mod-move");
        del.off("mousedown").mousedown(function (e) {
            $scope.focus = Mid;
            if (e.which != 1 || $(e.target).is("textarea") || window.kp_only) return;
            e.preventDefault();
            var x = e.pageX;
            var y = e.pageY;
            var _this = $(this).parent();
            var w = _this.width();
            var h = _this.height();
            var w2 = w / 2;
            var h2 = h / 2;
            var p = _this.position();
            var left = p.left;
            var top = p.top;
            window.kp_only = true;
            _this.before('<div id="kp_widget_holder"></div>');
            var wid = $("#kp_widget_holder");
            var nod = $(".fe-mod-nodrag");
            wid.css({
                "border": "2px dashed #ccc",
                "height": _this.outerHeight(true)
            });
            _this.css({
                "width": w,
                "height": h,
                "position": "absolute",
                opacity: 0.8,
                "z-index": 900,
                "left": left,
                "top": top
            });
            $(document).mousemove(function (e) {
                $scope.focus = Mid;
                e.preventDefault();
                var l = left + e.pageX - x;
                var t = top + e.pageY - y;
                _this.css({
                    "left": l,
                    "top": t
                });
                var ml = l + w2;
                var mt = t + h2;
                del.parent().not(_this).not(wid).each(function (i) {
                    var obj = $(this);
                    var p = obj.position();
                    var a1 = p.left;
                    var a2 = p.left + obj.width();
                    var a3 = p.top;
                    var a4 = p.top + obj.height();
                    if (a1 < ml && ml < a2 && a3 < mt && mt < a4) {
                        if (!obj.next("#kp_widget_holder").length) {
                            wid.insertAfter(this);
                        } else {
                            wid.insertBefore(this);
                        }
                        return;
                    }
                });
            });
            $(document).mouseup(function () {
                $(document).off('mouseup').off('mousemove');
                $(container).each(function () {
                    var obj = $(this).children();
                    var len = obj.length;
                    if (len == 1 && obj.is(_this)) {
                        $("<div></div>").appendTo(this).attr("class", "kp_widget_block").css({
                            "height": 100
                        });
                    } else if (len == 2 && obj.is(".kp_widget_block")) {
                        $(this).children(".kp_widget_block").remove();
                    }
                });
                var p = wid.position();
                _this.animate({
                    "left": p.left,
                    "top": p.top
                }, 100, function () {
                    _this.removeAttr("style");
                    wid.replaceWith(_this);
                    window.kp_only = null;
                    var arr = [];
                    $(".fe-mod-repeat").find(".fe-mod-parent").each(function (i, val) {
                        arr[i] = val.id;
                    });
                    var newarr = [];
                    angular.forEach(arr, function (aid) {
                        angular.forEach($scope.Items, function (obj) {
                            if (obj.id == aid) {
                                newarr.push(obj);
                                return false;
                            }
                        });
                    });
                    $scope.Items = newarr;
                });
            });
        });
    }

    $scope.addItem = function (Nid) {
        //alert(344);
        var Mid = 'M' + new Date().getTime();
        var Navs = $scope.navs;
        angular.forEach(Navs, function (n, index) {
            if (n.id == Nid) {
                newparams = !clone(n.params) ? '' : clone(n.params);
                newdata = !n.data ? '' : cloneArr(n.data);
                newother = !clone(n.other) ? '' : clone(n.other);
                newcontent = !clone(n.content) ? '' : clone(n.content);
                if (Nid == 'cube') {
                    for (row = 0; row < 4; row++) {
                        for (newparams.layout[row] = {}, col = 0; col < 4; col++) {
                            newparams.layout[row][col] = {
                                cols: 1,
                                rows: 1,
                                isempty: !0,
                                imgurl: "",
                                classname: ""
                            };
                        }
                    }
                }
                var newitem = {
                    id: Mid,
                    temp: Nid,
                    params: newparams,
                    data: newdata,
                    other: newother,
                    content: newcontent
                };
                var insertindex = -1;
                if ($scope.focus != '') {
                    var Items = $scope.Items;
                    angular.forEach(Items, function (a, index) {
                        if (a.id == $scope.focus) {
                            insertindex = index;
                        }
                    });
                }
                if (insertindex == -1) {
                    $scope.Items.push(newitem);
                } else {
                    $scope.Items.splice(insertindex + 1, 0, newitem);
                }
                //$("div[id="+newitem.id+"]").trigger('ng-click');
                setTimeout(function () {
                    $scope.setfocus(newitem.id, null);
                }, 100);

                console.log($scope.Items);
            }
        });
    }

    $scope.delItem = function (id) {
        if (confirm("此操作不可逆，确认移除？")) {
            var Items = $scope.Items;
            angular.forEach(Items, function (a, index) {
                if (a.id == id) {
                    Items.splice(index, 1);
                    $scope.focus = '';
                }
            });
        }
    }
    $scope.over = function (id) {
        $("div[id=" + id + "]").parent().find(".fe-mod-del").stop().show();
    }
    $scope.out = function (id) {
        $("div[id=" + id + "]").parent().find(".fe-mod-del").stop().hide();
    }
    $scope.save = function (n) {
        var pageid = "{{ $designerModel->id or ''}}";
        var items = cloneArr($scope.Items);

        angular.forEach(items, function (m, index1) {
            if (m.temp == 'richtext') {
                m.content = encodeURI(m.content);
            }
        });
        var datas = angular.toJson(items);
        var page_info = angular.toJson($scope.pages);
        var page_name = $("input[name=pagename]").val();
        var page_type = $("input[name=pagetype]").val();
        if (!page_name) {
            alert('请给你的页面起一个响亮的名字吧');
            return;
        }
        if (!page_type) {
            alert('你还没有选择页面的类型哦~');
            return;
        }
        if (window.dosave == '1') {
            alert('触发关键字已存在！请重新填写。');
            $scope.focus = 'M0000000000000';
            return;
        }
        $(".save-submit").text('保存中...').addClass("fe-save-disabled").data('saving', '1');
        $(".fe-save-submit2").css("color", "#bbb");
        if ($(".fe-save-submit").data('saving') == 1) {
            $.ajax({
                type: 'POST',
                url: storeUrl,
                data: {
                    id: pageid,
                    datas: datas,
                    page_type: page_type,
                    page_name: page_name,
                    page_info: page_info
                },
                success: function (data) {
                    console.log(data);
                    if (n == 2) {
                        alert("保存成功！正在生成览页面...");
                        setcookie(data);
                        if (!pageid) {
                            location.href = designerIndex;
                        } else {
                            preview(data);
                        }
                    } else {
                        alert("保存成功！");
                        location.href = designerIndex;
                    }
                    $(".save-submit").text('保存').removeClass("fe-save-disabled").data('saving', '0');
                    $(".fe-save-submit2").css("color", "#4bb5fb")
                },
                error: function () {
                    alert('保存失败请重试');
                    $(".save-submit").text('保存').removeClass("fe-save-disabled").data('saving', '0');
                    $(".fe-save-submit2").css("color", "#4bb5fb")
                }
            });
        }
    }

    $scope.addGood = function (action, Mid, Gid) {
        $('#floating-good').modal();
        $('#floating-good').attr({
            'action': action,
            'Gid': Gid
        });
    }

    $scope.delGood = function (Mid, Gid) {
        if (confirm("此操作不可逆，确认移除？")) {
            var Items = $scope.Items;
            angular.forEach(Items, function (m, index1) {
                if (m.id == Mid) {
                    angular.forEach(m.data, function (g, index2) {
                        if (g.id == Gid) {
                            m.data.splice(index2, 1);
                        }
                    });
                }
            });
        }
    }

    $scope.addArticle = function (action, Mid, Aid) {
        $('#floating-article').modal();
        $('#floating-article').attr({
            'action': action,
            'Gid': Aid
        });
    }

    $scope.delArticle = function (Mid, Aid) {
        if (confirm("此操作不可逆，确认移除？")) {
            var Items = $scope.Items;
            angular.forEach(Items, function (m, index1) {
                if (m.id == Mid) {
                    angular.forEach(m.data, function (g, index2) {
                        if (g.id == Aid) {
                            m.data.splice(index2, 1);
                        }
                    });
                }
            });
        }
    }

    $scope.shopImg = function (Mid) {
        require(['jquery', 'util'], function ($, util) {
            util.image('', function (data) {
                var Items = $scope.Items;
                angular.forEach(Items, function (m, index1) {
                    if (m.id == Mid) {
                        m.params.bgimg = data['url'];
                        $("div[mid=" + Mid + "]").mouseover();
                    }
                });
            });
        });
    }

    $scope.pageImg = function (Mid, type) {
        require(['jquery', 'util'], function ($, util) {
            util.image('', function (data) {
                if (type == 'floatimg') {
                    $scope.pages[0].params.floatimg = data['url'];
                } else {
                    $scope.pages[0].params.img = data['url'];
                }
                $("div[mid=" + Mid + "]").trigger("click");
            });
        });
    }

    $scope.$on('ngRepeatFinished', function (ngRepeatFinishedEvent) {
        $('.fe-mod-2 .swipe').each(function () {
            initswipe($(this));
        })
        $('.fe-mod-8-main-img img').each(function () {
            $(this).height($(this).width());
        });
        $('.fe-mod-12 img').each(function () {
            $(this).height($(this).width());
        });
    });
}]);

myModel.directive('stringHtml', function () {
    return function (scope, el, attr) {
        if (attr.stringHtml) {
            scope.$watch(attr.stringHtml, function (html) {
                el.html(html || '');
            });
        }
    };
});

myModel.directive("onFinishRenderFilters", function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            if (scope.$last === true) {
                $timeout(function () {
                    scope.$emit('ngRepeatFinished');
                });
            }
        }
    };
});