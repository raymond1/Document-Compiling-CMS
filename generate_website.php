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

//A .cdf file is a "compiled documents file" which specifies
//Takes in a .cdf file and generates an output file.
//Although the .cdf extension is not required, it is customary.
function process_compiled_documents_file($cdf_filename){
  //This parsing is really fragile.
  if (!file_exists($cdf_filename)){
    echo ("CDF file not found:$cdf_filename\n");
    return false;
  }

  //Each line of a cdf file is structured as follows:
  //[.template file (required)] [.content file 1 (optional)] [.content file 2 (optional)] ... [output location (required)]
  //The first token is the path to a .template file
  //Every subsequent token except the last is a path to a .content file.
  //The last file is the path and filename that will be the output location of the file.
  //generated by replacing each MAGIC string in the .template file with the contents of the .content file.
  //All paths are relative to the directory where the script was run
  $cdf_contents = @file_get_contents($cdf_filename);
  $lines = explode("\n", $cdf_contents);
  foreach($lines as $line){
    if (trim($line) == '') continue;//ignore blank lines

    $tokens = explode(" ", $line);
    $number_of_tokens = count($tokens);
    $number_of_content_tokens = $number_of_tokens - 2;
  
    $template_file = $tokens[0];
    if (file_exists($template_file)){
      $template_contents = file_get_contents($template_file);
      //Each template file will contain one or more MAGIC strings
      //The .content files will populate the contents of the .template files, each in turn.
      for ($i = 1; $i < $number_of_content_tokens + 1; $i++){
        $content_file = $tokens[$i];
        if (file_exists($content_file)){
          $content_fragment = file_get_contents($content_file);
          $template_contents = replace_one_magic($template_contents, $content_fragment);
        }else{
          echo "Unable to open content file: $content_file.\n";
        }
      }
    
      //Save the contents of the file to the indicated output
      $last_token_index = $number_of_tokens - 1;
      $output_file = $tokens[$last_token_index];
      $directory = pathinfo($output_file)['dirname'];
      if (!is_dir($directory)){
        mkdir($directory,0777,true);
      }
      file_put_contents($output_file,$template_contents);
    }else{
      echo "Inside file: $cdf_filename: unable to open template file $template_file.\n";
    }
  }

  return true;
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
        $cdf_filename = $tokens[1];
        echo("$cdf_filename\n");
        if (file_exists($cdf_filename)){
          process_compiled_documents_file($cdf_filename);
        }else{
          echo "Unable to open cdf file: $cdf_filename.\n";
        }
      }
      catch(Exception $e){
        echo ("Error processing compiled documents file.");
      }
    }else if (is_command_directive($line)){
      $command = substr($line,strlen("command "));
      echo ("Running command: $command\n");
      exec($command);
    }
    else{
      echo ("Syntax error in script.txt around line $i at the part that says '$line'\n");
    }
  }
}

follow_script_file();