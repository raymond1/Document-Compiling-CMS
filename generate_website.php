<?php

/*
Debugging function
*/
function d_log($s){
  $file = fopen('log.log', 'a+');
  fwrite($file, $s);
  fclose($file);
}

//These are essentially hard-coded constants
$GLOBALS['source directory'] = 'src';
$GLOBALS['output directory'] = 'output';
$GLOBALS['copy instructions file'] = $GLOBALS['source directory'] . "/" . 'copy.txt';
$GLOBALS['directory structure file'] = $GLOBALS['source directory'] . "/" . 'directories.txt';

function generate_alt_string($english_info, $chinese_info){
  $returnString = $english_info . " " . $chinese_info['traditional'];
  return $returnString;
}

function generate_chinese_string($chinese_info){
  $returnString = "Traditional: " . $chinese_info['traditional'] . "<br>";
  $returnString .= "Simplified: " . $chinese_info['simplified'];
  return $returnString;
}

//$images_info is of the form
//array(
//  array('path' => 'image/path'),
//  array('path' => 'image/path2'),
//  ...
//)
//
//$english_info is of the form
//array(
//  
//)
//
function generate_images_string($images_info, $english_info, $chinese_info){
  $returnString = '';
  foreach ($images_info as $image_info){
    $path = $image_info['path'];
    $alt = generate_alt_string($english_info,$chinese_info);
    $returnString .= "<img src='$path' alt='$alt' class='example'>";
  }
  return $returnString;
}

// array(
//  'images' => array('path' => '/image.jpg', 'alt' => 'alt'),
//  'English' => 'English text'
//  'Chinese' => 'Chinese text'
//  'Phonetic' => 'Phonetic description'
//)
function generate_table_row($array){
  //print_r($array);
  //exit;
  $images_info = $array['images'];
  $images_string = generate_images_string($images_info, $array['english'], $array['chinese']);
  $chinese_string = generate_chinese_string($array['chinese']);
  return "<tr><td class='image'>$images_string</td><td>{$array['phonetic']}</td><td>{$array['english']}</td><td lang='zh_HK'>$chinese_string</td></tr>";
}


//Array is of the form
//array([0] => array(
//  'images' => array('path' => path/image.jpg, 'alt' => 'alt text'),
//  'English' => 'English text'
//  'Chinese' => array('traditional' =>..., 'simplified'=>...)
//  'Phonetic' => 'Phonetic description'
//))
function generate_table($array){
  $returnString = "<table class='vocabulary_list'>";
  $returnString .= "<tr><th>Image</th><th>Phonetics</th><th>English</th><th>Chinese</th></tr>";
  foreach ($array as $item){
    $returnString .= generate_table_row($item);
  }
  $returnString .= "</table>";
  return $returnString;
}

function generate_menu(){
  return "
    <ul>
      <li><a href='pronunciation.html'>Pronuncation</a>
      <li><a href='pronouns.html'>Pronouns(You, me, he, she)</a>
      <li><a href='greetings_and_partings.html'>Greetings and Partings / 招呼</a>
      <li><a href='basic_etiquette.html'>Basic Etiquette / 禮貌</a>
      <li><a href='simple_sentences.html'>Simple Sentences / 簡單句嘅例子</a>
      <li><a href='list_of_basic_nouns.html'>List of basic nouns / 普通名詞</a>
      <li><a href='washroom_words.html'>Washroom words / 
      廁所嘅字</a>
      <li><a href='live-labels/body/html/index.html'>Body parts / 身體</a>
      <li><a href='live-labels/face/html/index.html'>Face / 面</a>
      <li><a href='new-year_新年.html'>New year / 新年</a>
    </ul>
  ";
}

//Generate index.html
function generate_index_html(){
  $title = 'Cantonese Central';

  //$vocabulary_list_table = generate_table();
  $contents = generate_menu();
  
  $output = template($contents);

	$file = fopen($GLOBALS['output directory'] . '/index.html', 'w+');
	fwrite($file, $output);
	fclose($file);

  return $output;
?>

<?php

}

//Looks for the string 'MAGIC' in $string and replaces the first occurrence
function replace_one_magic($string, $replacement){
echo("Inside replace_one_magic");
echo("string:$string|||\n");
	$first_occurrence_of_MAGIC = strpos($string, 'MAGIC');
echo("first_occurrence_of_MAGIC:$first_occurrence_of_MAGIC\n");
	$left_of_MAGIC = substr($string,0, $first_occurrence_of_MAGIC);
	$right_of_MAGIC = substr($string,$first_occurrence_of_MAGIC + 5);
	return $left_of_MAGIC . $replacement . $right_of_MAGIC;
}

//takes in two relative paths for directories without end slashes
//Copies all files in source into destination
function copy_directory_contents($source_path, $destination_path){
	$files = scandir($source_path);
	foreach ($files as $file){
		if ($file == '.'||$file == '..'){
			continue;
		}
		copy("$source_path/$file", "$destination_path/$file");
  }
}

//Opens up a .content file, puts it in the template, and then saves the file in output_file
//Or, if you pass in a string, puts the contents of the string into the output file
function generate_templated_file($content_string_or_filename, $output_file){
  $contents = '';
  if (substr($content_string_or_filename, -strlen('.content')) == '.content'){ //assume content filename was passed in
    $contents = file_get_contents($content_string_or_filename);
  }else{
    $contents = $content_string_or_filename;
  }
	$file = fopen($output_file, 'w+');
	fwrite($file, template($contents));
	fclose($file);
}

function generate_basic_etiquette_html(){
}

function generate_pronouns_html(){
}

function generate_simple_sentences_html(){
}

function generate_list_of_basic_nouns_html(){
}

function generate_washroom_html(){
  $washroom_words_array = array(
    array(
      'images' => array(
        ['path'=> 'bathroom/facecloth.jpg']
      ),
      'english' => 'facecloth',
      'chinese' => ['traditional' => '洗面巾', 'simplified' => '洗面巾'],
      'phonetic' => 'sai meen gun'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/toothbrush.jpg']
      ),
      'english' => 'toothbrush',
      'chinese' => ['traditional' => '牙刷', 'simplified' => '牙刷'],
      'phonetic' => 'nga chaat'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/toothpaste.jpg']
      ),
      'english' => 'toothpaste',
      'chinese' => ['traditional' => '牙膏', 'simplified' => '牙膏'],
      'phonetic' => 'nga go'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/cup.jpg']
      ),
      'english' => 'cup',
      'chinese' => ['traditional' => '杯', 'simplified' => '杯'],
      'phonetic' => 'booi'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/mirror.jpg']
      ),
      'english' => 'mirror',
      'chinese' => ['traditional' => '鏡', 'simplified' => '镜'],
      'phonetic' => 'geng'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/cabinet.jpg']
      ),
      'english' => 'cabinet',
      'chinese' => ['traditional' => '櫃桶', 'simplified' => '柜桶'],
      'phonetic' => 'gwaii tong'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/toilet.jpg']
      ),
      'english' => 'toilet',
      'chinese' => ['traditional' => '廁所', 'simplified' => '厕所'],
      'phonetic' => 'tchi soh'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/toilet handle.jpg']
      ),
      'english' => 'toilet handle',
      'chinese' => ['traditional' => '廁所製', 'simplified' => '厕所製'],
      'phonetic' => 'tchi soh jai'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/toilet seat.jpg']
      ),
      'english' => 'toilet seat',
      'chinese' => ['traditional' => '廁所板', 'simplified' => '厕所板'],
      'phonetic' => 'tchi so ban'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/light.jpg']
      ),
      'english' => 'light',
      'chinese' => ['traditional' => '燈', 'simplified' => '灯'],
      'phonetic' => 'dung'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/fan.jpg']
      ),
      'english' => 'fan',
      'chinese' => ['traditional' => '風扇', 'simplified' => '风扇'],
      'phonetic' => 'fung seen'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/towel.jpg']
      ),
      'english' => 'towel',
      'chinese' => ['traditional' => '毛巾', 'simplified' => '毛巾'],
      'phonetic' => 'mo gun'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/shampoo.jpg']
      ),
      'english' => 'shampoo',
      'chinese' => ['traditional' => '洗頭水', 'simplified' => '洗头水'],
      'phonetic' => 'sai tau seui'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/hair conditioner.jpg']
      ),
      'english' => 'hair conditioner',
      'chinese' => ['traditional' => '護髮素', 'simplified' => '护发素'],
      'phonetic' => 'wu fat so'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/moisturizer.jpg']
      ),
      'english' => 'moisturizer',
      'chinese' => ['traditional' => '潤膚膏', 'simplified' => '润肤膏'],
      'phonetic' => 'yeun fu go'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/scouring pad.jpg']
      ),
      'english' => 'scouring pad',
      'chinese' => ['traditional' => '百潔布', 'simplified' => '百洁布'],
      'phonetic' => 'bak geet bo'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/light switch.jpg']
      ),
      'english' => 'light switch',
      'chinese' => ['traditional' => '燈製', 'simplified' => '灯製'],
      'phonetic' => 'dung zai'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/electrical socket.jpg']
      ),
      'english' => 'electrical socket',
      'chinese' => ['traditional' => '插蘇', 'simplified' => '插苏'],
      'phonetic' => 'tchap so'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/toilet paper.jpg']
      ),
      'english' => 'toilet paper',
      'chinese' => ['traditional' => '廁紙', 'simplified' => '厕纸'],
      'phonetic' => 'tchee jee'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/toilet paper roll.jpg']
      ),
      'english' => 'toilet paper roll',
      'chinese' => ['traditional' => '廁紙券', 'simplified' => '厕纸券'],
      'phonetic' => 'tchi jee guen'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/out of toilet paper.jpg']
      ),
      'english' => 'out of toilet paper',
      'chinese' => ['traditional' => '冇晒廁紙', 'simplified' => '冇晒厕纸'],
      'phonetic' => 'mo sai tchee jee'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/trash.jpg']
      ),
      'english' => 'trash',
      'chinese' => ['traditional' => '垃圾', 'simplified' => '垃圾'],
      'phonetic' => 'lap sap'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/trash can 1.jpg'],
        ['path' => 'bathroom/trash can 2.jpg']
      ),
      'english' => 'trash can',
      'chinese' => ['traditional' => '垃圾桶', 'simplified' => '垃圾桶'],
      'phonetic' => 'lap sap tong'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/bucket.jpg']
      ),
      'english' => 'bucket',
      'chinese' => ['traditional' => '桶', 'simplified' => '桶'],
      'phonetic' => 'tong'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/dental floss.jpg']
      ),
      'english' => 'dental floss',
      'chinese' => ['traditional' => '牙線', 'simplified' => '线'],
      'phonetic' => 'nga seen'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/soap.jpg']
      ),
      'english' => 'soap',
      'chinese' => ['traditional' => '番梘', 'simplified' => '番枧'],
      'phonetic' => 'faan gaan'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/liquid soap.jpg']
      ),
      'english' => 'liquid soap',
      'chinese' => ['traditional' => '梘液', 'simplified' => '枧液'],
      'phonetic' => 'gaan yik'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/soap box.jpg']
      ),
      'english' => 'soap box',
      'chinese' => ['traditional' => '番梘箱', 'simplified' => '番枧箱'],
      'phonetic' => 'faan gaan seung'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/lid.jpg']
      ),
      'english' => 'lid',
      'chinese' => ['traditional' => '蓋', 'simplified' => '盖'],
      'phonetic' => 'goy'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/sink.jpg']
      ),
      'english' => 'sink',
      'chinese' => ['traditional' => '洗手盆', 'simplified' => '洗手盆'],
      'phonetic' => 'sai sau poon'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/hot water tap.jpg']
      ),
      'english' => 'hot water tap',
      'chinese' => ['traditional' => '熱水制', 'simplified' => '热水制'],
      'phonetic' => 'yit seui jai'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/cold water tap.jpg']
      ),
      'english' => 'cold water tap',
      'chinese' => ['traditional' => '凍水制', 'simplified' => '冻水制'],
      'phonetic' => 'dung seui jai'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/water.jpg']
      ),
      'english' => 'water',
      'chinese' => ['traditional' => '水', 'simplified' => '水'],
      'phonetic' => 'seui'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/hair brush.jpg']
      ),
      'english' => 'hair brush',
      'chinese' => ['traditional' => '梳', 'simplified' => '梳'],
      'phonetic' => 'saw'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/chain.jpg']
      ),
      'english' => 'chain',
      'chinese' => ['traditional' => '鏈', 'simplified' => '链'],
      'phonetic' => 'leen'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/hair gel.jpg']
      ),
      'english' => 'hair gel',
      'chinese' => ['traditional' => '髮膠', 'simplified' => '发胶'],
      'phonetic' => 'faat gau'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/plug.jpg']
      ),
      'english' => 'plug',
      'chinese' => ['traditional' => '𣘚', 'simplified' => '𣘚'],
      'phonetic' => 'sut'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/curtain.jpg']
      ),
      'english' => 'curtain',
      'chinese' => ['traditional' => '簾', 'simplified' => '帘'],
      'phonetic' => 'leem'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/bathtub.jpg']
      ),
      'english' => 'bathtub',
      'chinese' => ['traditional' => '沖涼缸', 'simplified' => '冲凉缸'],
      'phonetic' => 'chung leung gong'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/mat.jpg']
      ),
      'english' => 'mat',
      'chinese' => ['traditional' => '地氈', 'simplified' => '地毡'],
      'phonetic' => 'day jeen'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/toilet scrubber.jpg']
      ),
      'english' => 'toilet scrubber',
      'chinese' => ['traditional' => '廁所刷', 'simplified' => '厕所刷'],
      'phonetic' => 'tchi saw chaat'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/sponge.jpg']
      ),
      'english' => 'sponge',
      'chinese' => ['traditional' => '海綿', 'simplified' => '海绵'],
      'phonetic' => 'hoi meen'
    ),
    array(
      'images' => array(
        ['path' => 'bathroom/toilet sprayer.jpg']
      ),
      'english' => 'toilet sprayer',
      'chinese' => ['traditional' => '廁所噴', 'simplified' => '厕所喷'],
      'phonetic' => 'tchi saw pun'
    )
  );
  
  $washroom_words_table = generate_table($washroom_words_array);

  $washroom_html_contents = 
  "<p>Here is a list of words for things that can be found inside a washroom. You can listen to the words being spoken on a video on Youtube with this link: <a href='https://www.youtube.com/watch?v=qc48dlSuoFQ' class='video'>Washroom words in Cantonese Movie</a>. The below table contains almost the same information as in the movie with some minor differences.
  
  $washroom_words_table
  ";
  generate_templated_file($washroom_html_contents, $GLOBALS['output directory'] . '/washroom_words.html');
}

//Takes in a file
//source_path destination_path
//source_path2 destination_path2
function copy_files($copy_instructions_filename = null){
  if (empty($copy_instructions_filename)){
    $copy_instructions_filename = $GLOBALS['copy instructions file'];
  }
  $lines = explode(PHP_EOL, file_get_contents($copy_instructions_filename));
  foreach ($lines as $line){
    $source = $GLOBALS['source directory'] . "/" . $line;
    $destination = $GLOBALS['output directory'] . "/" . $line;

    //If source is a file, simply copy it
    if (is_file($source)){
      exec("cp -R $source $destination");
    }else{
      //Otherwise, if it is a directory, copy it if it is new
      //Copy the contents in it if the directory is not new
      //Copying is done recursively.
      if (!file_exists($destination)){
        exec("cp -R $source $destination");
      }
      else{
        exec("cp -R $source/* $destination");
      }
    }
  }
}

//Assumes folders_configuration_file exists
function generate_directory_structure($folders_configuration_file){
  $lines = explode("\n",file_get_contents($folders_configuration_file));
  foreach ($lines as $line){
    $folder_to_create = trim($GLOBALS['output directory'] . '/' .$line);
    if (!file_exists($folder_to_create)){
      mkdir($folder_to_create, 0777, true);
    }
  }
}

function generate_templated_pages(){
  //Each entry is of the form [output_filename, content_file]
  $list_of_templated_pages = array(
    ['destination' => $GLOBALS['output directory'] . '/pronunciation.html', 'source' => $GLOBALS['source directory'] . '/pronunciation.content'],
    ['destination' => $GLOBALS['output directory'] . '/greetings_and_partings.html','source' => $GLOBALS['source directory'] . '/greetings_and_partings.content'],
    ['destination' => $GLOBALS['output directory'] . '/basic_etiquette.html','source' =>  $GLOBALS['source directory'] . '/basic_etiquette.content'],
    ['destination' => $GLOBALS['output directory'] . '/pronouns.html','source' => $GLOBALS['source directory'] . '/pronouns.content'],
    ['destination' => $GLOBALS['output directory'] . '/simple_sentences.html','source' => $GLOBALS['source directory'] . '/simple_sentences.content'],
    ['destination' => $GLOBALS['output directory'] . '/list_of_basic_nouns.html','source' => $GLOBALS['source directory'] . '/list_of_basic_nouns.content'],
    ['destination' => $GLOBALS['output directory'] . '/body_parts.html','source' => $GLOBALS['source directory'] . '/body_parts.content'],
    ['destination' => $GLOBALS['output directory'] . '/contact.html','source' => $GLOBALS['source directory'] . '/contact.content'],
    ['destination' => $GLOBALS['output directory'] . '/links.html', 'source' => $GLOBALS['source directory'] . '/links.content'],
    ['destination' => $GLOBALS['output directory'] . '/blog.html', 'source' => $GLOBALS['source directory'] . '/blog.content'],
    ['destination' => $GLOBALS['output directory'] . '/new-year_新年.html', 'source' => $GLOBALS['source directory'] . '/new-year_新年.content']
  );

  foreach ($list_of_templated_pages as $page){
    generate_templated_file($page['source'], $page['destination']);
  }
}

//Opens up the src/directories.txt file and generates the directories listed in that file.
//The format of the src/directories.txt file is as follows:
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
  $directories_file = "src/directories.txt";
  if (file_exists($directories_file)){
    $contents = file_get_contents($directories_file);
    $lines = explode("\n", $contents);
    foreach ($lines as $line){
      if (!empty($line)){
        @mkdir($GLOBALS['output directory'] . "/" . $line,0777,true);
      }
    }
  }else{
    echo("Missing src/directories.txt file.\n");
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


//Takes in a .cdf file and generates an output file.
function process_compiled_documents_file($cdf_filename){
  //This parsing is really fragile.
  $cdf_contents = @file_get_contents($cdf_filename);
  if (!$cdf_contents){
    echo ("CDF file not found:$cdf_filename\n");
    return;
  }
  
  //Each line of a .cdf file is structured as follows:
  //The first token is the path to a .container file
  //Every subsequent token except the last is a path to a .content file.
  //The last file is the path and filename that will be the output location of the file
  //generated by replacing each MAGIC string in the .container file with the contents of the .content file.
  //path/container1.container path/content1.content path/content2.content path_relative_to_output.html

  $lines = explode("\n", $cdf_contents);
  foreach($lines as $line){
    $tokens = explode(" ", $line);
    $number_of_tokens = count($tokens);
    $number_of_content_tokens = $number_of_tokens - 2;
  
    $template_contents = file_get_contents($tokens[0]);   
    //Each container file will contain one or more MAGIC strings
    //The .content files will populate the contents of the .container files, each in turn.
    for ($i = 1; $i < $number_of_content_tokens + 1; $i++){
  echo("tokens[$i]");
  echo($tokens[$i]);
      $content_fragment = file_get_contents($tokens[$i]);
  echo("content_fragment:");
  echo($content_fragment);
  echo("template_contents:");
  echo($template_contents);
      $template_contents = replace_one_magic($template_contents, $content_fragment);
  echo("template contents after replacement:");
  echo($template_contents);
    }
  
    //Save the contents of the file to the indicated output
    $last_token_index = $number_of_tokens - 1;
    file_put_contents($tokens[$last_token_index],$template_contents);
  }
}

//parses the script.txt file and executes the commands within it
function follow_script_file(){
  //hard coded
  $script_file = "src/script.txt";
  $script_file_contents = @file_get_contents($script_file);
  if ($script_file_contents === false){
    echo("Missing src/script.txt file.\n");
  }

  //From here on, assume file is present
  $lines = explode("\n", $script_file_contents);
  for($i = 0; $i < count($lines); $i++){
    $line = $lines[$i];
    if ($line == 'generate directories'){
      echo ("Generating directories.\n");
      process_directories();
    }else if ($line == 'copy files'){
      echo ("Copying files.\n");
      copy_files();
    }else if (is_compile_directive($line)){
      echo ("Compiling documents:");
      try{
        $tokens = explode(" ", $line);
        $cdf_filename = $tokens[1];
        echo("$cdf_filename\n");
        process_compiled_documents_file($cdf_filename);
      }
      catch(Exception $e){
        echo ("Error processing compiled documents file.");
      }
    }
    else{
      echo ("Syntax error in script.txt around line $i at the part that says'$line'\n");
    }
  }
}

// function generate_website(){
  // read_script_file();
  // if (file_exists($GLOBALS['directory structure file'])){
  //   generate_directory_structure($GLOBALS['directory structure file']);
  // }

  // if (file_exists($GLOBALS['copy instructions file'])){
  //   copy_assets($GLOBALS['copy instructions file']);
  // }
  
  // generate_index_html();
  // generate_washroom_html();
  // generate_templated_pages();  
// }

//generate_website();
follow_script_file();