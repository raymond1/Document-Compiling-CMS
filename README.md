# Document Compiling CMS
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

# Usage
This is a console run php script and is activated using the following command:

php generate_website.php

By default, nothing happens because the actions that the cms should take have not been specified yet. A file called script.txt needs to be configured with instructions to specify actions that will be taken when the generate_website.php script is run.

By default, the placement of the script.txt file is expected to be the working directory(the directory where the command php generate_website.php was typed in). If the file is not there, you will need to create it.

Further details on how to create the script file can be found later in this read me file.

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

If a directory already exists, it will not be recreated. If a directory already exists and is not empty, the directory will not be wiped clean due to the generate directories directive.

## "copy files" directive

Usage:

In the script file script.txt in the src directory, add a line that contains only the following text:

copy files

When the php generate_directories.php command is run, the copy files directive will copy files or directories from a source location into a destination folder. The files that will be selected for the copy operation will come from the contents listed in the copy.txt file. You will need to create this file if it doesn't exist. By default, the working directory is searched for the copy.txt file.

For each line in the copy.txt file, a copy command will be executed. Both the source and destination specified are relative to the working directory.

For example, to copy the file snail2.jpg from the src directory into a directory named output, the following line would need to be added to copy.txt:

snail2.jpg output

The result after processing the copy files directive is that the file snail2.jpg located in the src directory would be copied into the output directory.

Similary, a directory can be copied recursively into the output directory by specifying a directory. You can use nested directories such as directory1/directory2/directory3 for this purpose.

## "compile" directive

Often, websites have repeating sections containing parts that are different. The Document Compiling CMS handles repeating sections by using three types of files:

1).cdf files
2).template files
3).content files

A .cdf file is specified in the script.txt file and cnotains information as to which pieces of content go into which template files. Each line in a .cdf file generates one output file. The syntax for a line of the .cdf file is as follows:

[.template filename] [.content filename1] [.content filename2] [.content filename3] ... [output filename]

In other words, each line in a .cdf file consists of a filename followed by any number of .content filenames and ends with an output filename. When a line of the .cdf file is processed, the following things happen:

1)The .template filename is opened and read
2)For each MAGIC string encountered in the .template file, the contents of a .content file replaces it.
3)An output file corresponding to the last filename listed on the line of a .cdf file is generated containing the contents of the .template file after all the MAGIC constants have been replaced.
