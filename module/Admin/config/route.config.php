<?php 

namespace Admin;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'admin' => [
                'type'    => Literal::class,
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/admin',
                    'defaults' => [
                        'controller'    => Controller\IndexController::class,
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    // You can place additional routes that match under the
                    // route defined above here.
                    'verify' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/verify[/:action[/email[/:email[/token[/:token]]]]]',
                            'constraints' => [
                                'action'    => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'token'    => '[a-zA-Z0-9_-]+',
                            ],                             
                            'defaults' => [
                                'controller' => Controller\VerifyController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'role' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/role[/:action[/:id]]',
                            'constraints' => [
                                'action'    => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'id'        => '[0-9]+'
                            ],                            
                            'defaults' => [
                                'controller' => Controller\RoleController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],  
                    'user' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '/user[/:action[/:id]]',
                            'constraints' => [
                                'action'    => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'id'        => '[0-9]+'
                            ],                            
                            'defaults' => [
                                'controller' => Controller\UserController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ], 
                ],
            ],
        ],
    ]  
];