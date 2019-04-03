# simple-irclogs-website
Ascetic website for browsing old and live irc logs produced by irssi

# Simple usage

## Set logging in irssi
Run: 
```
    /LOG OPEN -targets #your-channel ~/irclogs/%y-%m-%d.log PUBLIC
```
  (not quite sure about the exact command)

You should get these settings (if not, set them manually with /set): 
```
    autolog = ON
    autolog_level = PUBLIC
    autolog_path = ~/irclogs/%y-%m-%d.log
```

## Setup the website
```
    cp index.php ~/irclogs
    cd /var/www/html
    sudo ln -s /full/path/to/irclogs .
```

