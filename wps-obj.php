<?php

/*
 * 	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
 *	id mediumint(9) NOT NULL AUTO_INCREMENT,
		funnel_message varchar(255) NOT NULL,
		active boolean NOT NULL DEFAULT FALSE,
		phone boolean NOT NULL DEFAULT TRUE,
		hero_image varchar(255),
		header_icon varchar(255), 
		header_text varchar(255),
		header_subtext varchar(255),
		button_text varchar(255),
		PRIMARY KEY (id)
	) $charset_collate;";
 */

class FunnelObject
{
	public $id;
	public $message;
	public $active;
	public $phone;
	public $hero_image;
	public $header_icon;
	public $header_text;
	public $header_subtext;
	public $button_text;

	//-1 id = new object
	public function __construct($id = -1, $message = '', $active = true, $phone = true, $hero_image = '', $header_icon = '', $header_text = '', $header_subtext = '', $button_text = '')
	{
		$this->id = $id;
		$this->message = $message;
		$this->active = $active;
		$this->phone = $phone;
		$this->hero_image = $hero_image;
		$this->header_icon = $header_icon;
		$this->header_text = FunnelObject::clear_backslashes($header_text);
		$this->header_subtext = FunnelObject::clear_backslashes($header_subtext);
		$this->button_text = FunnelObject::clear_backslashes($button_text);
	}

	public static function clear_backslashes($string)
	{
		return str_replace('\\', '', $string);
	}
}

