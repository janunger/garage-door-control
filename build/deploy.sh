#!/usr/bin/env bash

rsync -avz --delete --no-perms --no-owner --no-group ../ gdc:/var/www/ --exclude-from=./deployment_exclusions
sudo supervisorctl restart gdc
