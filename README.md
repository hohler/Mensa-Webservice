Mensa-Unibe-Webservice
======================

Mensa-Unibe-Webservice is a simple JSON/REST API for receiving the daily meals of the canteens at the University of Bern.

Unfortunately there is no official API or a nice way to obtain these data in an appropriate exchange format such as xml odr json.
That's the reason why I started this small project called Mensa-Unibe-Webservice!

###How does it work
Mensa-Unibe-Webservice is based on the newsletter service [mensaunibe.zfv.ch](http://mensaunibe.zfv.ch/ "") which sends every day at 5:00 am, an email to the subscried email addresses.
Using this newsletter service the data of the daily meals are parsed and stored on the remote service Mensa-Unibe-Webservice.
This web service provides the stored daily meal plans in the JSON format.

###Project
The project consists of three parts:

- Backend:  		pdf menuplan parser written in ruby
- Frontend: 		Mensa-Unibe-Webservice written in php
- Mobile-Clients:	Android / Iphone App

###Web service API
The main address of the Mensa-Unibe-Webservice is http://mensa.xonix.ch/.
Each request is performed using a HTTP GET request.

API version 1 supports these kinds of get requests:

- List of all canteens
- Daily menuplan
- Weekly menuplan


####List of all canteens
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
        "name":"Mensa Gesellschaftsstrasse",
        "street":"Gesellschaftsstrasse 2",
        "plz":"3012 Bern",
        "lat":"46.9518",
        "lon":"7.43835"
      },
      {
        "id":"2",
        "name":"Mensa Unitobler",
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
The content attribute contains a list of canteen objects.

By means of this response message you get all valid canteens id's which are needed for further requests.

####Get current daily menuplan
Using a valid canteen id you can get the current daily meal plan by the following URI:
```
http://mensa.xonix.ch/v1/mensa/{id}/dailyplan
```
An example is the following request:
```
http://mensa.xonix.ch/v1/mensas/1/dailyplan
```

The response is as follows:
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
The response contains the daily meal plan object in the content attribute. 
In the attribute menus we have a list of all menu objects. Besides that the mensa attribute stores the name of the canteen and the date attribute the date of the meal plan.

If the daily meal plan information is not available you'll get the following response:
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
In this case the attribute code is set to the number 404. 
The message attribute msg describes the error in a more human readable way.

####Get daily menuplan on a specific date
The date format is ISO 8601. E.g.: 2013-12-24
```
http://mensa.xonix.ch/v1/mensas/{id}/dailyplan/{date}
```
####Get current weekly menuplan
```
http://mensa.xonix.ch/v1/mensas/1/weeklyplan
```
Example response:
```
{
    "result": {
        "content": {
            "mensa": "Mensa Gesellschaftsstrasse Mittag",
            "week": "30",
            "menus": {
                "Monday": [
                    {
                        "title": "Men\u00fc",
                        "date": "2013-07-29",
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
                        "date": "2013-07-29",
                        "menu": [
                            "Quorngeschnetzeltes",
                            "mit Champignons",
                            "M\u00fcscheli",
                            "Sommergem\u00fcse"
                        ],
                        "price": "CHF 6.60 \/ 12.60"
                    }
                ],
                "Tuesday": [
                    {
                        "title": "Men\u00fc",
                        "date": "2013-07-30",
                        "menu": [
                            "Rindshamburger",
                            "an Majoransauce",
                            "Pilav-Reis mit Gem\u00fcse und",
                            "Kr\u00e4utern",
                            "Fleisch: Schweiz"
                        ],
                        "price": "CHF 6.90 \/ 12.60"
                    },
                    {
                        "title": "Vegimen\u00fc",
                        "date": "2013-07-30",
                        "menu": [
                            "BIO-Gem\u00fcseburger",
                            "an Majoransauce",
                            "Pilav-Reis mit Gem\u00fcse und",
                            "Kr\u00e4utern"
                        ],
                        "price": "CHF 6.60 \/ 12.60"
                    }
                ],
                "Wednesday": [
                    {
                        "title": "Men\u00fc",
                        "date": "2013-07-31",
                        "menu": [
                            "Mariniertes Schweinssteak",
                            "Barbecue-Sauce",
                            "Pommes frites",
                            "Kohlrabi und Broccoli",
                            "Fleisch: Schweiz"
                        ],
                        "price": "CHF 6.90 \/ 12.60"
                    },
                    {
                        "title": "Vegimen\u00fc",
                        "date": "2013-07-31",
                        "menu": [
                            "Paniertes Gem\u00fcseschnitzel",
                            "Barbecue-Sauce",
                            "Pommes frites",
                            "Kohlrabi und Broccoli"
                        ],
                        "price": "CHF 6.60 \/ 12.60"
                    }
                ]
            }
        },
        "code": 200,
        "msg": "OK"
    }
}
```

####Get daily menuplan on a specific day
Valid values for the placeholder {day} are: monday,tuesday,wednesday,...
```
http://mensa.xonix.ch/v1/mensas/1/weeklyplan/{day}
```
Example response:
```
{
    "result": {
        "content": {
            "mensa": "Mensa Gesellschaftsstrasse Mittag",
            "week": "30",
            "menus": [
                [
                    {
                        "title": "Men\u00fc",
                        "date": "2013-07-29",
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
                        "date": "2013-07-29",
                        "menu": [
                            "Quorngeschnetzeltes",
                            "mit Champignons",
                            "M\u00fcscheli",
                            "Sommergem\u00fcse"
                        ],
                        "price": "CHF 6.60 \/ 12.60"
                    }
                ]
            ]
        },
        "code": 200,
        "msg": "OK"
    }
}
```

