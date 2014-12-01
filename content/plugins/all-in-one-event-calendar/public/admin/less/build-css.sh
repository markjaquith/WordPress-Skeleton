#!/bin/bash

LESSC="lessc --no-color --yui-compress --include-path=."

which lessc >/dev/null

if [[ $? -eq 0 ]]
then
	$LESSC timely-bootstrap.less > ../css/bootstrap.min.css
  errcode=$?
  if [[ $errcode -ne 0 ]]; then
    echo 'Error during compilation. Please ensure you have less >= 1.7.0 installed.'
  fi
	exit $errcode
else
	echo 'Error: lessc not found.'
	echo 'Install Node.js then: npm install -g less'
	exit 1;
fi
