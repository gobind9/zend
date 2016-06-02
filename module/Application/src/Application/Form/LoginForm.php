<?php
namespace Application\Form;
use Zend\Form\Form;
class LoginForm extends Form
{
    public function __construct($name = null){
        parent::__construct('Login');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype','multipart/form-data');
        $this->setAttribute('role','form');
        $this->setAttribute('class','form-horizontal');
        
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'email',
				'required' => 'required',
        		'class'=>'form-control', 
            ),
            'options' => array(
                'label' => 'Email',
            	
            ),
        )); 
        
		$this->add(array(
	            'name' => 'password',
	            'attributes' => array(
	                'type'  => 'password',
					'required' => 'required',
					'class'=>'form-control',                
	            ),
	            'options' => array(
	                'label' => 'Password',
	            ),
	        )); 
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Login',
        		'class'=>'btn btn-primary',
            ),
        )); 
    }
}