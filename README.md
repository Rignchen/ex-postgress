# ex-postgress

## Postgresql
### Installation
This project uses a postgresql database wich is run in a docker container\
Start by creating a database called `memoires-info`\
Then to create the tables, you can run the file `init.sql` in the backend folder
```bash
psql -h localhost -U postgres -d memoires-info -a -f backend/init.sql
```
### Launch
Once the database is created, you can start the docker container in the backend folder\
This will run the database on port 5432\
This also run adminer on port 8080 wich is a web interface to manage the database\
This also run a postg rest server on port 3000
```bash
cd backend
docker-compose up
```
### Adminer
You can access adminer by going to `localhost:8080` in your web browser\
You can then connect to the database with the following credentials
```
System: PostgreSQL
Server: db
Username: postgres
Password: postgres
Database: memoires-info
```
### Postgrest
Postg rest is an api used to push and pull data from the database\
get request can be made to pull data from the database and post request can be made to push data to the database\
You can look at the database's content by going to `localhost:3000/data` in your web browser

## Esp32
### Installation
This project uses esp home to manage the esp32 wich is run in a docker container\
Inside the config folder, start by copying the `secret.example.yaml` file to `secret.yaml` and fill in credentials\
Then to start the esp32, you can run the following command
```bash
cd esp32
docker-compose up
```
### Home assistant
You can access home assistant by going to `localhost:6052` in your web browser
### Add esp32 to home assistant
Tou can then add the esp32 to home assistant by pluging the esp32 to your computer that runs the docker\
Then press the `+ ADD DEVICE` button inside home assistant\
Then press continue and name your device, give him a clever name so you know wich device it is\
Then press `next` and select `ESP32` (on the top of the list)\
Once that's done you want to skip the installation you'll have to reinstall it later anyway\
Go to the card of the device and press `EDIT`\
Here you want to copy everything from the line 31 of the `esp32/esp32.yaml` and paste it at the end of your esp32's configuration file\
Once that's done, go to [jwt.io](https://jwt.io/) to generate a token for the esp32 with your jwt secret and the following payload
```json
{
  "role": "esp32",
  "ip": "the ip address of the esp32"
}
```
Then copy the token and paste it on line 123 instead of the line of `xxxx`\
Then press `INSTALL` and select `Plug into the computer running ESPHome Dashboard` (the 3rd option)\
You then want to look for the device for wich the path is `/dev/ttyUSB0` and select it\
Now the installation should start and you should see the logs of the installation\
It can take a few minutes to compile and install the firmware, be patient\
Once the installation is done, you can press `STOP` to exit the logs\
You can now unplug the esp32 the computer and plug it to any other power source\
If you need to change the configuration of the esp32, you can now do it wirelessly from the home assistant interface

## Web Interface
### Installation
This project uses a react web interface to display the data from the database\
To start the web interface, you can run the following command

```bash
cd interface
npm install
npm run dev
```
## Access
You can then access the web interface by going to `localhost:5174` in your web browser
![web interface](/assets/dashboard.png)
