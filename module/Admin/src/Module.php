<?php

namespace Admin;

use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        $module = include __DIR__ . '/../config/module.config.php';
        $route = include __DIR__ . '/../config/route.config.php';
        return array_merge($module, $route);
    }

    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $serviceManager 	 = $e->getApplication()->getServiceManager();
        $shared  			 = $eventManager->getSharedManager();

        // send email event
        $mailEvent = new Event\SendMailEvent;
        $mailEvent->attach($eventManager);

        $shared->attach(__NAMESPACE__, 
            MvcEvent::EVENT_DISPATCH, 
            function($e) use($serviceManager)
        {
            // Get controller
            $controller = $e->getTarget(); 
            $controller->layout('layout/admin'); 

            // Get acl manager
            $aclManager = $serviceManager->get('acl_manager');

            //get ViewModel:
            $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
            $viewModel->currentUser = $aclManager->getUserName();
            $viewModel->acl = $aclManager->getAcl();
            $viewModel->role = $aclManager->getRole();            

            // get controller & action
            $route = $e->getRouteMatch();       
            $controllerRoute = $route->getParam('controller');       
            $moduleName = strtolower(substr($controllerRoute, 0, strpos($controllerRoute,'\\')));     
            $arr = explode('\\',$controllerRoute);       
            $controllerName = strtolower(array_pop($arr));
            $resourceName = $moduleName.':'.$controllerName;
            $actionName = $route->getParam('action');
            // Convert dash-style action name to camel-case.
            $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));             

            if (!$aclManager->checkRole($resourceName,$actionName)) {
                $controller->plugin('redirect')->toRoute('admin/verify', [
                    'action' => 'denied'
                ]);
            }

        }, 100);
    }

}
