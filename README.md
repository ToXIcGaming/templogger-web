# templogger-web

Used to display data from [DHT22-TemperatureLogger](https://github.com/tsamu/DHT22-TemperatureLogger).

Simply configure the MySQL DB settings in the api.php file.

The bash file queried for the CPU temperature was written using [this](https://helloacm.com/cpu-temperature-on-raspberry-pi/) tutorial, basically:
```
#!/bin/bash

temp=`cat /sys/class/thermal/thermal_zone0/temp`

echo "$(($temp/1000))"
```

Saved as a file named temp with no extension in the pi home directory and `chmod +x temp`.

## Credits
Uses [Bootstrap](https://getbootstrap.com/) and [jQuery](https://jquery.com/).

Favicon from [here](https://www.iconsdb.com/soylent-red-icons/temperature-2-icon.html).
