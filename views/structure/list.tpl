{function menu_vert level =0}
    {foreach $data as $entry}

        {if isset($entry.children)}
            <div class='row elementMenu' nom='{$entry.id}' style='margin-left:{($entry.lvl-1)*50}px'>

                <div class='oneline addStruct' onClick="location.href = '/admin/structure/add/{$entry.id}'"></div>
                {if $entry.lvl!=1}<div class='oneline deleteStruct' onClick="location.href = '/admin/structure/delete/{$entry.id}'"></div>{/if}
                {if $entry.visible}
                    <div class='oneline'><img src='/public/img/eyeBig.png' style='width:14px;height:14px;'></div>
                    {/if}
                <div class='oneline'>
                    <a href="/admin/structure/edit/{$entry.id}" class="red_link dir">
                        {if !empty($entry.img)}<div class='oneline'><img src='/media/img/icons/{$entry.img}'></div>{/if}
                        <div class='oneline'>{$entry.title|truncate:10}</div>
                    </a>
                </div>

            </div>
            {menu_vert data=$entry.children level=level+1}
        {else}
            <div class='row elementMenu {if $param==$entry.id}selected{/if}' nom='{$entry.id}' style='margin-left:{($entry.lvl-1)*50}px'>
                {*<div class='oneline deleteStruct' onClick="location.href='/admin/structure/delete/{$entry.id}'"></div>*}
                <div class='oneline addStruct' onClick="location.href = '/admin/structure/add/{$entry.id}'"></div>
                <div class='oneline deleteStruct' onClick="location.href = '/admin/structure/delete/{$entry.id}'"></div>
                {if $entry.visible}
                    <div class='oneline'><img src='/public/img/eyeBig.png' style='width:14px;height:14px;'></div>
                    {/if}

                <div class='oneline'>
                    <a href="/admin/structure/edit/{$entry.id}" class="red_link dir">
                        {if !empty($entry.img)}<div class='oneline'><img src='/media/img/icons/{$entry.img}'></div>{/if}
                        <div class='oneline'>{$entry.title|truncate:10}</div>
                    </a>
                </div>

            </div>
        {/if}

    {/foreach}

{/function}

{menu_vert data=$left_menu_arr}
