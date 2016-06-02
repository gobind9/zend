<?php
namespace Application\Form;
use Zend\Form\Form;
class ProductForm extends Form {
    public function __construct($name = null) {
        parent::__construct('Product');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
		
		$this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
            'options' => array(
                'label' => 'ID',
            ),
        ));         
                
        
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
				'class' => 'form-control'
            ),
            'options' => array(
                'label' => 'Product Name',
				'label_attributes' => array(
					'class' => 'col-md-4 control-label',
					),
            ),
            'attributes' => array(
                'required' => 'required',
				'class' => 'form-control',
            ),
			'filters' => array(
                array('name' => 'StringTrim'),
            ),
        ));
		
		
		$this->add(array(
		'type' =>'Zend\Form\Element\Select',
		'name' =>'id_uom',
		 'attributes' => array(
		 'class' => 'form-control',
		 ),
		'options' => array (
		'label' => 'Unit of Measure',
		'label_attributes' => array(
					'class' => 'col-md-4 control-label',
					),
		 'class' => 'col-md-4 control-label',
		'empty_option' => '--select--',
		'disable_inarray_validator' => true),
				
		));
		
		
        
		
        $this->add(array(
            'name' => 'price_per_unit',
            'options' => array(
                'label' => 'Price',
				'label_attributes' => array(
					'class' => 'col-md-4 control-label',
					),
            ),
			'attributes' => array(
                'required' => 'required',
				'class' => 'form-control',
            ),
        ));
		 $this->add(array(
            'name' => 'qty_in_stock',
            'options' => array(
                'label' => 'Quantity',
				'label_attributes' => array(
					'class' => 'col-md-4 control-label',
					),
            ),
			'attributes' => array(
                'required' => 'required',
				'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
                'id' => 'submitbutton',
            ),
        ));
    }
    
    
}