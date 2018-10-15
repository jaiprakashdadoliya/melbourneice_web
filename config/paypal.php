<?php
 return array(

	 // set your paypal live credential
	 'client_id' => 'Af3dzwOdBXP8OP2iUbM0Ilv0tLtd3UUavl7uU6JHepABnRVuBVocwG2T5i_xKvco0-VDECDzfbOP8Rcs',
	 'secret' => 'EB1TJCLwfIsqWyBLh49ylpt22Jdu6qhwGr9nCq8p7Fh_hW3lDLeU7NYvZerQQAm8KMxs7SugdCKeVz_o',
	 
	/**
	 * SDK configuration
	 */
	 'settings' => array(
		 /**
		 * Available option 'sandbox' or 'live'
		 */
		 'mode' => 'live',

		/**
		 * Specify the max request time in seconds
		 */
		 'http.ConnectionTimeOut' => 300,

		/**
		 * Whether want to log to a file
		 */
		 'log.LogEnabled' => true,

		/**
		 * Specify the file that want to write on
		 */
		 'log.FileName' => storage_path() . '/logs/paypal.log',

		/**
		 * Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
		 *
		 * Logging is most verbose in the 'FINE' level and decreases as you
		 * proceed towards ERROR
		 */
		 'log.LogLevel' => 'FINE'
	 ),
 );