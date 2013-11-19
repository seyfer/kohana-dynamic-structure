<div class='oneline work_field' id='leftWindow'>
    {$struct}
</div>

<div class='oneline work_field'>
    {if !empty($id)}
        <form action='/structure/changearticle/{$id}' method="POST"
              enctype="multipart/form-data">

            <h2>{$article.title}</h2>

            <div id='showSettings'>Показать настройки</div>

            <div id='settings' style='display:none'>
                <div class='settings'>
                    <span class='settings-label'>Title</span>
                    <input type='text' name='title' value='{$article.title}'>
                </div>

                <div class='settings'>
                    <span class='settings-label'>Иконка кнопки</span>
                    <input type='file' name='logotip'>
                    {if !empty($article.img)}<img src='/media/img/icons/{$article.img}'>{/if}
                </div>

                <div class='settings'>
                    <span class='settings-label'>Ссылка</span> <input type='text' name='link' value='{$article.link}'>
                </div>

                <div class='settings'>
                    <span class='settings-label'>Язык</span> <select name="language">
                        <option value="RU"
                                {if $article.language=='RU'}selected{/if}>RU
                        </option>
                        <option value="EN"
                                {if $article.language=='EN'}selected{/if}>EN
                        </option>
                    </select>
                </div>

                <div class='settings'>
                    <span class='settings-label'>Доступность пользователям</span>
                    {html_options options=$roles name='role' selected=$article.role}
                </div>

                <div class='settings'>
                    <span class='settings-label'>Отображать на сайте</span>
                    <input type='checkbox' name='visible' {if $article.visible}checked{/if}>
                </div>
            </div>

            <textarea class='tinymce' name='text'>{if $article}{$article.text}{/if}</textarea>
            <br>
            <input type='submit' value='Создать/Изменить'>
            <a href ="/structure/">Назад</a>
        </form>

    {/if}

</div>