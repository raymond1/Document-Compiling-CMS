<?php


/*
Debugging function
*/
function d_log($s){
  $file = fopen('log.log', 'a+');
  fwrite($file, $s);
  fclose($file);
}

$GLOBALS['script file'] = 'script.txt';
if ($argc == 2) $GLOBALS['script file'] = $argv[1];

$GLOBALS['output directory'] = 'output';
$GLOBALS['source directory'] = 'src';
$GLOBALS['directory structure file'] = $GLOBALS['source directory'] . "/" . 'directories.txt';

function is_execute_directive($s){
  $tokens = explode(" ", $s);

  if (count($tokens) != 2) return false;
  if ($tokens[0]!='execute') return false;

  return true;
}

//Executes the commands inside $filename, which is the path to the command file.
function processCommandFile($filename){
  $lines = file($filename, FILE_IGNORE_NEW_LINES);

  foreach ($lines as $line) {
    if (trim($line) != ''){
      exec($line);
    }
  }
}

//Looks for the string 'MAGIC' in $string and replaces the first occurrence
function replace_one_magic($string, $replacement){
	$first_occurrence_of_MAGIC = strpos($string, 'MAGIC');
	$left_of_MAGIC = substr($string,0, $first_occurrence_of_MAGIC);
	$right_of_MAGIC = substr($string,$first_occurrence_of_MAGIC + 5);
	return $left_of_MAGIC . $replacement . $right_of_MAGIC;
}

//Syntax of copy.txt file is:
//source destination

//source can be a file or a directory
//destination refers to a directory where source will be copied to
function copy_files($copy_instructions_filename){
  if (file_exists($copy_instructions_filename)){
    echo ("Copying files.\n");
    $lines = explode(PHP_EOL, file_get_contents($copy_instructions_filename));
    foreach ($lines as $line){
      if (trim($line)=='') continue;

      $parts = explode(" ", $line);
      $source = $parts[0];
      $destination = $parts[1];
  
      //Otherwise, if it is a directory, copy it if it is new
      //Copy the contents in it if the directory is not new
      //Copying is done recursively.
      if (!file_exists($destination)){
        mkdir($destination, 0777, true);
      }
      exec("cp -R $source $destination");
    }  
  }else{
    echo "Copyscript not found: $copy_instructions_filename\n";
  }
}

//Opens up the directories.txt file and generates the directories listed in that file.
//The format of the directories.txt file is as follows:
//Every line contains the name of a directory or a / separated list of directories.
//For example:
//
//cat
//bat
//cat/dog
//cat/dog/fish
//
//For each line in the file that contains a list of directories, those directories will be created if they did not previously exist.
//If the directories already exist, nothing is done.
function process_directories(){
  $directories_file = "directories.txt";
  if (file_exists($directories_file)){
    $contents = file_get_contents($directories_file);
    $lines = explode("\n", $contents);
    foreach ($lines as $line){
      if (!empty($line)){
        @mkdir($line,0777,true);
      }
    }
  }else{
    echo("Missing directories.txt file.\n");
  }
}

//Given a string $s, determine if $s is a compile directive
//A compile directive consists of the word "compile" followed by the name of a file.
//The filename is assumed to not contain any spaces
function is_compile_directive($s){
  //Syntax:
  //compile file.compiled_documents

  //Minimum acceptable string is compile followed by a space and a single letter
  if (strlen($s) < strlen("compile") + 2){
    return false;
  }

  if (!(substr($s,0,strlen("compile")) == "compile")){
    return false;
  }

  //Check if there is a parameter after the compile keyword
  $right_string = substr($s,strlen("compile"));
  if (trim($right_string) == ""){
    return false;
  }
  
  return true;
}


//Takes in a string $s and finds the first occurrence of the string "<%" that is followed by 0 or more letters and then the string "%>".
//If both the opening and closing tags exist, then 
//This function will return an array with two values[START,LENGTH].
//START will be equal to the index of the first character after "<%" inside $s.
//LENGTH will be equal to the number of characters in between the first character after "<%" and the first character before "%>".
//If opening does not exist, then -1 is returned.
//If opening tag exists but closing tag doesn't exist, then -1 is returned.
function getFirstDirectiveLocation($s){
  $returnObject = new stdClass;
  $returnObject->start = -1;
  $returnObject->length = -1;

  $openingTagStart = strpos($s,"<%");
  if ($openingTagStart === false){
    return $returnObject;
  }
  $closingTagStart = strpos($s, "%>", $openingTagStart + 2);
  if ($closingTagStart === false){
    return $returnObject;
  }

  //Start of area between "<%" and "%>"
  $returnObject->start = $openingTagStart + 2;
  $returnObject->length = $closingTagStart - ($openingTagStart + 2);
  return $returnObject;
}

//First non-empty line gives the type of the command
//directives are: copy and print
//Takes in a directive string and returns the processed result
//For example:
//copy
// src/nodes.js 
// src/parser_fragment.js 
// src/tree.js 
// src/strings.js 
// src/tree_viewer.js 
// src/module_ending.js 
//
// Will produce the string created by concatenating all the listed files.
// The syntax for the copy directive is as follows:
// "copy" followed by a carriage return is the first line.
// The first line is followed by n or more non-empty lines starting with a single space. Each non-empty line that follows the first
// lists a file whose contents will be read and added to the output string.
// The end result is the output string after it contains all the contents from the lines indicated by the copy directive.
//
//print
// string1
// string2
//Adds the strings from each line to the output string. One purpose of this command is so that the string "<%" can be printed.
function processTemplateDirective($s){
  // echo "Inside processTemplateDirective s is:|$s|";
  $parts = explode("\n", trim($s));
  $directive = $parts[0];
  $outputString = '';
  switch ($directive){
    case 'transcribe':
      for ($i = 1; $i < count($parts); $i++){
        $filename = trim($parts[$i]);
        if (file_exists($filename)){
          $outputString .= file_get_contents($filename);
        }
        else{
          echo "Could not find $filename while processing the ". $directive . " directive.\n";
          exit;
        }
      }

      break;
    case 'print':
      for ($i = 1; $i < count($parts); $i++){
        $outputString .= substr($parts[$i], 1);
      }
      break;
    default:
      echo "Error. Unknown directive in template file:" . $parts[0] . ".";
      exit;
      break;
  }
  return $outputString;
}

//Takes in a template file, processes its directives, and produces an output file.
function processTemplate($template, $outputFilename){
  //This parsing is really fragile.
  if (!file_exists($template)){
    echo ("Template not found:$template\n");
  }

  //Everything not enclosed in <% %> will be printed
  //Within the <% %> tags, directives can be given. Currently, there are only three directives, "join", "copy", "print"
  //Syntax:
  //join(carriage return)
  //(spaces and tabs)filepath 1(carriage return)
  //(spaces and tabs>filepath 2(carriage return)
  //(carriage return)
  //

  //copy(space)<filepath>(end of line)

  //All paths are relative to the directory where the script was run
  $templateContents = @file_get_contents($template);

  //Does <% exist?
  //If Yes, does %> exist after it?
  //If both are yes, then there is a directive. Process it.
  $caret = 0; //caret refers to the position in the template file that has been processed
  $outputString = '';
  while($caret < strlen($templateContents)){
    $templateStringProcessed = '';

    $directiveInformation = getFirstDirectiveLocation(substr($templateContents,$caret));
    if ($directiveInformation->start == -1){
      //If no more directives remain, then simply print the contents from $caret to the end of the template
      $templateStringProcessed = substr($templateContents, $caret);
      $outputString = $outputString . $templateStringProcessed;
    }else{
      //Read until directive start
      $templateStringProcessed .= substr($templateContents,$caret, $directiveInformation->start);
      $outputString .= substr($templateStringProcessed,0, -2);

      $stuffBetweenDirectives = substr($templateContents,$caret + $directiveInformation->start, $directiveInformation->length);
      $templateStringProcessed .= $stuffBetweenDirectives;
      $outputString .= processTemplateDirective($stuffBetweenDirectives);

      //Skip the end tag
      $templateStringProcessed .= substr($templateContents, $caret + $directiveInformation->start + $directiveInformation->length, 2);
    }

    $caret += strlen($templateStringProcessed);
  }

  $directory = dirname($outputFilename);

  if (!file_exists($directory)) mkdir($directory);
  $outputFile = fopen($outputFilename, "w+");
  fwrite($outputFile, $outputString);
  fclose($outputFile);
}

//copyscript <filename>
function is_copyscript_directive($s){
  $tokens = explode(" ", $s);

  if (count($tokens) != 2) return false;
  if ($tokens[0]!='copyscript') return false;
  
  return true;
}

function is_command_directive($s){
  if (substr($s,0,strlen("command ")) == "command "){
    return true;
  }

  return false;
}

//parses the script.txt file and executes the commands within it
function follow_script_file(){
  $script_file_contents = @file_get_contents($GLOBALS['script file']);
  if ($script_file_contents === false){
    echo("Missing script.txt file.\n");
    exit;
  }

  //From here on, assume file is present
  $lines = explode("\n", $script_file_contents);
  for($i = 0; $i < count($lines); $i++){
    $line = $lines[$i];

    if (trim($line) == ''){
      continue; //ignore whitespace-only lines
    }
    else if ($line == 'generate directories'){
      echo ("Generating directories.\n");
      process_directories();
    }else if (is_copyscript_directive($line)){
      echo ("Processing copyscript directive\n");
      $tokens = explode(" ", $line);
      copy_files($tokens[1]);
    }else if (is_compile_directive($line)){
      echo ("Compiling documents:");
      try{
        $tokens = explode(" ", $line);
        $template = $tokens[1];
        $outputFilename = $tokens[2];
        echo("template:$template\noutput file:$outputFilename\n");
        if (file_exists($template)){
          processTemplate($template, $outputFilename);
        }else{
          echo "Unable to open template: $template.\n";
        }
      }
      catch(Exception $e){
        echo ("Error processing compiled documents file.");
      }
    }else if (is_execute_directive($line)){
      echo ("Executing scripts\n");
      try {
        $tokens = explode(" ", $line);
        $command_file = $tokens[1];

        echo("Running commands in $command_file.");
        if (file_exists($command_file)){
          processCommandFile($command_file);
        }else{
          // echo "Unable to open command file: $command_file.\n";
        }
      }catch(Exception $e){
        echo "Error processing command file";
      }
    }
    else{
      echo ("Command not recognized. Error in script.txt around line $i at the part that says '$line'\n");
    }
  }
}

follow_script_file();