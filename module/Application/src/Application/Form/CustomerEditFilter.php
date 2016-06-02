<?php
namespace Application\Form;
use Zend\InputFilter\InputFilter;
class CustomerEditFilter extends InputFilter
{
    public function __construct()
    {
        
		$this->add(array(
			'name'     => 'name',
			//'required' => true,
			'filters'  => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),
			),
			'validators' => array(
			
			
				array(
					'name'    => 'StringLength',
					'options' => array(
					
						'encoding' => 'UTF-8',
						'min'      => 1,
						'max'      => 100,
						'messages' =>array(
						'stringLengthInvalid'=>'Please provide valid name',
						
						)
						
					),
				),
			),
		));
		
		
		
		
		$this->add(array(
            'name'       => 'credit_limit',
            'required'   => true,
            'validators' => array(
			
			),
          
        ));
		
		
		$this->add(array(
			'name' =>'city',
		    'required'=>true,
			'validators'=>array(
			
			)
		));
		
		$this->add(array(
			'name' =>'country',
		    'required'=>true,
			'validators'=>array(
			 
			)
		));
		
	
		
		
    }
}