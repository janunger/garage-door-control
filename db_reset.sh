#!/bin/bash

if [ "$1" != '--force' ]; then
    echo "WARNING! Do not use this in production!"
    echo "Provide parameter '--force' to reset the database."
    exit
fi

app/console doctrine:database:drop --force && \
app/console doctrine:database:create && \
app/console doctrine:schema:create
app/console doctrine:fixtures:load -n
