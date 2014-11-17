<?php
use \App;
use \View;
use \Input;
use \Response;

Class HomeController extends BaseController
{

    public function welcome()
    {
        $this->data['title'] = 'Welcome to Slim Starter Application';
        
         View::display('welcome.twig', $this->data);
    }

    
}