# PHP client-server file download demo

build steps:

1. Ensure that Docker and docker-compose are installed on your system.
2. Ensure that ports 8777 and 8778 are not currently in use.
3. Run the following:

```
$ mkdir php
$ cd php
$ git clone git@github.com:tkivari/c-php.git .
$ docker-compose up -d
```

Once your environment is built, you can access it by visiting http://localhost:8777 in your web browser.
