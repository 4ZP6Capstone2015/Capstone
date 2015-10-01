#! runhaskell

import System.Directory
import qualified System.Process as Proc 
import System.FilePath
import Control.Monad 
import Data.Char (toLower)
import Data.List (intercalate)
import Control.Exception hiding (assert)


--------------------------
-------------------------- 

targets = 
  [ "ProblemStatement/ProblemStatement.tex"
  , "SoftwareRequirementSpecification/SRS.tex"
  ]

--------------------------
--------------------------


callCommand cmd = putStrLn ("Running: " ++ cmd) >> Proc.callCommand cmd 

callProcess cmd args = 
  putStrLn (intercalate " " $ "Running :" : cmd : args) >> 
  Proc.callProcess cmd args 

assert :: Bool -> String -> a -> a 
assert True = const id 
assert False = const . error 

pdflatex :: Bool -> FilePath -> IO ()
pdflatex isD rawPath = do 
  assert  (isValid rawPath) "Invalid file path" $ 
   assert (ext == ".tex") "Not a tex file" $ 
   return () 
          
  bracket getCurrentDirectory setCurrentDirectory $ const $ 
   setCurrentDirectory dir >>
   replicateM_ (fromEnum isD + 1) texCommand 

    where 
      (dir, fileName) = splitFileName rawPath 
      (jobName, ext) = splitExtension fileName 
      jobName' = jobName ++ if isD then "_draft" else ""
      texInput = concat 
        [ "\\providecommand{\\draftmode}{" 
        , map toLower (show isD)
        , "}\\input{", fileName, "}"]
      texCommand = callProcess "pdflatex" 
        [ "-interaction=nonstopmode", "-jobname="++jobName', texInput ] 

main = sequence_ [ pdflatex isD target | isD <- [False, True], target <- targets] 
