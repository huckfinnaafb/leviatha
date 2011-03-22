This folder contains plug-ins for use with the PHP Fat-Free Framework. We expect this archive to expand as the the community grows in size.

Plug-ins are nothing more than autoloaded classes that use framework built-ins to extend Fat-Free's features and functionality. Framework plug-ins should be saved in the path that your AUTOLOAD global variable points to. Static methods inside these classes can be called using the F3:: prefix. Fat-Free automatically searches the autoload/ folder for a class that has a matching method name, so you don't have to remember which class a method belongs to. Be wary though, if you have more than one class with similarly-named methods - you need to prefix the appropriate class name in these cases.

If you'd like to contribute your plug-in to the Fat-Free community, leave a note at the Developers Forum:-

http://sourceforge.net/projects/fatfree/forums/forum/1041719
