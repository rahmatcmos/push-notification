<?php

namespace DeveloperDynamo\PushNotification\Payload;

use Config;

abstract class AbstractPayload implements \ArrayAccess
{
    /**
     * Notification's attributes.
     *
     * @var array
     */
    protected $attributes = [
    		'badge_number' => 0,
    ];
    
    /**
	 * Container for mandatory attributes
     *
     * @var array
     */
    protected $mandatory = [];
    
    /**
	 * Basic mandatory attributes to create minimal format for all platform
     *
     * @var array
     */
    private $private_mandatory = ['title', 'content'];
    
    /**
     * The attributes that should be insert in custom payload position when converting in specific platform format.
     * Example:
     * protected $custom = ['img', 'state', 'params'];
     * 
     * @var array
     */
    protected $custom = [];
	
	/**
	 * Generate payload for ios plaform
	 * 
	 * @return array
	 */
	public function getIosFormat()
	{
		$this->checkMandatoryFields();
		
		$this->applyRawFilter();
		
		/*
		 * Create standard payload for specific platform
		 */
		$payload = [
				"aps" => [
					"alert" => [
						"title"=> $this->title,
						"body"=> $this->content
					],
					"badge" => $this->badge_number,
					"sound" => "default"
				],
		];

		/*
		 * Add custom payload fields
		 */
		$payload = array_merge($payload, $this->getCustomPayload());
		
		return $payload;
	}
	
	/**
	 * Generate payload for android plaform
	 * 
	 * @return array
	 */
	public function getAndroidFormat()
	{
		$this->checkMandatoryFields();
		
		$this->applyRawFilter();
		
		/*
		 * Create standard payload for specific platform
		 */
		$payload = [
				"image" => Config::get('pushnotification.android.defaultIcon'),
				"icon" => Config::get('pushnotification.android.defaultIcon'),
				"iconColor" => "#0f9d58",
				"title" => $this->title,
				"message" => $this->content,
				"msgcnt" => $this->badge_number,
		];

		/*
		 * Add custom payload fields
		 */
		$payload = array_merge($payload, $this->getCustomPayload());
		
		return $payload;
	}
	
	/**
	 * Check if exists mandatory field to compose essential notification payload
	 * 
	 * @throws \Exception
	 * @return boolean
	 */
	public function checkMandatoryFields()
	{
		$fields = array_merge($this->mandatory, $this->private_mandatory);
		
		foreach ($fields as $field){
			if(!$this->getAttribute($field))
				throw new \Exception($field.' is mandatory field in notification payload');
		}
		
		return true;
	}
	
	/**
	 * Compose array only with custom property signed in $custom array
	 * 
	 * @return array
	 */
	public function getCustomPayload()
	{
		$custom = [];
		
		foreach($this->attributes as $key => $value){
        	if (array_key_exists($key, $this->custom)) {
        		$custom[$key] = $value;
        	}
		}
		
		return $custom;
	}
	
	public function applyRawFilter()
	{
		foreach ($this->attributes as $key => $value){
			$this->attributes[$key] = strip_tags($value);
		}
		
		foreach($this->custom as $key => $value){
			$this->custom[$key] = strip_tags($value);
		}
	}

    /**
     * Dynamically retrieve attributes on the notification's payload.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the notification's payload.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Get an attribute from the Notifiction.
     *
     * @param  string  $key
     * @return mixed
     */
    protected function getAttribute($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        
        return null;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    protected function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

	/**
	 * Determine if the given attribute exists.
	 *
	 * @param  mixed  $offset
	 * @return bool
	 */
	public function offsetExists($offset) 
	{
		return isset($this->$offset);
	}

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
	public function offsetGet($offset) 
	{
		return $this->$offset;
	}

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
	public function offsetSet($offset , $value) 
	{
		$this->$offset = $value;
	}

	/**
	 * Unset the value for a given offset.
	 *
	 * @param  mixed  $offset
	 * @return void
	 */
	public function offsetUnset($offset) 
	{
		unset($this->$offset);
	}
}