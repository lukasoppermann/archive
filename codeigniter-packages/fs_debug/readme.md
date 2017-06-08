# FS_Debug

The Form&System debug class is a small code igniter extension to make it easier to develop for this fantastic framework.

## Features
* debug console displayed above everything (slightly transparent)
* debug console can be hidden (X)
* time of output and the file from the function has been called is displayed
* no "header already sent" warnings
* debug log is only displayed while CI is in "development environment" (set in index.php)

## Install
There are 4 files to install:

* fs_debug.php (this goes into the libraries directory)
* fs_debug_helper.php (this goes into the helper directory)
* debug.css (put it wherever you have your css files)
* debug.js (put it wherever you have your js files)

After putting those files into the right directories you need to do a little configuration.
In the autoload.php add the library and the helper.

Afterwards you need to add 2 functions to your view files, one to you header (I use header.php) to load the css and one to the footer (I use footer.php). 

You can of course add the js to the header, but since its a speed decrease I suggest this solution.

The two functions to apply are fs_debug_print_css() and fs_debug_print_js().

NOTE: The JavaScript only works with jQuery.

## Usage
All that is left to do now is to use fs_log() anywhere in your code like so

```php
fs_log(array('number' => 123, 'name' => 'schmidt'));
```

You can use the function multiple times, all inputs will be displayed.

Somewhere in your html code inside the <body> use fs_show_log() and you will get a nicely layout output.
