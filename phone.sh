#!/bin/bash

if [[ -z "$1" ]] ; then

    echo "Error: no url specified"
    exit 1
fi

if [[ -z "$2" ]] ; then

    echo "Error: no proxy specified"
    exit 1
fi

if [[ -z `pidof google-chrome` ]] ; then

    /usr/bin/google-chrome --remote-debugging-port=9222 --headless  --proxy-server="$2" --disable-gpu >~/logs/google-chrome.log 2>&1 &
fi

/usr/bin/node phone.js "$1"
