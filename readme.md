# PHP GDPR Contact form

This simple PHP project will help you to create GDPR/DSGVO conform contact forms.
It is targeted on Javascript-Based websites,which might not have a backend that is capable of sending emails or storing data in a database.
This tool can easily be deployed on your own PHP host. Then you can send the contact form as a javascript request using formdata format.

## php.ini mail config
Mail config in php.ini must be set up correctly and should support SSL/TLS in order to transfer the data securely.

## HTTPS hosting
Also this project should be hosted on a HTTPS secured server, so the data can be transered securely from the frontend to this php page.

## CORS setup
Currently there is no CORS yet, since it is not yet included in our real case scenario project, but this will follow.

## Captcha using securimage
This project will protect your mailbox from spam using the securimage project (https://www.phpcaptcha.org/). I will not include it in this package, so you will have to download it from https://www.phpcaptcha.org/download/ and unzip it to the subfolder called "securimage" next to these php files.

## Configuration
Check out the `config.inc.php` for the settings.
- $mailFrom = 'mysender@localmail.de';
- $mailTo = 'mytest@localmail.de';
- $mailSubject= 'New Contact form entry';
- $dbServer = 'localhost';
- $dbDatabase = 'contactform';
- $dbUsername = 'root';
- $dbPassword = '';

You only need to provide a valid database. A table called captchas will automatically be created. Un-resolved captchas are automatically removed, so do not worry about a hard disk full of captchas. But you still might need a rate limiter on the endpoint if you play it hard.

## Usage

### Get Captcha token
Request GET `https://yourhost/yourpath/captchaid.php`
to retrieve a captcha id. This will be in JSON format like so:
`{
    "captchaId": "4e320158cbffde426e2ba53ceccf9b9beec5638d"
}`

Put this captchaId into a hidden field called `id`

### Display Captcha image
Create an image tag using this url as source: `https://yourhost/yourpath/captcha.php?id=4e320158cbffde426e2ba53ceccf9b9beec5638d` using your captcha id. This will display the captcha.
To Update the image and slightly change it, just reload the image source.

### Validate and send email
Create a POST request to `https://yourhost/yourpath/formtarget.php` with a formdata body using the following keys:
- `id` - Token id
- `value` - User's input for token text
- `name` - Name of the contact, format is sanitized
- `email` - Email of the contact, format is validated
- `message` - Message, format is sanitized
- `phone` - Phone number, optional, sanitized
- `accept` - Boolean value of (true, on, 1) to document the user has selected a checkbox accepting terms.

## Return codes
- `500` If something went really wrong.
- `400` Bad request, form data invalid, or captcha id is too old / invalid.
- `403` Captcha id correct, but solution wrong.
- `200` All good.

## Docker image
This image does not use any volumes and is stateless. For persistence please apply the Dockerfile to the master branch (untested, but mysqli is installed).
Just clone this repository and build your image.

Example nginx configuration if the project should be hosted within a subfolder of the domain. This configuration extracts the path information and only supplies the script name. Maybe the timeout needs to be changed, as sending the mail may take some time.
Please ensure that nginx serves this only via https. Please edit the Access-Control-Allow-Origin Header for safety.

```
location ~ "/gdpr-contact/(.+\.php)(/|$)" {
      fastcgi_split_path_info ^(.+?\.php)(/.*)$;
      fastcgi_read_timeout 300;
      resolver 127.0.0.11;
      fastcgi_pass <container_name>:9000;
      fastcgi_param SCRIPT_FILENAME $1;
      fastcgi_param HTTPS on;
      add_header Access-Control-Allow-Origin * always;
      include fastcgi_params;
}
```



## Feedback
Please be aware, that this took about 3 hours of work with few php skills, so feel free to optimize.

