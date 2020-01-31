#!/bin/sh
mysqldump -uroot -proot bbs_v2 | gzip > /var/www/html/bbs2-api/data/auto-backup/bbs_v2_`date +%Y-%m-%d_%H%M%S`.sql.gz
cd /var/www/html/bbs2-api/data/auto-backup
rm -rf `find . -name '*.sql.gz' -mtime 31`  #删除31天前的备份文件
