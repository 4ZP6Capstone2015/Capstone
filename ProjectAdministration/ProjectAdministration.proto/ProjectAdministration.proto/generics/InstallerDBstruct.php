<?php
// Try to connect to the database

include "dbSettings.php";

$DB_name='ampersand_projectadministration';
$DB_link = mysqli_connect($DB_host,$DB_user,$DB_pass);
// Check connection
if (mysqli_connect_errno()) {
  die("Failed to connect to MySQL: " . mysqli_connect_error());
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

// Drop the database if it exists
$sql="DROP DATABASE $DB_name";
mysqli_query($DB_link,$sql);
// Don't bother about the error if the database didn't exist...

// Create the database
$sql="CREATE DATABASE $DB_name DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN";
if (!mysqli_query($DB_link,$sql)) {
  die("Error creating the database: " . mysqli_error($DB_link));
  }

// Connect to the freshly created database
$DB_link = mysqli_connect($DB_host,$DB_user,$DB_pass,$DB_name);
// Check connection
if (mysqli_connect_errno()) {
  die("Failed to connect to the database: " . mysqli_connect_error());
  }

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/*** Create new SQL tables ***/

// Session timeout table
if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `__SessionTimeout__`')){
    mysqli_query($DB_link, 'DROP TABLE `__SessionTimeout__`');
}
mysqli_query($DB_link,"CREATE TABLE `__SessionTimeout__`
                       ( `SESSION` VARCHAR(255) UNIQUE NOT NULL
                       , `lastAccess` BIGINT NOT NULL
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

// Timestamp table
if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `__History__`')){
    mysqli_query($DB_link, 'DROP TABLE `__History__`');
}
mysqli_query($DB_link,"CREATE TABLE `__History__`
                       ( `Seconds` VARCHAR(255) DEFAULT NULL
                       , `Date` VARCHAR(255) DEFAULT NULL
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

$time = explode(' ', microTime()); // copied from DatabaseUtils setTimestamp
$microseconds = substr($time[0], 2,6);
$seconds =$time[1].$microseconds;
date_default_timezone_set("Europe/Amsterdam");
$date = date("j-M-Y, H:i:s.").$microseconds;
mysqli_query($DB_link, "INSERT INTO `__History__` (`Seconds`,`Date`) VALUES ('$seconds','$date')");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

// Signal table
if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `__all_signals__`')){
    mysqli_query($DB_link, 'DROP TABLE `__all_signals__`');
}
mysqli_query($DB_link,"CREATE TABLE `__all_signals__`
                       ( `conjId` VARCHAR(255) NOT NULL
                       , `src` VARCHAR(255) NOT NULL
                       , `tgt` VARCHAR(255) NOT NULL
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }


//// Number of plugs: 14
/***********************************************\
* Plug Project                                  *
*                                               *
* fields:                                       *
* I[Project]  [UNI,TOT,INJ,SUR,SYM,ASY,TRN,RFX] *
* projectName  [UNI,TOT]                        *
* projectStatus  [UNI]                          *
* projectDescription  [UNI]                     *
* projectStartDate  [UNI]                       *
* projectStarted  [SYM,ASY,UNI,INJ]             *
\***********************************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `Project`')){
    mysqli_query($DB_link, 'DROP TABLE `Project`');
}
mysqli_query($DB_link,"CREATE TABLE `Project`
                       ( `Project` VARCHAR(255) DEFAULT NULL
                       , `tgt_projectName` VARCHAR(255) DEFAULT NULL
                       , `tgt_projectStatus` VARCHAR(255) DEFAULT NULL
                       , `tgt_projectDescription` TEXT DEFAULT NULL
                       , `tgt_projectStartDate` DATE DEFAULT NULL
                       , `tgt_projectStarted` VARCHAR(255) DEFAULT NULL
                       , PRIMARY KEY (`Project`)
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/**************************************************\
* Plug Assignment                                  *
*                                                  *
* fields:                                          *
* I[Assignment]  [UNI,TOT,INJ,SUR,SYM,ASY,TRN,RFX] *
* project  [UNI,TOT]                               *
* assignee  [UNI,TOT]                              *
* pplStartDate  [UNI]                              *
* pplStarted  [SYM,ASY,UNI,INJ]                    *
\**************************************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `Assignment`')){
    mysqli_query($DB_link, 'DROP TABLE `Assignment`');
}
mysqli_query($DB_link,"CREATE TABLE `Assignment`
                       ( `Assignment` VARCHAR(255) DEFAULT NULL
                       , `tgt_project` VARCHAR(255) DEFAULT NULL
                       , `tgt_assignee` VARCHAR(255) DEFAULT NULL
                       , `tgt_pplStartDate` DATE DEFAULT NULL
                       , `tgt_pplStarted` VARCHAR(255) DEFAULT NULL
                       , PRIMARY KEY (`Assignment`)
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/**********************************************\
* Plug Person                                  *
*                                              *
* fields:                                      *
* I[Person]  [UNI,TOT,INJ,SUR,SYM,ASY,TRN,RFX] *
* personName  [UNI]                            *
* personStatus  [UNI]                          *
* personEmail  [UNI,TOT]                       *
\**********************************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `Person`')){
    mysqli_query($DB_link, 'DROP TABLE `Person`');
}
mysqli_query($DB_link,"CREATE TABLE `Person`
                       ( `Person` VARCHAR(255) DEFAULT NULL
                       , `tgt_personName` VARCHAR(255) DEFAULT NULL
                       , `tgt_personStatus` VARCHAR(255) DEFAULT NULL
                       , `tgt_personEmail` VARCHAR(255) DEFAULT NULL
                       , PRIMARY KEY (`Person`)
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/***********************************************\
* Plug SESSION                                  *
*                                               *
* fields:                                       *
* I[SESSION]  [UNI,TOT,INJ,SUR,SYM,ASY,TRN,RFX] *
\***********************************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `SESSION`')){
    mysqli_query($DB_link, 'DROP TABLE `SESSION`');
}
mysqli_query($DB_link,"CREATE TABLE `SESSION`
                       ( `SESSION` VARCHAR(255) DEFAULT NULL
                       , PRIMARY KEY (`SESSION`)
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/****************************************************\
* Plug PersonStatus                                  *
*                                                    *
* fields:                                            *
* I[PersonStatus]  [UNI,TOT,INJ,SUR,SYM,ASY,TRN,RFX] *
\****************************************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `PersonStatus`')){
    mysqli_query($DB_link, 'DROP TABLE `PersonStatus`');
}
mysqli_query($DB_link,"CREATE TABLE `PersonStatus`
                       ( `PersonStatus` VARCHAR(255) DEFAULT NULL
                       , PRIMARY KEY (`PersonStatus`)
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/**************************************************\
* Plug PersonName                                  *
*                                                  *
* fields:                                          *
* I[PersonName]  [UNI,TOT,INJ,SUR,SYM,ASY,TRN,RFX] *
\**************************************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `PersonName`')){
    mysqli_query($DB_link, 'DROP TABLE `PersonName`');
}
mysqli_query($DB_link,"CREATE TABLE `PersonName`
                       ( `PersonName` VARCHAR(255) DEFAULT NULL
                       , PRIMARY KEY (`PersonName`)
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/********************************************\
* Plug Date                                  *
*                                            *
* fields:                                    *
* I[Date]  [UNI,TOT,INJ,SUR,SYM,ASY,TRN,RFX] *
\********************************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `Date`')){
    mysqli_query($DB_link, 'DROP TABLE `Date`');
}
mysqli_query($DB_link,"CREATE TABLE `Date`
                       ( `Date` DATE DEFAULT NULL
                       , PRIMARY KEY (`Date`)
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/***************************************************\
* Plug Description                                  *
*                                                   *
* fields:                                           *
* I[Description]  [UNI,TOT,INJ,SUR,SYM,ASY,TRN,RFX] *
\***************************************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `Description`')){
    mysqli_query($DB_link, 'DROP TABLE `Description`');
}
mysqli_query($DB_link,"CREATE TABLE `Description`
                       ( `Description` TEXT DEFAULT NULL
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/*****************************************************\
* Plug ProjectStatus                                  *
*                                                     *
* fields:                                             *
* I[ProjectStatus]  [UNI,TOT,INJ,SUR,SYM,ASY,TRN,RFX] *
\*****************************************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `ProjectStatus`')){
    mysqli_query($DB_link, 'DROP TABLE `ProjectStatus`');
}
mysqli_query($DB_link,"CREATE TABLE `ProjectStatus`
                       ( `ProjectStatus` VARCHAR(255) DEFAULT NULL
                       , PRIMARY KEY (`ProjectStatus`)
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/*********************************************\
* Plug Email                                  *
*                                             *
* fields:                                     *
* I[Email]  [UNI,TOT,INJ,SUR,SYM,ASY,TRN,RFX] *
\*********************************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `Email`')){
    mysqli_query($DB_link, 'DROP TABLE `Email`');
}
mysqli_query($DB_link,"CREATE TABLE `Email`
                       ( `Email` VARCHAR(255) DEFAULT NULL
                       , PRIMARY KEY (`Email`)
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/***************************************************\
* Plug ProjectName                                  *
*                                                   *
* fields:                                           *
* I[ProjectName]  [UNI,TOT,INJ,SUR,SYM,ASY,TRN,RFX] *
\***************************************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `ProjectName`')){
    mysqli_query($DB_link, 'DROP TABLE `ProjectName`');
}
mysqli_query($DB_link,"CREATE TABLE `ProjectName`
                       ( `ProjectName` VARCHAR(255) DEFAULT NULL
                       , PRIMARY KEY (`ProjectName`)
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/**************************\
* Plug pl                  *
*                          *
* fields:                  *
* I[Project] /\ pl;pl~  [] *
* pl  []                   *
\**************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `pl`')){
    mysqli_query($DB_link, 'DROP TABLE `pl`');
}
mysqli_query($DB_link,"CREATE TABLE `pl`
                       ( `Project` VARCHAR(255) DEFAULT NULL
                       , `Person` VARCHAR(255) DEFAULT NULL
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/**********************************\
* Plug member                      *
*                                  *
* fields:                          *
* I[Project] /\ member;member~  [] *
* member  []                       *
\**********************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `member`')){
    mysqli_query($DB_link, 'DROP TABLE `member`');
}
mysqli_query($DB_link,"CREATE TABLE `member`
                       ( `Project` VARCHAR(255) DEFAULT NULL
                       , `Person` VARCHAR(255) DEFAULT NULL
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

/***************************************\
* Plug workswith                        *
*                                       *
* fields:                               *
* I[Person] /\ workswith;workswith~  [] *
* workswith  []                         *
\***************************************/

if($columns = mysqli_query($DB_link, 'SHOW COLUMNS FROM `workswith`')){
    mysqli_query($DB_link, 'DROP TABLE `workswith`');
}
mysqli_query($DB_link,"CREATE TABLE `workswith`
                       ( `SrcPerson` VARCHAR(255) DEFAULT NULL
                       , `TgtPerson` VARCHAR(255) DEFAULT NULL
                       ) ENGINE=InnoDB DEFAULT CHARACTER SET UTF8 DEFAULT COLLATE UTF8_BIN");
if($err=mysqli_error($DB_link)) {
  $error=true; echo $err.'<br />';
}

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

mysqli_query($DB_link,'SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
?>
