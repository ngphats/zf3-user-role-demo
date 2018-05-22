<?php
namespace Admin;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Interop\Container\ContainerInterface;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Zend\Session\SessionManager;
use Zend\Authentication\Storage\Session as SessionStorage;

return [
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\VerifyController::class => function($container, $requestedName) {
                $authManager = $container->get('auth_manager');
                $entityManager = $container->get('doctrine.entitymanager.orm_default');
                $config = $container->get('config');
                $recapchaKey = $config['recapcha'];
                $mailService = $container->get('mail_service');
                return new $requestedName($authManager, $entityManager, $recapchaKey, $mailService);
            },
            Controller\RoleController::class => function($container, $requestedName) {
                $entityManager = $container->get('doctrine.entitymanager.orm_default');
                $config = $container->get('config');
                $aclConfig = $config['acl_config'];                
                return new $requestedName($aclConfig, $entityManager);
            },   
            Controller\UserController::class => function($container, $requestedName) {
                return new $requestedName;
            } 
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\AuthManager::class => function($container, $requestedName) {
                $entityManager = $container->get('doctrine.entitymanager.orm_default');
                $authService = $container->get('auth_service');
                $sessionManager = $container->get(SessionManager::class);
                return new $requestedName($authService, $sessionManager, $entityManager);
            }, 
            Service\AuthAdapter::class => function($container, $requestedName) {
                $entityManager = $container->get('doctrine.entitymanager.orm_default');
                return new $requestedName($entityManager);
            },
            /* Dependencies into AuthenticationService */
            \Zend\Authentication\AuthenticationService::class => function($container, $requestedName) {
                $sessionManager = $container->get(SessionManager::class);
                $authStorage = new SessionStorage('Zend_Auth','session',$sessionManager);
                $authAdapter = $container->get('auth_adapter'); 
                return new $requestedName($authStorage, $authAdapter);
            },
            // Acl manager
            Service\AclManager::class => function($container, $requestedName) {
                $authManager = $container->get('auth_manager');
                $config = $container->get('config');
                $aclConfig = $config['acl_config'];
                return new $requestedName($authManager, $aclConfig);
            }, 
            // Mail service
            Service\MailService::class => function($container, $requestedName) {
                return new $requestedName();
            },            
            'admin_nav' => Navigation\AdminNavigationFactory::class
        ],
        'aliases' => [
            'auth_manager' => Service\AuthManager::class,
            'auth_adapter' => Service\AuthAdapter::class,
            'auth_service' => \Zend\Authentication\AuthenticationService::class,
            'acl_manager' => Service\AclManager::class,
            'mail_service'  => Service\MailService::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'Admin' => __DIR__ . '/../view',
        ],
    ],
    // all controller & action for acl.
    'acl_config' => [
        'Index' => [
            'resource'      => 'admin:indexcontroller',
            'privileges'    => ['index']
        ],
        'Verify' => [
            'resource'      => 'admin:verifycontroller',
            'privileges'    => ['index', 'login','logout','forgot','denied']
        ],
        'Role' => [
            'resource'      => 'admin:rolecontroller',
            'privileges'    => ['index', 'create','store','edit']
        ], 
        'User' => [
            'resource'      => 'admin:usercontroller',
            'privileges'    => ['index']
        ]   
    ], 
    'recapcha' => [
        'public_key' => '6Le0Z0AUAAAAAMt_ylJLK0r0eDenzocLrdEXXSCj',
        'private_key' => '6Le0Z0AUAAAAAG6kLmyRNJ9_NMni55XOJtNP4YRz'
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],   
    'navigation' => [
        'admin_nav' => [
            [
                'label' => 'Admin',
                'route' => 'admin',
                'resource' => 'admin:indexcontroller',
                'privilege' => 'index',                
            ], 
            [
                'label' => 'Role',
                'route' => 'admin/role',
                'resource' => 'admin:rolecontroller',
                'privilege' => 'index',
            ], 
            [
                'label' => 'User',
                'route' => 'admin/user',
                'resource' => 'admin:usercontroller',
                'privilege' => 'index'
            ]
        ],
    ],               
];
