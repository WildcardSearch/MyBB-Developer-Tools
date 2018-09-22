<?php
/*
 * Plugin Name: Developer Tools for MyBB 1.8.x
 * Copyright 2018 WildcardSearch
 * http://www.rantcentralforums.com
 *
 * this file contains an object wrapper for script definitons
 */

class PhiddleProject extends PortableObject010000
{
	/**
	 * @var string
	 */
	protected $title = '';

	/**
	 * @var string
	 */
	protected $content = '';

	/**
	 * @var string
	 */
	protected $tableName = 'phiddles';
}

?>
