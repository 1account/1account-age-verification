# 1 Account Age Validation for Magento 2

## The features of this extension:
### Frontend:
- Only validated by 1Account users can place orders.
- Saving users AV_STATUS for next orders.
- If guests can make orders in your store their AV_STATUS will save to cookies.

### Backend:
- Display customers AV_STATUSES in the customer grid.
- Possibility to change users AV_STATUSES.
- Show orders AV_STATUSES in order grid.
- Possibility to change orders AV_STATUSES 


## Introduction installation:

### Step 1: Download extension using Composer

#### 1.1 Login to your Magento 2 Hosting site using SSH connection

#### 1.2 Go to Magento root directory

    cd /Magento_root_directory
    
#### 1.2 Download extension
  
```
composer require 1account/1account-age-verification
```

### Step 2: Enable extension
   
#### 2.1 Check if module OneAccount_OneAccountAgeVerification upload successfully

    php bin/magento module:status
    
    // output should contain OneAccount_OneAccountAgeVerification at "List of disabled modules"
    
    List of disabled modules:
    OneAccount_OneAccountAgeVerification
    
#### 2.2 Enable module

    php bin/magento module:enable OneAccount_OneAccountAgeVerification
    
    // output
    
    The following modules have been enabled:
    - OneAccount_OneAccountAgeVerification

##### (we recommended enable maintenance mode for clear downtime)
    
    php bin/magento maintenance:enable
    
#### 2.4 Run upgrade command

    php bin/magento setup:upgrade
    
#### 2.5 Recompile your site

    php bin/magento setup:di:compile
    
#### 2.6 Rebuild static content

    php bin/magento setup:static-content:deploy

#### 2.7 Reindex your store

    php bin/magento indexer:reindex
    
#### 2.8 Clear cache

    php bin/magento c:c
    
(if there are no command bin/magento c:c go to Admin Panel -> System -> Cache Management and press "Flush Magento Cache")

##### (disable maintenance if you enabled it before)
    
    php bin/magento maintenance:disable


## Results

### Backend

#### Enable extension

Go to 

    Admin Panel->Stores->Configuration->ONEACCOUNT->General
    
 - Choose YES in Enable Module
 - Enter your valid clientID and clientSecret from 1account.net
 - Press Save Config and if data valid avLevel will set automatically 
 - In the orders list appeared a new column AV Validation
    + It set automatically when the order created
    + You can change it manually in the order view page in the "1Account Status" tab 
 - In the customers list appeared a new column AV Status
    + It sets automatically when a customer validated on checkout
    + You can change it manually in customer view page in "Account information" tab      

### Frontend

When you place an order before creating an order you will see 1Account modal. If you pass 1Account validation - order will continue.

## Remove module

    composer remove 1account/1account-age-verification
