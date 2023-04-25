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
echo "Start upgrading!!!"
cd $site_directory/app/Http/Controllers/Client/Protocols && rm -rf Free.php && wget https://raw.githubusercontent.com/MoeV2Board/v2board-freenode/master/Free.php
cd $site_directory/resources && rm -rf free_example.json && wget https://raw.githubusercontent.com/MoeV2Board/v2board-freenode/master/free_example.json
echo "Upgrade successful!"
echo "You can use the 'Subscription Link&flag=free' to view software compatibility and import the 'Subscription Link&flag=free&client=Software Information' to the software for free subscriptions."
echo "The free node configuration file is located in '/resources/free.json', you can refer to '/resources/free_example.json ', delete unnecessary comments and add node information in the format as needed."
exit 0
