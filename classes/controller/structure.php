<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Structure extends Kohana_Controller_Template {

    /**
     * @var   string   The path to the template view.
     */
    public $template = 'structure/main.tpl';

    public function action_index()
    {
        $this->template->styles  = array();
        $this->template->scripts = array();

        $route = Route::get('structure/media');

        $this->template->styles[]  = $route->uri(array('file' => 'css/admin.css'));
        $this->template->scripts[] = 'media/js/tinymce/jquery.tinymce.js';
        $this->template->scripts[] = $route->uri(array('file' => 'js/edit.js'));

        $this->template->page = "structure";

        $structure = (new Model_ORM_Structure)->getTreeAsArray();

        $id = (int) $this->request->param('id');

        $structureList = View::factory('structure/list.tpl')
                ->set('left_menu_arr', $structure)
                ->set('param', $id)
                ->render();

        $article = (new Model_ORM_Articles())->findArticle($id);

        $roles = (new Model_ORM_Roles)->find_all()
                ->showAsArray('name', 'description');

        $this->content = View::factory('structure/content.tpl')
                ->set('struct', $structureList)
                ->set('edit', $id)
                ->set('article', $article)
                ->set('roles', $roles);

        $this->template->content = $this->content;

//        echo $this->template;
    }

    /**
     * Добовляем новую ветвь
     */
    public function action_add()
    {
        $structure        = ORM::factory('ORM_Structure');
        $structure->title = "Новое поле";
        $cat              = $this->request->param('id');

        if (!$cat) {
            $structure->make_root();
        }
        else {
            $structure->insert_as_last_child($cat);
        }

        $id = $structure->id;
        HTTP::redirect("structure/edit/{$id}");
    }

    /**
     *
     */
    public function action_changeartical()
    {

        $id = $this->request->param('id');

        $post = $this->request->post();

        $artical = ORM::factory('ORM_Articles')->where('parent_id', '=', $id)
                ->find();


        $artical->parent_id = $id;
        $keyes              = array('text', 'link', 'language', 'visible', 'namehtml', 'role');
        //Убираем лишнии интервалы
        $post['text']       = str_replace('<p>&nbsp;</p>', '', $post['text']);

        foreach ($keyes as $key) {
            if (isset($post[$key])) {
                $val = $post[$key];
            }
            if ($key === 'visible') {
                $val = isset($post[$key]) ? 1 : 0;
            }

            $artical->$key = $val;
        }

        $artical->save();

        //А теперь займемся структуркой
        $link = ORM::factory('ORM_Structure')->where('id', '=', $id)
                ->find();

        if ($_FILES['logotip']['error'] == 0) {

            $validation = Validation::factory($_FILES)
                    ->label('image', 'Picture')
                    ->rule('image', array(
                'Upload::valid'     => array(),
                'Upload::size'      => array('1M'),
                'Upload::not_empty' => array(),
                'Upload::type'      => array('Upload::type' => array('jpg', 'png', 'gif')),
            ));

            if ($validation->check()) {
                Upload::save($validation['logotip'], $_FILES['logotip']['name'], MEDIA .
                        "img/icons");
                $link->img = $_FILES['logotip']['name'];
            }
        }

        $link->title = $post['title'];

        $link->update();

        HTTP::redirect("structure/edit/{$id}");
    }

    public function action_move()
    {
        $id      = $this->request->param('id');
        $id2     = $this->request->param('id2');
        $struct1 = ORM::factory('ORM_Structure')->where('id', '=', $id)
                ->find();

        $struct2 = ORM::factory('ORM_Structure')->where('id', '=', $id2)
                ->find();

        if (!$struct1->parent() && !$struct2->parent()) {
            $scope          = $struct1->scope;
            $struct1->scope = $struct2->scope;
            $struct2->scope = $scope;
            $struct2->save();
            $struct1->save();
            //$struct1->move_to_prev_sibling($id2);
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

        ORM::factory('Admin_Structure')->rebuild_tree();
    }

    public function action_delete()
    {
        $id   = $this->request->param('id');
        $link = ORM::factory('ORM_Structure')->where('id', '=', $id)
                ->find();
        $link->delete();

        ORM::factory("ORM_Articles")->where('parent_id', '=', $id)->find()->delete();

        HTTP::redirect("structure/edit");
    }

    /**
     * Displays media files
     */
    public function action_media()
    {
        // Get the file path from the request
        $file = $this->request->param('file');

        // Find the file extension
        $path = pathinfo($file);

        // Array ( [dirname] => css [basename] => reset.css [extension] => css [filename] => reset )
        $file = Kohana::find_file('media', $path['dirname'] .
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
