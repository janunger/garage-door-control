#!/usr/bin/env bash

rsync -avz --delete --no-perms --no-owner --no-group ../ gdc:/var/www/ --exclude-from=./deployment_exclusions
ssh gdc << EOF
    set -xe
    sudo supervisorctl restart gdc
EOF