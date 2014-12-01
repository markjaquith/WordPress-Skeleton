<?php

/**
 * XML document manipulations library.
 *
 * @author     Time.ly Network Inc.
 * @since      2.0
 *
 * @package    AI1EC
 * @subpackage AI1EC.Xml
 */
class Ai1ec_XML_Builder {

		/**
		 * Serializes given data (array, object, etc.) and generates an XML
		 * document from it. If $wrap_json is true, simply serializes the data as
		 * JSON and generates a simple XML wrapper around it.
		 *
		 * Function adapted from
		 * http://www.sean-barton.co.uk/2009/03/turning-an-array-or-object-into-xml-using-php/
		 *
		 * @param  mixed    $data       Data to serialize.
		 * @param  boolean  $wrap_json  Whether to serialize data in JSON format.
		 * @param  string   $node_block Name of root-level XML element.
		 * @param  string   $node_name  Name of XML element to wrap around ordinal array elements.
		 * @return string               Valid XML document.
		 */
		public static function serialize_to_xml(
			$data, $wrap_json = true, $node_block = 'data', $node_name = 'node'
		) {
			$xml = '<?xml version="1.0" encoding="UTF-8" ?>';

			$xml .= '<' . $node_block . '>';

			if ( $wrap_json ) {
				$xml .= '<![CDATA[' . json_encode( $data ) . ']]>';
			}
			else {
				$xml .= self::_generate_xml_from_value( $data, $node_name );
			}

			$xml .= '</' . $node_block . '>';

			return $xml;
		}

		/**
		 * Serializes $value into an XML document fragment.
		 *
		 * Function adapted from
		 * http://www.sean-barton.co.uk/2009/03/turning-an-array-or-object-into-xml-using-php/
		 *
		 * @param  mixed    $array      Value to serialize.
		 * @param  string   $node_name  Name of XML element to wrap around ordinal array elements.
		 * @return string               Valid XML document.
		 */
		private static function _generate_xml_from_value( $value, $node_name ) {
			if ( is_array( $value ) || is_object( $value ) ) {
				$xml = '';

				foreach ( $value as $key => $value ) {
					if ( is_numeric( $key ) ) {
						$key = $node_name;
					}

					$xml .= '<' . $key . '>' .
						self::_generate_xml_from_value( $value, $node_name ) .
						'</' . $key . '>';
				}
			}
			else {
				$xml = htmlspecialchars( $value, ENT_QUOTES );
			}

			return $xml;
		}
}
