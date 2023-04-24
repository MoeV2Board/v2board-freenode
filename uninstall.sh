#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH
echo "
+----------------------------------------------------------------------
| V2Board-FreeNode Dev For V2Board 1.7.3
+----------------------------------------------------------------------
| Copyright © 2020-2023 SANYIMOE Inc. All rights reserved.
+----------------------------------------------------------------------
| The FreeNode file in /resources/free.json and the FreeNode flag=free
+----------------------------------------------------------------------
"
echo -n "Please enter your site directory:"
read site_directory
echo -e "Your site directory is: $site_directory"
echo "Start uninstalling!!!"
rm -rf $site_directory/app/Http/Controllers/Client/Protocols/Free.php
rm -rf $site_directory/resources/free*.json
echo "congratulations 🎉！ Uninstallation successful!"
exit 0
