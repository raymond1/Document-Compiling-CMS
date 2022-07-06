# cms
This is a program that generates a website. It is capable of doing the following things:

1)Generating the directory structure of a website
2)Copying files from one location to another
3)Generating multiple files based off of a template and individual fragments of data.



# Usage
This is a php script and is activated using the following command:

php generate_website.php

By default, nothing happens, but configuration files can be added to specify actions to occur.


Overview

Running this command will do the following things:
1)Create the directory structure of a website in an output directory.
2)Copy files from an input directory to an output directory.
3)Create templated files

These files will be put into the output directory, which should contain the exact files that will be uploaded via FTP to a remote server. In other words, the output directory will contain what is usually found in the public_html folder.

## Feature 1: adding directories

Usage:

Add in the file src/directories.txt (relative to generate_website.php).

The format of the file is a series of directory names followed by newlines. One example file is as follows:

example
example/images
example2




XXX
The file src

When you run the generate_website.php script, the following things happen:
1)The directory structures indicated in src/directories.txt is generated.
2)The files and directories indicated in src/copy.txt will copy files from the source to destination. This is done recursively using cp -R.
3)There is a function, generate_website() which will generate the different pages of the website
4)Currently, generate_website works as follows:
  1)Generate the index.html file
5)Templated files are generated
  Templating works as follows:  there is a file: template.container in the src directory that is always used. Inside the template.container file, there is a
  string, MAGIC that can be replaced by content located inside .content files that are also inside the src directory.

Files are placed in the 'output' directory, currently specified by $GLOBALS['output directory'].

To edit the menu, edit the generate_menu function.
To add a new page,



Ideally, there should be a generation_sequence.txt file.

template 1.txt

Design:
1)Read through a script for what generate_website.php should do. Script file is called script.txt and is located in the same directory as generate_website.php.
2)
