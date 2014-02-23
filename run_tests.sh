#!/bin/bash

rm -fr tests/reports/coverage/*
./bin/phpunit --coverage-html tests/reports/coverage -c app/
