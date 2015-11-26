<?php
// Connect to the database
include "dbSettings.php";

$DB_link = mysqli_connect($DB_host,$DB_user,$DB_pass,$DB_name);
// Check connection
if (mysqli_connect_errno()) {
  die("Failed to connect to the database: " . mysqli_connect_error());
  }
$error=false;
$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

mysqli_query($DB_link, 'INSERT INTO `__all_signals__` (`conjId`, `src`, `tgt`)
                        VALUES (\'conj_0\', \'p10001\', \'p10002\')
                             , (\'conj_0\', \'p10001\', \'p10003\')
                             , (\'conj_0\', \'p10001\', \'p10004\')
                             , (\'conj_0\', \'p10001\', \'p10005\')
                             , (\'conj_0\', \'p10001\', \'p10012\')
                             , (\'conj_0\', \'p10002\', \'p10001\')
                             , (\'conj_0\', \'p10002\', \'p10003\')
                             , (\'conj_0\', \'p10002\', \'p10005\')
                             , (\'conj_0\', \'p10002\', \'p10012\')
                             , (\'conj_0\', \'p10003\', \'p10001\')
                             , (\'conj_0\', \'p10003\', \'p10002\')
                             , (\'conj_0\', \'p10003\', \'p10004\')
                             , (\'conj_0\', \'p10003\', \'p10005\')
                             , (\'conj_0\', \'p10003\', \'p10012\')
                             , (\'conj_0\', \'p10004\', \'p10001\')
                             , (\'conj_0\', \'p10004\', \'p10003\')
                             , (\'conj_0\', \'p10004\', \'p10005\')
                             , (\'conj_0\', \'p10004\', \'p10012\')
                             , (\'conj_0\', \'p10005\', \'p10001\')
                             , (\'conj_0\', \'p10005\', \'p10002\')
                             , (\'conj_0\', \'p10005\', \'p10003\')
                             , (\'conj_0\', \'p10005\', \'p10004\')
                             , (\'conj_0\', \'p10005\', \'p10012\')
                             , (\'conj_0\', \'p10006\', \'p10008\')
                             , (\'conj_0\', \'p10006\', \'p10009\')
                             , (\'conj_0\', \'p10006\', \'p10010\')
                             , (\'conj_0\', \'p10008\', \'p10006\')
                             , (\'conj_0\', \'p10008\', \'p10009\')
                             , (\'conj_0\', \'p10008\', \'p10010\')
                             , (\'conj_0\', \'p10009\', \'p10006\')
                             , (\'conj_0\', \'p10009\', \'p10008\')
                             , (\'conj_0\', \'p10009\', \'p10010\')
                             , (\'conj_0\', \'p10010\', \'p10006\')
                             , (\'conj_0\', \'p10010\', \'p10008\')
                             , (\'conj_0\', \'p10010\', \'p10009\')
                             , (\'conj_0\', \'p10012\', \'p10001\')
                             , (\'conj_0\', \'p10012\', \'p10002\')
                             , (\'conj_0\', \'p10012\', \'p10003\')
                             , (\'conj_0\', \'p10012\', \'p10004\')
                             , (\'conj_0\', \'p10012\', \'p10005\')
                             , (\'conj_16\', \'1970.13\', \'p10008\')
                             , (\'conj_16\', \'2013.01\', \'p10012\')
                             , (\'conj_16\', \'2014.01\', \'p10012\')
                             , (\'conj_16\', \'2014.03\', \'p10012\')
                             , (\'conj_16\', \'2014.04\', \'p10011\')
                             , (\'conj_16\', \'2014.05\', \'p10007\')'
            );
if($err=mysqli_error($DB_link)) { $error=true; echo $err.'<br />'; }
mysqli_query($DB_link, 'INSERT INTO `Project` (`Project`,`tgt_projectName`,`tgt_projectStatus`,`tgt_projectDescription`,`tgt_projectStartDate`,`tgt_projectStarted`)
                 VALUES (\'2013.01\', \'Alfa board\', \'completed\', \'Create an Alpha board for the customer\', \'2013-12-31\', \'2013.01\')
                      , (\'2014.01\', \'Beta board\', \'under way\', \'further development of the board to Beta\', \'2014-02-15\', \'2014.01\')
                      , (\'2014.03\', \'Release board\', NULL, \'Make board production-ready\', \'2014-07-15\', NULL)
                      , (\'2014.04\', \'Odysseus\', NULL, \'Research how to destroy Troje.\', NULL, NULL)
                      , (\'2014.05\', \'Argos\', NULL, \'get the Golden Fleece\', NULL, \'2014.05\')
                      , (\'1970.13\', \'Apollo 13\', \'Succes\', \'Joosten, get us back to earth please!\', \'1970-04-17\', \'1970.13\')
                ');
if($err=mysqli_error($DB_link)) { $error=true; echo $err.'<br />'; }
mysqli_query($DB_link, 'INSERT INTO `Person` (`Person`,`tgt_personName`,`tgt_personStatus`,`tgt_personEmail`)
                 VALUES (\'p10001\', \'A. Arends\', \'has left the company\', \'arends@example.com\')
                      , (\'p10002\', \'B. Billekens\', NULL, \'billekens@example.com\')
                      , (\'p10003\', \'C. Curly\', \'maternity leave\', \'curly@example.com\')
                      , (\'p10004\', \'D. Diskstation\', \'has left the company\', \'diskstation@example.com\')
                      , (\'p10005\', \'E. Evernote\', NULL, \'evernote@example.com\')
                      , (\'p10006\', \'F. Haise\', NULL, \'jlovell@example.com\')
                      , (\'p10007\', \'J. Ason\', NULL, \'j.ason@example.com\')
                      , (\'p10008\', \'J. Lovell\', NULL, \'l.lovell@example.com\')
                      , (\'p10009\', \'J. Swigert\', NULL, \'j.swigert@example.com\')
                      , (\'p10010\', \'K. Mattingly\', NULL, \'k.mattingly@example.com\')
                      , (\'p10011\', \'O. Dysseus\', \'is on sabbatical until 4th of July\', \'o.dysseus@example.com\')
                      , (\'p10012\', \'P. Leider\', NULL, \'pleider@example.com\')
                ');
if($err=mysqli_error($DB_link)) { $error=true; echo $err.'<br />'; }
mysqli_query($DB_link, 'INSERT INTO `PersonStatus` (`PersonStatus`)
                 VALUES (\'has left the company\')
                      , (\'maternity leave\')
                      , (\'is on sabbatical until 4th of July\')
                ');
if($err=mysqli_error($DB_link)) { $error=true; echo $err.'<br />'; }
mysqli_query($DB_link, 'INSERT INTO `PersonName` (`PersonName`)
                 VALUES (\'A. Arends\')
                      , (\'B. Billekens\')
                      , (\'C. Curly\')
                      , (\'D. Diskstation\')
                      , (\'E. Evernote\')
                      , (\'F. Haise\')
                      , (\'J. Ason\')
                      , (\'J. Lovell\')
                      , (\'J. Swigert\')
                      , (\'K. Mattingly\')
                      , (\'O. Dysseus\')
                      , (\'P. Leider\')
                ');
if($err=mysqli_error($DB_link)) { $error=true; echo $err.'<br />'; }
mysqli_query($DB_link, 'INSERT INTO `Date` (`Date`)
                 VALUES (\'2013-12-31\')
                      , (\'2014-02-15\')
                      , (\'2014-07-15\')
                      , (\'1970-04-17\')
                ');
if($err=mysqli_error($DB_link)) { $error=true; echo $err.'<br />'; }
mysqli_query($DB_link, 'INSERT INTO `Description` (`Description`)
                 VALUES (\'Create an Alpha board for the customer\')
                      , (\'further development of the board to Beta\')
                      , (\'Make board production-ready\')
                      , (\'Research how to destroy Troje.\')
                      , (\'get the Golden Fleece\')
                      , (\'Joosten, get us back to earth please!\')
                ');
if($err=mysqli_error($DB_link)) { $error=true; echo $err.'<br />'; }
mysqli_query($DB_link, 'INSERT INTO `ProjectStatus` (`ProjectStatus`)
                 VALUES (\'completed\')
                      , (\'under way\')
                      , (\'Succes\')
                ');
if($err=mysqli_error($DB_link)) { $error=true; echo $err.'<br />'; }
mysqli_query($DB_link, 'INSERT INTO `Email` (`Email`)
                 VALUES (\'arends@example.com\')
                      , (\'billekens@example.com\')
                      , (\'curly@example.com\')
                      , (\'diskstation@example.com\')
                      , (\'evernote@example.com\')
                      , (\'jlovell@example.com\')
                      , (\'j.ason@example.com\')
                      , (\'l.lovell@example.com\')
                      , (\'j.swigert@example.com\')
                      , (\'k.mattingly@example.com\')
                      , (\'o.dysseus@example.com\')
                      , (\'pleider@example.com\')
                ');
if($err=mysqli_error($DB_link)) { $error=true; echo $err.'<br />'; }
mysqli_query($DB_link, 'INSERT INTO `ProjectName` (`ProjectName`)
                 VALUES (\'Alfa board\')
                      , (\'Beta board\')
                      , (\'Release board\')
                      , (\'Odysseus\')
                      , (\'Argos\')
                      , (\'Apollo 13\')
                ');
if($err=mysqli_error($DB_link)) { $error=true; echo $err.'<br />'; }
mysqli_query($DB_link, 'INSERT INTO `pl` (`Project`,`Person`)
                 VALUES (\'1970.13\', \'p10008\')
                      , (\'2013.01\', \'p10012\')
                      , (\'2014.01\', \'p10012\')
                      , (\'2014.03\', \'p10012\')
                      , (\'2014.04\', \'p10011\')
                      , (\'2014.05\', \'p10007\')
                ');
if($err=mysqli_error($DB_link)) { $error=true; echo $err.'<br />'; }
mysqli_query($DB_link, 'INSERT INTO `member` (`Project`,`Person`)
                 VALUES (\'1970.13\', \'p10010\')
                      , (\'1970.13\', \'p10006\')
                      , (\'1970.13\', \'p10009\')
                      , (\'2013.01\', \'p10003\')
                      , (\'2013.01\', \'p10002\')
                      , (\'2013.01\', \'p10001\')
                      , (\'2014.01\', \'p10005\')
                      , (\'2014.01\', \'p10004\')
                      , (\'2014.01\', \'p10003\')
                      , (\'2014.01\', \'p10001\')
                      , (\'2014.03\', \'p10005\')
                      , (\'2014.03\', \'p10003\')
                      , (\'2014.03\', \'p10002\')
                ');
if($err=mysqli_error($DB_link)) { $error=true; echo $err.'<br />'; }
?>
