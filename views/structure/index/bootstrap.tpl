{function menu_vert level =0}

    {foreach $data as $entry}

        {if isset($entry.children) && !empty($entry.children)}

            {if $level == 0}
                <li class="menu-item dropdown">
                    <a href="{$entry.link}" class="dropdown-toggle"
                       data-toggle="dropdown">
                        {$entry.title} <b class="caret"></b>
                    </a>

                    <ul class="dropdown-menu" id="level{$level}">
                        {menu_vert data=$entry.children level=level+1}
                    </ul>
                </li>
            {else}
                <li class="menu-item dropdown dropdown-submenu">
                    <a class="dropdown-toggle"
                       data-toggle="dropdown" href="{$entry.link}">{$entry.title}</a>

                    <ul class="dropdown-menu" id="level{$level}">
                        {menu_vert data=$entry.children level=level+1}
                    </ul>
                </li>
            {/if}
        {else}
            <li class="menu-item" {*class="active"*}>
                <a href="{$entry.link}">{$entry.title}</a>
            </li>
        {/if}

    {/foreach}

{/function}

<!-- Fixed navbar -->
<div class="navbar navbar-default {*navbar-fixed-top*}" role="navigation">
    <div class="container">
        {if $header}
            <div class="navbar-header">
                <button type="button" class="navbar-toggle"
                        data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">{$header}</a>
            </div>
        {/if}
        <div class="navbar-collapse collapse">

            <ul class="nav navbar-nav">
                {menu_vert data=$structure}
            </ul>

        </div><!--/.nav-collapse -->
    </div>
</div>

<script src="{$boostrapFixJsPath}"></script>
