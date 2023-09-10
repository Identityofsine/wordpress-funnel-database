<?php

//used plugin-wide

//create a response type that allows my functions to have a same return type
class DatabaseResponse {
	public $status;
	public $message;


	//constructor that takes in a status and a message
	public function __construct($status = '', $message = null) {
		$this->status = $status;
		$this->message = $message;
	}
}
