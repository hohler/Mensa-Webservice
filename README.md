Mensa-Unibe-Webservice
======================

Mensa-Unibe-Webservice is a simple JSON/REST API for receiving the daily meals of the canteens at the university of bern.

Unfortunately there is no official API or a nice way to obtain these data in an appropriate exchange format such as xml odr json.
That's the reason why I started this small project called Mensa-Unibe-Webservice!

###How does it work
Mensa-Unibe-Webservice is based on the newsletter service [mensaunibe.zfv.ch](http://mensaunibe.zfv.ch/ "") which sends every day at 5:00 am, an email to the subscried email addresses.
Using this newsletter service the data of the daily meals are parsed and stored on the remote service Mensa-Unibe-Webservice.
This web service provides the stored daily meal plans in the JSON format.

##Where's the source code
At the moment the source code of this project is not public available, because the email parser is not mature yet.
The project consists of two parts:

- Backend:  Email parser written in python
- Frontend: Mensa-Unibe-Webservice written in php

As soon as possible I'll make the source code public.


###Web service API
The main address of the Mensa-Unibe-Webservice is http://mensa.xonix.ch/.
Each request is performed using a HTTP GET request.

Currently the API supports only two kinds of requests:

- Get all available canteens
- Get the current daily meal plan of a canteen with id {id}


####Get all available canteens
You can use the following URI to get all available canteens:
```
http://mensa.xonix.ch/mensas
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
    "msg":"ok",
    "ok":"true"
  }
}
```
The content attribute contains a list of canteen objects.

By means of this response message you get all valid canteens id's which are needed for further requests.

####Get the current daily meal plan of a canteen with id {id}
Using a valid canteen id you can get the current daily meal plan by the following URI:
```
http://mensa.xonix.ch/mensa/{id}/plan
```
An example is the following request:
```
http://mensa.xonix.ch/mensa/1/plan
```

The response is as follows:
```
{
  "result":{
    "content":{
      "date":"2013-06-24",
      "menus":[
        {
          "menu":[
            "Rindsgeschnetzeltes",
            "an gr\u00fcner Pfeffersauce",
            "neue Bratkartoffeln im Rock",
            "Zucchetti",
            "Fleisch: Schweiz ",
            "CHF 6.90 \/ 12.60"
          ],
          "title":"Men\u00fc"
        },
        {
          "menu":[
            "VEGI+",
            "Gebratene Tofuscheiben",
            "an roter Currysauce",
            "neue Bratkartoffeln im Rock",
            "Fenchelgem\u00fcse",
            "CHF 6.60 \/ 12.60"
          ],
          "title":"Vegimen\u00fc"
        },
        {
          "menu":[
            "Sautierte Crevetten",
            "mit Knoblauch-Mayonnaise",
            "Reis",
            "Tagesgem\u00fcse",
            "Crevetten: Vietnam\/aus nachhaltiger Fischerei",
            "CHF 9.90"
          ],
          "title":"Daily Special"
        },
        {
          "menu":[
            "F\u00fcr alle, die gerne nach Lust und Laune w\u00e4hlen...",
            "CHF 2.20 \/ 2.40 pro 100g"
          ],
          "title":"Salatbuffet \/ Free choice"
        },
        {
          "menu":[
            "Das Men\u00fc mit der Legi bezahlen und jedes Mal 10 Rappen sparen!",
            "Men\u00fc CHF 6.80 (statt CHF 6.90)",
            "Vegimen\u00fc CHF 6.50 (statt CHF 6.60)"
          ],
          "title":"News"
        }
      ],
      "mensa":"Mensa Gesellschaftsstrasse"
    },
    "msg":"ok",
    "ok":"true"
  }
}
```
The response contains the daily meal plan object in the content attribute. 
In the attribute menus we have a list of all menu objects. Besides that the mensa attribute stores the name of the canteen and the date attribute the date of the meal plan.

If the daily meal plan information is not available you'll get the following response:
```
{
  "result":{
    "content":"",
    "msg":"not found",
    "ok":"false"
  }
}
```
In this case the attribute ok is set to the string "false" and the content attribute is an empty string. 
The message attribute msg describes the error in more human readable way.
