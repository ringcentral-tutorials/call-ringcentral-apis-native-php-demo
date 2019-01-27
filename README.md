### Overview
How to authenticate and access RingCentral platform services using PHP native API.

### RingCentral Platform
RingCentral Platform is a rich RESTful API platform with more than 60 APIs for business communication includes advanced voice calls, chat messaging, SMS/MMS and Fax.


### Clone and Setup the project
```
$ git clone https://github.com/ringcentral-tutorials/call-ringcentral-apis-native-php-demo

$ cd call-ringcentral-apis-native-php-demo

$ curl -sS https://getcomposer.org/installer | php

$ php composer.phar install

$ cp environment/dotenv-sandbox environment/.env-sandbox

$ cp environment/dotenv-production environment/.env-production

```

### Create an app
* Create an application at [RingCentral Developer Portal](https://developer.ringcentral.com).
* Select `Server-only (No UI)` for the Platform type.
* Add the `ReadAccounts` and `SMS` permissions for the app.
* Copy the Client id and Client secret and add them to the `.env-[environment]` file.
```
RC_CLIENT_ID=
RC_CLIENT_SECRET=
```
* Add the account login credentials to the `.env-[environment]` file.
```
RC_USERNAME=
RC_PASSWORD=
RC_EXTENSION=
```
If you don't know how to create a RingCentral app. Click [https://developer.ringcentral.com/library/getting-started.html](here) for instructions.

### Run the demo
Set `ENVIRONMENT=sandbox` in the `.env` file to run in the sandbox environment.

Set `ENVIRONMENT=production` in the `.env` file to run in the production environment.

```
$ php demo.php
```

### RingCentral Developer Portal
To setup a free developer account, click [here](https://developer/ringcentral.com)
