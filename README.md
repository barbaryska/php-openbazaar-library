# PHP Library for OpenBazaar API

Slim PHP client for interaction with the OpenBazaar API via cURL

### Basic Usage

```php

    $OpenBazaar = new OpenBazaar('username', 'password');
    
    // get your Profile profile info
    $result = $OpenBazaar->getProfile();
    
    // get Listings for specific GUID
    $result = $OpenBazaar->getListings('1a61b9f2a862187b80e9d8ea57531f034afcd562');
    
    // follow BazaarBay
    $result = $OpenBazaar->follow('7a94af9cf4d784a974a17e18089ce62f529ce41f');
    
``` 