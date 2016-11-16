# SenseOne Module #

## Module ##
This module is a project for [Optimus Smart City].
The module parses several data sources.
Then RDF triples are been produced and stores in a local [MariaDB].
The last process of this module is the publishing of these RDF triples.

## Dataset ##

Data sets are in miscellaneous file formats like CSV, XML, SQLITE, MYsql.
The Data retrieval can be done via FTP, Http Request, SQL DB.

## Mapping ##
The user has to map the measure names from DataSets with specific RDF stream names.


## Procedure ##
Already existing parsers(Modules)(Mapping is required when a new aggregator added)

### Mapping ###
[php_Installation_Path]\php.exe  [optimus_installation_path]\optimus CSVMapping

### Parsing ###
[php_Installation_Path]\php.exe [optimus_installation_path]\optimus XML
[php_Installation_Path]\php.exe  [optimus_installation_path]\optimus CSVAuditori
[php_Installation_Path]\php.exe  [optimus_installation_path]\optimus CSVSavona

### Publishing ###
[php_Installation_Path]\php.exe  [optimus_installation_path]\optimus publish


## Installation notes ##
* location of files($filename) for mapping in optimus/src/Command/CSVMappingImporterCommand
* location of local File ($local_file) in optimus/src/Importer/CSVImporter
* change paramters for database in optimus/src/configuration.php
* change paramters for database in optimus/src/config/database.php
* location of local File ($local_file) optimus/src/Importer/SQLiteImporter

[Optimus Smart City]: http://optimus-smartcity.eu/
[MariaDB]: http://mariadb.org/


