## Simple PHP Validator

This is my version of a simple php validator class.

### Install
``` 
composer require tyurderi/validator
```

### Usage
``` php
$v = new Validator\Validator();

$v->addRule('unique_username', function($fields, $value, $params) {
    // logic goes here
    return true;
});

$v->add('username', 'tyurderi', 'required|min:3|max:30', array(
    'required'       => 'The username is required.',
    'min'            => 'The username should be at least 3 characters long.',
    'max'            => 'The username should be at most 30 characters long.',
    'uniqe_username' => 'The username is already in use.'
));

$v->validate();

if ($v->passes())
{
    echo 'Validation was successfully!';
}
else
{
    echo 'Validation failed.', PHP_EOL;
    foreach ($v->errors() as $message)
    {
        echo $message, PHP_EOL;
    }
 }

```

### Available rules

##### required
The value must be not empty.

##### in:1,2,3
The value should equals 1, 2 or 3.

##### email
The value should be a valid email address.

##### notin:1, 2, 3
The value should not equals 1, 2 or 3.

##### min:3
The value should be at least 3 characters long.

##### max:30
The value should be at most 30 characters long.

##### len:30
The value should be exact 30 characters long.

##### min_value:3
The value should equal or greater than 3.

##### max_value:30
The value should equal or smaller than 30.

##### matches:anotherField
The value should equal with the value of another registered field.

##### is:someValue
The value should equals the value at parameter 1. (someValue)

##### min_words:3
The value should contain at least 3 words.

### License
MIT