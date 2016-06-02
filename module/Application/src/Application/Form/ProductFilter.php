<?php
namespace Application\Form;
use Zend\InputFilter\InputFilter;
class ProductFilter extends InputFilter {
    public function __construct() {
        $this->add(array(
            'name' => 'name',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StripTags',
                ),
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min' => 2,
                        'max' => 140,
                    ),
                ),
            ),
        ));
		
        $this->add(array(
            'name' => 'id_uom',
            'required' => true,                
        ));
        
        $this->add(array(
            'name' => 'price_per_unit',
            'required' => true,
        ));
		
		$this->add(array(
            'name' => 'qty_in_stock',
            'required' => true,
        ));
    }
}