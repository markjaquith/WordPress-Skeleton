<?php
/**
 * Utility class for transforming PHP objects into
 * appropriate service payload type and vice versa
 */
class PPObjectTransformer
{

	public function toString( $object )
	{

		if ( $object == null ) {
			throw new PPTransformerException( "Empty object" );
		}

		$confManager = PPConfigManager::getInstance();
		switch ( strtoupper( $confManager->get( "service.Binding" ) ) ) {
			case 'SOAP':
				return $object->toXMLString();

			case 'XML':
			case 'JSON':
				return "";
			case 'NVP':
			default:
				return $object->toNVPString();
		}
	}


}

?>
