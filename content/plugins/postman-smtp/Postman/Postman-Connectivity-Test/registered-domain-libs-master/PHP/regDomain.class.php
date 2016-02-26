<?php

/*
 * Calculate the effective registered domain of a fully qualified domain name.
 *
 * <@LICENSE>
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to you under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at:
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * </@LICENSE>
 *
 * Florian Sager, 25.07.2008, sager@agitos.de, http://www.agitos.de
 */

/*
 * Remove subdomains from a signing domain to get the registered domain.
 *
 * dkim-reputation.org blocks signing domains on the level of registered domains
 * to rate senders who use e.g. a.spamdomain.tld, b.spamdomain.tld, ... under
 * the most common identifier - the registered domain - finally.
 *
 * This function returns NULL if $signingDomain is TLD itself
 * 
 * $signingDomain has to be provided lowercase (!)
 */

class regDomain {
	/* tld tree */
	protected $tldTree = array();

	/* main function */
	public function getRegisteredDomain($signingDomain, $fallback = TRUE) {
		$signingDomainParts = explode('.', $signingDomain);

		$result = $this->findRegisteredDomain($signingDomainParts, $this->tldTree);

		if ($result===NULL || $result=="") {
			// this is an invalid domain name
			return NULL;
		}

		// assure there is at least 1 TLD in the stripped signing domain
		if (!strpos($result, '.')) {
			if ($fallback===FALSE) {
				return NULL;
			}
			$cnt = count($signingDomainParts);
			if ($cnt==1 || $signingDomainParts[$cnt-2]=="") return NULL;
			if (!$this->validDomainPart($signingDomainParts[$cnt-2]) || !$this->validDomainPart($signingDomainParts[$cnt-1])) return NULL;
			return $signingDomainParts[$cnt-2].'.'.$signingDomainParts[$cnt-1];
		}
		return $result;
	}

	/* validate parts */
	public function validDomainPart($domPart) {
		// see http://www.register.com/domain-extension-rules.rcmx
		$len = strlen($domPart);

		// not more than 63 characters
		if ($len>63) return FALSE;

		// not less than 1 characters --> there are TLD-specific rules that could be considered additionally
		if ($len<1) return FALSE;

		// Use only letters, numbers, or hyphen ("-")
		// not beginning or ending with a hypen (this is TLD specific, be aware!)
		if (!preg_match("/^([a-z0-9])(([a-z0-9-])*([a-z0-9]))*$/", $domPart)) return FALSE;

		return TRUE;
	}

	/* recursive helper method */
	public function findRegisteredDomain($remainingSigningDomainParts, &$treeNode) {
		$sub = array_pop($remainingSigningDomainParts);

		$result = NULL;
		if (isset($treeNode['!'])) {
			return '#';
		}

		if (!$this->validDomainPart($sub)) {
			return NULL;
		}

		if (is_array($treeNode) && array_key_exists($sub, $treeNode)) {
			$result = $this->findRegisteredDomain($remainingSigningDomainParts, $treeNode[$sub]);
		} else if (is_array($treeNode) && array_key_exists('*', $treeNode)) {
			$result = $this->findRegisteredDomain($remainingSigningDomainParts, $treeNode['*']);
		} else {
			return $sub;
		}

		// this is a hack 'cause PHP interpretes '' as NULL
		if ($result == '#') {
			return $sub;
		} else if (strlen($result)>0) {
			return $result.'.'.$sub;
		}
		return NULL;
	}

	/* load tld tree into object */
	function __construct() {
		/* include tld tree data */
		include(dirname(__FILE__) . '/effectiveTLDs.inc.php');
	}

}
