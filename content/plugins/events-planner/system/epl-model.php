<?php
/**
 * Model superclass
 * Using this to get access to the loader class and
 * the global wpdb object in child models
 */
class EPL_model{

        function __construct() {
            //parent::__construct();

            $this->_parent_name = ucfirst(get_class($this));
            $this->_assign_libraries();
            

        }

        function _assign_libraries($use_reference = TRUE)
	{
		$epl = EPL_Base::get_instance();
                
		foreach (array_keys(get_object_vars($epl)) as $key)
		{
                    
			if ( ! isset($this->$key) AND $key != $this->_parent_name)
			{
				// In some cases using references can cause
				// problems so we'll conditionally use them
				if ($use_reference == TRUE)
				{
					$this->$key = NULL; // Needed to prevent reference errors with some configurations
					$this->$key = $epl->$key;
				}
				else
				{

					$this->$key = $epl->$key;
				}
			}
		}
	}
}