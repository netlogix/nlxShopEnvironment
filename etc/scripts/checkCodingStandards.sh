#!/usr/bin/env bash

FILTERED_FOLDERS=`find ./ -mindepth 1 -maxdepth 1 -type d | grep -Ev 'Resources|tests|etc|logs|.phpspec|.git|vendor|.idea'`
SEPERATOR=" "
FOLDERS=$(printf "${SEPERATOR}%s" "${FILTERED_FOLDERS[@]}")

vendor/bin/ecs-standalone.phar check --no-progress-bar -n -c etc/easy-coding-standard.yml $FOLDERS $@
