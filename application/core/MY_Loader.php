<?php
class MY_Loader extends CI_Loader{
	protected $_ci_services = array();
	
	public function __construct()
	{
        parent::__construct();
		$this->_ci_service_paths = array(APPPATH);
    }
	
	/**
	 * Service Loader
	 *
	 * This function lets users load and instantiate Services.
	 *
	 * @param	string	the name of the class
	 * @param	string	name for the service
	 * @param	bool	database connection
	 * @return	void
	 */
	public function service($service, $name = '', $db_conn = FALSE)
	{
		if (is_array($service))
		{
			foreach ($service as $babe)
			{
				$this->service($babe);
			}
			return;
		}

		if ($service == '')
		{
			return;
		}

		$path = '';

		// Is the service in a sub-folder? If so, parse out the filename and path.
		if (($last_slash = strrpos($service, '/')) !== FALSE)
		{
			// The path is in front of the last slash
			$path = substr($service, 0, $last_slash + 1);

			// And the service name behind it
			$service = substr($service, $last_slash + 1);
		}

		if ($name == '')
		{
			$name = $service;
		}

		if (in_array($name, $this->_ci_services, TRUE))
		{
			return;
		}

		$CI =& get_instance();
		if (isset($CI->$name))
		{
			show_error('The service name you are loading is the name of a resource that is already being used: '.$name);
		}

		$service = strtolower($service);

		foreach ($this->_ci_service_paths as $mod_path)
		{
			if ( ! file_exists($mod_path.'services/'.$path.$service.'.php'))
			{
				continue;
			}
			
			if ( ! class_exists('MY_Service'))
			{
				load_class('Service', 'core');
			}

			require_once($mod_path.'services/'.$path.$service.'.php');

			$service = ucfirst($service);

			$CI->$name = new $service();

			$this->_ci_services[] = $name;
			return;
		}

		// couldn't find the service
		show_error('Unable to locate the service you have specified: '.$service);
	}

}