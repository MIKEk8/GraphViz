## ⚠️ NOTICE: This is a fork of [phpdocumentor/graphviz](https://github.com/phpDocumentor/GraphViz) library, modified for PHP 8.4 compatibility.
# 🚨 This fork is no longer maintained or supported. Use at your own risk! 🚨

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
