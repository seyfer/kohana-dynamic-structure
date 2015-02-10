<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Structure extends Kohana_Controller_Template
{

    /**
     * @var   string   The path to the template view.
     */
    public $template = 'structure/main.tpl';

    /**
     * вынести в конфиг
     */
    private $uploadPath;

    /**
     *
     * @var \Structure
     */
    private $modelStructure;

    /**
     *
     * @var \Structure_Article
     */
    private $modelStructureArticle;

    /**
     *
     * @var \Structure_Role
     */
    private $modelStructureRole;

    /**
     *
     * @var \Kohana_Config
     */
    private $config;

    /**
     *
     * @var \Kohana_Auth
     */
    private $auth;

    public function before()
    {
        parent::before();

        $this->checkAuth();

        $this->modelStructure        = new Structure();
        $this->modelStructureArticle = new Structure_Article();
        $this->modelStructureRole    = new Structure_Role();

        $this->config = Kohana::$config->load("structure");

        $this->uploadPath = MODPATH . "/dynamic-structure/upload/";
    }

    /**
     * провкрка авторизации по конфигу
     *
     * @return type
     * @throws HTTP_Exception_401
     */
    private function checkAuth()
    {
        if (!class_exists("Kohana_Auth")) {
            return;
        }

        $haveAccess = NULL;
        $authConfig = Kohana::$config->load("structure.kohana.auth");
        $authRoles  = explode(",", $authConfig['roles']);

        if ($authConfig['enabled']) {
            $this->auth = Auth::instance();

            $user = $this->auth->get_user();
            if (!$user) {
                HTTP_Exception::factory(401)->authenticate('Unauthorized');
            }

            $roles = $user->roles->find_all()->as_array();
            foreach ($roles as $userRole) {
                $userRoles[] = $userRole->name;
            }

            foreach ($userRoles as $currentUserRole) {
                if (in_array($currentUserRole, $authRoles)) {
                    $haveAccess = $currentUserRole;
                }
            }

            if (!$haveAccess) {
                HTTP_Exception::factory(401)->authenticate('Unauthorized');
            }
        }

        return $haveAccess;
    }

    /**
     * добавить корневой узел
     */
    public function action_addRoot()
    {
        $this->modelStructure->addRoot();

        $this->redirect($this->config->routePath);
    }

    /**
     * главная, только список
     */
    public function action_index()
    {
        $this->addCssAnsJs();

        $structure = $this->modelStructure->getTreeAsArray();

        $structureList = View::factory('structure/list.tpl')
                             ->set("routePath", "/" . $this->config->routePath)
                             ->set('left_menu_arr', $structure)
                             ->render();

        $this->content = View::factory('structure/content.tpl')
                             ->set("routePath", "/" . $this->config->routePath)
                             ->set('struct', $structureList);

        $this->template->content = $this->content;
    }

    /**
     * загрузить нужные стили и скрипты
     */
    private function addCssAnsJs()
    {
        $this->template->styles  = [];
        $this->template->scripts = [];

        $routeMedia  = Route::get('structure/media');
        $routeVendor = Route::get('structure/vendor');

        $this->template->styles[] = $routeMedia->uri(['file' => 'css/content.css']);

        $this->template->scripts[] = $routeMedia->uri(['file' => 'js/jquery-1.9.1.js']);
        $this->template->scripts[] = $routeMedia->uri(['file' => 'js/jquery-ui-1.9.2.custom.js']);
        $this->template->scripts[] = $routeVendor->uri(['file' => 'media/tinymce/js/tinymce/tinymce.min.js']);
        $this->template->scripts[] = $routeVendor->uri(['file' => 'media/tinymce/js/tinymce/jquery.tinymce.min.js']);

        $this->template->scripts[] = $routeMedia->uri(['file' => 'js/edit.js']);

        $this->template->page = "structure";
    }

    /**
     * редактирование узла
     */
    public function action_edit()
    {
        $this->addCssAnsJs();

        $structure = $this->modelStructure->getTreeAsArray();

        $id = (int)$this->request->param('id');

        $structureList = View::factory('structure/list.tpl')
                             ->set("routePath", "/" . $this->config->routePath)
                             ->set('left_menu_arr', $structure)
                             ->set('param', $id)
                             ->render();

        $article = (new Model_ORM_Articles())
            ->findArticle($id);

        $roles = (new Model_ORM_Roles())
            ->find_all();

        $rolesArr = (new Model_ORM_Roles())->showAsArray($roles, 'description');

        $this->content = View::factory('structure/content.tpl')
                             ->set("routePath", "/" . $this->config->routePath)
                             ->set('struct', $structureList)
                             ->set('id', $id)
                             ->set('article', $article)
                             ->set('roles', $rolesArr);

        $this->template->content = $this->content;
    }

    /**
     * Добовляем новую ветвь
     */
    public function action_add()
    {
        $parent_id = $this->request->param('id');

        $id = (new Model_ORM_Structure())
            ->addNewElement($parent_id);

        $this->redirect($this->config->routePath . "/edit/{$id}");
    }

    /**
     * редактировать статью
     */
    public function action_changearticle()
    {
        $id                = $this->request->param('id');
        $post              = $this->request->post();
        $post['parent_id'] = $id;

        //сохранить статью
        $article = (new Model_ORM_Articles())
            ->findByParent($id);

        if ($article) {
            $article->savePost($post);
        } else {
            (new Model_ORM_Articles())->savePost($post);
        }

        try {
            (new Model_ORM_Structure())
                ->findById($id)
                ->setImg($this->processFileUpload())
                ->setTitle($post['title'])
                ->update();

            $this->redirect($this->config->routePath . "/index/{$id}");
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->redirect($this->config->routePath . "/index/{$id}");
    }

    /**
     * загрузка иконок
     *
     * @return boolean
     */
    private function processFileUpload()
    {
        if ($_FILES['logotip']['error'] == 0) {

            $validation = Validation::factory($_FILES)
                                    ->label('image', 'Picture')
                                    ->rule('image', [
                                        'Upload::valid'     => [],
                                        'Upload::size'      => ['1M'],
                                        'Upload::not_empty' => [],
                                        'Upload::type'      => [
                                            'Upload::type' => ['jpg', 'png', 'gif']
                                        ],
                                    ]);

            if ($validation->check()) {
                Upload::save($validation['logotip'], $_FILES['logotip']['name'], $this->uploadPath);

                return $_FILES['logotip']['name'];
            }
        }

        return FALSE;
    }

    /**
     * двигать
     *
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

            //нельзя переместить родителя в ребенка
            if ($struct2->parent() && ($struct1->id == $struct2->parent()->id)) {
                return false;
            }

            if (!$struct1->parent() && !$struct2->parent()) {
                $scope          = $struct1->scope;
                $struct1->scope = $struct2->scope;
                $struct2->scope = $scope;
                $struct2->save();
                $struct1->save();
                $struct1->move_to_prev_sicbling($id2);

                return true;
            }

            if (!$struct1->parent()) {
                return false;
            }

            if (!$struct2->parent()) {
                $struct1->move_to_first_child($id2);

                return false;
            }

            //по умолчанию
            $struct1->move_to_first_child($id2);

            //перестроить дерево
            (new Model_ORM_Structure())->rebuild_tree();
        } catch (\Exception $exc) {

            $this->response->body($exc->getMessage());
        }
    }

    /**
     * удалить узел
     */
    public function action_delete()
    {
        $id = $this->request->param('id');

        $this->modelStructure->delete($id);

        $this->modelStructureArticle->deleteByParent($id);

        $this->redirect($this->config->routePath . "/index");
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
     *
     * @param type $dir
     */
    private function getFileContent($dir)
    {
        // Get the file path from the request
        $fileParam = $this->request->param('file');

        // Find the file extension
        $path = pathinfo($fileParam);

        // Array ( [dirname] => css [basename] => reset.css
        // [extension] => css [filename] => reset )
        $file = Kohana::find_file($dir, $path['dirname'] .
                                        DIRECTORY_SEPARATOR . $path['filename'], $path['extension']);

        if ($file) {
            // Send the file content as the response
            $this->response->body(file_get_contents($file));
        } else {
            // Return a 404 status
            $this->request->status = 404;
        }

        $contentType = File::mime_by_ext($path['extension']);

        // Set the content type for this extension
        $this->response->headers('Content-Type', $contentType);

        echo file_get_contents($file);
    }

}
