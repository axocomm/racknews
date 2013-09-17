# RackNews

## Overview

**RackNews**, (possibly) the missing RackTables reports tool.

---
**RackNews** is a reporting tool for the datacenter inventory management system [RackTables](http://www.racktables.org). It allows objects in RackTables to be searhed, filtered, and displayed in a variety of formats, including JSON, ~~XML~~, and ~~HTML pages~~ (not yet).

### How to Use
#### Installation
To install, simply clone this repository/extract the archive into RackTables' **wwwroot**.

#### Operation
##### Web UI
RackNews allows for use on the command line, through cURL/wget for scripts, as well as through a web interface. To access the web interface, simply visit \<RackTables root\>/racknews. You may be prompted to login; simply use your RackTables credentials. You should then see a form containing a table, a few textboxes, and a combo box for various parameters. Simply fill out any desired information and click the button at the bottom to see the report.

##### cURL/wget/etc.
`report.php` can also be worked with by using the set of available URL parameters shown below. This allows the tool to be used in scripts and other applications. Query strings can be generated using the web UI using the 'Query String' button, or formulated by consulting the list of parameters below. A report could then be used in a script by using a URL like the following:

	https://rt.foo.com/racknews/report.php?report=unused_ip&fields=name,FQDN

##### Command line
Coming soon

