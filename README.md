# 🚨 Use at your own risk! 🚨

## ⚠️ Notice: This is a fork of the [phpdocumentor/graphviz](https://github.com/phpDocumentor/GraphViz) library, modified for compatibility with PHP 8.4.
- Exceptions have been modified: Now the library throws an error when attempting to access non-existent attributes.

### ⚠️ Support and Updates:

**This fork will no longer receive official support or updates.**  
Updates may occur, but compatibility may be broken without notice.




GraphViz
========

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

GraphViz is a library meant for generating .dot files for GraphViz with a
fluent interface.


### PHPStan extension

This library contains a number of magic methods to set attributes on `Node`, `Graph` and `Edge`
this will result in errors when using the library with checks by PHPStan. For your convenience this
library provides an phpStan extension so your code can be checked correctly by phpstan.

```
includes:
    - vendor/phpdocumentor/graphviz/extension.neon
```
