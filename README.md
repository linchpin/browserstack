Browserstack Screenshot API PHP Library
============

An easy-to-use PHP library for the Browserstack Screenshots API. Working examples included.

## Install

Install via [composer](https://getcomposer.org):

```javascript
{
    "require": {
        "linchpinagency/browserstack": "0.0.3"
    }
}
```

Run `composer install`.

## Example usage

#### Get an array of available browsers

```php
use Alexschwarz89\Browserstack\Screenshots\ScreenshotsAPI;
$screenshots_api = new ScreenshotsAPI( 'username', 'password' );
$browser_list    = $api->get_browsers();
```

#### Generate a screenshot
```php
use Alexschwarz89\Browserstack\Screenshots\Api;
use Alexschwarz89\Browserstack\Screenshots\Request;
$screenshots_api = new ScreenshotsAPI( 'account', 'password' );
$request         = Request::build_request( 'http://www.example.org', 'Windows', '8.1', 'ie', '11.0' );
$response        = $screenshots_api->send_request( $request );
$job_ID          = $response->job_ID;
```

#### Query information about the request

```php
$status = $api->get_job_status( 'browserstack_jobID' );
if ( $status->is_finished() ) {
  foreach ( $status->finished_screenshots as $screenshot ) {
    print $screenshot->image_url ."\n";
  }
}
```
