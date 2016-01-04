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

require_once("effectiveTLDs.inc.php");
require_once("regDomain.inc.php");

if ($_SERVER["argc"]<2) {
	print("test-regDomain.php <(fully-qualified-domain-name )+>\n");
	exit;
}

// strip subdomains from every signing domain
// char dom[] = "sub2.sub.registered.nom.ad";

$argc = $_SERVER["argc"];
$argv = $_SERVER["argv"];

for ($i=1; $i<$argc; $i++) {

	$registeredDomain = getRegisteredDomain($argv[$i], $tldTree);

	if ( $registeredDomain === NULL ) {
		printf("error: %s\n", $argv[$i]);
	} else {
		printf("%s\n", $registeredDomain);
	}
}

?>
