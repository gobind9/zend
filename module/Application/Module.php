<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Db\ResultSet\ResultSet;
use Application\Model\Product;
use Application\Model\ProductTable;
use Application\Model\Order;
use Application\Model\OrderTable;
use Application\Model\OrderLine;
use Application\Model\OrderLineTable;
use Application\Model\User;
use Application\Model\UserTable;
use Application\Helper\AuthHelper;
use Zend\Db\TableGateway\TableGateway;
use Zend\Session\Container; // We need this when using sessions
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface; 
//use Zend\Permissions\Acl\Role\GenericRole as Role;
//use Zend\Permissions\Acl\Resource\GenericResource as Resource;


class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ViewHelperProviderInterface
{
    public function onBootstrap(MvcEvent $e){
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
	    // add event
	    $eventManager->attach('dispatch', array($this, 'checkLogin')); 
    }

    public function getConfig(){
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig(){
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
	public function getViewHelperConfig(){
    	return array(
            'factories' => array(
                
        	)
        );   
    }
    
	public function getServiceConfig() {
        return array(
            'abstract_factories' => array(),
            'aliases' => array(),
            'factories' => array(
                // SERVICES
                'AuthService' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'user', 'email', 'password', 'MD5(?)');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter);
                    return $authService;
                },
				// DB
                'ProductTable' => function($sm) {
                    $tableGateway = $sm->get('ProductTableGateway');
                    $table = new ProductTable($tableGateway);
                    return $table;
                },
                'ProductTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Product());
                    return new TableGateway('products', $dbAdapter, null, $resultSetPrototype);
                },
                'OrderTable' => function($sm) {
                    $tableGateway = $sm->get('OrderTableGateway');
                    $table = new OrderTable($tableGateway);
                    return $table;
                },
                'OrderTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Order());
                    return new TableGateway('order', $dbAdapter, null, $resultSetPrototype);
                },  
                'OrderLineTable' => function($sm) {
                    $tableGateway = $sm->get('OrderLineTableGateway');
                    $table = new OrderLineTable($tableGateway);
                    return $table;
                },
                'OrderLineTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new OrderLine());
                    return new TableGateway('order_line', $dbAdapter, null, $resultSetPrototype);
                },              
                'UserTable' => function($sm) {
                    $tableGateway = $sm->get('UserTableGateway');
                    $table = new UserTable($tableGateway);
                    return $table;
                },
                'UserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new User());
                    return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
                },
                'UploadTable' => function($sm) {
                    $tableGateway = $sm->get('UploadTableGateway');
                    $uploadSharingTableGateway = $sm->get('UploadSharingTableGateway');
                    $table = new UploadTable($tableGateway, $uploadSharingTableGateway);
                    return $table;
                },
                'UploadTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Upload());
                    return new TableGateway('uploads', $dbAdapter, null, $resultSetPrototype);
                },
                'UploadSharingTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new TableGateway('uploads_sharing', $dbAdapter);
                },
                'ImageUploadTable' => function($sm) {
                    $tableGateway = $sm->get('ImageUploadTableGateway');
                    $table = new ImageUploadTable($tableGateway);
                    return $table;
                },
                'ImageUploadTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ImageUpload());
                    return new TableGateway('image_uploads', $dbAdapter, null, $resultSetPrototype);
                },
				// FORMS
                'LoginForm' => function ($sm) {
                    $form = new \Application\Form\LoginForm();
                    $form->setInputFilter($sm->get('LoginFilter'));
                    return $form;
                },
                'ProductForm' => function ($sm) {
                    $form = new \Application\Form\ProductForm();
                    $form->setInputFilter($sm->get('ProductFilter'));
                    return $form;
                },
                'BasketForm' => function ($sm) {
                    $form = new \Application\Form\BasketForm();
                    return $form;
                },
                'RegisterForm' => function ($sm) {
                    $form = new \Application\Form\RegisterForm();
                    $form->setInputFilter($sm->get('RegisterFilter'));
                    return $form;
                },
                'UserEditForm' => function ($sm) {
                    $form = new \Application\Form\UserEditForm();
                    $form->setInputFilter($sm->get('UserEditFilter'));
                    return $form;
                },
                'UploadForm' => function ($sm) {
                    $form = new \Application\Form\UploadForm();
                    return $form;
                },
                'UploadEditForm' => function ($sm) {
                    $form = new \Application\Form\UploadEditForm();
                    return $form;
                },
                'UploadShareForm' => function ($sm) {
                    $form = new \Application\Form\UploadShareForm();
                    return $form;
                },
                'ImageUploadForm' => function ($sm) {
                    $form = new \Application\Form\ImageUploadForm();
                    $form->setInputFilter($sm->get('ImageUploadFilter'));
                    return $form;
                },
                'MultiImageUploadForm' => function ($sm) {
                    $form = new \Application\Form\MultiImageUploadForm();
                    return $form;
                },
				'CustomerForm' => function ($sm) {
                    $form = new \Application\Form\CustomerForm();
                    $form->setInputFilter($sm->get('CustomerFilter'));
                    return $form;
                },
				// FILTERS 
                'LoginFilter' => function ($sm) {
                    return new \Application\Form\LoginFilter();
                },
                'ProductFilter' => function ($sm) {
                    return new \Application\Form\ProductFilter();
                },
                'RegisterFilter' => function ($sm) {
                    return new \Application\Form\RegisterFilter();
                },
                'UserEditFilter' => function ($sm) {
                    return new \Application\Form\UserEditFilter();
                },
                'ImageUploadFilter' => function ($sm) {
                    return new \Application\Form\ImageUploadFilter();
                },
				 'CustomerFilter' => function ($sm) {
                    return new \Application\Form\CustomerFilter();
                },
				
            ),
            'invokables' => array(),
            'services' => array(),
            'shared' => array(),
        );
    }
    
	protected $whitelist = array(
	    'Application\Controller\Index'
	);

	public function checkLogin($e){
	    $auth   = $e->getApplication()->getServiceManager()->get("Zend\Authentication\AuthenticationService");
	    $target = $e->getTarget();
	    $match  = $e->getRouteMatch();
	    $controller = $match->getParam('controller');
	
	    if( !in_array($controller, $this->whitelist)){
	        if( !$auth->hasIdentity() ){
	            return $target->redirect()->toUrl('/application/index/index');
	        }
	    }
	}

}
