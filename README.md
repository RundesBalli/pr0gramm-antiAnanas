# pr0gramm-antiAnanas
Anti-Ananas.Club Autoresponse Bot for the German imageboard [pr0gramm.com](https://pr0gramm.com).

Refer to the [Anti Ananas Club Project](https://github.com/RundesBalli/anti-ananas.club) and the [Website](https://anti-ananas.club/).

## Dependencies
- [pr0gramm-apiCall](https://github.com/RundesBalli/pr0gramm-apiCall)

## Configuration
1. Rename the config-template file:  
```bash
mv includes/config.template.php includes/config.php
```
2. Edit the file and configure the variables in it:  
```bash
nano includes/config.php
```
> [!NOTE]
> The whole config file is commented and you will see how to configure it properly.


## Systemd service
1. Edit the `ExecStart`-path in the service file:  
```bash
nano pr0gramm-antiAnanas.service
```
2. Create a systemlink to the service file:  
```bash
sudo ln -s /path/to/pr0gramm-antiAnanas.service /etc/systemd/system/pr0gramm-antiAnanas.service
```
3. Enable and start the service:  
```bash
sudo systemctl enable pr0gramm-antiAnanas && sudo systemctl start pr0gramm-antiAnanas
```
4. Check if the service has started:  
```bash
sudo systemctl status pr0gramm-antiAnanas
```
