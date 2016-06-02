<?php
// filename : module/Users/src/Users/Form/RegisterForm.php
namespace Application\Form;
use Zend\Form\Form;
class CustomerForm extends Form
{
    public function __construct($name = null){
        parent::__construct('customer');
        $this->setAttribute('method', 'post');
        //$this->setAttribute('enctype','multipart/form-data');
        
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
				//'required' => 'required',
				'class' => 'form-control'
				
            ),
            'options' => array(
			'label' => 'Name',
			'label_attributes' => array(
            'class' => 'col-md-4 control-label',
			),
		),
        )); 
        
		$this->add(array(
	            'name' => 'email',
	            'attributes' => array(
	                'type'  => 'email',
					'class' => 'form-control',
					'required' => 'required email',
					             
	            ),
	            'options' => array(
	                'label' => 'Email Address',
					'label_attributes' => array(
					'class' => 'col-md-4 control-label',
					),

	            ),
	        )); 
			
			
		$this->add(array(
	            'name' => 'password',
	            'attributes' => array(
	                'type'  => 'password',
					'class' => 'form-control',
					'required' => 'required'                 
	            ),
	            'options' => array(
	                'label' => 'Password',
					'label_attributes' => array(
					'class' => 'col-md-4 control-label',
					),
	            ),
	        )); 


				$this->add(array(
	            'name' => 'password_confirmation',
	            'attributes' => array(
	                'type'  => 'password',
					'class' => 'form-control',
					'required' => 'required'                 
	            ),
	            'options' => array(
	                'label' => 'Confirm Password',
					'label_attributes' => array(
					'class' => 'col-md-4 control-label',
					),
	            ),
	        )); 
			
			$this->add(array(
	            'name' => 'address1',
				'type'=>'textarea',
				'attributes' => array(
                    'class' => 'form-control',
                    'required' => 'required',
					'class' => 'form-control',
                    'rows' => 10
                ),
	            'options' => array(
	                'label' => 'Address 1',
					'label_attributes' => array(
					'class' => 'col-md-4 control-label',
					),
	            ),
	        )); 
			
			$this->add(array(
	            'name' => 'address2',
				'type'=>'textarea',
				'attributes' => array(
                    'class' => 'form-control',
                    'rows' => 10
                ),
	            'options' => array(
	                'label' => 'Address 2',
					'label_attributes' => array(
					'class' => 'col-md-4 control-label',
					),
	            ),
	        )); 
			
			$this->add(array(
	            'name' => 'city',
	            'attributes' => array(
	                'type'  => 'text',
					'class' => 'form-control',
					'required' => 'required'                 
	            ),
	            'options' => array(
	                'label' => 'City',
					'label_attributes' => array(
					'class' => 'col-md-4 control-label',
					),
	            ),
	        )); 
			
			
		$this->add(array(
		'type' =>'Zend\Form\Element\Select',
		'name' =>'country',
		 'attributes' => array(
		 'class' => 'form-control',
		 ),
		'options' => array (
		'label' => 'Country',
		'label_attributes' => array(
					'class' => 'col-md-4 control-label',
					),
		 'class' => 'col-md-4 control-label',
		'empty_option' => '--select--',
		'disable_inarray_validator' => true),
				
		));
		
		$this->add(array(
	            'name' => 'credit_limit',
	            'attributes' => array(
	                'type'  => 'text',
					'class' => 'form-control',
					'required' => 'required'                 
	            ),
	            'options' => array(
	                'label' => 'Credit Limit',
					 'label_attributes' => array(
					'class' => 'col-md-4 control-label',
					),
	            ),
	    )); 
			
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Submit'
            ),
        )); 
    }
}