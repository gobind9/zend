<?php
namespace Application\Model;

class User
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $remember_token;
    public $address1;
    public $address2;
    public $city;
    public $country;
    public $credit_limit;
    public $user_type;
    public $created_at;
    public $updated_at;

    public function setPassword($clear_password)
    {
        $this->password = md5($clear_password);
    }

	function exchangeArray($data)
	{
		$this->id		= (isset($data['id'])) ? $data['id'] : null;
		$this->name		= (isset($data['name'])) ? $data['name'] : null;
		$this->email	= (isset($data['email'])) ? $data['email'] : null;
		$this->password	= (isset($data['password'])) ? $data['password'] : null;
		$this->remember_token = (isset($data['remember_token'])) ? $data['remember_token'] : null;
		$this->address1	= (isset($data['address1'])) ? $data['address1'] : null;
		$this->address2	= (isset($data['address2'])) ? $data['address2'] : null;
		$this->city	= (isset($data['city'])) ? $data['city'] : null;
		$this->country	= (isset($data['country'])) ? $data['country'] : null;
		$this->credit_limit	= (isset($data['credit_limit'])) ? $data['credit_limit'] : null;
		$this->user_type	= (isset($data['user_type'])) ? $data['user_type'] : null;
		$this->created_at	= (isset($data['created_at'])) ? $data['created_at'] : null;
		$this->updated_at	= (isset($data['updated_at'])) ? $data['updated_at'] : null;	
	}
	
	public function getArrayCopy()
	{
		return get_object_vars($this);
	}	
}
