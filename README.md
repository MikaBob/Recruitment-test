# Blexr
Stand alone browser based app for tracking remote and on-site employee

# Requirements
- PHP >=7.4 +  activate the pdo extension
- MySQL
- [Composer](https://getcomposer.org/download/)
- For local environement, a virtual host is required. The domain must be a simple url like mydomain.com (no localhost/path/to/project)

# Installation
After cloning the project and arrange a virtual host:

- `composer install`

- Rename `.env.example` to .env and edit the environement vars.

- To create the database run `php ./scripts/create_database.php` or simply create a database manually and import the create_datase.sql file into. (Admin's password is 'admin' by default)

# Tests
To run the phpunit test, we have to specify the bootstrap:
`./vendor/bin/phpunit --bootstrap .\Autoloader.php src/Tests`

# Explaination
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

## Database
For now it only contains one table (User) which really has a classic structure except the **dynamicFields** column.
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