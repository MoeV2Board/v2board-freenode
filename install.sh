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
while [ "$site_directory" == null ] ; do
    read -p "Please enter your site directory:" site_directory
done


echo "Your site directory is: $site_directory"
