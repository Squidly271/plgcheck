#!/bin/bash

mkdir -p "/tmp/GitHub/plgcheck/source/plgcheck/usr/local/emhttp/plugins/plgcheck/"
mkdir -p "/tmp/GitHub/plgcheck/source/plgcheck/etc/cron.daily/"

cp /usr/local/emhttp/plugins/plgcheck/* /tmp/GitHub/plgcheck/source/plgcheck/usr/local/emhttp/plugins/plgcheck -R -v
cp /etc/cron.daily/pl* /tmp/GitHub/plgcheck/source/plgcheck/etc/cron.daily -R -v



