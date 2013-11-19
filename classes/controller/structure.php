<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Structure extends Kohana_Controller_Template {

    /**
     * @var   string   The path to the template view.
     */
    public $template    = 'structure/main.tpl';
    //в конфиг
    private $uploadPath = "vendor/kohana/modules/dynamic-menu/upload/";

    public function action_index()
    {

        $this->addCssAnsJs();
        $structure = (new Model_ORM_Structure)->getTreeAsArray();

        $id = (int) $this->request->param('id');

        $structureList = View::factory('structure/list.tpl')
                ->set('left_menu_arr', $structure)
                ->set('param', $id)
                ->render();

        $this->content = View::factory('structure/content.tpl')
                ->set('struct', $structureList);

        $this->template->content = $this->content;
    }

    private function addCssAnsJs()
    {
        $this->template->styles  = array();
        $this->template->scripts = array();

        $routeMedia  = Route::get('structure/media');
        $routeVendor = Route::get('structure/vendor');

        $this->template->styles[] = $routeMedia->uri(array('file' => 'css/content.css'));

        $this->template->scripts[] = $routeMedia->uri(array('file' => 'js/jquery-1.9.1.js'));
        $this->template->scripts[] = $routeMedia->uri(array('file' => 'js/jquery-ui-1.9.2.custom.js'));
        $this->template->scripts[] = $routeVendor->uri(array('file' => 'tinymce/js/tinymce/tinymce.min.js'));
        $this->template->scripts[] = $routeVendor->uri(array('file' => 'tinymce/js/tinymce/jquery.tinymce.min.js'));

        $this->template->scripts[] = $routeMedia->uri(array('file' => 'js/edit.js'));

        $this->template->page = "structure";
    }

    public function action_edit()
    {
        $this->addCssAnsJs();
        $structure = (new Model_ORM_Structure)->getTreeAsArray();

        $id = (int) $this->request->param('id');

//        Debug::vars($id);

        $structureList = View::factory('structure/list.tpl')
                ->set('left_menu_arr', $structure)
                ->set('param', $id)
                ->render();

        $article = (new Model_ORM_Articles())
                ->findArticle($id);

        $roles = (new Model_ORM_Roles())
                ->find_all()
                ->showAsArray('name', 'description');

        $this->content = View::factory('structure/content.tpl')
                ->set('struct', $structureList)
                ->set('id', $id)
                ->set('article', $article)
                ->set('roles', $roles);

        $this->template->content = $this->content;
    }

    /**
     * Добовляем новую ветвь
     */
    public function action_add()
    {
        $structure        = (new Model_ORM_Structure());
        $structure->title = "Новое поле";
        $cat              = $this->request->param('id');

        if (!$cat) {
            $structure->make_root();
        }
        else {
            $structure->insert_as_last_child($cat);
        }

        $id = $structure->id;

        $this->request->redirect("structure/edit/{$id}");
    }

    /**
     * редактировать статью
     */
    public function action_changearticle()
    {
        $id   = $this->request->param('id');
        $post = $this->request->post();

        $article = (new Model_ORM_Articles())
                ->where('parent_id', '=', $id)
                ->find();

        $article->parent_id = $id;
        $keyes              = array(
            'text', 'link', 'language', 'visible', 'namehtml', 'role'
        );

        //Убираем лишнии интервалы
        $post['text'] = str_replace('<p>&nbsp;</p>', '', $post['text']);

        foreach ($keyes as $key) {
            if (isset($post[$key])) {
                $val = $post[$key];
            }

            if ($key === 'visible') {
                $val = isset($post[$key]) ? 1 : 0;
            }

            $article->$key = $val;
        }

        $article->save();

        //А теперь займемся структуркой
        $link = (new Model_ORM_Structure())
                ->where('id', '=', $id)
                ->find();

        if ($_FILES['logotip']['error'] == 0) {

            $validation = Validation::factory($_FILES)
                    ->label('image', 'Picture')
                    ->rule('image', array(
                'Upload::valid'     => array(),
                'Upload::size'      => array('1M'),
                'Upload::not_empty' => array(),
                'Upload::type'      => array(
                    'Upload::type' => array('jpg', 'png', 'gif')
                ),
            ));

            if ($validation->check()) {

//                $routeMedia = Route::get("structure/media");
//                $link       = $routeMedia->uri(array("file" => "img/icons"));

                Upload::save($validation['logotip'], $_FILES['logotip']['name'], $this->uploadPath);

                $link->img = $_FILES['logotip']['name'];
            }
        }

        $link->title = $post['title'];
        $link->update();

        $this->request->redirect("structure/index/{$id}");
    }

    /**
     * двигать
     * @return boolean
     */
    public function action_move()
    {
        $this->auto_render = FALSE;

        $id  = $this->request->param('id');
        $id2 = $this->request->param('id2');

        try {

            $struct1 = (new Model_ORM_Structure())
                    ->where('id', '=', $id)
                    ->find();

            $struct2 = (new Model_ORM_Structure())
                    ->where('id', '=', $id2)
                    ->find();

            if (!$struct1->loaded() || !$struct2->loaded()) {
                throw new Exception("one of elements is not finded");
            }

            if (!$struct1->parent() && !$struct2->parent()) {
                $scope          = $struct1->scope;
                $struct1->scope = $struct2->scope;
                $struct2->scope = $scope;
                $struct2->save();
                $struct1->save();
//                $struct1->move_to_prev_sicbling($id2);
                return true;
            }

            if (!$struct1->parent()) {
                return false;
            }

            if (!$struct2->parent()) {
                $struct1->move_to_first_child($id2);
                return false;
            }

            if ($struct1->parent()->id != $struct2->parent()->id) {
                $struct1->move_to_first_child($id2);
            }
            else {
                $struct1->move_to_prev_sibling($id2);
            }

            (new Model_ORM_Structure())->rebuild_tree();
        }
        catch (\Exception $exc) {

            $this->response->body($exc->getMessage());
        }
    }

    /**
     * удалить узел
     */
    public function action_delete()
    {
        echo 1;

        $id = $this->request->param('id');

        $link = (new Model_ORM_Structure())
                ->where('id', '=', $id)
                ->find();

        $link->delete();

        $ormArticle = (new Model_ORM_Articles())
                ->where('parent_id', '=', $id)
                ->find();

        if ($ormArticle->loaded()) {
            $ormArticle->delete();
        }

        $this->request->redirect("structure/index");
    }

    /**
     * Displays media files
     */
    public function action_media()
    {
        $this->getFileContent($this->request->action());
    }

    /**
     * Displays media files
     */
    public function action_vendor()
    {
        $this->getFileContent($this->request->action());
    }

    /**
     * Displays media files
     */
    public function action_upload()
    {
        $this->getFileContent($this->request->action());
    }

    /**
     * получить содержимое файла
     * установить заголовки по типу файла
     * @param type $dir
     */
    private function getFileContent($dir)
    {
        // Get the file path from the request
        $file = $this->request->param('file');

        // Find the file extension
        $path = pathinfo($file);

        // Array ( [dirname] => css [basename] => reset.css
        // [extension] => css [filename] => reset )
        $file = Kohana::find_file($dir, $path['dirname'] .
                        DIRECTORY_SEPARATOR . $path['filename'], $path['extension']);

        if ($file) {
            // Send the file content as the response
            $this->response->body(file_get_contents($file));
        }
        else {
            // Return a 404 status
            $this->request->status = 404;
        }

        $contentType = File::mime_by_ext($path['extension']);

        // Set the content type for this extension
        $this->response->headers('Content-Type', $contentType);

        echo file_get_contents($file);
    }

}
