dnb-visualization
=================

Visualizing the German National Library (DNB) Data 

#DNB XML Parser

The DNB Api (http://www.dnb.de/DE/Service/DigitaleDienste/digitaledienste_node.html) is not made for requesting the whole data stored in the GND. It is only build for synchronizing databases that already hold the same dataset. So you usually just request changes after date XY.
Instead you need to download the whole database as an XML. For Person-Data this file is *Tpgesamt1402gndmrc.xml*. This file is about 10GB. So it is not that easy extracting the data and storing it in a e.g. SQL database for better retrieval.

Therefore i have create a php parser parser/parser.php that allows handling of really large XMLs. Inside the parser you can define the features you want to extract etc. It takes a couple hours to finish the job. 
You can setup a cron job or simply use your command line to call the parser:

```
for i in `seq 1 2000`; do curl URL/parser.php; done
```

#DNB Visualizations

##City
city.php

![DNB Visualization](sebastian-meier.github.com/dnb-visualization/img/city.jpg)


##Map
map.php

![DNB Visualization](sebastian-meier.github.com/dnb-visualization/img/map.jpg)


##Person
person.php

![DNB Visualization](sebastian-meier.github.com/dnb-visualization/img/person.jpg)


An Demonstrator for the visualizations can be found on:
http://www.sebastianmeier.eu/2014/06/21/deutsche-national-bibliothek-data-explorer/

The data required to run the visualizations is available as a SQL Dump:
https://dl.dropboxusercontent.com/u/4352238/dnb.sql.zip

This project was part of the Coding Da Vinci - Hackathon:
http://www.codingdavinci.de