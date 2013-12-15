# RackNews

## Overview

**RackNews**, (possibly) the missing RackTables reports tool.

---
**RackNews** is a reporting tool for the datacenter inventory management system [RackTables](http://www.racktables.org). It allows objects in RackTables to be searched, filtered, and displayed in a variety of formats, including CSV, JSON, XML, and HTML.

This tool can consolidate most of the data within RackTables and present it in a variety of ways, making it a viable option for scripts, larger applications, and just viewing it.

### How to Use
#### Installation
To install, simply clone this repository/extract the archive into RackTables' **wwwroot**.

#### Authorisation
RackNews uses the same authentication that RackTables uses; if you are prompted again for your credentials, you should use those.

#### Operation
##### Web UI
RackNews allows for use on the command line, through cURL/wget for scripts, as well as through a web interface. To access the web interface, simply visit RackTables_Root/racknews. You may be prompted to login. Use your RackTables credentials. You should then see a form containing a table, a few textboxes, and a combo box for various parameters. Simply fill out any desired information and click the button at the bottom to see the report.

##### cURL/wget/etc.
`report.php` can also be worked with by using the set of available URL parameters shown below. This allows the tool to be used in scripts and other applications. Query strings can be generated using the web UI using the 'Query String' button, or formulated by consulting the list of parameters below. A report could then be used in a script by using a URL like the following:

	https://rt.foo.com/racknews/report.php?report=unused_ip&fields=name,FQDN

##### Command line
To use via the command line, you may execute `report.php` and pass parameters in as long options. For example:

	php report.php --report=unused_ip --fields=name,FQDN
	
will produce the same report as above.

### Available Parameters
#### report
A pre-made or special set of modifications made on the objects list. For example:

- unused_ip: find objects that are empty or marked unused
- fields: get a list of available fields

Reports can be added by modifying `php/RackNews/Report.class.php::pre_build`

#### has
A comma-separated list of fields that must be set and not empty. For example:

	report.php?has=FQDN
	
finds objects that have fully qualified domain names defined.

#### types
A comma-separated list of type names to search for. For example:

	report.php?types=Server
	
finds, unsurprisingly, server objects.

#### names
A comma-separated list of names to search for. For example:

	report.php?names=foo,bar,baz

gets the object entries for foo, bar, and baz.

#### id
A comma-separated list of object IDs (the object_ids RackTables uses) to search for.

#### log
A comma-separated list of log queries to search for. For example:

	report.php?log=loan
	
would return a set of objects whose log records contain the word 'loan'.

#### comment
Similar to `log`, except searches the comments set for each object.

#### fields
A comma-separated list of fields to select from the objects list. For now these are case-sensitive, unfortunately. For example:

	report.php?fields=name,FQDN,ipv4
	
gets an array containing objects with their names, FQDNs, and IPv4 addresses.

#### and
A comma-separated list of matches to perform on objects' attributes (kind of works). For example:

	report.php?and=rack_id:4,has_problems:yes
	
would get all objects with problems in the rack with ID 4.

#### or
Similarly, `or` gets objects satisfying any of the given matches. For example:

	report.php?types=Server&or=Rack_name:Rack1,Rack_name:Rack2
	
would get servers that are in racks named "Rack 1" and "Rack 2".

#### format
The format in which data will be shown. The following are currently available:

+ JSON
+ XML
+ HTML
+ CSV
+ PHP array

### Examples
Here are a few simple examples of combinations:

##### Find unused FQDNs and output as a CSV

	report.php?report=unused_ip&fields=FQDN&format=csv
	
##### Find the names of laptops that are on loan, output as an HTML table

	report.php?log=loan&fields=name&format=html
	
##### Get the names and serial ports of serial console-enabled servers as XML

	report.php?matching=etags:serial&fields=name,Console%20Port&format=xml