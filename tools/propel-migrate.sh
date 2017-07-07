#!/bin/bash
DIRNAME=$(dirname $0)
ROOT=$DIRNAME/../../../../

$ROOT/vendor/propel/propel/bin/propel migrate --config-dir  $ROOT/app/config/dev || exit 1


#src/vendor/propel/propel/bin/propel migrate --config-dir src/app/config/dev  -v
