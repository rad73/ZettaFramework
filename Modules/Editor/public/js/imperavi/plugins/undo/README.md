# Redactor Word Count

## Description

A simple Javascript plugin for the [Redactor WYSIWYG editor](http://imperavi.com/redactor/ "Redactor WYSIWYG editor") which facilitates undo functionality in Microsoft Internet Explorer < 9 via a toolbar button.

## Installation

* Copy undo.RedactorPlugin.js into your project

* Link undo.RedactorPlugin.js in the header of the page containing Redactor

* Add undo to the list of available plugins when you initialize Redactor:
  >  $('#redactor').redactor({
  >      plugins: ['undo']
  >  });
