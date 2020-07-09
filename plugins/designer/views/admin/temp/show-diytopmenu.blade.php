<div class="main-topmenu" ng-style="{'border-color':params.bordercolor}" ng-class="{'designer-topmenu-noborder': params.showborder!=1}">
<div class="topmenu-input" ng-style="{'background':params.bgcolor,'opacity':params.bgalpha}"> 
    <input type="text" value="@{{params.searchword}}"  />
    <i class="fa fa-search"></i> 
</div>
<div class="designer-menu designer-topmenu">
    <ul>
        <li ng-repeat="menu in menus"
            ng-class="{'designer-menu-w1':menus.length == 1,'designer-menu-w2':menus.length == 2,'designer-menu-w3':menus.length == 3,'designer-menu-w4':menus.length == 4,'designer-menu-w5':menus.length == 5,
                        }"
        >
            <div class="designer-menu-bg" ng-style="{'background':menu.bgcolor,'opacity':params.bgalpha}"></div>
            <div class="designer-menu-item"
                 ng-style="{'color':menu.textcolor}">
                <span ng-class="{'designer-menu-block':params.showicon==2,'designer-menu-icon':params.showicon==2}">
                    </span>
                <span ng-class="{'designer-menu-block':params.showicon==2,'designer-menu-text':params.showicon==2}">@{{menu.title}}</span>
            </div>
        </li>
    </ul>
</div>

</div>
