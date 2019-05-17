# REST API ENDPOINT

> This plugin provides you different endpoints using WordPress REST API.

* :bust_in_silhouette: Login End Point
* :page_with_curl: Create Post End Point

## Login Endpoint Demo :video_camera:

When we access the end point on URI: `http://your-domain/wp-json/wp/v2/rae/user/login`,
and we pass our username and password in the body using postman, we get:
* User Object on success
* Error when fields are empty or incorrect credentials
* Any other error.
![](login-endpoint-demo.gif)

## Getting Started :clipboard:

These instructions will get you a copy of the project up and running on your local machine for development purposes.

## Prerequisites :door:

You need to have any WordPress theme activated on your WordPress project, which has REST API enabled.

## Installation :wrench:

1. Clone the plugin directory in the `/wp-content/plugins/` directory, or install a zipped directory of this plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

## Use :ski:

There are different end points that are available. Some are public while others are protected.

* :bust_in_silhouette: Login End Point `http://your-domain/wp-json/wp/v2/rae/user/login`
* :page_with_curl: Create Post End Point

## Contributing :busts_in_silhouette:

Please read [CONTRIBUTING.md](https://gist.github.com/PurpleBooth/b24679402957c63ec426) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

I use [Git](https://github.com/) for versioning. 

## Author :pencil:

* **[Imran Sayed](https://codeytek.com)**

## License :page_facing_up:

[![License](http://img.shields.io/:license-mit-blue.svg?style=flat-square)](http://badges.mit-license.org)

- **[MIT license](http://opensource.org/licenses/mit-license.php)**
