# CodeIgniterDB Library
This project abstracts the functionality of the original CodeIgniter 3 database library which has a well-implemented 
Active Record pattern. 

#### Introduction

This composer package provides an adapter for using the build-in CodeIgniter database library within applications 
that do not use CodeIgniter. The original problem is that the library is highly coupled with the rest of the framework 
and it is not possible to use it without some modifications. So what this package does is to create a fake CodeIgniter 
environment so that the library works as expected. 

Note that some features of the database might not be available yet. 

#### Miscellaneous

Copyright &copy; Gambio GmbH

Licensed Under GPL-2.0
