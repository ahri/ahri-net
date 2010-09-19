#!/bin/sh
for t in $(echo '.tables' | sqlite3 db.sq3)
do
        echo ".dump $t" | sqlite3 db.sq3
done > db.sql
