<?php
class PPUtils
{

	const SDK_VERSION = "1.2.95";
	const SDK_NAME    = "sdk-adaptivepayments-php ";

	/**
	 *
	 * Convert a Name Value Pair (NVP) formatted string into
	 * an associative array taking care to urldecode array values
	 *
	 * @param string $nvpString
	 */
	public static function nvpToMap( $nvpString )
	{

		$ret    = array();
		$params = explode( "&", $nvpString );
		foreach ( $params as $p ) {
			list( $k, $v ) = explode( "=", $p );
			$ret[ $k ] = urldecode( $v );
		}

		return $ret;
	}

	/**
	 * Returns true if the array contains a key like $key
	 *
	 * @param array $map
	 * @param regex $key
	 */
	public static function array_match_key( $map, $key )
	{
		$key     = str_replace( "(", "\(", $key );
		$key     = str_replace( ")", "\)", $key );
		$key     = str_replace( ".", "\.", $key );
		$pattern = "/$key*/";
		foreach ( $map as $k => $v ) {
			preg_match( $pattern, $k, $matches );
			if ( count( $matches ) > 0 )
				return true;
		}

		return false;
	}

	/**
	 *
	 * Get the local IP address. The client address is a required
	 * request parameter for some API calls
	 */
	public static function getLocalIPAddress()
	{

		if ( array_key_exists( "SERVER_ADDR", $_SERVER ) ) {
			// SERVER_ADDR is available only if we are running the CGI SAPI
			return $_SERVER[ 'SERVER_ADDR' ];
		} else if ( function_exists( "gethostname" ) ) {
			// gethostname is available only in PHP >= v5.3
			return gethostbyname( gethostname() );
		} else {
			// fallback if nothing works
			return "127.0.0.1";
		}
	}

	/**
	 *
	 * Compute the value that needs to sent for the PAYPAL_REQUEST_SOURCE
	 * parameter when making API calls
	 */
	public static function getRequestSource()
	{
		return str_replace( " ", "_", self::SDK_NAME ) . self::SDK_VERSION;
	}

	public static function xmlToArray( $xmlInput )
	{

		$xml = simplexml_load_string( $xmlInput );

		$ns = $xml->getNamespaces( true );

		$soap     = $xml->children( $ns[ 'SOAP-ENV' ] );
		$getChild = $soap->Body->children();

		$ret = PPUtils::convertXmlObjToArr( $getChild, $array = array() );

		return $ret;
	}

	/*foreach ($ret as $arry)
	{
		if (isset($arry['children']) && is_array($arry['children'])&& ($arry['children'])!=null) 	{
			foreach ($arry['children'] as $novel)
			{

			}
		}
		else if ($arry['name'] != null)
		{
			$a = $arry['name'] ;
			$b= $arry['text'];
			if (isset($arry['attribute']))
			{
				$c = $arry['attribute'];
			}
		}


		}*/

	/*public function xml2array ( $xmlObject, $out = array () )
	{
		foreach ( (array) $xmlObject as $index => $node )
		{
			$out[$index] = ( is_object ( $node ) ) ? PPUtils::xml2array ( $node ) : $node;
		}
		return $out;
	}*/


	function convertXmlObjToArr( $obj, &$arr )
	{
		$children = $obj->children();
		foreach ( $children as $elementName => $node ) {
			$nextIdx                         = count( $arr );
			$arr[ $nextIdx ]                 = array();
			$arr[ $nextIdx ][ 'name' ]       = strtolower( (string) $elementName );
			$arr[ $nextIdx ][ 'attributes' ] = array();
			$attributes                      = $node->attributes();
			foreach ( $attributes as $attributeName => $attributeValue ) {
				$attribName                                     = strtolower( trim( (string) $attributeName ) );
				$attribVal                                      = trim( (string) $attributeValue );
				$arr[ $nextIdx ][ 'attributes' ][ $attribName ] = $attribVal;
			}
			$text = (string) $node;
			$text = trim( $text );
			if ( strlen( $text ) > 0 ) {
				$arr[ $nextIdx ][ 'text' ] = $text;
			}
			$arr[ $nextIdx ][ 'children' ] = array();
			PPutils::convertXmlObjToArr( $node, $arr[ $nextIdx ][ 'children' ] );
		}

		return $arr;
	}

}

/**
 * @class    xml2array
 */


/**
 * XMLToArray Generator Class
 * @author     :  MA Razzaque Rupom <rupom_315@yahoo.com>, <rupom.bd@gmail.com>
 *             Moderator, phpResource (LINK1http://groups.yahoo.com/group/phpresource/LINK1)
 *             URL: LINK2http://www.rupom.infoLINK2
 * @version    :  1.0
 * @date       06/05/2006
 * Purpose  : Creating Hierarchical Array from XML Data
 * Released : Under GPL
 */

class XmlToArray
{

	var $xml = '';

	/**
	 * Default Constructor
	 *
	 * @param $xml = xml data
	 *
	 * @return none
	 */

	function XmlToArray( $xml )
	{
		$this->xml = $xml;
	}

	/**
	 * _struct_to_array($values, &$i)
	 *
	 * This is adds the contents of the return xml into the array for easier processing.
	 * Recursive, Static
	 *
	 * @access    private
	 *
	 * @param    array $values this is the xml data in an array
	 * @param    int   $i      this is the current location in the array
	 *
	 * @return    Array
	 */

	function _struct_to_array( $values, &$i )
	{
		$child = array();
		if ( isset( $values[ $i ][ 'value' ] ) ) array_push( $child, $values[ $i ][ 'value' ] );

		while ( $i++ < count( $values ) ) {
			switch ( $values[ $i ][ 'type' ] ) {
				case 'cdata':
					array_push( $child, $values[ $i ][ 'value' ] );
					break;

				case 'complete':
					$name = $values[ $i ][ 'tag' ];
					if ( !empty( $name ) ) {
						$child[ $name ] = ( $values[ $i ][ 'value' ] ) ? ( $values[ $i ][ 'value' ] ) : '';
						if ( isset( $values[ $i ][ 'attributes' ] ) ) {
							$child[ $name ] = $values[ $i ][ 'attributes' ];
						}
					}
					break;

				case 'open':
					$name                    = $values[ $i ][ 'tag' ];
					$size                    = isset( $child[ $name ] ) ? sizeof( $child[ $name ] ) : 0;
					$child[ $name ][ $size ] = $this->_struct_to_array( $values, $i );
					break;

				case 'close':
					return $child;
					break;
			}
		}

		return $child;
	}

	//_struct_to_array

	/**
	 * createArray($data)
	 *
	 * This is adds the contents of the return xml into the array for easier processing.
	 *
	 * @access    public
	 *
	 * @param    string $data this is the string of the xml data
	 *
	 * @return    Array
	 */
	function createArray()
	{
		$xml    = $this->xml;
		$values = array();
		$index  = array();
		$array  = array();
		$parser = xml_parser_create();
		xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parse_into_struct( $parser, $xml, $values, $index );
		xml_parser_free( $parser );
		$i              = 0;
		$name           = $values[ $i ][ 'tag' ];
		$array[ $name ] = isset( $values[ $i ][ 'attributes' ] ) ? $values[ $i ][ 'attributes' ] : '';
		$array[ $name ] = $this->_struct_to_array( $values, $i );

		return $array;
	}
	//createArray


}

//XmlToArray
?>