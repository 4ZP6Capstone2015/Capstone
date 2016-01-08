/* Recommend: a select all query to show the table, or get table architecture from f-spec */

--ecaRule1
INSERT INTO projectName(pName) VALUES ('newProjName')

--or

ALTER TABLE projectName ADD Alias varchar(255) 
/* ADD (column_1 column-def
	 column_2 column-def ..
	 column_n column-def) */


--ecaRule3
INSERT INTO projectName(pStatus) VALUES ('On going')
-- or
ALTER TABLE projectName ADD pStatus varchar(255);
INSERT INTO projectName(pStatus) VALUES ('In Progress')

--ecaRule5
INSERT INTO projectName(pDescription) VALUES('This project describes a long journey')
--or
ALTER TABLE projectName ADD Description varchar(4000)
INSERT INTO projectName(pDescription) VALUES('got fleece, on way back home')
--ecaRule7
INSERT INTO projectName(StartDate) VALUES (GETDATE()) -- add constraints for dates (i.e., ALTER TABLE <tablename> ADD CONSTRAINT <constraint_name> DEFAULT GETDATE() For <constraint_column>


--or
UPDATE projectName
SET StartDate = null || pName
WHERE projID IN (SELECT BeginDate FROM OngoingProjects) -- OngoingProjects is a table of all ongoing projects, begin date is the projected date of when projects are meant to begin

--ecaRule9

UPDATE projectName 
SET pStatus='In Progress'
WHERE (StartDate < GETDATE()) AND (EndDate > GETDATE())

/* Other SQL date functions are 
DATEPART(): returns single part of date
DATEADD(): adds or subtracts a specified time interval from a date
DATEDIFF(): returns the time between two dates (I was hoping to track let's say a 3 month long project using this as a conditional)
CONVERT(): Displays date/time data in different formats

**FORMATS**
Date: YYYY-MM-DD
DATETIME: YYYY-MM-DD HH:MI:SS
TIMESTAMP: returns a unique number 
*/

-- ecaRule11
SELECT * 
FROM MEMBERS
WHERE (CREATE ASSERTION MemInProj 
CHECK (NOT EXIST(SELECT Name FROM allPersonnel where (allpersonnel.name IN (SELECT name from AllProjects where pName<>'pName'  ) )

/*I got lost in this query a little, but essentially, you want to check that your group members are not part of other projects and you do that by querying all members of all projects, and running check where those who are in project that are not this specific project does; NOT exist is part of an expression used after check, but that could be shortened by using <>. 

CREATE ASSERTION <NAMEOFASSETION> CHECK <EXPRESSION> can also be used 
*/

--ecaRule13
CREATE TRIGGER AddingNewPerson
AFTER UPDATE OF members On projectName
REFERENCING	
	OLD ROW AS oldTuple
	NEW ROW AS newTuple
FOR EACH ROW
WHEN (oldTuple.members <> newTuple.members ) 
THEN (DELETE FROM projectName WHERE oldTuple(NOT EXIST IN newTuple))

-- INSERT person 




