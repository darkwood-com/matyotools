Usage
-----

```
$ app/console harvest:running   => Truncate harvest timesheet
$ app/console harvest:stats     => Stats harvest timesheet
$ app/console harvest:stop      => Stop harvest timesheet
$ app/console harvest:truncate  => Truncate harvest timesheet
```

Install harvest:coffee
----------------------

into ~/Library/LaunchAgents/fr.darkwood.harvest.plist

    <?xml version="1.0" encoding="UTF-8"?>
    <!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
    <plist version="1.0">
    <dict>
    	<key>Label</key>
    	<string>fr.darkwood.harvest</string>
    	<key>Disabled</key>
    	<false/>
    	<key>ProgramArguments</key>
    	<array>
    		<string>/bin/bash</string>
    		<string>-c</string>
    		<string>/Users/math/Sites/darkwood/matyotools/php/Harvest/app/console harvest:coffee</string>
        </array>
    	<key>StartCalendarInterval</key>
    	<dict>
    		<key>Hour</key>
    		<integer>21</integer>
    		<key>Minute</key>
    		<integer>30</integer>
    	</dict>
    	<key>RunAtLoad</key>
    	<false/>
    </dict>
    </plist>

launchctl usage

	launchctl list | grep darkwood
	launchctl load ~/Library/LaunchAgents/fr.darkwood.harvest.plist

do not forget to check your php.ini (date.timezone = "Europe/Paris" and http://symfony.com/fr/doc/current/reference/requirements.html)

into /usr/local/bin/harvest_coffee

	#!/bin/bash
	/Users/math/Sites/darkwood/matyotools/php/Harvest/app/console harvest:coffee
	
Install harvest:domino
----------------------

into /usr/local/bin/harvest_domino

	#!/bin/bash
	/Users/math/Sites/darkwood/matyotools/php/Harvest/app/console harvest:domino