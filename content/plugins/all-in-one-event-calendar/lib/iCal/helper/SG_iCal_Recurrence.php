<?php // BUILD: Remove line

/**
 * A wrapper for recurrence rules in iCalendar.  Parses the given line and puts the
 * recurrence rules in the correct field of this object.
 *
 * See http://tools.ietf.org/html/rfc2445 for more information.  Page 39 and onward contains more
 * information on the recurrence rules themselves.  Page 116 and onward contains
 * some great examples which were often used for testing.
 *
 * @package SG_iCalReader
 * @author Steven Oxley
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class SG_iCal_Recurrence {

	public $rrule;

	protected $freq;

	protected $until;
	protected $count;

	protected $interval;
	protected $bysecond;
	protected $byminute;
	protected $byhour;
	protected $byday;
	protected $bymonthday;
	protected $byyearday;
	protected $byyearno;
	protected $bymonth;
	protected $bysetpos;

	protected $wkst;

	/**
	 * A list of the properties that can have comma-separated lists for values.
	 * @var array
	 */
	protected $listProperties = array(
		'bysecond', 'byminute', 'byhour', 'byday', 'bymonthday',
		'byyearday', 'byyearno', 'bymonth', 'bysetpos'
	);

	/**
	 * Creates an recurrence object with a passed in line.  Parses the line.
	 * @param object $line an SG_iCal_Line object which will be parsed to get the
	 * desired information.
	 */
	public function __construct(SG_iCal_Line $line) {
		$this->parseLine($line->getData());
	}

	/**
	 * Parses an 'RRULE' line and sets the member variables of this object.
	 * Expects a string that looks like this:  'FREQ=WEEKLY;INTERVAL=2;BYDAY=SU,TU,WE'
	 * @param string $line the line to be parsed
	 */
	protected function parseLine($line) {
		$this->rrule = $line;

		//split up the properties
		$recurProperties = explode(';', $line);
		$recur = array();
		//loop through the properties in the line and set their associated
		//member variables
		foreach ($recurProperties as $property) {
		  if( empty( $property ) )
		    continue;

			$nameAndValue = explode('=', $property);

			//need the lower-case name for setting the member variable
			$propertyName = strtolower($nameAndValue[0]);
			$propertyValue = $nameAndValue[1];

			//split up the list of values into an array (if it's a list)
			if (in_array($propertyName, $this->listProperties)) {
				$propertyValue = explode(',', $propertyValue);
			}
			$this->$propertyName = $propertyValue;
		}
	}

	/**
	 * Set the $until member
	 * @param mixed timestamp (int) / Valid DateTime format (string)
	 */
	public function setUntil($ts) {
		if ( is_int($ts) )
			$dt = new DateTime('@'.$ts);
		else
			$dt = new DateTime($ts);
		$this->until = $dt->format('Ymd\THisO');
	}

	/**
	 * Retrieves the desired member variable and returns it (if it's set)
	 * @param string $member name of the member variable
	 * @return mixed the variable value (if set), false otherwise
	 */
	protected function getMember($member)
	{
		if (isset($this->$member)) {
			return $this->$member;
		}
		return false;
	}

	/**
	 * Returns the frequency - corresponds to FREQ in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getFreq() {
		return $this->getMember('freq');
	}

	/**
	 * Returns when the event will go until - corresponds to UNTIL in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getUntil() {
		return $this->getMember('until');
	}

	/**
	 * Returns the count of the times the event will occur (should only appear if 'until'
	 * does not appear) - corresponds to COUNT in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getCount() {
		return $this->getMember('count');
	}

	/**
	 * Returns the interval - corresponds to INTERVAL in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getInterval() {
		return $this->getMember('interval');
	}

	/**
	 * Returns the bysecond part of the event - corresponds to BYSECOND in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getBySecond() {
		return $this->getMember('bysecond');
	}

	/**
	 * Returns the byminute information for the event - corresponds to BYMINUTE in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getByMinute() {
		return $this->getMember('byminute');
	}

	/**
	 * Corresponds to BYHOUR in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getByHour() {
		return $this->getMember('byhour');
	}

	/**
	 *Corresponds to BYDAY in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getByDay() {
		return $this->getMember('byday');
	}

	/**
	 * Corresponds to BYMONTHDAY in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getByMonthDay() {
		return $this->getMember('bymonthday');
	}

	/**
	 * Corresponds to BYYEARDAY in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getByYearDay() {
		return $this->getMember('byyearday');
	}

	/**
	 * Corresponds to BYYEARNO in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getByYearNo() {
		return $this->getMember('byyearno');
	}

	/**
	 * Corresponds to BYMONTH in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getByMonth() {
		return $this->getMember('bymonth');
	}

	/**
	 * Corresponds to BYSETPOS in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getBySetPos() {
		return $this->getMember('bysetpos');
	}

	/**
	 * Corresponds to WKST in RFC 2445.
	 * @return mixed string if the member has been set, false otherwise
	 */
	public function getWkst() {
		return $this->getMember('wkst');
	}
}