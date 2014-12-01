#!/bin/bash

# Processes the given 3rd-party .less files, including all of Bootstrap, to
# prefix CSS classes with "ai1ec-" so as to eliminate conflicts with WordPress
# theme or other WordPress plugins.
#
# This script must be run each time such a 3rd-party .less component is updated.
#
# Usage examples:
#
#   $ ./build-3rdparty-less.sh datepicker3.less
#
#   $ ./build-3rdparty-less.sh timepicker.less --preview
#
#   $ ./build-3rdparty-less.sh bootstrap/*

which replace >/dev/null

if [[ $? -eq 0 ]]
then
  replace \
    '\.(?!eot|woff|ttf|svg|Microsoft|gradient\(|less)(-?[_a-zA-Z]+[_a-zA-Z0-9-]*)' \
    '.ai1ec-$1' \
    "$@"
  exit 0
else
  echo 'Error: replace not found. Install Node.js then: npm install -g replace'
  exit 1
fi
