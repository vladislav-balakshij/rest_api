

<h3 align="center">Symfony 3.4 test task for digicode</h3>
## Overview
Rest API aplication based on symfony framework LTS.
<p>
Data are protected.<br>
Users provider store apiKey in database.<br>

GUI with list of transaction available by URL:

```sh
localhost:8000/admin
```

Sum of transaction are stored in db by console command:

```sh
php bin/console app:sum-transaction
```

<p/>


## List of methods

- [Adding of a customer](#add-of-a-customer)
- [Getting a transaction](#Getting-a-transaction)
- [Getting transaction by filters](#getting-transaction-by-filter)
- [Adding a transaction](#Adding-a-transaction)
- [Updating a transaction](#updating-a-transaction)
- [Deleting a transaction](#deleting-a-transaction)

## Cron job 

 Set up the cron job to run every 2 days at 23:47.
```sh
47 23 */2 * * project_path php bin/console app:sum-transaction
```

##Adding of a customer
URL:/customer<br>
Method: POST<br>
Request: name, cnp<br>
Response: customerId

##Getting a transaction
URL:/transaction/{customerId}/{transactionId}<br>
Method: GET<br>
Request: ​customerId, transactionId<br>
Response: transactionId, amount, date 

##Getting transaction by filter
URL:/transactionByFilter/{customerId}/{amount}/{date}/{offset}/{limit}<br>
Method: GET<br>
Request:​ customerId, amount, date, offset, limit<br>
Response:​ an array of transactions 

##Adding a transaction
URL:/transaction<br>
Method: POST<br>
Request:​ transactionId, amount<br>
Response:​ transactionId, customerId, amount, date 

##Updating a transaction
URL:/transaction<br>
Method: PUT<br>
Request: ​customerId, transactionId<br>
Response: transactionId, amount, date 

##Deleting a transaction
URL:/transaction<br>
Method: DELETE<br>
Request:​ trasactionId<br>
 Response:​ success/fail

