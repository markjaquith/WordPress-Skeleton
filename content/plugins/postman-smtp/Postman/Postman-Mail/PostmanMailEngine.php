<?php
if (! interface_exists ( "PostmanMailEngine" )) {

	interface PostmanMailEngine {
		public function getTranscript();
		public function send(PostmanMessage $message);
	}

}

