#!/bin/bash
DIRNAME=$(dirname $0)
ROOT=$DIRNAME/../../../../

$ROOT/vendor/propel/propel/bin/propel model:build --config-dir $ROOT/app/config/dev --schema-dir $ROOT/app/config/dev  -v
