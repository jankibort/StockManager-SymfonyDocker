#### Follow steps below to run a project:
- git clone
- cd [project]/docker
- docker-compose up -d
- cd ../src
- composer install
- docker exec -it php ./bin/console doctrine:migrations:diff && docker exec -it php ./bin/console doctrine:migrations:migrate
#### Service should be available on localhost. Homepage consists of list of endpoints which can be found in /templates/base.html.twig.
