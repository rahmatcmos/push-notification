<?php

return [

    "ios" => [
    	/*
    	 * A valid PEM certificate generated from Apple Push Service certificate
    	 */
        "certificate" 	=> storage_path('doc')."/aps.pem",
    		
    	/*
    	 * Password used to generate a certificate
    	 */
        "passPhrase"  	=> ""
    ],
	
    "android" => [
    	/*
    	 * Google GCM api key
    	 * You can retrieve your key in Google Developer Console
    	 */
        "apiKey"      	=> "",
    ]

];