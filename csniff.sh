#!/bin/bash
./vendor/bin/phpcs  --colors  -w --standard=code_standard.xml  src
if [ "$?" != 0 ]
then
./vendor/bin/phpcbf  --colors  -w --standard=code_standard.xml  src
    exit 100
fi
