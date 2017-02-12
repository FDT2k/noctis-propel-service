#!/bin/bash

src/vendor/propel/propel/bin/propel sql:build --config-dir src/app/config/dev --schema-dir src/app/config/dev  -v $@
