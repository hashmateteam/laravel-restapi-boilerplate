## About improvement-branch
Working on exceptions in JSON and other stuff related to generating response

## About Laravel-RESTAPI-Boilerplate

Laravel-RESTAPI-Boilerplate is the repository for developers to easily create their project with pre-installed Laravel-Passport authenication for default class "User" that laravel makes on first intialization of any new project.
This boilerplate has "UserController" that contains two sample function that use to register / login the user which is named as :
- **[authup]** -for register
- **[authin]** -for login


## About Laravel-Passport

Laravel already makes it easy to perform authentication via traditional login forms, but what about APIs? APIs typically use tokens to authenticate users and do not maintain session state between requests. Laravel makes API authentication a breeze using Laravel Passport, which provides a full OAuth2 server implementation for your Laravel application in a matter of minutes. Passport is built on top of the League OAuth2 server that is maintained by Andy Millington and Simon Hamp.

## Installation

1. git clone https://github.com/hashmateteam/laravel-restapi-boilerplate.git
2. cd laravel-restapi-boilerplate
3. make .env file and copy .env.example content to .env file
4. set your database credentials in .env file
5. composer i
6. php artisan key:generate
7. php artisan migrate:fresh
8. php artisan passport:install
9. php artisan serve

ENJOY !!

## Usage
By Default we configured three methods that are as follows:

1. http://127.0.0.1:8000/api/authup || {APP_URL}/api/authup
   with params {name:'ANYNAME',email:'ANYEMAIL@PROVIDER.TLS',password:'ANYPASSWORD',c_password:'ANYPASSWORD'}
   <br>
It will give json response similar to :
    <br>
   {
    "success": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiODQ3YjIzZDBmYmFmZWFlOTliZjhjZTdhMzM5MGYzMzI2MTc0YzYxOGFiY2Y5MjY4NTVhOWY3MTdiYzBiMDFjMWM1OTNhOWU5Yjc2ZDcxODkiLCJpYXQiOjE1OTE3MTkxNjQsIm5iZiI6MTU5MTcxOTE2NCwiZXhwIjoxNjIzMjU1MTY0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.w3tJj_TwnepsVBtYuDSw8eUhEW5OaIhzYQmIKAn0-AQPXukuAIewa9ODpkGvSUg07rKFnivUN4BPkN79H4VR5ltdrVe4w_uMPHoz-oRNecXsyYjAiSDiGei4b4NqGhc-8R4paNDNzEsBue6GYCD6Ph1Dd3l6lmGzbA9VBH2Mwd7r1-abmmdHG5TTtHpTI9xOw3iE6z5lWDDS3-Q47JuMZuEd1T9Djs6n0Ga7Nnp6zmeNM70dg-uEe4m8fomsGYNGTR_JaURhFVmSW3E_cMKhWNuPuNXB0DrazMGwybp115xjIXk-RNoyZu910RPwWOEqdkwjoK2FjFSMb7nVCarte1TNsvgOyZ8r8gGR5fcv3QWFxET8WPex663VGJivu3AGl4l1Zyy1IZIzWqgt7B6-7wChcG70VBw3FFHmUzHqeXEmnSlcQxfH7a2jBGO5iugx0sS-3CAjiZdVHpFyLMj38JL_d5NAgarfu00SSy2X7o7GXwd6OY3G32cvj7ugNzy_4PqQ0jIFWWXbHOiyYhMyT6rYrtbXsjc-ca59S8C_rheX53GlfIBm7c9pyu3Z_p1BLyQE2H9lLw-Ao6sl4yfOfc4tuqi6dr0jBHBCvCPPjkMAA9VUVDHa1hOt2LlYpiv0KHsE3DT0Pz-mcuFYsuPurCdwljEs47blxfUGxy70-rw",
        "name": "Bilal Punjabi"
        }
    }
    <br>
SAVE THIS TOKEN THAT RECIEVED TO PERFORM OTHER AUTHENTICATED OPERATIONS

2. http://127.0.0.1:8000/api/authin || {APP_URL}/api/authin
   with params {email:'ANYEMAIL@PROVIDER.TLS',password:'ANYPASSWORD'}
<br>
it will give json response similar to :
<br>
    {
    "success": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZjhjY2MwNDM0YzY1NDNiZTljYzVhZmU2MTFmZTJlMDMxNTM1NTg2MWViNjE0YTIzN2RhZDEwZTkyMjM4NzMxZTYxNjdkODkzMjI2NGI5ZDEiLCJpYXQiOjE1OTE3MTk3NTAsIm5iZiI6MTU5MTcxOTc1MCwiZXhwIjoxNjIzMjU1NzQ5LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.uUqgPfnjFgzp2iJFr-SWam1EBoBFnqHoDpdLvg-B0WUrv6R5dU6lp-G1X4nJkMgaN0Lhq1Cl1rv5QJB3NAh9UcV7eKL8GHP0e0kcIGUjr1ckufkJhkr6cdNBapbscfVHKtISVHqM9ELtovCska5gw2WdxsPOSeGJeQze-rn9U_2r_yGkX4QOV9oywL0IyC2KKiu8VHkkIrjNpSedn32f4boIxg2GD-Z-Ofy0pzAtxllaNv87J08NMhO9jLbJ_vN-AuPmETC2OYr2KXEIxvMtHKyS78o5yxNQ4k-u6ix8B5DOGonhjMt6cCD-dXDVamqCDAelje6NmePAz6H5Esys0TINb5Y_dojDgl1Y7I8Wpl3LCsGXZHZzK9E2cwQdLdhID_yxzQ7CAu4M3lTpVdAdHwAqhmbxdoYhZ3UzHv_oz17BvaA2sbsFcN-ovC9cqGKldrx_uhefYvDXMskcyMZtWwvJ3WK0tZDHvE2730VjlLs4UOT6nZNRwlOrThOUzXtVEGhhnqdQrb2_9LCEoLoKtrrF9ARrncfRke8jkJET75IC1FWF4dgAeYePYmoa9JXgTLe_R0Xcry5WejyP485_84XfSTw-Hdw0jCTpi_YQufDoHd7lwnCwJn9FF9OskjaA3DQ3ffex4_vbke2Zave1YA6smdmtcH8bCBYvt-1pHfo"
        }
    }
<br>
SAVE THIS TOKEN THAT RECIEVED TO PERFORM OTHER AUTHENTICATED OPERATIONS

Still remember that AUTHENTICATED OPERATIONS we have one for you named "any"

3. http://127.0.0.1:8000/api/any || {APP_URL}/api/any
<br>   with headers as follows :
<br>   Authorization : Bearer[space]TOKEN_THAT_YOU_SAVED
<br>   Content-Type  : application/x-www-form-urlencoded
<br>   Accept        : application/json

<br>
YOU WILL RECEIVE DATA IF TOKEN IS VALID

THANKS...!!!
## Contributing

Thank you for considering contributing to this Boilerplate! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).


## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to hashmateteam via [hashmateteam@gmail.com](mailto:hashmateteam@gmail.com). All security vulnerabilities will be promptly addressed.

## License

This boilerplate is being developed on Laravel framework which is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).