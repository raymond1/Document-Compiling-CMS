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
4)Executing a series of commands from the command line.

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


# Creating a script file/Script file syntax

The file script.txt needs to be created and placed in the working directory. If it is not there, you need to add it. It contains the set of instructions that will be performed by the cms.

Example file:
generate directories
copyscript copy.txt

Explanation:
The format of the script.txt file consists of lines of instructions, with one instruction per line. Each line contains what are called "directives", which are key words or phrases that have special meaning for the CMS.

"generate directories" and "copyscript" in the above example file are known as directives. The complete list of available directives and their usage are indicated below.

# Script file directives

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

## "copyscript" directive

Usage:

In the script file, add a line that contains a line containing two tokens. The first token is string "copyscript". The second token is a filepath pointing to a file that contains a list of what files to copy over.

For example:

```
copyscript copy.txt
```

will tell the DCC to copy all the files indicated in the file copy.txt file when the php generate_directories.php command is run.

The format for the copy.txt file is a two-column file where the two columns are separated by a space. The left column will be a source file. The right column will be the destination file or folder name.

Both the source and destination specified are relative to the working directory.

For example, to copy the file snail2.jpg from the src directory into a directory named output, the following line would need to be added to copy.txt:

snail2.jpg output

The result after processing the ```copyscript copy.txt``` command from above is that the file snail2.jpg located in the src directory would be copied into the output directory.

Similary, a directory can be copied recursively into the output directory by specifying a directory. You can use nested directories such as directory1/directory2/directory3 for this purpose.

## "compile" directive
There are many websites that contain multiple pages that are the same, except for the content in a certain number of limited places.

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

Only the content of the body tags is different. In order to generate this website using the document compiling cms, the following steps need to be taken:

1) In the script.txt file, add a line like the following:
```
compile <target filename>
```
The syntax is the word "compile" followed by the name of a file.

2)Generate the target file. Each line in the target file consists of the name of a template file, followed by the name of a content file, followed by the resultant file name after substitution, all separated by spaces. Make sure your file names don't have spaces.

For the current mini tutorial, the contents of filename.cdf can  be:
```
template.template page1.content output_directory/page1.html
template.template page2.content output_directory/page2.html
...
<template filename> <content fragment filename> <output filename>
```

During processing,
1) The template file specified on each line is opened and read
2) For each <% tag encountered in the template file, the commands contained within it will be processed %>. See the section on the [template file mini-language](#template-file-mini-language) for more details.

3) An output file corresponding to the last filename listed on the line of a .cdf file is generated containing the contents of the template file after all the commands in the template file have been processed.

The template.template file will need to have the following contents for the example:

<html>
<title>A website</title>
<body>
<%
copy page1.content
%></body>
</html>

The file page1.content should have the following content:
```
<h1>Page 1<h1>
<p>Some content goes here.
```

Similarly, the page2.content file should have the following content:
```
<h1>Page 2<h1>
<p>Some different content for page 2 goes here.
```

4)Run php generate_website.php
The following files will be generated:
output_directory/page1.html
output_directory/page2.html

## "execute" directive
The commands directive is used to specify a file to run commands from the command line.

Usage:

execute <command file>

The format for the command file is that each line in the file will be the command that will be executed from the command line.

# Template file mini-language

Currently, the template files support only two commands, 'transcribe' and 'print'. Inside the template file

## 'transcribe' command

# Development process and Makefile
## Makefile for copying generate_website.php
Use the Makefile to copy your development generate_website.php into the target directory. 

Usage: make copy

## To bump the version number

In the document-compiling-cms folder, commit and tag your changes with the newest version.
git add .
git commit -m "..."
git push
git tag  (to get tag name)
git tag 1.1.7
git push origin 1.1.7

In your development/test directory, where the document-compiling-cms was installed with composer, do a composer update.