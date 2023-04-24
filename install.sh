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
read -p "Please enter your site directory": sitedirectory
echo -e "Your site directory is: $sitedirectory"
exit 0
