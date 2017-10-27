#!/usr/bin/env bash
sed -i 's/^dtoverlay=pi3-disable-wifi$/#dtoverlay=pi3-disable-wifi/' /boot/config.txt
echo 'Reboot device to enable the change'
