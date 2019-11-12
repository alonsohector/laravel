<p align="center">
    <img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="200"></p>
    
<p align="center">
    <h4>Laravel Fork to request POST and Limit requests per time</h4>
    <h5>Project required by Incfile</h5>
</p>    
    

## About Laravel POST Attempts/Timer

Laravel POST Attempts/timer, limits the request number stopping retries and interacting with time. 
I use RateLimiter and get the APP_KEY retrieved during installation, it is normal to use UserId, CompanyId, Projetc, etc.

It is very critical that the request reaches the destination and handle errors.
Secure request are managed by Attempts and Limit/Timers.

- Simple, fast POST request with [Guzzle](https://github.com/guzzle/guzzle).
- Command Artisan using parameters.
- Multiple Errors Exception and validations.
- Real-time events and Log files.

<hl>

<p align="center">
    <h2>Steps for Project</h2>
</p> 

<hl>
    
    
## Fork Laravel

After Fork Laravel repository, I redefined the origin remote to be associated and be able to push changes to my own fork.
For this project I will use [Guzzle](https://github.com/guzzle/guzzle) with latest version 6.x to use POST request 

## Install

Modify the **composer.json** file to apply installation for component.

<pre>
    {
       "require": {
          "guzzlehttp/guzzle": "~6.3.3"
       }
    }
</pre>

The install command reads the **composer.json** file from the current directory, resolves the dependencies, and installs them into vendor.

<pre>
    composer install
</pre>

Copy **.env.example** to **.env** file
<pre>
    copy .\.env.example .env
</pre>    

Include in **.env** file the **APP_KEY**
<pre>
    php artisan key:generate
</pre>

After Key generated, I configured the extra app information adding to **.env** file parameters as below:

<pre>
    API_URL=http://jsonplaceholder.typicode.com/posts
    POST_LIMITER_SECONDS=60
    POST_LIMITER_RETRIES=3
</pre>

*IMPORTANT*. The last two parameters are used to limit the request number stopping retries and interacting with time. I use **RateLimiter** and get the **APP_KEY** retrieved during installation, it is normal to use UserId, CompanyId, Projetc, etc.


## Command Artisan 

Then new Artisan console command is configured.

<pre>
    php artisan make:command Post
</pre>

Added POST Class Command to use and configure parameters as below:

<pre>
    timeout  	    = 2.0
    http_errors     = false
    max             = 10,        // allow at most 10 redirects.
    strict          = true,      // use "strict" RFC compliant redirects.
    referer         = true,      // add a Referer header
    protocols       = ['https'], // only allow https URLs
    track_redirects = true
</pre>

## Exception Handling

Created new exception and handle error, if environment is production don’t show error details and record errors in Log.
<pre>
    php artisan make:exception PostNotFoundException
</pre>


## Finished

<pre>
    php artisan help Incfile:POST
</pre>

<pre>
Incfile:POST

+ Description:
  Send a simple POST request to external URL.

+ Usage:
  Incfile:POST [options]

+ Options:
  -U, --URL_POST[=URL_POST]   [default: "Incfile_defined"]
  -h, --help                 Display this help message
  -q, --quiet                Do not output any message
  -V, --version              Display this application version
      --ansi                 Force ANSI output
      --no-ansi              Disable ANSI output
  -n, --no-interaction       Do not ask any interactive question
      --env[=ENV]            The environment the command should run under
  -v|vv|vvv, --verbose       Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
</pre>

## Executing

The application will validate if POST is acceptable, handle corresponding errors Log and manage Limits of Attempts per Time defined, 
Limit Attempts and Time aims to provide POST request protection and can be modified as the implementation is required.

<pre>
    php artisan Incfile:POST
</pre>

without parameters will request to Incfile default information as below:
<pre>
    API_URL=https://atomic.incfile.com/fakepost
    POST_LIMITER_SECONDS=60
    POST_LIMITER_RETRIES=3 (can be 100K requests)
</pre>
Attempts, Retries and Timeout are managed even if errors exists.

<pre>
    +----------+--------------+----------------------+
    | Attempts | Retries Left | Available time (Sec) |
    +----------+--------------+----------------------+
    | 1        | 3            | 60                   |
    +----------+--------------+----------------------+
    URL: https://atomic.incfile.com/fakepost
    Unable to connect. The url is not valid or you do not have permission to the site.
</pre>

<hl>

<pre>
    php artisan Incfile:POST -U http://jsonplaceholder.typicode.com/posts
</pre>
will request using URL input in parameter and manage Attempts, Retries and Timeout as well.
    
<pre>    
    +----------+--------------+----------------------+
    | Attempts | Retries Left | Available time (Sec) |
    +----------+--------------+----------------------+
    | 1        | 3            | 60                   |
    +----------+--------------+----------------------+
    URL: http://jsonplaceholder.typicode.com/posts

    POST sent correctly
</pre>    
    
You can use –verbose parameter to show Headers and Json Response details
<pre>
    php artisan  Incfile:POST -U http://jsonplaceholder.typicode.com/posts --verbose
</pre>

<pre>
    +----------+--------------+----------------------+
    | Attempts | Retries Left | Available time (Sec) |
    +----------+--------------+----------------------+
    | 1        | 3            | 60                   |
    +----------+--------------+----------------------+
    URL: http://jsonplaceholder.typicode.com/posts

    POST sent correctly

    HTTP/1.0 200 OK
    Cache-Control: no-cache, private
    Content-Type:  application/json
    Date:          Tue, 12 Nov 2019 04:17:24 GMT

    {"POST":"Completed"}
    Response:{
      "Parm1": "Parm1",
      "id": 101
    }
</pre>

## Contributing

Thank you for considering contributing to the Laravel framework! 

## Security Vulnerabilities

If you discover a security vulnerability within this project, please send an e-mail to Hector Alonso via [alonso.hector@gmail.com](mailto:alonso.hector@gmail.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
Then... this project too!
