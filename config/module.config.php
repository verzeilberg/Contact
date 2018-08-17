<?php

namespace Contact;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'controllers' => [
        'factories' => [
            Controller\ContactController::class => Factory\ContactControllerFactory::class,
        ],
        'aliases' => [
            'contactbeheer' => Controller\ContactController::class,
        ],
    ],
    'service_manager' => [
        'invokables' => [
            'Contact\Service\contactServiceInterface' => 'Contact\Service\contactService'
        ],
    ],
    // The following section is new and should be added to your file
    'router' => [
        'routes' => [
            'contact' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/contact[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'contactbeheer',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'contact' => __DIR__ . '/../view',
        ],
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
            'contactbeheer' => [
                // to anyone.
                ['actions' => '*', 'allow' => '+contact.manage']
            ],
        ]
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
];
