# Recruitment-test
Task for a job position
Stand alone browser based app for tracking remote and on-site employee

# Requirements
- PHP 8 and activate the pdo extension (shoud be by default)
- MySQL 8
- [Composer](https://getcomposer.org/download/)
- For local environement, a **virtual host is required** for the autoloading / routing to work properly. The domain must be a simple url like `mydomain.com` (not something like localhost/path/to/project)

# Installation
After cloning the project and arrange a virtual host:

- `composer install`

- Rename `.env.example` to `.env` and edit the environement vars.

- To create the database run `php ./scripts/create_database.php` or simply create a database manually and import the [create_datase.sql](https://github.com/MikaBob/blexr/blob/main/scripts/create_database.php) file into.

Two accounts are created:
| Username |  Password
|:-----|:--------:|
| `admin@blexr.com` | admin |
| `user@blexr.com` | user |

# Tests
To run the phpunit test, we have to specify the bootstrap file:
`./vendor/bin/phpunit --bootstrap .\Autoloader.php src/Tests`
. Until now only User* class are tested
# Detailed explanation
## General idea
### Summary
At Blexr we’ve hit a road bump: we’re finding it difficult to keep track of remote and on-site
employees, and we need a stand-alone, browser-based app to handle this.
### Main Features
 - **Adding new users:** When new employees are hired, the administrator must have a screen to add
new employees. An email should be sent out with the employee's log in details and random
password.
 - **Onboarding tasks completed:** Each employee is to have a set of checkboxes which the
administrator is to tick off when they have been completed. These tick boxes should include:
"Microsoft Office License", "Email Access Granted", "Git Repository Granted", "Jira Access
Granted". These are just to keep track of what licenses have been allocated to the employee.

 - **User work-from-home request:** Once registered, a user can request or cancel a request to work
from home on specific dates. Basic authentication should be implemented so that only registered
users can make a request.
A work-from-home request can be made according to the following rules:
    - A request must be made at least 4 hours before the end of the previous day;
    -  Work-from-home can be booked by number of hours, rather than a full work-day.

 - **Request approval:** The administrator requires a screen to be able to see the requests, filter them,
approve or reject them. The employee should be notified on status change of a request.

## Bootstrap and Routing
As explicitly setup in [.htaccess](https://github.com/MikaBob/blexr/blob/main/.htaccess#L4), every request will go throught **entrypoint.php** except for files in public_html.

The two first lines of entrypoint.php will require the autoloaders to get all the require files on the go. The autoloader for the project has one task: rename the namespace to start at the actual root-folder.

```php
// Autoloader.php

spl_autoload_register(function ($fullQualifiedClassName) {
    $parts = explode('\\', $fullQualifiedClassName);
    // remove root folder
    unset($parts[0]);
    $classPath = implode("\\", $parts);

    // concat proper fodler (/src)
    include_once __DIR__ . "\\src\\$classPath.php";
});
```
In this case, the namespace is \Blexr which mean we will have a Full Qualified Name (FQN) controller called `\Blexr\Controller\IndexController` but the actual file is in `/src/Controller/IndexController.php`. So the autoloader replace \Blexr with /src to find the file and follow the same path from there.

Example:
`\Blexr\Some\Path\To\FQNController` is rename as `\src\Some\Path\To\FQNController`


After checking up the config in the .env file, the Router take it from here and parse the url `/Controller/Action/param1/param2/...`

If the Controller and his action exist, the router will print the controller's response. If the Controler or Action is not specified, **AuthenticationControler** and **Index** are taken as default. Otherwise we will have a redirection to a 404.

## Authentication & Authorization
Except for user's account creation, all request can be done through the API, which involve an access token. For this project a [JSON Web Token](https://en.wikipedia.org/wiki/JSON_Web_Token) (JWT) solution is generated (after a successfull log in) and stored as a cookie for 15min. The JWT contains the user id and an expiration date (Extra payload on "top" of the secret data).

For now, to check if a user as enough right to access, the AuthenticationController simply check if the user is **THE** admin. But implementing a real role and rights system is on the todo list ;). Basically only an admin can access the User administration, otherwise they are not any restriction for pages.

## Database
It only contains 2 tables. Request and User which really have a classic structure except the **dynamicFields** for User.
This is a string formated in JSON and every "field" has a small identifier. For example "Microsoft Office License" will be reduce to `MOL` and its value is true or false.

```javascript
// Example of dynamicFields content
{"MOL":true,"EAG":false,"GRG":true,"JAG":false}
 ```

The idea is to not have an extra column for a simple value (boolean, int, datetime, string). But this field can be temporary, and we might have severals of them (3 today, 7 next year, 4 the next one), and such solution is a bit heavier (in bytes) than having a specific column for each field but it is easier to maintain.

Plus these are plain informations, it will not be use for any operation nor predicat (Like `SELECT [...] WHERE MOL = false` will not be needed)

## Model
Folowing the **dynamicFields** explanation, let us continue with its implementation in the user entity.

The [User](https://github.com/MikaBob/blexr/blob/main/src/Model/Entity/User.php#L7) entity actually extends the abstract class UserAbstract which hold the getters and setters of each dynamic fields.

Here is a code snippet with only one dynamic field *Microsoft Office license*

```php
// src/Model/Entity/User.php

class User extends UserAbstract {

    [...]

    /**
     * @var array|null
     */
    private $dynamicFields;

    [...]

    function getDynamicFields(): ?array {
        return $this->dynamicFields;
    }

    function setDynamicFields(?array $dynamicFields): void {
        $this->dynamicFields = $dynamicFields;
    }
}
```
The Entity only contain a simple getter / setter for DynamicFields
```php
// src/Model/Entity/UserAbstract.php

abstract class UserAbstract {

    const DYNAMIC_FIELD_MICROSOFT_OFFICE_LICENSE = 'MOL';

    public function __construct() {
        $this->setDynamicFieldMicrosoftOffice(false);
    }

    public function getDynamicFieldMicrosoftOffice() {
        $dynamicFields = $this->getDynamicFields() ?: [];
        return isset($dynamicFields[self::DYNAMIC_FIELD_MICROSOFT_OFFICE_LICENSE]) ? $dynamicFields[self::DYNAMIC_FIELD_MICROSOFT_OFFICE_LICENSE] : null;
    }

    public function setDynamicFieldMicrosoftOffice(bool $isGranted) {
        $dynamicFields = $this->getDynamicFields() ?: [];
        $dynamicFields[self::DYNAMIC_FIELD_MICROSOFT_OFFICE_LICENSE] = $isGranted;
        $this->setDynamicFields($dynamicFields);
    }
}
```
The abstract class has the method to edit the dynamicField. From here we can add/remove dynamic fields, easy to maintain and no need to update the database :)

## Views
First of all, the website is Responsive (thanks [Bootstrap](https://getbootstrap.com/)). You can play with the window size to see some columns disapear or button getting aligned.

[Twig](https://twig.symfony.com/) is an excellent template engine for php project. Easy to install, and to use. Anyway, to keep it simple there is have only 1 main template ([base.html.twig](https://github.com/MikaBob/blexr/tree/main/src/View)) which is extented by all the other, with only 3 blocks: title, content and javascript.

Block name are quite self explaining, every child just fill or complete each block.

There are 2 main pages. [User index](https://github.com/MikaBob/blexr/blob/main/src/View/User/index.html.twig) (admin only) and [Request index](https://github.com/MikaBob/blexr/blob/main/src/View/Request/index.html.twig). Both present the list of each entities in a table and both contain a modal window to edit or confirm some actions.

# Bugs & improvements
There are still somes bugs. Lack of time, not a priority, etc... you know the song :) for example spam clicking modal's buttons will send severals request before it the modal properly.

With more time, I would have improve the front end. Put more colors, format date in the table, separate dealed request (archive) on another table, make more code refactoring and unit test and some more ^^

Thank you for reading, I spend ~43h on it over 5 days <3
