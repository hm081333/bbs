#!/bin/sh
mysqldump -uroot -proot bbs2 | gzip > /var/www/bbs/data/auto-backup/bbs2_`date +%Y-%m-%d_%H%M%S`.sql.gz
cd /var/www/bbs/data/auto-backup
rm -rf `find . -name '*.sql.gz' -mtime 31`  #删除31天前的备份文件
