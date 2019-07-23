
## TODO

- Unit tests on all major classes

Things to solve:

- Do we want to sync transactions that don't validate?
    (implied, do we want to validate transactions before syncing?)
- (Yes!) Do we want transactions to have another set of operations for validating the results of the transaction?

### Tests

* Setup: `./tests/bin/install-wp-tests.sh`
* Run: `composer test`

# Lark

Lark is a WordPress plugin that allows developers to define complex functionality as YAML files.

## Concepts

There are two main concepts to understand when working with this plugin; **Operations** and **Transactions**.

### Operations

An Operation is a single piece of functionality that can be performed. You can think of this 
as a callback, consisting of a function and its parameters. In actuality, an Operation is a 
PHP class that implements the `OperationInterface`.

Theoretically, a simple Operation looks something like this:

```php
class MyOperation implements OperationInterface {

    public function execute( $details ) {
        // do something with the $details array...
    }
} 
```

See [ExampleOperation.php](examples/ExampleOperation.php) for a much more accurate depiction 
of a basic Operation.

### Transactions

A Transaction a set of defined operations that includes some descriptive metadata. You can 
think of it as instructions for performing tasks on your site. Transactions are defined as 
YAML files and contain some specific properties.

Simple Transaction example:

```yaml
id: 'MyTransaction'
title: 'An Example Transaction with a simple process'
process:
  - operation: post_insert
    post:
      post_title: 'A new post!!!'
      post_type: page
      post_status: publish
```

When a Transaction is executed, Lark will loop through the `process:` array and perform
each operation.

See [Example Transactions](examples) for many more examples.

## Hooks

Lark provides few, but important hooks. Primary are those used to discover your custom
transaction YAML files and your custom operations.

### Filter: `lark/transaction-locations`

Add to or alter the array of filesystem directories where Lark will look for transactions. 

```php
add_filter( 'lark/transaction-locations', function( $locations ) {
	$locations[] = __DIR__ . '/transactions';
	return $locations;
} );
```

### Filter: `lark/operation-locations`

Add to or alter the array of filesystem directories where Lark will look for operations.

Each item in the array should be keyed on a unique namespace, and the value is the base
directory where Lark will begin looking for Operations. Alternatively if you are not using
PSR-4, you can simply append a new directory to the locations array.

**In all cases, the filename for the Operation class must match the class name.**

#### PSR-4 Example

This example follows the PSR-4 autoloading standard.


**Operation location:** `my-plugin/src/Operation/SomeFolder/MyOperation.php`

```php
namespace MyNamespace\Operation\SomeFolder;

use Lark\Operation\OperationBase;

class MyOperation extends OperationBase {}
```

**Filter implementation:**

```php
add_filter( 'lark/operation-locations', function ( $locations ) {
    $locations['MyNamespace\Operation'] = __DIR__ . '/src/Operation';
	return $locations;
} );
```

When the `OperationManager` discovers operations for this example, it will create the operation 
class names following roughly this pattern: `str_replace( directory, namespace, filepath );`.
Lark will understand the full class name to be: `\MyNamespace\Operation\SomeFolder\MyOperation()`

#### Non-PSR-4 Example:

This example loads any arbitrary class. 

**Note:** The filename should match the class name (without the `.php` extension).

**Operation location:** `my-plugin/my-operations/SomeOperation.php`

```php
use Lark\Operation\OperationBase;

class SomeOperation extends OperationBase {}
```

**Filter implementation:**

```php
add_filter( 'lark/operation-locations', function ( $locations ) {
    $locations[] = __DIR__ . '/my-operations';
	return $locations;
} );
```

When the `OperationManager` discovers operations for this example, it will create the operation 
class names following roughly this pattern: `str_replace( '.php', '', filename );`.
Lark will understand the full class name to be: `\SomeOperation()`

