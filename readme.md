Initial Setup
Please ensure you have created the Database.

Type the following in your terminal:
create database infinity;
use database infinity
sudo mysql infinity < createTable.sql (Optional: Since the php script will create it if it doesn't already exist)

To run the code:
Once you have cloned the repository, navigate to "infinity" folder where you will find
the index.php file.

Type the following in your terminal:
php index.php

To access syslog:
This will depend on your system settings but it will most likely be found under /var/log.
Here you will find all the messages logged during the import process, including datetime the process
took place and the source of the error including the file name and its row/column etc

Type the following in your terminal:
cd /var/log
vim syslog

Crontab job
The crontab just below will ensure that crontab job will run every minute only if there is no instance already running.
Type the following in your terminal:
crontab -e
* * * * * /usr/bin/pgrep -f /path/to/infinity/index.php > /dev/null 2> /dev/null || /path/to/infinity/index.php

Exit and save the file. Ensure you write the correct path on your system.

The Development
Steps taken to handle errors included the creation of the Validator class which validates
all rows and column fields extracted from the CSV file. This way the DB exceptions will not
occur since it is taken place before it arrives on the DB end.

There is also some exception handling implemented when dealing with non-existent DB table.

Some Considerations
An alternative would have been to expand upon the code in the index.php which would have contained the 
foreach loop and pass the csv file to the import class. Import class code could perhaps be broken 
down further into smaller units for testing. Move out createTableIfNotExists from the importClass and perhaps make use of the external sql file instead.