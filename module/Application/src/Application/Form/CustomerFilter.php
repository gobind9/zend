<?php
namespace Application\Form;
use Zend\InputFilter\InputFilter;
class CustomerFilter extends InputFilter
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
						'max'      => 25,
						'messages' =>array(
						'stringLengthInvalid'=>'Please provide valid name',
						
						)
						
					),
				),
			),
		));
		
		
		$this->add(array(
            'name'       => 'email',
           // 'required'   => true,
            'validators' => array(
			array(
			  'name'=>'EmailAddress', 
			  'options'=>array(
				'messages'=>array(
				  'emailAddressInvalid'         => "Please Provide valid email Adress.!!!",
				  'emailAddressInvalidFormat'   => "Please Provide valid email format.!!!",
				  'emailAddressInvalidHostname' => "Invalid email address",
				  'emailAddressLengthExceeded'   =>'length is more than required',
				)
			  )
			)
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
		
		
		
        $this->add(array(
            'name'       => 'password',
            'required'   => true,
			'validators'=>array(
			
			)
        ));
		
		 $this->add(array(
            'name'       => 'password_confirmation',
            'required'   => true,
			'validators'=>array(
			array(
			'name'    => 'Identical',
            'options' => array(
                'token' => 'password',
				'Message'=>array(
				'password_confirmation'=>"Password  and confirm password must be same."),
				
            )))
        ));
    }
}