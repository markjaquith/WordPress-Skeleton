<?php // BUILD: Remove line

/**
 * A class to store Frequency-rules in. Will allow a easy way to find the
 * last and next occurrence of the rule.
 *
 * No - this is so not pretty. But.. ehh.. You do it better, and I will
 * gladly accept patches.
 *
 * Created by trail-and-error on the examples given in the RFC.
 *
 * TODO: Update to a better way of doing calculating the different options.
 * Instead of only keeping track of the best of the current dates found
 * it should instead keep a array of all the calculated dates within the
 * period.
 * This should fix the issues with multi-rule + multi-rule interference,
 * and make it possible to implement the SETPOS rule.
 * By pushing the next period onto the stack as the last option will
 * (hopefully) remove the need for the awful simpleMode
 *
 * @package SG_iCalReader
 * @author Morten Fangel (C) 2008
 * @license http://creativecommons.org/licenses/by-sa/2.5/dk/deed.en_GB CC-BY-SA-DK
 */
class SG_iCal_Freq {
	protected $weekdays = array('MO'=>'monday', 'TU'=>'tuesday', 'WE'=>'wednesday', 'TH'=>'thursday', 'FR'=>'friday', 'SA'=>'saturday', 'SU'=>'sunday');
	protected $knownRules = array('month', 'weekno', 'day', 'monthday', 'yearday', 'hour', 'minute'); //others : 'setpos', 'second'
	protected $ruleModifiers = array('wkst');
	protected $simpleMode = true;

	protected $rules = array('freq'=>'yearly', 'interval'=>1);
	protected $start = 0;
	protected $freq = '';

	protected $excluded; //EXDATE
	protected $added;    //RDATE

	protected $cache; // getAllOccurrences()

	/**
	 * Constructs a new Freqency-rule
	 * @param $rule string
	 * @param $start int Unix-timestamp (important : Need to be the start of Event)
	 * @param $excluded array of int (timestamps), see EXDATE documentation
	 * @param $added array of int (timestamps), see RDATE documentation
	 */
	public function __construct( $rule, $start, $excluded=array(), $added=array(), $exrule = false) {
		$this->start = $start;
		$this->excluded = array();

		$rules = array();
		foreach( explode(';', $rule) AS $v) {
		  if( strpos( $v, '=' ) === false )
		    continue;

			list($k, $v) = explode('=', $v);
			$this->rules[ strtolower($k) ] = $v;
		}

		if( isset($this->rules['until']) && is_string($this->rules['until']) ) {
			$this->rules['until'] = strtotime($this->rules['until']);
		}
		$this->freq = strtolower($this->rules['freq']);

		foreach( $this->knownRules AS $rule ) {
			if( isset($this->rules['by' . $rule]) ) {
				if( $this->isPrerule($rule, $this->freq) ) {
					$this->simpleMode = false;
				}
			}
		}

		if(!$this->simpleMode) {
			if(! (isset($this->rules['byday']) || isset($this->rules['bymonthday']) || isset($this->rules['byyearday']))) {
				$this->rules['bymonthday'] = date('d', $this->start);
			}
		}

		//set until, and cache
		if( isset($this->rules['count']) ) {
			if( $exrule )
				$this->rules['count']++;

			$cache[$ts] = $ts = $this->start;
			for($n=1; $n < $this->rules['count']; $n++) {
				$ts = $this->findNext($ts);
				$cache[$ts] = $ts;
			}
			$this->rules['until'] = $ts;

			//EXDATE
			if (!empty($excluded)) {
				foreach($excluded as $ts) {
					unset($cache[$ts]);
				}
			}
			//RDATE
			if (!empty($added)) {
				$cache = $cache + $added;
				asort($cache);
			}

			$this->cache = array_values($cache);
		}

		$this->excluded = $excluded;
		$this->added = $added;
	}


	/**
	 * Returns all timestamps array(), build the cache if not made before
	 * @return array
	 */
	public function getAllOccurrences() {
		if (empty($this->cache)) {
			//build cache
			$next = $this->firstOccurrence();
			while ($next) {
				$cache[] = $next;
				$next = $this->findNext($next);
			}
			if (!empty($this->added)) {
				$cache = $cache + $this->added;
				asort($cache);
			}
			$this->cache = $cache;
		}
		return $this->cache;
	}

	/**
	 * Returns the previous (most recent) occurrence of the rule from the
	 * given offset
	 * @param int $offset
	 * @return int
	 */
	public function previousOccurrence( $offset ) {
		if (!empty($this->cache)) {
			$t2=$this->start;
			foreach($this->cache as $ts) {
				if ($ts >= $offset)
					return $t2;
				$t2 = $ts;
			}
		} else {
			$ts = $this->start;
			while( ($t2 = $this->findNext($ts)) < $offset) {
				if( $t2 == false ){
					break;
				}
				$ts = $t2;
			}
		}
		return $ts;
	}

	/**
	 * Returns the next occurrence of this rule after the given offset
	 * @param int $offset
	 * @return int
	 */
	public function nextOccurrence( $offset ) {
		if ($offset < $this->start)
			return $this->firstOccurrence();
		return $this->findNext($offset);
	}

	/**
	 * Finds the first occurrence of the rule.
	 * @return int timestamp
	 */
	public function firstOccurrence() {
		$t = $this->start;
		if ( is_array( $this->excluded ) && in_array($t, $this->excluded))
			$t = $this->findNext($t);
		return $t;
	}

	/**
	 * Finds the absolute last occurrence of the rule from the given offset.
	 * Builds also the cache, if not set before...
	 * @return int timestamp
	 */
	public function lastOccurrence() {
		//build cache if not done
		$this->getAllOccurrences();
		//return last timestamp in cache
		return end($this->cache);
	}

	/**
	 * Calculates the next time after the given offset that the rule
	 * will apply.
	 *
	 * The approach to finding the next is as follows:
	 * First we establish a timeframe to find timestamps in. This is
	 * between $offset and the end of the period that $offset is in.
	 *
	 * We then loop though all the rules (that is a Prerule in the
	 * current freq.), and finds the smallest timestamp inside the
	 * timeframe.
	 *
	 * If we find something, we check if the date is a valid recurrence
	 * (with validDate). If it is, we return it. Otherwise we try to
	 * find a new date inside the same timeframe (but using the new-
	 * found date as offset)
	 *
	 * If no new timestamps were found in the period, we try in the
	 * next period
	 *
	 * @param int $offset
	 * @return int
	 */
	public function findNext($offset) {
		if (!empty($this->cache)) {
			foreach($this->cache as $ts) {
				if ($ts > $offset)
					return $ts;
			}
		}

		$debug = false;

		//make sure the offset is valid
		if( $offset === false || (isset($this->rules['until']) && $offset > $this->rules['until']) ) {
			if($debug) echo 'STOP: ' . date('r', $offset) . "\n";
			return false;
		}

		$found = true;

		//set the timestamp of the offset (ignoring hours and minutes unless we want them to be
		//part of the calculations.
		if($debug) echo 'O: ' . date('r', $offset) . "\n";
		$hour = (in_array($this->freq, array('hourly','minutely')) && $offset > $this->start) ? date('H', $offset) : date('H', $this->start);
		$minute = (($this->freq == 'minutely' || isset($this->rules['byminute'])) && $offset > $this->start) ? date('i', $offset) : date('i', $this->start);
		$t = mktime($hour, $minute, date('s', $this->start), date('m', $offset), date('d', $offset), date('Y',$offset));
		if($debug) echo 'START: ' . date('r', $t) . "\n";

		if( $this->simpleMode ) {
			if( $offset < $t ) {
				$ts = $t;
				if ($ts && in_array($ts, $this->excluded))
					$ts = $this->findNext($ts);
			} else {
				$ts = $this->findStartingPoint( $t, $this->rules['interval'], false );
				if( !$this->validDate( $ts ) ) {
					$ts = $this->findNext($ts);
				}
			}
			return $ts;
		}

		$eop = $this->findEndOfPeriod($offset);
		if($debug) echo 'EOP: ' . date('r', $eop) . "\n";

		foreach( $this->knownRules AS $rule ) {
			if( $found && isset($this->rules['by' . $rule]) ) {
				if( $this->isPrerule($rule, $this->freq) ) {
					$subrules = explode(',', $this->rules['by' . $rule]);
					$_t = null;
					foreach( $subrules AS $subrule ) {
						$imm = call_user_func_array(array($this, 'ruleBy' . $rule), array($subrule, $t));
						if( $imm === false ) {
							break;
						}
						if($debug) echo strtoupper($rule) . ': ' . date('r', $imm) . ' A: ' . ((int) ($imm > $offset && $imm < $eop)) . "\n";
						if( $imm > $offset && $imm < $eop && ($_t == null || $imm < $_t) ) {
							$_t = $imm;
						}
					}
					if( $_t !== null ) {
						$t = $_t;
					} else {
						$found = $this->validDate($t);
					}
				}
			}
		}

		if( $offset < $this->start && $this->start < $t ) {
			$ts = $this->start;
		} else if( $found && ($t != $offset)) {
			if( $this->validDate( $t ) ) {
				if($debug) echo 'OK' . "\n";
				$ts = $t;
			} else {
				if($debug) echo 'Invalid' . "\n";
				$ts = $this->findNext($t);
			}
		} else {
			if($debug) echo 'Not found' . "\n";
			$ts = $this->findNext( $this->findStartingPoint( $offset, $this->rules['interval'] ) );
		}
		if ( is_array( $this->excluded ) && $ts && in_array($ts, $this->excluded))
			return $this->findNext($ts);

		return $ts;
	}

	/**
	 * Finds the starting point for the next rule. It goes $interval
	 * 'freq' forward in time since the given offset
	 * @param int $offset
	 * @param int $interval
	 * @param boolean $truncate
	 * @return int
	 */
	private function findStartingPoint( $offset, $interval, $truncate = true ) {
		$_freq = ($this->freq == 'daily') ? 'day__' : $this->freq;
		$t = '+' . $interval . ' ' . substr($_freq,0,-2) . 's';
		if( $_freq == 'monthly' && $truncate ) {
			if( $interval > 1) {
				$offset = strtotime('+' . ($interval - 1) . ' months ', $offset);
			}
			$t = '+' . (date('t', $offset) - date('d', $offset) + 1) . ' days';
		}

		$sp = strtotime($t, $offset);

		if( $truncate ) {
			$sp = $this->truncateToPeriod($sp, $this->freq);
		}

		return $sp;
	}

	/**
	 * Finds the earliest timestamp posible outside this perioid
	 * @param int $offset
	 * @return int
	 */
	public function findEndOfPeriod($offset) {
		return $this->findStartingPoint($offset, 1);
	}

	/**
	 * Resets the timestamp to the beginning of the
	 * period specified by freq
	 *
	 * Yes - the fall-through is on purpose!
	 *
	 * @param int $time
	 * @param int $freq
	 * @return int
	 */
	private function truncateToPeriod( $time, $freq ) {
		$date = getdate($time);
		switch( $freq ) {
			case "yearly":
				$date['mon'] = 1;
			case "monthly":
				$date['mday'] = 1;
			case "daily":
				$date['hours'] = 0;
			case 'hourly':
				$date['minutes'] = 0;
			case "minutely":
				$date['seconds'] = 0;
				break;
			case "weekly":
				if( date('N', $time) == 1) {
					$date['hours'] = 0;
					$date['minutes'] = 0;
					$date['seconds'] = 0;
				} else {
					$date = getdate(strtotime("last monday 0:00", $time));
				}
				break;
		}
		$d = mktime($date['hours'], $date['minutes'], $date['seconds'], $date['mon'], $date['mday'], $date['year']);
		return $d;
	}

	/**
	 * Applies the BYDAY rule to the given timestamp
	 * @param string $rule
	 * @param int $t
	 * @return int
	 */
	private function ruleByday($rule, $t) {
		$dir = ($rule{0} == '-') ? -1 : 1;
		$dir_t = ($dir == 1) ? 'next' : 'last';


		$d = $this->weekdays[substr($rule,-2)];
		$s = $dir_t . ' ' . $d . ' ' . date('H:i:s',$t);

		if( $rule == substr($rule, -2) ) {
			if( date('l', $t) == ucfirst($d) ) {
				$s = 'today ' . date('H:i:s',$t);
			}

			$_t = strtotime($s, $t);

			if( $_t == $t && in_array($this->freq, array('monthly', 'yearly')) ) {
				// Yes. This is not a great idea.. but hey, it works.. for now
				$s = 'next ' . $d . ' ' . date('H:i:s',$t);
				$_t = strtotime($s, $_t);
			}

			return $_t;
		} else {
			$_f = $this->freq;
			if( isset($this->rules['bymonth']) && $this->freq == 'yearly' ) {
				$this->freq = 'monthly';
			}
			if( $dir == -1 ) {
				$_t = $this->findEndOfPeriod($t);
			} else {
				$_t = $this->truncateToPeriod($t, $this->freq);
			}
			$this->freq = $_f;

			$c = preg_replace('/[^0-9]/','',$rule);
			$c = ($c == '') ? 1 : $c;

			$n = $_t;
			while($c > 0 ) {
				if( $dir == 1 && $c == 1 && date('l', $t) == ucfirst($d) ) {
					$s = 'today ' . date('H:i:s',$t);
				}
				$n = strtotime($s, $n);
				$c--;
			}

			return $n;
		}
	}

	private function ruleBymonth($rule, $t) {
		$_t = mktime(date('H',$t), date('i',$t), date('s',$t), $rule, date('d', $t), date('Y', $t));
		if( $t == $_t && isset($this->rules['byday']) ) {
			// TODO: this should check if one of the by*day's exists, and have a multi-day value
			return false;
		} else {
			return $_t;
		}
	}

	private function ruleBymonthday($rule, $t) {
		if( $rule < 0 ) {
			$rule = date('t', $t) + $rule + 1;
		}
		return mktime(date('H',$t), date('i',$t), date('s',$t), date('m', $t), $rule, date('Y', $t));
	}

	private function ruleByyearday($rule, $t) {
		if( $rule < 0 ) {
			$_t = $this->findEndOfPeriod();
			$d = '-';
		} else {
			$_t = $this->truncateToPeriod($t, $this->freq);
			$d = '+';
		}
		$s = $d . abs($rule -1) . ' days ' . date('H:i:s',$t);
		return strtotime($s, $_t);
	}

	private function ruleByweekno($rule, $t) {
		if( $rule < 0 ) {
			$_t = $this->findEndOfPeriod();
			$d = '-';
		} else {
			$_t = $this->truncateToPeriod($t, $this->freq);
			$d = '+';
		}

		$sub = (date('W', $_t) == 1) ? 2 : 1;
		$s = $d . abs($rule - $sub) . ' weeks ' . date('H:i:s',$t);
		$_t  = strtotime($s, $_t);

		return $_t;
	}

	private function ruleByhour($rule, $t) {
		$_t = mktime($rule, date('i',$t), date('s',$t), date('m',$t), date('d', $t), date('Y', $t));
		return $_t;
	}

	private function ruleByminute($rule, $t) {
		$_t = mktime(date('h',$t), $rule, date('s',$t), date('m',$t), date('d', $t), date('Y', $t));
		return $_t;
	}

	private function validDate( $t ) {
		if( isset($this->rules['until']) && $t > $this->rules['until'] ) {
			return false;
		}

		if ( is_array( $this->excluded ) && in_array($t, $this->excluded)) {
			return false;
		}

		if( isset($this->rules['bymonth']) ) {
			$months = explode(',', $this->rules['bymonth']);
			if( !in_array(date('m', $t), $months)) {
				return false;
			}
		}
		if( isset($this->rules['byday']) ) {
			$days = explode(',', $this->rules['byday']);
			foreach( $days As $i => $k ) {
				$days[$i] = $this->weekdays[ preg_replace('/[^A-Z]/', '', $k)];
			}
			if( !in_array(strtolower(date('l', $t)), $days)) {
				return false;
			}
		}
		if( isset($this->rules['byweekno']) ) {
			$weeks = explode(',', $this->rules['byweekno']);
			if( !in_array(date('W', $t), $weeks)) {
				return false;
			}
		}
		if( isset($this->rules['bymonthday'])) {
			$weekdays = explode(',', $this->rules['bymonthday']);
			foreach( $weekdays As $i => $k ) {
				if( $k < 0 ) {
					$weekdays[$i] = date('t', $t) + $k + 1;
				}
			}
			if( !in_array(date('d', $t), $weekdays)) {
				return false;
			}
		}
		if( isset($this->rules['byhour']) ) {
			$hours = explode(',', $this->rules['byhour']);
			if( !in_array(date('H', $t), $hours)) {
				return false;
			}
		}

		return true;
	}

	private function isPrerule($rule, $freq) {
		if( $rule == 'year')
			return false;
		if( $rule == 'month' && $freq == 'yearly')
			return true;
		if( $rule == 'monthday' && in_array($freq, array('yearly', 'monthly')) && !isset($this->rules['byday']))
			return true;
		// TODO: is it faster to do monthday first, and ignore day if monthday exists? - prolly by a factor of 4..
		if( $rule == 'yearday' && $freq == 'yearly' )
			return true;
		if( $rule == 'weekno' && $freq == 'yearly' )
			return true;
		if( $rule == 'day' && in_array($freq, array('yearly', 'monthly', 'weekly')))
			return true;
		if( $rule == 'hour' && in_array($freq, array('yearly', 'monthly', 'weekly', 'daily')))
			return true;
		if( $rule == 'minute' )
			return true;

		return false;
	}
}
