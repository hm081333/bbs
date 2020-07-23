#!/bin/sh
database_name="bbs2"
base_path=$(cd `dirname $0`; pwd)
auto_backup_path=$(cd $base_path"/../data/auto-backup"; pwd)
MYSQL_PWD=root mysqldump -uroot -N $database_name | gzip > $auto_backup_path/$database_name"_"`date +%Y-%m-%d_%H%M%S`.sql.gz
cd $auto_backup_path
rm -rf `find . -name '*.sql.gz' -mtime 31`  #删除31天前的备份文件
