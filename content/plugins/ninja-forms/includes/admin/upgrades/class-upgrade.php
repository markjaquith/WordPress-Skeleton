<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_Upgrade
 *
 * The Upgrade class should be extended by all upgrades to be used by the Upgrade Handler.
 */
abstract class NF_Upgrade
{
    /**
     * @var name
     *
     * The name is the unique identifier for the upgrade.
     */
    public $name;



    /**
     * @var priority
     *
     * The priority determines the oder in which the upgrades are run.
     * Priorities are compared as version numbers that corresponds to when they were introduced.
     */
    public $priority;



    /**
     * @var decription
     *
     * The description will be displayed for the user in the Upgrade Handler admin screen.
     */
    public $description;



    /**
     * @var total_steps
     *
     * The total number of steps that need to be processed.
     */
    public $total_steps;



    /**
     * @var args
     *
     * The args variable is passes between calls.
     */
    public $args = array();



    /**
     * @var errors
     *
     * The errors property is used to store errors for the Upgrade Handler to reference.
     */
    public $errors = array();



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->nice_name = ucwords( str_replace( '_', ' ', $this->name) );
        $this->description = __( $this->description, 'ninja-forms' );
    }



    /**
     * Loading
     *
     * The loading method is used to setup the upgrade and is called by the Upgrade Handler.
     */
    abstract function loading();



    /**
     * Step
     *
     * @param $step
     *
     * The step method will be called by the parent _step method.
     */
    abstract public function step( $step );



    /**
     * Complete
     *
     * The complete method will be called by the Upgrade Handler when all steps are complete.
     */
    abstract public function complete();



    /**
     * Is Complete
     *
     * The isComplete method checks to see if the upgrade has already been completed.
     */
    abstract public function isComplete();



    /**
     * _Step
     *
     * @param $step
     *
     * The _step method is called by the Upgrade Handler and is a middleman for step.
     */
    public function _step( $step )
    {
        $last_step = $this->getLastStep();

        if( $step < $last_step ) {
            $step = $last_step;
        }

        $this->_beforeStep( $step );
        $this->step( $step );
        $this->_afterStep( $step );
        $this->setLastStep( $step );
    }



    /**
     * Before Step
     *
     * @param $step
     *
     * The _beforeStep method is called by the _step method before calling the extended step method.
     */
    public function _beforeStep( $step )
    {
        // This method is optionally extended and is intentionally left blank.
    }



    /**
     * After Step
     *
     * @param $step
     *
     * The _afterStep method is called by the _step method after calling the extended step method.
     */
    public function _afterStep( $step )
    {
        // This method is optionally extended and is intentionally left blank.
    }



    /**
     * Get Last Step
     *
     * Gets the last step processed from the wp_options table.
     *
     * @return mixed
     */
    public function getLastStep()
    {
        return get_option( 'nf_upgrade_' . $this->name . '_last_step', 0 );
    }



    /**
     * Set Last Step
     *
     * Updates the value in the wp_options table with the last step processed.
     *
     * @param $step
     */
    public function setLastStep( $step )
    {
        update_option( 'nf_upgrade_' . $this->name . '_last_step', $step );
    }

}
