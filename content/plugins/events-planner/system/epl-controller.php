<?php
/*
 * This is the parent class that all other controllers extend,
 * to get access to all the methods and properties of the EPL_Base super object.
 * From codeigniter
 */
class EPL_controller{


        function __construct() {

            $this->_parent_name = get_class($this);
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