#! runhaskell

{-# LANGUAGE RecordWildCards, LambdaCase #-}

import System.Directory
import System.Process
import System.Exit (ExitCode(..))
import System.FilePath
import Control.Monad 
import Data.Char (toLower)
import Data.List (intercalate)
import Control.Exception hiding (assert)
import Text.Read (readMaybe)
import Text.Printf (printf)


--------------------------
-------------------------- 

targets = 
  [  defOpts "ProblemStatement/ProblemStatement.tex"
  , (defOpts "SoftwareRequirementSpecification/SRS.tex") { hasBib = True }
  ]

--------------------------
--------------------------

standardCmd :: CreateProcess -> IO () 
standardCmd pr = do 
  let cmdString = 
        case cmdspec pr of 
          ShellCommand c -> c
          RawCommand x s -> showCommandForUser x s
  (cd, out, err) <- readCreateProcessWithExitCode pr ""
  case cd of 
    ExitSuccess -> putStrLn $ "Command succeeded: " ++ cmdString
    x -> putStrLn $ unlines 
           ["An error occured while running: " 
            ++ cmdString ++ "  -  " ++ show x
           , out
           , err
           ]

assert :: Bool -> String -> a -> a 
assert True = const id 
assert False = const . error 

data PdfLatexOpts = Opts 
  { isDraft :: Bool
  , hasBib :: Bool
  , invocations :: Int 
  , rawPath :: FilePath 
  } 

optsJobName :: PdfLatexOpts -> String 
optsJobName Opts{..} = jobName' ++ if isDraft then "_draft" else "" where 
  (_, fileName) = splitFileName rawPath 
  (jobName', _) = splitExtension fileName 

defOpts :: FilePath -> PdfLatexOpts
defOpts rawPath = Opts { isDraft = False, hasBib = False, invocations = 2, ..}

pdflatex :: PdfLatexOpts -> IO ()
pdflatex Opts{isDraft = isD, ..} = do 
  assert  (isValid rawPath) "Invalid file path" $ 
   assert (ext == ".tex") "Not a tex file" $ 
   return () 
          
  putStrLn $ "Job starting... " ++ jobName 
  bracket getCurrentDirectory setCurrentDirectory $ const $ do 
   setCurrentDirectory dir 
   if hasBib then texCommand >> bibCommand else return ()
   texCommand >> texCommand 
  putStrLn $ "Job finished.\n"
   
    where 
      (dir, fileName) = splitFileName rawPath 
      (jobName', ext) = splitExtension fileName 
      jobName = jobName' ++ if isD then "_draft" else ""

      texInput = concat 
        [ "\\providecommand{\\draftmode}{" 
        , map toLower (show isD)
        , "}\\input{", fileName, "}"]
      texCommand = standardCmd $ proc "pdflatex" 
        [ "-interaction=nonstopmode", "-jobname="++jobName, texInput ] 
      bibCommand = standardCmd $ proc "bibtex" [ jobName ] 

runOneOf :: [(IO a,String)] -> IO a 
runOneOf opts = do 
  let len = length opts 
  mapM_ (\(n,s) -> printf "%3d: %s\n" n s) (zip [(0 :: Int)..] (map snd opts)) 
  n <- let f = putStrLn ("Pick a number between 0 and " ++ show (len-1)) >> 
               getLine >>= 
               maybe f (\x -> if x >= 0 && x < len then return x else f) . readMaybe
           f :: IO Int in f 
  fst (opts !! n)
  
main = runOneOf [ (pdflatex r, optsJobName r) 
                | isD <- [False, True]
                , target <- targets
                , let r = target { isDraft = isD } ] 
