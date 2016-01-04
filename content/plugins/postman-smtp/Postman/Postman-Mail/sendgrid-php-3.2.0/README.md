# SendGrid-PHP

This library allows you to quickly and easily send emails through SendGrid using PHP.

WARNING: This module was recently upgraded from [2.2.x](https://github.com/sendgrid/sendgrid-php/tree/v2.2.1) to 3.X. There were API breaking changes for various method names. See [usage](https://github.com/sendgrid/sendgrid-php#usage) for up to date method names.

## PLEASE READ THIS

**TLDR: If you upgrade and don't change your code appropriately, things *WILL* break.**

One of the most notable changes is how `addTo()` behaves. We are now using our Web API parameters instead of the X-SMTPAPI header. What this means is that if you call `addTo()` multiple times for an email, **ONE** email will be sent with each email address visible to everyone. To utilize the original behavior of having an individual personalized email sent to each recipient you must now use `addSmtpapiTo()`. **This will break substitutions if there is more than one To address added unless you update to use `addSmtpapiTo()`.** 

Smtpapi addressing methods cannot be mixed with non Smtpapi addressing methods. Meaning you cannot currently use Cc and Bcc with `addSmtpapiTo()`.

The `send()` method now raises a `\SendGrid\Exception` by default if the response code is not 200 and returns an instance of `\SendGrid\Response`.

---

Important: This library requires PHP 5.3 or higher.

[![BuildStatus](https://travis-ci.org/sendgrid/sendgrid-php.svg?branch=master)](https://travis-ci.org/sendgrid/sendgrid-php)
[![Latest Stable Version](https://poser.pugx.org/sendgrid/sendgrid/version.svg)](https://packagist.org/packages/sendgrid/sendgrid)

```php
$sendgrid = new SendGrid('username', 'password');
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ->setFrom('me@bar.com')
    ->setSubject('Subject goes here')
    ->setText('Hello World!')
    ->setHtml('<strong>Hello World!</strong>')
;

$sendgrid->send($email);

// Or catch the error

try {
	$sendgrid->send($email);
} catch(\SendGrid\Exception $e) {
	echo $e->getCode();
	foreach($e->getErrors() as $er) {
		echo $er;
	}
}
```

## Installation

Add SendGrid to your `composer.json` file. If you are not using [Composer](http://getcomposer.org), you should be. It's an excellent way to manage dependencies in your PHP application. 

```json
{  
  "require": {
    "sendgrid/sendgrid": "~3.2"
  }
}
```

Then at the top of your PHP script require the autoloader:

```bash
require 'vendor/autoload.php';
```

#### Alternative: Install from zip

If you are not using Composer, simply download and install the **[latest packaged release of the library as a zip](https://sendgrid-open-source.s3.amazonaws.com/sendgrid-php/sendgrid-php.zip)**. 

[**⬇︎ Download Packaged Library ⬇︎**](https://sendgrid-open-source.s3.amazonaws.com/sendgrid-php/sendgrid-php.zip)

Then require the library from package:

```php
require("path/to/sendgrid-php/sendgrid-php.php");
```

Previous versions of the library can be found in the [version index](https://sendgrid-open-source.s3.amazonaws.com/index.html).

## Example App

There is a [sendgrid-php-example app](https://github.com/sendgrid/sendgrid-php-example) to help jumpstart your development.

## Usage

To begin using this library, initialize the SendGrid object with your SendGrid credentials OR a SendGrid [API Key](https://sendgrid.com/docs/Classroom/Send/api_keys.html). API Key is the preferred method. To configure API keys, visit https://app.sendgrid.com/settings/api_keys.

```php
$sendgrid = new SendGrid('username', 'password');
// OR
$sendgrid = new SendGrid('sendgrid api key');
```

Create a new SendGrid Email object and add your message details.

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ->addTo('bar@foo.com')
    ->setFrom('me@bar.com')
    ->setSubject('Subject goes here')
    ->setText('Hello World!')
    ->setHtml('<strong>Hello World!</strong>')
;
```

Send it. 

```php
$sendgrid->send($email);
```

### Exceptions

A `SendGrid\Exception` is raised by default if the response is not 200 OK.

To disable exceptions, pass in the `raise_exceptions => false` option when creating a `SendGrid\Client`.

```php
$client = new SendGrid('SENDGRID_APIKEY', array('raise_exceptions' => false));
```

### Options
Options may be passed to the library when initializing the SendGrid object:

```php
$options = array(
    'turn_off_ssl_verification' => false,
    'protocol' => 'https',
    'host' => 'api.sendgrid.com',
    'endpoint' => '/api/mail.send.json',
    'port' => null,
    'url' => null,
    'raise_exceptions' => false
);
$sendgrid = new SendGrid('username', 'password', $options);
// OR
$sendgrid = new SendGrid('sendgrid api key', $options);
```

#### Changing URL
You may change the URL sendgrid-php uses to send email by supplying various parameters to `options`, all parameters are optional:

```php
$sendgrid = new SendGrid(
    'username', 
    'password', 
    array(
        'protocol' => 'http', 
        'host' => 'sendgrid.org', 
        'endpoint' => '/send', 
        'port' => '80' 
    )
);
// OR
$sendgrid = new SendGrid(
    'sendgrid_api_key', 
    array(
        'protocol' => 'http', 
        'host' => 'sendgrid.org', 
        'endpoint' => '/send', 
        'port' => '80' 
    )
);
```

A full URL may also be provided:

```php
$sendgrid = new SendGrid(
    'username', 
    'password', 
    array( 'url' => 'http://sendgrid.org:80/send')
);
// OR
$sendgrid = new SendGrid(
    'sendgrid_api_key', 
    array( 'url' => 'http://sendgrid.org:80/send')
);
```

#### Ignoring SSL certificate verification

You can optionally ignore verification of SSL certificate when using the Web API.

```php
$sendgrid = new SendGrid(
    'username', 
    'password', 
    array("turn_off_ssl_verification" => true)
);
// OR
$sendgrid = new SendGrid(
    'sendgrid_api_key', 
    array("turn_off_ssl_verification" => true)
);
```

#### Response ####

An instance of `\SendGrid\Response` is returned from the `send()` method.

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ->setFrom('me@bar.com')
    ->setSubject('Subject goes here')
    ->setText('Hello World!');
$res = sendgrid->send($email);

var_dump($res);

// Output
object(SendGrid\Response)#31 (4) {
  ["code"]=>
  int(200)
  ["headers"]=>
  object(Guzzle\Http\Message\Header\HeaderCollection)#48 (1) {
    ["headers":protected]=>
    array(6) {
	...
      ["content-type"]=>
      object(Guzzle\Http\Message\Header)#41 (3) {
        ["values":protected]=>
        array(1) {
          [0]=>
          string(16) "application/json"
        }
        ["header":protected]=>
        string(12) "Content-Type"
        ["glue":protected]=>
        string(1) ","
      }
   ...
    }
  }
  ["raw_body"]=>
  string(21) "{"message":"success"}"
  ["body"]=>
  array(1) {
    ["message"]=>
    string(7) "success"
  }
}
```

#### getCode ####

Returns the status code of the response.

```
$res = $sendgrid->send($email);
echo $res->getCode()
```

#### getHeaders ####

Returns the headers of the response as a [Guzzle\Http\Message\Header\HeaderCollection object](https://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Guzzle.Http.Message.Header.HeaderCollection.html).

```
$res = $sendgrid->send($email);
$guzzle = $res->getHeaders();
echo var_dump($guzzle);
```

#### getRawBody ####

Returns the unparsed JSON response from SendGrid.

```
$res = $sendgrid->send($email);
echo $res->getRawBody()
```

#### getBody ####

Returns the parsed JSON from SendGrid.

```
$res = $sendgrid->send($email);
echo var_dump($res->getBody());
```

### Exception ###

A `\SendGrid\Exception` is raised if the response code is not 200. Catching it is optional but highly recommended.

```php
try {
    $sendgrid->send($email);
} catch(\SendGrid\Exception $e) {
    echo $e->getCode() . "\n";
    foreach($e->getErrors() as $er) {
        echo $er;
    }
}

// Output
400
Permission denied, wrong credentials
```

### SMTPAPI ###

This library makes use of [sendgrid/smtpapi-php](https://github.com/sendgrid/smtpapi-php/) for all things related to the [X-SMTPAPI Header](https://sendgrid.com/docs/API_Reference/SMTP_API/index.html).

---

### Library Methods ###

#### addTo

You can add one or multiple TO addresses using `addTo` along with an optional TO name. Note: If using TO names, each address needs a name.

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ->addTo('another@another.com')
;
$sendgrid->send($email);

// With names
$email = new SendGrid\Email();
$email
	->addTo('foo@bar.com', 'Frank Foo')
	->addTo('another@another.com', 'Joe Bar')
;
$sendgrid->send($email);

// As an array
$email = new SendGrid\Email();
$email
    ->addTo(array('foo@bar.com', 'bar@example'), array('Frank Foo', 'Brian Bar'))
;
$sendgrid->send($email);
```

#### addSmtpapiTo

Add a TO address to the smtpapi header along with an optional name.

```php
$email = new SendGrid\Email();
$email
    ->addSmtpapiTo('foo@bar.com')
    ->addSmtpapiTo('another@another.com', 'Mike Bar')
;
$sendgrid->send($email);
```

#### setTos

If you prefer, you can add multiple TO addresses as an array using the `setTos` method. This will unset any previous `addTo`s you appended.

```php
$email = new SendGrid\Email();
$emails = array("foo@bar.com", "another@another.com", "other@other.com");
$email->setTos($emails);
$sendgrid->send($email);
```

#### setSmtpapiTos

```php
$email = new SendGrid\Email();
$emails = array("foo@bar.com", "Brian Bar <bar@example.com>", "other@example.com");
$email->setSmtpapiTos($emails);
$sendgrid->send($email);
```

#### setFrom

```php
$email = new SendGrid\Email();
$email->setFrom('foo@bar.com');
$sendgrid->send($email);
```

#### setFromName

```php
$email = new SendGrid\Email();
$email
    ->setFrom('foo@bar.com')
    ->setFromName('Foo Bar')
;
$sendgrid->send($email);
```

#### setReplyTo

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ->setReplyTo('someone.else@example.com')
    ->setFromName('John Doe')
   ...
;
```

### Cc

#### addCc

```php
$email = new SendGrid\Email();
$email->addCc('foo@bar.com');
$sendgrid->send($email);
```

#### setCc

```php
$email = new SendGrid\Email();
$email->setCc('foo@bar.com');
$sendgrid->send($email);
```

#### setCcs

```php
$email = new SendGrid\Email();
$emails = array("foo@bar.com", "another@another.com", "other@other.com");
$email->setCcs($emails);
$sendgrid->send($email);
```

#### removeCc

```php
$email->removeCc('foo@bar.com');
```

### Bcc

Use multiple `addSmtpapiTo`s as a superior alternative to `setBcc`.

```php
$email = new SendGrid\Email();
$email
    ->addSmtpapiTo('foo@bar.com')
    ->addSmtpapiTo('someotheraddress@bar.com')
    ->addSmtpapiTo('another@another.com')
   ...
;
```

But if you do still have a need for Bcc you can do the following:

#### addBcc

```php
$email = new SendGrid\Email();
$email->addTo('bar@example.com');
$email->addBcc('foo@bar.com');
$sendgrid->send($email);
```

#### setBcc

```php
$email = new SendGrid\Email();
$email->setBcc('foo@bar.com');
$sendgrid->send($email);
```

#### setBccs

```php
$email = new SendGrid\Email();
$emails = array("foo@bar.com", "another@another.com", "other@other.com");
$email->setBccs($emails);
$sendgrid->send($email);
```

#### removeBcc

```php
$email->removeBcc('foo@bar.com');
```

**Important Gotcha**: Using multiple `addSmtpapiTo`s is recommended over bcc whenever possible. Each user will receive their own personalized email with that setup, and only see their own email.

Standard `setBcc` will hide who the email is addressed to. If you use multiple `addSmtpapiTo`'s, each user will receive a personalized email showing *only* their email. This is more friendly and more personal.

#### setSubject

```php
$email = new SendGrid\Email();
$email->setSubject('This is a subject');
$sendgrid->send($email);
```

#### setText

```php
$email = new SendGrid\Email();
$email->setText('This is some text');
$sendgrid->send($email);
```

#### setHtml

```php
$email = new SendGrid\Email();
$email->setHtml('<h1>This is an html email</h1>');
$sendgrid->send($email);
```

#### setDate

```php
$email = new SendGrid\Email();
$email->setDate('Wed, 17 Dec 2014 19:21:16 +0000');
$sendgrid->send($email);
```

#### setSendAt

```php
$email = new SendGrid\Email();
$email->setSendAt(1409348513);
$sendgrid->send($email);
```

#### setSendEachAt

```php
$email = new SendGrid\Email();
$email->setSendEachAt(array(1409348513, 1409348514, 1409348515));
$sendgrid->send($email);
```

#### addSendEachAt

```php
$email = new SendGrid\Email();
$email
    ->addSendEachAt(1409348513)
    ->addSendEachAt(1409348514)
    ->addSendEachAt(1409348515)
;
$sendgrid->send($email);
```

### Categories ###

Categories are used to group email statistics provided by SendGrid.

To use a category, simply set the category name.  Note: there is a maximum of 10 categories per email.

#### addCategory

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    ->addCategory("Category 1")
    ->addCategory("Category 2")
;
```

#### setCategory

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    ->setCategory("Category 1")
;
```

#### setCategories

```php
$email = new SendGrid\Email();
$categories = array("Category 1", "Category 2", "Category 3");
$email->setCategories($categories);
```

#### removeCategory

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    ->removeCategory("Category 1")
;
```

### Attachments ###

Attachments are currently file based only, with future plans for an in memory implementation as well.

File attachments are limited to 7 MB per file.

#### addAttachment

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    ->addAttachment("../path/to/file.txt")
;
```

#### setAttachment

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    ->setAttachment("../path/to/file.txt")
;
```

#### setAttachments

```php
$email = new SendGrid\Email();
$attachments = array("../path/to/file1.txt", "../path/to/file2.txt");
$email
    ->addTo('foo@bar.com')
    ...
    ->setAttachments($attachments)
;
```

#### removeAttachment

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    ->addAttachment("../path/to/file.txt")
    ->removeAttachment("../path/to/file.txt")
;
```

You can tag files for use as inline HTML content. It will mark the file for inline disposition using the specified "cid".

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ->setHtml('<div>Our logo:<img src="cid:file-cid"></div>')
    ->addAttachment("../path/to/file.png", "super_file.png", "file-cid")
;
```

### Substitutions ###

Substitutions can be used to customize multi-recipient emails, and tailor them for the user.

Unless you are only sending to one recipient, please make sure to use `addSmtpapiTo()`.

#### addSubstitution

```php
$email = new SendGrid\Email();
$email
    ->addSmtpapiTo('john@somewhere.com')
    ->addSmtpapiTo('harry@somewhere.com')
    ->addSmtpapiTo('Bob@somewhere.com')
       ...
    ->setHtml("Hey %name%, we've seen that you've been gone for a while")
    ->addSubstitution('%name%', array('John', 'Harry', 'Bob'))
;
```

Substitutions can also be used to customize multi-recipient subjects.

```php
$email = new SendGrid\Email();
$email
    ->addSmtpapiTo(array('john@somewhere.com', 'harry@somewhere.com', 'bob@somewhere.com'))
    ->setSubject('%subject%')
    ->addSubstitution(
        '%subject%',
        array('Subject to John', 'Subject to Harry', 'Subject to Bob')
    )
    ...
;
```

#### setSubstitutions

```php
$email = new SendGrid\Email();
$email
    ->addSmtpapiTo(array('john@somewhere.com', 'harry@somewhere.com', 'bob@somewhere.com'))
    ->setSubject('%subject%')
    ->setSubstitutions(array(
        '%name%' => array('John', 'Harry', 'Bob'), 
        '%subject%' => array('Subject to John', 'Subject to Harry', 'Subject to Bob')
    ))
    ...
;
```

### Sections ###

Sections can be used to further customize messages for the end users. A section is only useful in conjunction with a substitution value.

#### addSection

```php
$email = new SendGrid\Email();
$email
    ->addSmtpapiTo('john@somewhere.com')
    ->addSmtpapiTo("harry@somewhere.com")
    ->addSmtpapiTo("Bob@somewhere.com")
    ...
    ->setHtml("Hey %name%, you work at %place%")
    ->addSubstitution("%name%", array("John", "Harry", "Bob"))
    ->addSubstitution("%place%", array("%office%", "%office%", "%home%"))
    ->addSection("%office%", "an office")
    ->addSection("%home%", "your house")
;
```

#### setSections

```php
$email = new SendGrid\Email();
$email
    ->addSmtpapiTo('john@somewhere.com')
    ->addSmtpapiTo("harry@somewhere.com")
    ->addSmtpapiTo("Bob@somewhere.com")
    ...
    ->setHtml("Hey %name%, you work at %place%")
    ->addSubstitution("%name%", array("John", "Harry", "Bob"))
    ->addSubstitution("%place%", array("%office%", "%office%", "%home%"))
    ->setSections(array("%office%" => "an office", "%home%" => "your house"))
;
```

### Unique Arguments ###

[Unique Arguments](https://sendgrid.com/docs/API_Reference/SMTP_API/unique_arguments.html) are used for tracking purposes.

#### addUniqueArg / addUniqueArgument

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    ->addUniqueArg("Customer", "Someone")
    ->addUniqueArg("location", "Somewhere")
;
```

#### setUniqueArgs / setUniqueArguments

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    ->setUniqueArgs(array('cow' => 'chicken'))
;
```

### Filter Settings ###

[Filter Settings](https://sendgrid.com/docs/API_Reference/SMTP_API/apps.html) are used to enable and disable apps, and to pass parameters to those apps.

#### addFilter / addFilterSetting

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    addFilter("gravatar", "enable", 1)
    ->addFilter("footer", "enable", 1)
    ->addFilter("footer", "text/plain", "Here is a plain text footer")
    ->addFilter(
        "footer", 
        "text/html", 
        "<p style='color:red;'>Here is an HTML footer</p>"
    )
;
```

#### setFilters / setFilterSettings

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    setFilters(array("gravatar" => array("settings" => array("enable" => 1))))
;
```

### Templates ###

You can easily use SendGrid's [template engine](https://sendgrid.com/docs/User_Guide/Apps/template_engine.html) by applying filters.

#### setTemplateId

```php
$email = new SendGrid\Email();
$email
    ->addTo('someone@example.com')
    ->setFrom('support@example.com')
    ->setFromName('Support')
    ->setSubject('Subject goes here')
    // set html or text to an empty space (see http://git.io/hCNy)
    ->setHtml(' ') // <-- triggers the html version of the template
    // AND / OR
    ->setText(' ') // <-- triggers the plaintext version of the template
    ->setTemplateId($templateId);
```

This is simply a convenience method for:

```php
$email = new SendGrid\Email();
$email
    ->addFilter('templates', 'enabled', 1)
    ->addFilter('templates', 'template_id', $templateId)
;
```

### Advanced Suppression Manager ###

[ASM](https://sendgrid.com/docs/User_Guide/advanced_suppression_manager.html) is used to handle suppression groups.

#### setAsmGroupId ####

```php
$email = new SendGrid\Email();
$email->setAsmGroupId('my_group_id');
```

### Headers ###

You can add standard email message headers as necessary.

#### addHeader

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    ->addHeader('X-Sent-Using', 'SendGrid-API')
    ->addHeader('X-Transport', 'web')
;
```

#### setHeaders

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    ->setHeaders(array('X-Sent-Using' => 'SendGrid-API', 'X-Transport' => 'web'))
;
```

#### removeHeader

```php
$email = new SendGrid\Email();
$email
    ->addTo('foo@bar.com')
    ...
    ->addHeader('X-Sent-Using', 'SendGrid-API')
    ->addHeader('X-Transport', 'web')
;
$email->removeHeader('X-Transport');
```

### Sending to 1,000s of emails in one batch

Sometimes you might want to send 1,000s of emails in one request. You can do that. It is recommended you break each batch up in 1,000 increments. So if you need to send to 5,000 emails, then you'd break this into a loop of 1,000 emails at a time.

```php
$sendgrid = new SendGrid(SENDGRID_USERNAME, SENDGRID_PASSWORD);
// OR
$sendgrid = new SendGrid(SENDGRID_APIKEY);
$email = new SendGrid\Email();

$recipients = array(
    "alpha@mailinator.com", 
    "beta@mailinator.com", 
    "zeta@mailinator.com"
);
$names = array("Alpha", "Beta", "Zeta");

$email
    ->setFrom("from@mailinator.com")
    ->setSubject('[sendgrid-php-batch-email]')
    ->setSmtpapiTos($recipients)
    ->addSubstitution("%name%", $names)
    ->setText("Hey %name%, we have an email for you")
    ->setHtml("<h1>Hey %name%, we have an email for you</h1>")
;

$result = $sendgrid->send($email);
```

## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Added some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request

## Running Tests

The existing tests in the `test` directory can be run using [PHPUnit](https://github.com/sebastianbergmann/phpunit/) with the following command:

````bash
composer update --dev
cd test
../vendor/bin/phpunit
```

or if you already have PHPUnit installed globally.

```bash
cd test
phpunit
```

## Releasing

To release a new version of this library, update the version in all locations, tag the version, and then push the tag up. Packagist.org takes care of the rest.

#### Testing uploading to Amazon S3

If you want to test uploading the zipped file to Amazon S3 (SendGrid employees only), do the following.

```
export S3_SIGNATURE="secret_signature"
export S3_POLICY="secret_policy"
export S3_BUCKET="sendgrid-open-source"
export S3_ACCESS_KEY="secret_access_key"
./scripts/s3upload.sh
```
