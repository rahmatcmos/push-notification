<?php

namespace DeveloperDynamo\PushNotification\Contracts;

abstract class Payload
{
	/**
	 * IOS payload structure
	 * 
	 * @var array
	 */
    protected $iosPayload = [];
    
    /**
     * Android payload structure
     * 
     * @var unknown
     */
    protected $androidPayload = [];
    
    /**
	 * Basic mandatory attributes for ios
     *
     * @var array
     */
    private $iosMandatoryFields = ['title', 'body'];
    
    /**
	 * Basic mandatory attributes for android
     *
     * @var array
     */
    private $androidMandatoryFields = ['title', 'message'];
	
	/**
	 * Generate payload for ios plaform
	 * 
	 * @return array
	 */
	final public function getIosFormat()
	{
		$this->checkIosMandatoryFields();
		
		return ["aps" => $this->rawFilter($this->iosPayload)];
	}
	
	/**
	 * Generate payload for android plaform
	 * 
	 * @return array
	 */
	final public function getAndroidFormat()
	{
		$this->checkAndroidMandatoryFields();
		
		return $this->rawFilter($this->androidPayload);
	}
	
	/**
	 * Check if exists mandatory field to compose essential notification payload
	 * 
	 * @throws \Exception
	 * @return boolean
	 */
	public function checkIosMandatoryFields()
	{
		foreach ($this->iosMandatoryFields as $field){
			if(! array_key_exists($field, $this->iosPayload) )
				return false;
		}
		
		return true;
	}
	
	/**
	 * Check if exists mandatory field to compose essential notification payload
	 * 
	 * @throws \Exception
	 * @return boolean
	 */
	public function checkAndroidMandatoryFields()
	{
		foreach ($this->androidMandatoryFields as $field){
			if(! array_key_exists($field, $this->androidPayload) )
				return false;
		}
		
		return true;
	}
	
	/**
	 * Recursive methods to strip html tags from your payload attributes
	 * 
	 * @param array $payload
	 * @return void
	 */
	public function rawFilter($arr)
	{
		foreach ($arr as $key => $value){
			if(is_array($value))
				$this->applyRawFilter($value);
				
			$arr[$key] = strip_tags($value);
		}
		
		return $arr;
	}
}