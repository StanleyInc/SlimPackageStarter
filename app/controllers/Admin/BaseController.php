<?php
namespace Admin;

use \App;
use \Menu;
use \Module;

class BaseController extends \BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->resetJs();
        $this->resetCss();

        $minifyCss = "";
        $minifyCss .= "/assets/css/admin/font-awesome.css,";
        $minifyCss .= "/assets/css/admin/bootstrap.css,";
        $minifyCss .= "/assets/css/admin/sb-admin.css,"; 
        $minifyCss .= "/assets/css/admin/plugins/dataTables/dataTables.bootstrap.css,"; 
        $minifyCss .= "/assets/css/admin/custom.css&minify=true"; 
        $this->loadCss($minifyCss,array("location" => "minify"));

        $minifyJs = "";
        $minifyJs .= "/assets/js/admin/jquery-1.10.2.js,";
        $minifyJs .= "/assets/js/admin/bootstrap.js,";
        $minifyJs .= "/assets/js/admin/plugins/metisMenu/jquery.metisMenu.js,";
        $minifyJs .= "/assets/js/admin/plugins/dataTables/jquery.dataTables.js,";
        $minifyJs .= "/assets/js/admin/plugins/dataTables/dataTables.bootstrap.js,";
        $minifyJs .= "/assets/js/admin/sb-admin.js&minify=true";
        $this->loadJs($minifyJs,array("location" => "minify"));

        $this->data['menu_pointer'] = '<div class="pointer"><div class="arrow"></div><div class="arrow_border"></div></div>';

        $adminMenu = Menu::create('admin_sidebar');
        $dashboard = $adminMenu->createItem('dashboard', array(
            'label' => 'Dashboard',
            'icon'  => 'dashboard',
            'url'   => 'admin'
        ));

        $adminMenu->addItem('dashboard', $dashboard);
        $adminMenu->setActiveMenu('dashboard');

        foreach (Module::getModules() as $module) {
            $module->registerAdminMenu();
        }

    }
}