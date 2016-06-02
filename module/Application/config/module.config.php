<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
			'product' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Product',
                        'action'     => 'index',
                    ),
                ),
            ),
			'listcustomer' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/customer/index',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Customer',
                        'action'     => 'index',
                    ),
                ),
            ),
			
			'listuser' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/user/index',
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action'     => 'index',
                    ),
                ),
            ),
			
			'listproduct' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/product/index',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Product',
                        'action'     => 'index',
                    ),
                ),
            ),
			
			'listcustomerorder' => array(
                'type' => 'Segment',
                'options' => array(
                    'route'    => '/customer/customerorder',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Customer',
                        'action'     => 'customerorder',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
					'product' => array(
                        'type' => 'Segment',
                        'options' => array(
                            //  'route' => '/product[/:action]/productid[/:productid]',
                            'route' => '/product[/:action][/:id]',
							'constraints' => array(
								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id' => '[0-9]+',
							),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Product',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action][/:id]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id' 		=>'[0-9]+'
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
        'aliases' => array(
            'Zend\Authentication\AuthenticationService' => 'my_auth_service',
        ),
        'invokables' => array(
            'my_auth_service' => 'Zend\Authentication\AuthenticationService',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(

			'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Product' => 'Application\Controller\ProductController',
			'Application\Controller\Customer' => 'Application\Controller\CustomerController',
			'Application\Controller\User' => 'Application\Controller\UserController'

        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    /*'console' => array(
        'router' => array(
            'routes' => array(
				'product' => array(
                        'type' => 'Segment',
                        'options' => array(                           
                            'route' => '/product[/:action]/',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Application\Controller',
                                'controller' => 'Product',
                                'action' => 'index',
                            ),
                        ),
                    ),
            ),
        ),
    ),*/
);
