#!/usr/bin/env bash

rsync -avz --delete ../ gdc:/home/pi/gdc/ --exclude-from=./deployment_exclusions
