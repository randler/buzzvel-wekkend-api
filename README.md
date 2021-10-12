<p align="center">
  <a href="https://github.com/randler/buzzvel-weekend-api">
    <img alt="Buzzvel" src="https://buzzvel.com/images/buzzvel.png" width="200">
  </a>
</p>

<h1 align="center">
  <a href="https://github.com/randler/buzzvel-weekend-api">
    Buzzvel api for hotels weekend
  </a>
</h1>

<p align="center">
  <a href="https://github.com/php/php-src/blob/master/LICENSE">
    <img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="React Native is released under the MIT license." />
  </a>
  <a href="https://github.com/randler/buzzvel-weekend-api/releases">
    <img src="https://img.shields.io/badge/vers%C3%A3o-0.0.1-green" alt="Versão" />
  </a>
  <a href="https://github.com/randler/buzzvel-weekend-api/releases">
    <img src="https://img.shields.io/packagist/dt/randler/weekbuzz-php.svg" alt="Downloads" />
  </a>
</p>

## Summary

- [Install](#install)
- [Request all hotels](#request-all-hotels)
<br>
<br>
<br>
<br>


## Install

To install the the lib just enter the next command:

`composer require randler/weekbuzz-php`
<br>
<br>
<hr>

## Request all hotels

```php
<?php
  $search = new Search();
  
  $search->hotels()->list();
```

List all hotels.
<br>
<br>
<hr>