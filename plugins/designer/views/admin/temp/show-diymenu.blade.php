<div class="designer-menu">
    <ul>
        <li ng-repeat="menu in menus"
            ng-class="{'designer-menu-line':params.showicon==0 || params.showicon==1,'designer-menu-h2':params.showicon==2,
                        'designer-menu-w1':menus.length == 1,'designer-menu-w2':menus.length == 2,'designer-menu-w3':menus.length == 3,'designer-menu-w4':menus.length == 4,'designer-menu-w5':menus.length == 5,
                        'designer-menu-border': params.showborder==1,'designer-menu-noborder': params.showborder!=1}"
            ng-style="{'border-top-color': menu.bordercolor}"
            ng-click="openMenu(menu,$event)"
        >
            <div class="designer-menu-bg" ng-style="{'background':menu.bgcolor,'opacity':params.bgalpha}"></div>
            <div class="designer-menu-item"
                 ng-style="{'color':menu.textcolor}">
                <span ng-class="{'designer-menu-block':params.showicon==2,'designer-menu-icon':params.showicon==2}">
                    <i class="@{{menu.icon}}" ng-style="{color:menu.iconcolor}" ng-class="{'designer-menu-bigicon':params.showicon==2}" ng-show="params.showicon!=0"></i></span>
                <span ng-class="{'designer-menu-block':params.showicon==2,'designer-menu-text':params.showicon==2}" ng-show="params.showtext==1">@{{menu.title}}</span>
            </div>

            <div class="sub" style="display:none;bottom:50px;"
                 ng-style="{'border-color':params.bordercolor2,'background':params.bgcolor2}">
                <span>
                    <a href="javascript:;"
                       class="designer-menu-link"
                       ng-repeat="sub in menu.subMenus"
                       ng-style="{'border-bottom-color':params.bordercolor2,'color':params.textcolor2}"
                       data-href="">@{{sub.title}}</a>
                </span>
                <div class="corner" ng-style="{'border-top-color':params.bordercolor2}"></div>
                <div class="corner2" ng-style="{'border-top-color':params.bgcolor2}"></div>
            </div>
            <div class="designer-menu-spliter" ng-show="params.showborder==1"
                 ng-style="{'background':menu.bordercolor}"></div>
        </li>
    </ul>
</div>
