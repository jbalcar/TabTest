# TabTest
Demonstration of skills during Christmas time with using only basic PHP, MySQL, CSS, HTML

Created:
* Simple template system 
  * Variables replacing: {VAR}
  * Cycles (not nested): {CYCLE:{ARRAY}} ... {END_CYCLE}
  * Conditions (not nested): {IF:{VAR} AND {VAR} IS 1} ... {ELSEIF:{VAR} IS 0} ... {ELSE} ... {END_IF}
    * AND - logic conjuction
    * IS - compare equality
* Work with MySQL 3 tables (every have more than 100 000 items)
* Included:
  * Basic design
  * Pagination
  * Editing description of item in table
  * Expiration of editing
  * One person editing same item in same time
  * User friendly UI
  * Tested in IE, Edge, FireFox, Opera, Chrome

Install:
* In SQL directory is file to import (best via phpmyadmin)
* In CONFIG directory must change file database.php with actual connection to MySQL
* Web must be in http domain root

DEMO: https://test.webit-spec.com/
