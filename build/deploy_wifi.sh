#!/usr/bin/env bash

rsync -avz --delete --no-perms --no-owner --no-group ../ gdc-wifi:/var/www/ --exclude-from=./deployment_exclusions
ssh gdc-wifi << EOF
    set -xe
    sudo supervisorctl restart gdc
EOF
