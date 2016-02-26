<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Equation Operating System Classes.
 * 
 * This class was created for the safe parsing of mathematical equations
 * in PHP.  There is a need for a way to successfully parse equations
 * in PHP that do NOT require the use of `eval`.  `eval` at its core
 * opens the system using it to so many security vulnerabilities it is oft
 * suggested /never/ to use it, and for good reason.  This class set will
 * successfully take an equation, parse it, and provide solutions to the
 * developer.  It is a safe way to evaluate expressions without putting
 * the system at risk.
 *
 * 2013/04 UPDATE:
 * - Moved to native class functions for PHP5
 * - Removed deprecated `eregi` calls to `preg_match`
 * - Updated to PHPDoc comment syntax
 * - Added Exception throwing instead of silent exits
 * - Added additional variable prefix of '$', '&' is still allowed as well
 * - Fixed small implied multiplication problem
 *
 * TODO:
 * - Add factorial support. (ie 5! = 120)
 *
 * @author Jon Lawrence <jlawrence11@gmail.com>
 * @copyright Copyright �2005-2013, Jon Lawrence
 * @license http://opensource.org/licenses/LGPL-2.1 LGPL 2.1 License
 * @package EOS
 * @version 2.0
 */

//The following are defines for thrown exceptions

/**
 * No matching Open/Close pair
 */
define('EQEOS_E_NO_SET', 5500);
/**
 * Division by 0
 */
define('EQEOS_E_DIV_ZERO', 5501);
/**
 * No Equation
 */
define('EQEOS_E_NO_EQ', 5502);
/**
 * No variable replacement available
 */
define('EQEOS_E_NO_VAR', 5503);

if(!defined('DEBUG'))
	define('DEBUG', false);

//We use a stack class so we don't have to keep track of indices for an array
// May eventually update to use `array_pop()` `end()` and `array_push()` instead
// of this class.
require_once "stack.class.php";


/**
 * Equation Operating System (EOS) Parser
 *
 * An EOS that can safely parse equations from unknown sources returning
 * the calculated value of it.  Can also handle solving equations with
 * variables, if the variables are defined (useful for the Graph creation
 * that the second and extended class in this file provides. {@see eqGraph})
 * This class was created for PHP4 in 2005, updated to fully PHP5 in 2013.
 * 
 * @author Jon Lawrence <jlawrence11@gmail.com>
 * @copyright Copyright �2005-2013, Jon Lawrence
 * @license http://opensource.org/licenses/LGPL-2.1 LGPL 2.1 License
 * @package Math
 * @subpackage EOS
 * @version 2.0
 */
class eqEOS {
    /**#@+
     *Private variables
     */
	private $postFix;
	private $inFix;
    /**#@-*/
    /**#@+
     * Protected variables
     */
	//What are opening and closing selectors
	protected $SEP = array('open' => array('(', '['), 'close' => array(')', ']'));
	//Top presedence following operator - not in use
	protected $SGL = array('!');
	//Order of operations arrays follow
	protected $ST = array('^');
	protected $ST1 = array('/', '*', '%');
	protected $ST2 = array('+', '-');
	//Allowed functions
	protected $FNC = array('sin', 'cos', 'tan', 'csc', 'sec', 'cot');
    /**#@-*/
	/**
	 * Construct method
	 *
	 * Will initiate the class.  If variable given, will assign to
	 * internal variable to solve with this::solveIF() without needing
	 * additional input.  Initializing with a variable is not suggested.
	 *
	 * @see eqEOS::solveIF()
	 * @param String $inFix Standard format equation
	 */
	public function __construct($inFix = null) {
		$this->inFix = (isset($inFix)) ? $inFix : null;
		$this->postFix = array();
	}
	
	/**
	 * Check Infix for opening closing pair matches.
	 *
	 * This function is meant to solely check to make sure every opening
	 * statement has a matching closing one, and throws an exception if
	 * it doesn't.
	 *
	 * @param String $infix Equation to check
	 * @throws Exception if malformed.
	 * @return Bool true if passes - throws an exception if not.
	 */
	private function checkInfix($infix) {
		if(trim($infix) == "") {
			throw new Exception("No Equation given", EQEOS_E_NO_EQ);
			return false;
		}
		//Make sure we have the same number of '(' as we do ')'
		// and the same # of '[' as we do ']'
		if(substr_count($infix, '(') != substr_count($infix, ')')) {
			throw new Exception("Mismatched parenthesis in '{$infix}'", EQEOS_E_NO_SET);
			return false;
		} elseif(substr_count($infix, '[') != substr_count($infix, ']')) {
			throw new Exception("Mismatched brackets in '{$infix}'", EQEOS_E_NO_SET);
			return false;
		}
		$this->inFix = $infix;
		return true;
	}

	/**
	 * Infix to Postfix
	 *
	 * Converts an infix (standard) equation to postfix (RPN) notation.
	 * Sets the internal variable $this->postFix for the eqEOS::solvePF()
	 * function to use.
	 *
	 * @link http://en.wikipedia.org/wiki/Infix_notation Infix Notation
	 * @link http://en.wikipedia.org/wiki/Reverse_Polish_notation Reverse Polish Notation
	 * @param String $infix A standard notation equation
	 * @return Array Fully formed RPN Stack
	 */
	public function in2post($infix = null) {
		// if an equation was not passed, use the one that was passed in the constructor
		$infix = (isset($infix)) ? $infix : $this->inFix;
		
		//check to make sure 'valid' equation
		$this->checkInfix($infix);
		$pf = array();
		$ops = new phpStack();
		$vars = new phpStack();

		// remove all white-space
		preg_replace("/\s/", "", $infix);

		// Create postfix array index
		$pfIndex = 0;

		//what was the last character? (useful for decerning between a sign for negation and subtraction)
		$lChar = '';

		//loop through all the characters and start doing stuff ^^
		for($i=0;$i<strlen($infix);$i++) {
			// pull out 1 character from the string
			$chr = substr($infix, $i, 1);
			
			// if the character is numerical
			if(preg_match('/[0-9.]/i', $chr)) {
				// if the previous character was not a '-' or a number
				if((!preg_match('/[0-9.]/i', $lChar) && ($lChar != "")) && (@$pf[$pfIndex]!="-"))
					$pfIndex++;	// increase the index so as not to overlap anything
				// Add the number character to the array
				@$pf[$pfIndex] .= $chr;
			}
			// If the character opens a set e.g. '(' or '['
			elseif(in_array($chr, $this->SEP['open'])) {
				// if the last character was a number, place an assumed '*' on the stack
				if(preg_match('/[0-9.]/i', $lChar))
					$ops->push('*');

				$ops->push($chr);
			}
			// if the character closes a set e.g. ')' or ']'
			elseif(in_array($chr, $this->SEP['close'])) {
				// find what set it was i.e. matches ')' with '(' or ']' with '['
				$key = array_search($chr, $this->SEP['close']);
				// while the operator on the stack isn't the matching pair...pop it off
				while($ops->peek() != $this->SEP['open'][$key]) {
					$nchr = $ops->pop();
					if($nchr)
						$pf[++$pfIndex] = $nchr;
					else {
						throw new Exception("Error while searching for '". $this->SEP['open'][$key] ."' in '{$infix}'.", EQEOS_E_NO_SET);
						return false;
					}
				}
				$ops->pop();
			}
			// If a special operator that has precedence over everything else
			elseif(in_array($chr, $this->ST)) {
				$ops->push($chr);
				$pfIndex++;
			}
			// Any other operator other than '+' and '-'
			elseif(in_array($chr, $this->ST1)) {
				while(in_array($ops->peek(), $this->ST1) || in_array($ops->peek(), $this->ST))
					$pf[++$pfIndex] = $ops->pop();

				$ops->push($chr);
				$pfIndex++;
			}
			// if a '+' or '-'
			elseif(in_array($chr, $this->ST2)) {
				// if it is a '-' and the character before it was an operator or nothingness (e.g. it negates a number)
				if((in_array($lChar, array_merge($this->ST1, $this->ST2, $this->ST, $this->SEP['open'])) || $lChar=="") && $chr=="-") {
					// increase the index because there is no reason that it shouldn't..
					$pfIndex++;
					$pf[$pfIndex] = $chr; 
				}
				// Otherwise it will function like a normal operator
				else {
					while(in_array($ops->peek(), array_merge($this->ST1, $this->ST2, $this->ST)))
						$pf[++$pfIndex] = $ops->pop();
					$ops->push($chr);
					$pfIndex++;
				}
			}
			// make sure we record this character to be refered to by the next one
			$lChar = $chr;
		}
		// if there is anything on the stack after we are done...add it to the back of the RPN array
		while(($tmp = $ops->pop()) !== false)
			$pf[++$pfIndex] = $tmp;

		// re-index the array at 0
		$pf = array_values($pf);
		
		// set the private variable for later use if needed
		$this->postFix = $pf;

		// return the RPN array in case developer wants to use it fro some insane reason (bug testing ;]
		return $pf;
	} //end function in2post

	/**
	 * Solve Postfix (RPN)
	 * 
	 * This function will solve a RPN array. Default action is to solve
	 * the RPN array stored in the class from eqEOS::in2post(), can take
	 * an array input to solve as well, though default action is prefered.
	 *
	 * @link http://en.wikipedia.org/wiki/Reverse_Polish_notation Postix Notation
	 * @param Array $pfArray RPN formatted array. Optional.
         * @return Float Result of the operation.
	 */
	public function solvePF($pfArray = null) {
		// if no RPN array is passed - use the one stored in the private var
		$pf = (!is_array($pfArray)) ? $this->postFix : $pfArray;
		
		// create our temporary function variables
		$temp = array();
		$tot = 0;
		$hold = 0;

		// Loop through each number/operator 
		for($i=0;$i<count($pf); $i++) {
			// If the string isn't an operator, add it to the temp var as a holding place
			if(!in_array($pf[$i], array_merge($this->ST, $this->ST1, $this->ST2))) {
				$temp[$hold++] = $pf[$i];
			}
			// ...Otherwise perform the operator on the last two numbers 
			else {
				switch ($pf[$i]) {
					case '+':
						@$temp[$hold-2] = $temp[$hold-2] + $temp[$hold-1];
						break;
					case '-':
						@$temp[$hold-2] = $temp[$hold-2] - $temp[$hold-1];
						break;
					case '*':
						@$temp[$hold-2] = $temp[$hold-2] * $temp[$hold-1];
						break;
					case '/':
						if($temp[$hold-1] == 0) {
							throw new Exception("Division by 0 on: '{$temp[$hold-2]} / {$temp[$hold-1]}' in {$this->inFix}", EQEOS_E_DIV_ZERO);
							return false;
						}
						@$temp[$hold-2] = $temp[$hold-2] / $temp[$hold-1];
						break;
					case '^':
						@$temp[$hold-2] = pow($temp[$hold-2], $temp[$hold-1]);
						break;
					case '%':
						if($temp[$hold-1] == 0) {
							throw new Exception("Division by 0 on: '{$temp[$hold-2]} % {$temp[$hold-1]}' in {$this->inFix}", EQEOS_E_DIV_ZERO);
							return false;
						}
						@$temp[$hold-2] = bcmod($temp[$hold-2], $temp[$hold-1]);
						break;
				}
				// Decrease the hold var to one above where the last number is 
				$hold = $hold-1;
			}
		}
		// return the last number in the array 
		return $temp[$hold-1];

	} //end function solvePF


	/**
	 * Solve Infix (Standard) Notation Equation
	 *
	 * Will take a standard equation with optional variables and solve it. Variables
	 * must begin with '&' will expand to allow variables to begin with '$' (TODO)
	 * The variable array must be in the format of 'variable' => value. If
	 * variable array is scalar (ie 5), all variables will be replaced with it.
	 *
	 * @param String $infix Standard Equation to solve
	 * @param String|Array $vArray Variable replacement
	 * @return Float Solved equation
	 */
	function solveIF($infix, $vArray = null) {
		$infix = ($infix != "") ? $infix : $this->inFix;
		
		//Check to make sure a 'valid' expression
		$this->checkInfix($infix);

		$ops = new phpStack();
		$vars = new phpStack();

		//remove all white-space
		preg_replace("/\s/", "", $infix);
		if(DEBUG)
			$hand=fopen("eq.txt","a");

		//Find all the variables that were passed and replaces them
		while((preg_match('/(.){0,1}[&$]([a-zA-Z]+)(.){0,1}/', $infix, $match)) != 0) {

			//remove notices by defining if undefined.
			if(!isset($match[3])) {
				$match[3] = "";
			}

			if(DEBUG)
				fwrite($hand, "{$match[1]} || {$match[3]}\n");
			// Ensure that the variable has an operator or something of that sort in front and back - if it doesn't, add an implied '*'
			if((!in_array($match[1], array_merge($this->ST, $this->ST1, $this->ST2, $this->SEP['open'])) && $match[1] != "") || is_numeric($match[1])) //$this->SEP['close'] removed
				$front = "*";
			else
				$front = "";

			if((!in_array($match[3], array_merge($this->ST, $this->ST1, $this->ST2, $this->SEP['close'])) && $match[3] != "") || is_numeric($match[3])) //$this->SEP['open'] removed
				$back = "*";
			else
				$back = "";
			
			//Make sure that the variable does have a replacement
			if(!isset($vArray[$match[2]]) && (!is_array($vArray != "") && !is_numeric($vArray))) {
				throw new Exception("Variable replacement does not exist for '". substr($match[0], 1, -1) ."' in {$this->inFix}", EQEOS_E_NO_VAR);
				return false;
			} elseif(!isset($vArray[$match[2]]) && (!is_array($vArray != "") && is_numeric($vArray))) {
				$infix = str_replace($match[0], $match[1] . $front. $vArray. $back . $match[3], $infix);
			} elseif(isset($vArray[$match[2]])) {
				$infix = str_replace($match[0], $match[1] . $front. $vArray[$match[2]]. $back . $match[3], $infix);
			}
		}

		if(DEBUG)
			fwrite($hand, "$infix\n");

		// Finds all the 'functions' within the equation and calculates them 
		// NOTE - when using function, only 1 set of paranthesis will be found, instead use brackets for sets within functions!! 
		while((preg_match("/(". implode("|", $this->FNC) . ")\(([^\)\(]*(\([^\)]*\)[^\(\)]*)*[^\)\(]*)\)/", $infix, $match)) != 0) {
			$func = $this->solveIF($match[2]);
			switch($match[1]) {
				case "cos":
					$ans = cos($func);
					break;
				case "sin":
					$ans = sin($func);
					break;
				case "tan":
					$ans = tan($func);
					break;
				case "sec":
					$tmp = cos($func);
					if($tmp == 0) {
						throw new Exception("Division by 0 on: 'sec({$func}) = 1/cos({$func})' in {$this->inFix}", EQEOS_E_DIV_ZERO);
						return false;
					}
					$ans = 1/$tmp;
					break;
				case "csc":
					$tmp = sin($func);
					if($tmp == 0) {
						throw new Exception("Division by 0 on: 'csc({$func}) = 1/sin({$func})' in {$this->inFix}", EQEOS_E_DIV_ZERO);
						return false;
					}
					$ans = 1/$tmp;
					break;
				case "cot":
					$tmp = tan($func);
					if($tmp == 0) {
						throw new Exception("Division by 0 on: 'cot({$func}) = 1/tan({$func})' in {$this->inFix}", EQEOS_E_DIV_ZERO);
						return false;
					}
					$ans = 1/$tmp;
					break;
				default:
					break;
			}
			$infix = str_replace($match[0], $ans, $infix);
		}
		if(DEBUG)
			fclose($hand);
		return $this->solvePF($this->in2post($infix));


	} //end function solveIF
} //end class 'eqEOS'


// fun class that requires the GD libraries to give visual output to the user 
/* extends the eqEOS class so that it doesn't need to create it as a private var 
    - and it extends the functionality of that class */
/**
 * Equation Graph
 *
 * Fun class that requires the GD libraries to give visual output of an
 * equation to the user.  Extends the eqEOS class.
 *
 * @author Jon Lawrence <jlawrence11@gmail.com>
 * @copyright Copyright �2005-2013 Jon Lawrence
 * @license http://opensource.org/licenses/LGPL-2.1 LGPL 2.1 License
 * @package Math
 * @subpackage EOS
 * @version 2.0
 */
class eqGraph extends eqEOS {
	private $width;
	private $height;
	//GD Image reference
	private $image;

	/**
	 * Constructor
	 *
	 * Sets up the Graph class with an image width and height defaults to
	 * 640x480
	 *
	 * @param Integer $width Image width
	 * @param Integer $height Image height
	 */
	public function __construct($width=640, $height=480) {
		// default width and height equal to that of a poor monitor (in early 2000s)
		$this->width = $width;
		$this->height = $height;
		//initialize main class variables
		parent::__construct();
	} //end function eqGraph


	/**
	 * Create GD Graph Image
	 *
	 * Creates a GD image based on the equation given with the parameters that are set
	 *
	 * @param String $eq Equation to use.  Needs variable in equation to create graph, all variables are interpreted as 'x'
	 * @param Integer $xLow Lower x-bound for graph
	 * @param Integer $xHigh Upper x-bound for graph
	 * @param Float $xStep Stepping points while solving, the lower, the better precision. Slow if lower than .01
	 * @param Boolean $xyGrid Draw gridlines?
	 * @param Boolean $yGuess Guess the upper/lower yBounds?
	 * @param Integer $yLow Lower y-bound
	 * @param Integer $yHigh Upper y-bound
	 * @return Null
	 */
	public function graph($eq, $xLow, $xHigh, $xStep, $xyGrid = false, $yGuess = true, $yLow=false, $yHigh=false) {
		//create our image and allocate the two colors
		$img = ImageCreate($this->width, $this->height);
		$white = ImageColorAllocate($img, 255, 255, 255);
		$black = ImageColorAllocate($img, 0, 0, 0);
		$grey = ImageColorAllocate($img, 220, 220, 220);
		$xStep = abs($xStep);
		//DEVELOPER, UNCOMMENT NEXT LINE IF WANTING TO PREVENT SLOW GRAPHS
		//$xStep = ($xStep > .01) ? $xStep : 0.01;
		if($xLow > $xHigh)
			list($xLow, $xHigh) = array($xHigh, $xLow);	//swap function
		
		$xScale = $this->width/($xHigh-$xLow);
		$counter = 0;
		if(DEBUG) {
			$hand=fopen("eqgraph.txt","w");
			fwrite($hand, "$eq\n");
		}
		for($i=$xLow;$i<=$xHigh;$i+=$xStep) {
			$tester = sprintf("%10.3f",$i);
			if($tester == "-0.000") $i = 0;
			$y = $this->solveIF($eq, $i);
			//eval('$y='. str_replace('&x', $i, $eq).";"); /* used to debug my eqEOS class results */
			if(DEBUG) {
				$tmp1 = sprintf("y(%5.3f) = %10.3f\n", $i, $y);
				fwrite($hand, $tmp1);
			}

			// If developer asked us to find the upper and lower bounds for y... 
			if($yGuess==true) {
				$yLow = ($yLow===false || ($y<$yLow)) ? $y : $yLow;
				$yHigh = ($yHigh===false || $y>$yHigh) ? $y : $yHigh;
			}
			$xVars[$counter] = $y;
			$counter++;			
		}
		if(DEBUG)
			fclose($hand);
		// add 0.01 to each side so that if y is from 1 to 5, the lines at 1 and 5 are seen 
		$yLow-=0.01;$yHigh+=0.01;

		//Now that we have all the variables stored...find the yScale
		$yScale = $this->height/(($yHigh)-($yLow));

		// if developer wanted a grid on the graph, add it now 
		if($xyGrid==true) {
			for($i=ceil($yLow);$i<=floor($yHigh);$i++) {
				$i0 = abs($yHigh-$i);
				ImageLine($img, 0, $i0*$yScale, $this->width, $i0*$yScale, $grey);
			}
			for($i=ceil($xLow);$i<=floor($xHigh);$i++) {
				$i0 = abs($xLow-$i);
				ImageLine($img, $i0*$xScale, 0, $i0*$xScale, $this->height, $grey);
			}
		}
		
		//Now that we have the scales, let's see if we can draw an x/y-axis
		if($xLow <= 0 && $xHigh >= 0) {
			//the y-axis is within our range - draw it.
			$x0 = abs($xLow)*$xScale;
			ImageLine($img, $x0, 0, $x0, $this->height, $black);
			for($i=ceil($yLow);$i<=floor($yHigh);$i++) {
				$i0 = abs($yHigh-$i);
				ImageLine($img, $x0-3, $i0*$yScale, $x0+3, $i0*$yScale, $black);
			}
		}
		if($yLow <= 0 && $yHigh >= 0) {
			//the x-axis is within our range - draw it.
			$y0 = abs($yHigh)*$yScale;
			ImageLine($img, 0, $y0, $this->width, $y0, $black);
			for($i=ceil($xLow);$i<=floor($xHigh);$i++) {
				$i0 = abs($xLow-$i);
				ImageLine($img, $i0*$xScale, $y0-3, $i0*$xScale, $y0+3, $black);
			}
		}
		$counter=2;

		//now graph it all ;]
		for($i=$xLow+$xStep;$i<=$xHigh;$i+=$xStep) {
			$x1 = (abs($xLow - ($i - $xStep)))*$xScale;
			$y1 = (($xVars[$counter-1]<$yLow) || ($xVars[$counter-1] > $yHigh)) ? -1 : (abs($yHigh - $xVars[$counter-1]))*$yScale;
			$x2 = (abs($xLow - $i))*$xScale;
			$y2 = (($xVars[$counter]<$yLow) || ($xVars[$counter] > $yHigh)) ? -1 : (abs($yHigh - $xVars[$counter]))*$yScale;
			
			// if any of the y values were found to be off of the y-bounds, don't graph those connecting lines 
			if($y1!=-1 && $y2!=-1)
				ImageLine($img, $x1, $y1, $x2, $y2, $black);
			$counter++;
		}
		$this->image = $img;
	} //end function 'graph'

	/**
	 * Sends JPG to browser
	 *
	 * Sends a JPG image with proper header to output
	 */
	public function outJPG() {
		header("Content-type: image/jpeg");
		ImageJpeg($this->image);
	}

	/**
	 * Sends PNG to browser
	 *
	 * Sends a PNG image with proper header to output
	 */
	function outPNG() {
		header("Content-type: image/png");
		ImagePng($this->image);
	}
	
	/**
	 * Output GD Image
	 *
	 * Will give the developer the GD resource for the graph that
	 * can be used to store the graph to the FS or other media
	 *
	 * @return Resource GD Image Resource
	 */
	public function getImage() {
		return $this->image;
	}
	
	/**
	 * Output GD Image
	 *
	 * Alias for eqGraph::getImage()
	 *
	 * @return Resource GD Image resource
	 */
	public function outGD() {
		return $this->getImage();
	}
} //end class 'eqGraph'
?>