#!/bin/bash

src/vendor/propel/propel/bin/propel sql:build --config-dir src/app/config/dev --schema-dir src/app/config/dev  -v $@

src/vendor/propel/propel/bin/propel model:build --config-dir src/app/config/dev --schema-dir src/app/config/dev  -v

src/vendor/propel/propel/bin/propel diff --config-dir src/app/config/dev --schema-dir src/app/config/dev  -v

src/vendor/propel/propel/bin/propel migrate --config-dir src/app/config/dev  -v
