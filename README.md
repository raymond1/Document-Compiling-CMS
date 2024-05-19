# Usage Overview
This is a console run php script and is activated using the following command:

php generate_website.php <optional script_file>

If no script file is specified, it is assumed by default to be "script.txt" from the current directory.

See below for information on how to configure the Document Compiling CMS (abbreviated DCC), the format of the script file and files needed to control the behaviour of generate_website.php.

# Capabilities
This is a program that generates a website. It is capable of doing the following things:

1. Generating the directory structure of a website
2. Copying files from one location to another
3. Generating a file by specifying a template, a variable area of text and what that area of text should be filled with.
4. Generating a file by gluing together different fragments of text.
5. Executing a series of commands from the command line.

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
1. open and read the file directories.txt. You will need to create this file if it doesn't exist.
2. generate the directories listed in the directories.txt file.

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
There are many websites that contain multiple pages that are very similar to each other, where, except for the content in a certain number of limited places, the pages are the same. To generate these files, the 'compile' directive exists to allow people to specify the template, the content, and the output file inside the script.txt file.

To use the 'compile directive', first modify the script.txt file. For each web page that shares the same structure, add a line with the following syntax:

```
compile <template file> <content file> <output file>
```

The output file will be produced by the document compiling cms by replacing the contents between and including the opening and end <% %> tag in the template file with the contents of the content file. The template file is assumed to have one <% %> tag in it, that is, the string <% followed by the string %>. If these two string sequences are not detected, the compile directive will not work.

## "template" directive
The template directive instructs the document compiling cms to read in a template file and process its instructions. Syntax:

```
template <template filename> <output filename>
```

The template file's contents will be copied to the output filename, with any <% %> tags processed along the way. Within the <% %> tags, certain commands can be used, as explained in the template file mini-language section below.

### Template file mini-language

Currently, the template files support only two commands, 'transcribe' and 'print'.

#### 'transcribe' command
Example:

<%

transcribe
 file_1
 file_2

%>

Transcribe will print the contents of the files listed under the transcribe node to the output.

#### 'print' command

Example:

<%

print
 string
%>

print will print the strings listed under the print node to the output. The purpose of the print command is to insert strings such as '<%', which might cause problems, or spaces or newline characters in between two transcribe commands.

## "execute" directive
The commands directive is used to specify a file to run commands from the command line.

Usage:

execute <command file>

The format for the command file is that each line in the file will be the command that will be executed from the command line.


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