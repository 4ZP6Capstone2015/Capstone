<?php 

/* Please forward any comments to the author: michiel.stornebrink@tno.nl

   This file defines the functions 'InsPair', 'DelPair', InsAtom, DelAtom and NewStruct
   There are no guarantees with respect to their 100% functioning. Have fun...
   
   This file has been modified to produce Exceptions rather than that it dies...
   Such exceptions may be caught. The syntax for doing this is as follows:
   
   try { <insert code here>;
         throw new Exception("identification string of the exception", httpStatusCode);
         <insert other code if needed>; 
       }
  catch (Exception $e)
       { <insert exception handling code here>;
         <the exception identifier is in variable $e>;
       }
*/

/*
   Example of rule that automatically inserts pairs into a relation (analogous stuff holds for DelPair):
   ROLE ExecEngine MAINTAINS "New Customers"
   RULE "New Customers": customerOrder[Person*Order];companyOrder[Company*Order]~ |- customerOf[Person*Company]
   MEANING "If a person places an order at a company, the person is a customer of that company"
   VIOLATION (TXT "InsPair;customerOf;Person;", SRC I, TXT";Company;", TGT I)
*/
// Use:  VIOLATION (TXT "InsPair;<relation>;<srcConcept>;<srcAtom>;<tgtConcept>;<tgtAtom>")
function InsPair($relationName,$srcConcept,$srcAtom,$tgtConcept,$tgtAtom){
	if(func_num_args() != 5) throw new Exception("Wrong number of arguments supplied for function InsPair(): ".func_num_args()." arguments", 500);
	Notifications::addLog("InsPair($relationName,$srcConcept,$srcAtom,$tgtConcept,$tgtAtom)", 'ExecEngine');
	try{	
		$database = Database::singleton();
		
		// Check if relation signature exists: $relationName[$srcConcept*$tgtConcept]
		$relation = Relation::isCombination($relationName, $srcConcept, $tgtConcept);
		
		if($srcAtom == "NULL" or $tgtAtom == "NULL") throw new Exception("Use of keyword NULL is deprecated, use '_NEW'", 500);
		
		// if either srcAtom or tgtAtom is not provided by the pairview function (i.e. value set to '_NULL'): skip the insPair
		if($srcAtom == '_NULL' or $tgtAtom == '_NULL') return 'InsPair ignored because src and/or tgt atom is _NULL';
		
		// if srcAtom is specified as _NEW, a new atom of srcConcept is created
	    if($srcAtom == "_NEW"){
			$srcAtom = $database->addAtomToConcept(Concept::createNewAtom($srcConcept), $srcConcept);
		}elseif(!$database->atomExists($srcAtom, $srcConcept)){
			$database->addAtomToConcept($srcAtom, $srcConcept);
		}
		
		// if tgtAtom is specified as _NEW, a new atom of tgtConcept is created
		if($tgtAtom == "_NEW"){
			$tgtAtom = $database->addAtomToConcept(Concept::createNewAtom($tgtConcept), $tgtConcept);
		}elseif(!$database->atomExists($tgtAtom, $tgtConcept)){
			$database->addAtomToConcept($tgtAtom, $tgtConcept);
		}
		
		$srcAtoms = explode('_AND', $srcAtom);
		$tgtAtoms = explode('_AND', $tgtAtom);
		foreach($srcAtoms as $a){
			foreach($tgtAtoms as $b){
				$database->editUpdate($relation, false, $a, $srcConcept, $b, $tgtConcept, null, 'ExecEngine');
			}
		}
		
		return 'Tuple ('.$srcAtom.' - '.$tgtAtom.') inserted into '.$relationName.'['.$srcConcept.'*'.$tgtConcept.']';
	}catch(Exception $e){
		Notifications::addError('InsPair: ' . $e->getMessage());
	}
}

/*
	Example of a rule that automatically deletes pairs from a relation:
	ROLE ExecEngine MAINTAINS "Remove Customers"
	RULE "Remove Customers": customerOf[Person*Company] |- customerOrder[Person*Order];companyOrder[Company*Order]~
	MEANING "Customers of a company for which no orders exist (any more), are no longer considered customers"
	VIOLATION (TXT "DelPair;customerOf;Person;", SRC I, TXT";Company;", TGT I)
*/
// Use: VIOLATION (TXT "DelPair;<rel>;<srcConcept>;<srcAtom>;<tgtConcept>;<tgtAtom>")
function DelPair($relationName,$srcConcept,$srcAtom,$tgtConcept,$tgtAtom){
	if(func_num_args() != 5) throw new Exception("Wrong number of arguments supplied for function DelPair(): ".func_num_args()." arguments", 500);
	Notifications::addLog("DelPair($relationName,$srcConcept,$srcAtom,$tgtConcept,$tgtAtom)", 'ExecEngine');
	try{
		$database = Database::singleton();
		
		// Check if relation signature exists: $relationName[$srcConcept*$tgtConcept]
		$relation = Relation::isCombination($relationName, $srcConcept, $tgtConcept);
		
		$srcAtoms = explode('_AND', $srcAtom);
		$tgtAtoms = explode('_AND', $tgtAtom);
		if(count($srcAtoms) > 1) throw new Exception('DelPair function call has more than one src atom', 501); // 501: Not implemented
		if(count($tgtAtoms) > 1) throw new Exception('DelPair function call has more than one tgt atom', 501); // 501: Not implemented
		
		foreach($srcAtoms as $a){
			foreach($tgtAtoms as $b){
				$database->editDelete($relation, false, $a, $srcConcept, $b, $tgtConcept, 'ExecEngine');
			}
		}
		
		return 'Tuple ('.$srcAtom.' - '.$tgtAtom.') deleted from '.$relationName.'['.$srcConcept.'*'.$tgtConcept.']';
	}catch(Exception $e){
		Notifications::addError('DelPair: ' . $e->getMessage());
	}
}

/* The function 'NewStruct' creates a new atom in some concept and uses this
   atom to create links (in relations in which the concept is SRC or TGT).

   Example:
   
   r :: ConceptA * ConceptB
   r1 :: ConceptA * ConceptC [INJ] -- multiplicity must be there (I think...)
   r2 :: ConceptC * ConceptB [UNI] -- multiplicity must be there (I think...)
   
   RULE "equivalence": r = r1;r2 -- this rule is to be maintained automatically
   
   ROLE ExecEngine MAINTAINS "insEquivalence" -- Creation of the atom
   RULE "insEquivalence": r |- r1;r2
   VIOLATION (TXT "NewStruct;ConceptC[;AtomC]" -- AtomC is optional. If not provided then create new, else used specified Atom
             ,TXT ";r1;ConceptA;", SRC I, TXT";ConceptC;_NEW"  -- Always use _NEW as ConceptC atom
             ,TXT ";r2;ConceptC;_NEW;ConceptB;atomB;", TGT I   -- Always use _NEW as ConceptC atom
              )

*/
function NewStruct(){ // arglist: ($ConceptC[,$newAtom][,$relation,$srcConcept,$srcAtom,$tgtConcept,$tgtAtom]+)
	Notifications::addLog("Newstruct", 'ExecEngine');
	try{
		$database = Database::singleton();
		
		// We start with parsing the first one or two arguments
		$ConceptC = func_get_arg(0);              // Name of concept for which atom is to be created
		$AtomC = Concept::createNewAtom($ConceptC);   // Default marker for atom-to-be-created.
		
		if (func_num_args() % 5 == 2){            // Check if name of new atom is explicitly specified
			$AtomC = func_get_arg(1);              // If so, we'll be using this to create the new atom
		}elseif(func_num_args() % 5 != 1){       // check for valid number of arguments
			throw new Exception("Wrong number of arguments supplied for function Newstruct(): ".func_num_args()." arguments", 500);
		}
		
		// Then, we create a new atom of type $ConceptC
		$database->addAtomToConcept($AtomC, $ConceptC);     // insert new atom in database
	
		// Next, for every relation that follows in the argument list, we create a link
		for ($i = func_num_args() % 5; $i < func_num_args(); $i = $i+5){
			
			$relation   = func_get_arg($i);
			$srcConcept = func_get_arg($i+1);
			$srcAtom    = func_get_arg($i+2);
			$tgtConcept = func_get_arg($i+3);
			$tgtAtom    = func_get_arg($i+4);
			
			if($srcAtom == "NULL" or $tgtAtom == "NULL") throw new Exception("Use of keyword NULL is deprecated, use '_NEW'", 500);
			
			// if either srcAtom or tgtAtom is not provided by the pairview function (i.e. value set to '_NULL'): skip the insPair
			if($srcAtom == '_NULL' or $tgtAtom == '_NULL') continue; 
			
			// populate relation r1, first checking for allowed syntax:		
			if (!($srcAtom == '_NEW' or $tgtAtom == '_NEW')){ // Note: when populating a [PROP] relation, both atoms can be new
				// NewStruct: relation $relation requires that atom $srcAtom or $tgtAtom must be _NEW
				throw new Exception("NewStruct: relation $relation requires that atom $srcAtom or $tgtAtom must be _NEW", 500);
			}
		
			if (!($srcConcept == $ConceptC or $tgtConcept == $ConceptC)){
				// NewStruct: relation $relation requires that concept $srcConcept or $tgtConcept must be $ConceptC
				throw new Exception("NewStruct: relation $relation requires that concept $srcConcept or $tgtConcept must be $ConceptC", 500);
			}
		
			if ($srcConcept == $ConceptC){
				if ($srcAtom == '_NEW'){
					$srcAtom = $AtomC;
	/* The following code prevents ASY (and other homogeneous) relations to be populated, and is therefore declared obsolete.
				}else{ // While it strictly not necessary to err here, for most cases this helps to find errors in the ADL script
					// NewStruct: $srcAtom must be _NEW when $ConceptC is the concept (in relation $relation)
					throw new Exception("NewStruct: $srcAtom must be _NEW when $ConceptC is the concept (in relation $relation)", 500); */
				}
			}
		
			if ($tgtConcept == $ConceptC){  
				if ($tgtAtom == '_NEW'){  
					$tgtAtom = $AtomC;
	/* The following code prevents ASY (and other homogeneous) relations to be populated, and is therefore declared obsolete.
				}else{ // While it strictly not necessary to err here, for most cases this helps to find errors in the ADL script
					// NewStruct: $tgtAtom must be _NEW when $ConceptC is the concept (in relation $relation)
					throw new Exception("NewStruct: $tgtAtom must be _NEW when $ConceptC is the concept (in relation $relation)", 500); */
				}
			}
			
			// Any logging is done by InsPair:
			InsPair($relation,$srcConcept,$srcAtom,$tgtConcept,$tgtAtom);
		}
		return "New structure '". $AtomC . "' created";
	
	}catch(Exception $e){
		Notifications::addError('NewStruct: ' . $e->getMessage());
	}
}

// Use: VIOLATION (TXT "InsAtom;<concept>") -- this may not be of any use in Ampersand, though.
function InsAtom($concept){
	if(func_num_args() != 1) throw new Exception("Wrong number of arguments supplied for function InsAtom(): ".func_num_args()." arguments", 500);
	Notifications::addLog("InsAtom($concept)", 'ExecEngine');
	try{
		$database = Database::singleton();
		
		$atom = Concept::createNewAtom($concept);
		$database->addAtomToConcept($atom, $concept); // insert new atom in database
		
		return "Atom '".$atom."' added to concept '". $concept . "'";
		
	}catch(Exception $e){
		Notifications::addError('InsAtom: ' . $e->getMessage());
	}
	
}

/* 
	ROLE ExecEngine MAINTAINS "delEquivalence" -- Deletion of the atom
	RULE "delEquivalence": I[ConceptC] |- r1~;r;r2~
	VIOLATION (TXT "DelAtom;ConceptC;" SRC I) -- all links in other relations in which the atom occurs are deleted as well.
*/
// Use: VIOLATION (TXT "DelAtom;<concept>;<atom>")
function DelAtom($concept, $atom){
	if(func_num_args() != 2) throw new Exception("Wrong number of arguments supplied for function DelAtom(): ".func_num_args()." arguments", 500);
	Notifications::addLog("DelAtom($concept, $atom)", 'ExecEngine');
	try{
		$database = Database::singleton();
		
		$database->deleteAtom($atom, $concept); // delete atom + all relations with other atoms
		return "Atom '".$atom."' deleted from concept '". $concept . "'";
	
	}catch(Exception $e){
		Notifications::addError('DelAtom: ' . $e->getMessage());
	}
	
}

/*
 ROLE ExecEngine MAINTAINS "SetConcept" -- Adding an atom[ConceptA] as member to ConceptB set. This can only be done when ConceptA and ConceptB are in the same classification tree.
 RULE "SetConcept": I[ConceptA] |- expr
 VIOLATION (TXT "SetConcept;ConceptA;ConceptB;" SRC I)
 */
// Use: VIOLATION (TXT "SetConcept;<ConceptA>;<ConceptB>;<atom>")
function SetConcept($conceptA, $conceptB, $atom){
	if(func_num_args() != 3) throw new Exception("Wrong number of arguments supplied for function SetConcept(): ".func_num_args()." arguments", 500);
	Notifications::addLog("SetConcept($conceptA, $conceptB, $atom)", 'ExecEngine');
	try{
		$database = Database::singleton();
		
		$database->atomSetConcept($conceptA, $atom, $conceptB);
		return "Atom '$atom' added as member to concept '$conceptB'";
	
	}catch(Exception $e){
		Notifications::addError('SetConcept: ' . $e->getMessage());
	}
}

/*
 ROLE ExecEngine MAINTAINS "ClearConcept" -- Removing an atom as member from a Concept set. This can only be done when the concept is a specialization of another concept.
 RULE "ClearConcept": I[Concept] |- expr
 VIOLATION (TXT "ClearConcept;Concept;" SRC I)
 */
// Use: VIOLATION (TXT "ClearConcept;<Concept>;<atom>")
function ClearConcept($concept, $atom){
	if(func_num_args() != 2) throw new Exception("Wrong number of arguments supplied for function ClearConcept(): ".func_num_args()." arguments", 500);
	Notifications::addLog("ClearConcept($concept, $atom)", 'ExecEngine');
	try{
		$database = Database::singleton();

		$database->atomClearConcept($concept, $atom);
		return "Atom '$atom' removed as member from concept '$concept'";

	}catch(Exception $e){
		Notifications::addError('ClearConcept: ' . $e->getMessage());
	}
}
?>