[youtube-dl](http://rg3.github.com/youtube-dl/documentation.html)
-----------------------------------------------------------------

This script sync your video feed up to date.

**usage**

    $ youtube-dl http://www.youtube.com/watch?v=foobar

Mac OS X [Launchd](https://developer.apple.com/library/mac/documentation/MacOSX/Conceptual/BPSystemStartup/Chapters/CreatingLaunchdJobs.html)
---------------------------------------------------------------------------------------------------------------------------------------------

    $ touch /usr/local/bin/tv
    
    #!/bin/bash
    ~/path/to/youtube/tv | while IFS= read -r line; do echo "$(date +'%Y-%m-%d %H:%M:%S') $line"; done >> ~/Library/Logs/TV/`date +\%Y_\%m_\%d_\%H_\%M`.log
    
    $ chmod a+x /usr/local/bin/tv
    
    $ touch ~/Library/LaunchAgents/fr.darkwood.matyotools.plist
    
    <?xml version="1.0" encoding="UTF-8"?>
    <!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
    <plist version="1.0">
    <dict>
    	<key>Label</key>
    	<string>fr.darkwood.matyotools.tv</string>
    	<key>Program</key>
    	<string>/usr/local/bin/tv</string>
    	<key>StartCalendarInterval</key>
    	<dict>
    		<key>Hour</key>
    		<integer>19</integer>
    		<key>Minute</key>
    		<integer>30</integer>
    	</dict>
    	<key>RunAtLoad</key>
    	<false/>
    </dict>
    </plist>
    
More info for configure launchd plist : [apple developer link](https://developer.apple.com/library/mac/documentation/Darwin/Reference/ManPages/man5/launchd.plist.5.html)

Linux [Contab](http://fr.wikipedia.org/wiki/Crontab)
----------------------------------------------------

    $ crontab -e
    19 30 * * * /usr/local/bin/tv

COMMANDS
--------

Extract youtube audio playlist

    $ youtube-dl --extract-audio -o "%(upload_date)s.%(title)s.%(ext)s" --audio-format mp3 <youtube playlist url>