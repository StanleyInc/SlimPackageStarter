<?php
namespace UserGroup\Controllers;

use \App;
use \View;
use \Menu;
use \Group;
use \Input;
use \Sentry;
use \Request;
use \Response;
use \Exception;
use \Admin\BaseController;

class GroupController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        Menu::get('admin_sidebar')->setActiveMenu('group');
    }

    public function index()
    {

        if(userIsLogin()){
            $user = Sentry::getUser();
        }else{
            App::flash('message', 'Please login below.');
            return Response::redirect($this->siteUrl('login'));
        }

        $controlAccess = routeLastPath().'.'.__FUNCTION__;
        if ($user->hasAccess($controlAccess))
        {    
            $this->data['title'] = 'Group List';
            $this->data['groups'] = Group::all()->toArray();

            /** load the group.js app */
            $minifyJs = "";
            $minifyJs .= "/assets/js/admin/app/group.js&minify=true";
            $this->loadJs($minifyJs,array("location" => "minify"));

            /** publish necessary js  variable */
            $this->publish('baseUrl', $this->data['baseUrl']);
            /** render the template */
            View::display('@usergroup/group/index.twig', $this->data);
        }else{
           /** render the template */
           View::display('@usergroup/noaccess.twig', $this->data);
        }
    }

   public function show($id)
   {
        if(Request::isAjax()){
            $group = null;
            $message = '';

            try{
                $group = Group::find($id);
            }catch(Exception $e){
                $message = $e->getMessage();
            }

            Response::headers()->set('Content-Type', 'application/json');
            Response::setBody(json_encode(
                array(
                    'success'   => !is_null($group),
                    'data'      => !is_null($group) ? $group->toArray() : $group,
                    'message'   => $message,
                    'code'      => is_null($group) ? 404 : 200
                )    
            ));
        }else{

        }

    }

   /**
     * create new resource
     */
   public function store()
   {
        $group   = null;
        $message = '';
        $success = false;

        try{
            $input = Input::post();

            $group = new Group;
            $group->name = $input['group_name'];
            $group->permissions = $input['group_name'];
            $group->save();
            $success = true;
            $message = 'Group created successfully';
        }catch (Exception $e){
            $message = $e->getMessage();
        }

        if(Request::isAjax()){
            Response::headers()->set('Content-Type', 'application/json');
            Response::setBody(json_encode(
                array(
                    'success'   => $success,
                    'data'      => ($group) ? $group->toArray() : $group,
                    'message'   => $message,
                    'code'      => $success ? 200 : 500
                    )
                ));
        }else{
            Response::redirect($this->siteUrl('admin/group'));
        }
    }

    public function edit($id)
    {
       try{
        $group = Group::find($id);

        $this->data['title'] = 'Edit Group';
        $this->data['group'] = $group->toArray();

        View::display('@usergroup/group/edit.twig', $this->data);
        }catch(Exception $e){
            Response::setBody($e->getMessage());
            Response::finalize();
        }
    }

    public function update($id)
    {

       $success = false;
       $message = '';
       $group    = null;
       $code    = 0;

       try{
            $input = Input::put();

            /** in case request come from post http form */
            $input = is_null($input) ? Input::post() : $input;

            $group = Group::find($id);
            $group->name        = $input['group_name'];
            $group->permissions   = $input['group_permissions'];

            $success = $group->save();
            $code    = 200;
            $message = 'Group updated sucessully';

        }catch (Exception $e){
            $message = $e->getMessage();
            $code    = 500;
        }

        if(Request::isAjax()){
            Response::headers()->set('Content-Type', 'application/json');
            Response::setBody(json_encode(
                array(
                    'success'   => $success,
                    'data'      => ($group) ? $group->toArray() : $group,
                    'message'   => $message,
                    'code'      => $code
                )
            ));
        }else{
            Response::redirect($this->siteUrl('admin/group/'.$id.'/edit'));
        }
    }

   /**
     * destroy resource with specific id
     */
   public function destroy($id)
   {
        $id      = (int) $id;
        $deleted = false;
        $message = '';
        $code    = 0;

        try{
            $group   = Group::find($id);
            $deleted = $group->delete();
            $code    = 200;
        }catch(Exception $e){
            $message = $e->getMessage();
            $code    = 500;
        }

        if(Request::isAjax()){
            Response::headers()->set('Content-Type', 'application/json');
            Response::setBody(json_encode(
                array(
                    'success'   => $deleted,
                    'data'      => array( 'id' => $id ),
                    'message'   => $message,
                    'code'      => $code
                )
            ));
        }else{
            Response::redirect($this->siteUrl('admin/group'));
        }
    }
}