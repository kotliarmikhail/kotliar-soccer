## What was done?
* The fetching of data from https://int.soccerway.com/national/england/premier-league/2011-2012/regular-season/r14829/matches/ has been implemented.
* The filtering teams on the main page by input box has been done.
* The getting data from DB in JSON format by /api/standings has been done.
* The filtering data from DB by /api/standings?from=2012-01-01&to=2012-02-01 has been done.

## NOT was done:
* Validation
* Unit tests

## For correct work of application you need to do:

### Migrate tables:
```
php bin/console doctrine:migrations:migrate 20180601213942
```
### Fetch data to db: 
```
php bin/console fetch:data
```

## For check implementation you may:

* yourhost.local/
* yourhost.local/api/standings
* yourhost.local//api/standings?from=2012-01-01&to=2012-02-01
