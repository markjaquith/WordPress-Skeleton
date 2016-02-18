<?php //vim: foldmethod=marker

class MockOAuthDataStore extends OAuthDataStore
{ /*{{{*/
	private $consumer;
	private $request_token;
	private $access_token;
	private $nonce;

	function __construct()
	{ /*{{{*/
		$this->consumer      = new OAuthConsumer( "key", "secret", null );
		$this->request_token = new OAuthToken( "requestkey", "requestsecret", 1 );
		$this->access_token  = new OAuthToken( "accesskey", "accesssecret", 1 );
		$this->nonce         = "nonce";
	}

	/*}}}*/

	function lookup_consumer( $consumer_key )
	{ /*{{{*/
		if ( $consumer_key == $this->consumer->key ) return $this->consumer;

		return null;
	}

	/*}}}*/

	function lookup_token( $consumer, $token_type, $token )
	{ /*{{{*/
		$token_attrib = $token_type . "_token";
		if ( $consumer->key == $this->consumer->key
			&& $token == $this->$token_attrib->key
		) {
			return $this->$token_attrib;
		}

		return null;
	}

	/*}}}*/

	function lookup_nonce( $consumer, $token, $nonce, $timestamp )
	{ /*{{{*/
		if ( $consumer->key == $this->consumer->key
			&& ( ( $token && $token->key == $this->request_token->key )
				|| ( $token && $token->key == $this->access_token->key ) )
			&& $nonce == $this->nonce
		) {
			return $this->nonce;
		}

		return null;
	}

	/*}}}*/

	function new_request_token( $consumer, $callback = null )
	{ /*{{{*/
		if ( $consumer->key == $this->consumer->key ) {
			return $this->request_token;
		}

		return null;
	}

	/*}}}*/

	function new_access_token( $token, $consumer, $verifier = null )
	{ /*{{{*/
		if ( $consumer->key == $this->consumer->key
			&& $token->key == $this->request_token->key
		) {
			return $this->access_token;
		}

		return null;
	}
	/*}}}*/
}

/*}}}*/
?>
