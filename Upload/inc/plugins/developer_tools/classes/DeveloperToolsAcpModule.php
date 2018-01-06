<?php
/*
 * Plugin Name: Developer Tools for MyBB 1.8.x
 * Copyright 2018 WildcardSearch
 * http://www.rantcentralforums.com
 *
 * module wrapper
 */

class DeveloperToolsAcpModule extends ConfigurableModule010010
{
	/**
	 * @var the path
	 */
	protected $path = DEVELOPER_TOOLS_MOD_URL;

	/**
	 * @var the function prefix
	 */
	protected $prefix = 'developer_tools';

	/**
	 * @var the function prefix
	 */
	protected $longDescription = '';

	/**
	 * execute the module page
	 *
	 * @return mixed
	 */
	public function execute($settings=null)
	{
		return $this->run('execute', $settings);
	}
}

?>
