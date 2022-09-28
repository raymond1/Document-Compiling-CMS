# Usage
This is a console run php script and is activated using the following command:

php generate_website.php <optional script_file>

If no script file is specified, it is assumed by default to be "script.txt" from the current directory.

See below for information on how to configure the Document Compiling CMS (abbreviated DCC), the format of the script file and files needed to control the behaviour of generate_website.php.

# Capabilities
This is a program that generates a website. It is capable of doing the following things:

1)Generating the directory structure of a website
2)Copying files from one location to another
3)Generating multiple files based off of a template and individual fragments of data.

# Installation

This software is supposed to be installed using composer. To install the software, you can try to do the following:

1)In the composer.json file, add the following repository:

        "repositories":[{
                        "type": "vcs",
                        "url": "git@github.com:raymond1/Document-Compiling-CMS.git"
        }]
    
2)From the command line, type the following command:

composer require raymond1/document-compiling-cms

3)Copy the file generate_website.php into the base of the folder where you are going to put the files used to create your website.


## Creating a script file/Script file syntax

Add the file script.txt needs to be placed in the working directory. It contains the set of instructions that will be performed by the cms.

Example file:
generate directories
copy files

Explanation:
The format of the script.txt file consists of lines of instructions, with one instruction per line. Each line contains what are called "directives", which are key words or phrases that have special meaning for the CMS.

"generate directories" and "copy files" in the above example file are known as directives. The complete list of available directives and their usage are indicated below.

## Script file directives

## "generate directories" directive

Usage:

In the script.txt file, add a line that contains only the following text:

generate directories

When the php generate_website.php script is run, the generate directories directive will do the following:
1)open and read the file directories.txt. You will need to create this file if it doesn't exist.
2)generate the directories listed in the directories.txt file.

The syntax for specifying the directories to be created is as follows: each line contains a series of directory names separated by slashes. These directories will be created when the "generate directories" directive is processed.

An example directories.txt file might contain the following:

output/example
output/example/images
example2

This will generate the folders output, containing a subfolder example, containing a subfolder images. It will also generate the folder example2. All folders are relative to the working directory.

## "copy files" directive

Usage:

In the script file, add a line that contains only the following text:

copy files

When the php generate_directories.php command is run, the copy files directive will copy files or directories from a source location into a destination folder. The files that will be selected for the copy operation will come from the contents listed in the copy.txt file. You will need to create this file if it doesn't exist. By default, the working directory is searched for the copy.txt file.

For each line in the copy.txt file, a copy command will be executed. Both the source and destination specified are relative to the working directory.

For example, to copy the file snail2.jpg from the src directory into a directory named output, the following line would need to be added to copy.txt:

snail2.jpg output

The result after processing the copy files directive is that the file snail2.jpg located in the src directory would be copied into the output directory.

Similary, a directory can be copied recursively into the output directory by specifying a directory. You can use nested directories such as directory1/directory2/directory3 for this purpose.

## "compile" directive
Overview:
This feature requires multiple files in order to be specified. The way to understand this feature is to understand the underlying use case it was meant to solve. The idea is that there are many websites that contain repeating sections containing parts within them that are different.

Consider, for example, the following two pages of a fictitious website:
Page 1:
```
<html>
<title>A website</title>
<body>
<h1>Page 1<h1>
<p>Some content goes here.
</body>
</html>
```

Page 2:
```
<html>
<title>A website</title>
<body>
<h1>Page 2<h1>
<p>Some different content for page 2 goes here.
</body>
</html>
```
Note that only the content between the body start and end tags is different. In order to generate this website using the document compiling cms, the following steps need to be taken.

1)In the script.txt file, add a line like the following:
```
compile <filename.cdf>
```
The syntax is the word "compile" followed by the name of a .cdf file. For the purposes of this mini tutorial, assume the name fo the cdf file is "filename.cdf".

2)Generate the filename.cdf file. Inside that file, put on one line the name of the template file, the name of the content files, and the resultant file after substitution, all separated by spaces. Make sure your file names don't have spaces.

For the current mini tutorial, the contents of filename.cdf can  be:
```
template.template page1.content output_directory/page1.html
template.template page2.content output_directory/page2.html
```

Explanation:

Each line of a .cdf file generates one output file. The syntax for a line of the .cdf file is as follows:

<.template filename> <one or more .content filenames> <output filename>

In other words, each line in a .cdf file consists of a filename followed by any number of .content filenames and ends with an output filename. When a line of the .cdf file is processed, the following things happen:

1)The .template filename specified on each line is opened and read
2)For each MAGIC string encountered in the .template file, the contents of a .content file is substituted for it.
3)An output file corresponding to the last filename listed on the line of a .cdf file is generated containing the contents of the .template file after all the MAGIC constants have been replaced.

3)Generate all the .template and .content files needed in step 2.

The template.template file will have the following contents for the example:

<html>
<title>A website</title>
<body>MAGIC</body>
</html>

page1.content will have the following contents:
```
<h1>Page 1<h1>
<p>Some content goes here.
```
page2.content will have the following contents:
```
<h1>Page 2<h1>
<p>Some different content for page 2 goes here.
```

4)Run php generate_website.php
The following files will be generated:
output_directory/page1.html
output_directory/page2.html

These two files were specified in the .cdf file in step 2 as the last argument on each line of the file. The contents will be determined by substituting the MAGIC string found in the template.template file with the contents specified by the second argument on each line of the .cdf file.


## Development process and Makefile
# Makefile for copying generate_website.php
Use the Makefile to copy your development generate_website.php into the target directory. 

Usage: make copy

# To bump the version number

In the document-compiling-cms folder, commit and tag your changes with the newest version.
git add .
git commit -m "..."
git push
git tag  (to get tag name)
git tag 1.0.7
git push origin 1.0.7

In your development/test directory, where the document-compiling-cms was installed with composer, do a composer update.

cp ~/Desktop/cantonese/cantonesecentral.com.v2/vendor/raymond1/document-compiling-cms/generate_website.php ~/Desktop/cantonese/cantonesecentral.com.v2/generate_website.php


