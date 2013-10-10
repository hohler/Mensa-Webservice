Mensa-Unibe-Webservice
======================

Mensa-Unibe-Webservice is a simple JSON/REST API for receiving the daily meals of the canteens at the University of Bern.

Unfortunately there is no official API or a nice way to obtain these data in an appropriate exchange format such as XML or JSON.
That's the reason why I started this small project called Mensa-Unibe-Webservice!

###Project
The project consists of three parts:

- Backend:  		[pdf menu plan reader](https://github.com/lexruee/Mensa-Unibe-MPC) [ruby]
- Frontend: 		Mensa-Unibe-Webservice [php]
- Mobile-Clients:	[Android](https://github.com/lexruee/Mensa-Unibe-Android-App) / IPhone App

###Web service API
The base address of the Mensa-Unibe-Webservice is http://mensa.xonix.ch/.
Each request is performed using a HTTP GET request.

API version 1 supports four basic get requests:

- List all canteens
- Get daily menu plan
- Get weekly menu plan
- Get update status

###API token
For each request an API token must be appened to the base url. For more details send me an email:
a.rueedlinger AT gmail.com

Example:
```
http://mensa.xonix.ch/v1/mensas?tok=YOUR_TOKEN
```

###Versioning
For each request the API version must be specified. The current API version is v1.
The version must be appened to the base uri.
E.g.:
```
http://mensa.xonix.ch/v1/ path etc.
```

###JSONP support
You can receive JSONP messages by appending a callback parameter on each request:
```
?callback=CALLBACK_NAME
```
Example:
```
http://mensa.xonix.ch/v1/mensas?callback=CALLBACK_NAME
```

The callback parameter specifies the name of the callback function.

You'll receive the original JSON message  wrapped in a callback function:
```
CALLBACK_NAME( json message );
```

####List all canteens
You can use the following URI to get all available canteens:
```
http://mensa.xonix.ch/v1/mensas
```

This will give you the following HTTP response which contains:
```
{
  "result":{
    "content":[
      {
        "id":"1",
        "mensa":"Mensa Gesellschaftsstrasse",
        "street":"Gesellschaftsstrasse 2",
        "plz":"3012 Bern",
        "lat":"46.9518",
        "lon":"7.43835"
      },
      {
        "id":"2",
        "mensa":"Mensa Unitobler",
        "street":"Lerchenweg \/ L\u00e4nggassstrasse",
        "plz":"3012 Bern",
        "lat":"46.9544",
        "lon":"7.43046"
      }
    ],
    "msg":"OK",
    "code":200
  }
}
```
The content attribute contains a list of canteens.

By means of this response message you get all valid canteens id's which are needed for further requests.

####Get current daily menu plan
Using a valid canteen id you can get the current daily menu plan by the following URI:
```
http://mensa.xonix.ch/v1/mensa/{id}/dailyplan
```
An example is the following request:
```
http://mensa.xonix.ch/v1/mensas/1/dailyplan
```

Response:
```
{
    "result": {
        "content": {
            "mensa": "Mensa Gesellschaftsstrasse Mittag",
            "date": "2013-07-29",
            "menus": [
                {
                    "title": "Men\u00fc",
                    "menu": [
                        "Schweinsgeschnetzeltes",
                        "mit Champignons",
                        "M\u00fcscheli",
                        "Sommergem\u00fcse",
                        "Fleisch: Schweiz"
                    ],
                    "price": "CHF 6.90 \/ 12.60"
                },
                {
                    "title": "Vegimen\u00fc",
                    "menu": [
                        "Quorngeschnetzeltes",
                        "mit Champignons",
                        "M\u00fcscheli",
                        "Sommergem\u00fcse"
                    ],
                    "price": "CHF 6.60 \/ 12.60"
                }
            ]
        },
        "code": 200,
        "msg": "OK"
    }
}
```
The response contains the daily menu plan in the content attribute. 
In the attribute menus is a list of all available menus.

If the daily menu plan is not available you'll receive the following response message:
```
{
    "result": {
        "content": {
            "menus": [

            ]
        },
        "code": 404,
        "msg": "Not Found"
    }
}
```
In this case the attribute code is set to the number 404 (see http codes). 
The message attribute msg describes the error.

####Get daily menuplan on a specific date
The date format is ISO 8601. E.g.: 2013-12-24
```
http://mensa.xonix.ch/v1/mensas/{id}/dailyplan/{date}
```
####Get current weekly menu plan
```
http://mensa.xonix.ch/v1/mensas/1/weeklyplan
```
Example response:
```
{
  "result":{
    "content":{
      "mensa":"Mensa Gesellschaftsstrasse",
      "week":"30",
      "menus":[
        {
          "title":"Men\u00fc",
          "date":"2013-07-29",
          "day":"Monday",
          "menu":[
            "Schweinsgeschnetzeltes",
            "mit Champignons",
            "M\u00fcscheli",
            "Sommergem\u00fcse",
            "Fleisch: Schweiz",
            "CHF 6.90 \/ 12.60"
          ]
        },
        {
          "title":"Men\u00fc",
          "date":"2013-07-30",
          "day":"Tuesday",
          "menu":[
            "Rindshamburger",
            "an Majoransauce",
            "Pilav-Reis mit Gem\u00fcse und",
            "Kr\u00e4utern",
            "Fleisch: Schweiz",
            "CHF 6.90 \/ 12.60"
          ]
        },
        {
          "title":"Men\u00fc",
          "date":"2013-07-31",
          "day":"Wednesday",
          "menu":[
            "Mariniertes Schweinssteak",
            "Barbecue-Sauce",
            "Pommes frites",
            "Kohlrabi und Broccoli",
            "Fleisch: Schweiz",
            "CHF 6.90 \/ 12.60"
          ]
        },
        {
          "title":"Vegimen\u00fc",
          "date":"2013-07-29",
          "day":"Monday",
          "menu":[
            "Quorngeschnetzeltes",
            "mit Champignons",
            "M\u00fcscheli",
            "Sommergem\u00fcse",
            "CHF 6.60 \/ 12.60"
          ]
        },
        {
          "title":"Vegimen\u00fc",
          "date":"2013-07-30",
          "day":"Tuesday",
          "menu":[
            "BIO-Gem\u00fcseburger",
            "an Majoransauce",
            "Pilav-Reis mit Gem\u00fcse und",
            "Kr\u00e4utern",
            "CHF 6.60 \/ 12.60"
          ]
        },
        {
          "title":"Vegimen\u00fc",
          "date":"2013-07-31",
          "day":"Wednesday",
          "menu":[
            "Paniertes Gem\u00fcseschnitzel",
            "Barbecue-Sauce",
            "Pommes frites",
            "Kohlrabi und Broccoli",
            "CHF 6.60 \/ 12.60"
          ]
        }
      ]
    },
    "code":200,
    "msg":"OK"
  }
}
```

####Get daily menu plan on a specific day
Valid values for the placeholder {day} are: monday,tuesday,wednesday,...
```
http://mensa.xonix.ch/v1/mensas/1/weeklyplan/{day}
```
Example request:
```
http://mensa.xonix.ch/v1/mensas/1/weeklyplan/monday
```
Example response:
```
{
  "result":{
    "content":{
      "mensa":"Mensa Gesellschaftsstrasse",
      "week":"30",
      "menus":[
        {
          "title":"Men\u00fc",
          "date":"2013-07-29",
          "day":"Monday",
          "menu":[
            "Schweinsgeschnetzeltes",
            "mit Champignons",
            "M\u00fcscheli",
            "Sommergem\u00fcse",
            "Fleisch: Schweiz",
            "CHF 6.90 \/ 12.60"
          ]
        },
        {
          "title":"Vegimen\u00fc",
          "date":"2013-07-29",
          "day":"Monday",
          "menu":[
            "Quorngeschnetzeltes",
            "mit Champignons",
            "M\u00fcscheli",
            "Sommergem\u00fcse",
            "CHF 6.60 \/ 12.60"
          ]
        }
      ]
    },
    "code":200,
    "msg":"OK"
  }
}
```
####Get update status
You can use the following uri to receive update status for all canteens.
```
http://mensa.xonix.ch/v1/mensas/updates
```

```
{
  "result":{
    "content":[
      {
        "id":"1",
        "mensa":"Mensa Gesellschaftsstrasse",
        "timestamp":1377477715,
        "datetime":"2013-08-26 02:41:55"
      },
      {
        "id":"2",
        "mensa":"Mensa Unitobler",
        "timestamp":1377477712,
        "datetime":"2013-08-26 02:41:52"
      },
      {
        "id":"3",
        "mensa":"Cafeteria Maximum",
        "timestamp":1377477712,
        "datetime":"2013-08-26 02:41:52"
      },
      {
        "id":"4",
        "mensa":"UNIESS - Bar Lounge",
        "timestamp":1377477712,
        "datetime":"2013-08-26 02:41:52"
      },
      {
        "id":"5",
        "mensa":"UNIESS - Bistro",
        "timestamp":1377477712,
        "datetime":"2013-08-26 02:41:52"
      }
    ],
    "code":200,
    "msg":"OK"
  }
}
```

#Copyright, license and usage
This software is written by Alexander Rüedlinger. If you want to use this service in a project then please let me know.

In order to use the service you need an API token! Email: a.rueedlinger AT gmail.com

License: Creative Commons [CC BY-NC 3.0](http://creativecommons.org/licenses/by-nc/3.0/deed.en_US)

<img src="http://i.creativecommons.org/l/by-nc/3.0/88x31.png" />

