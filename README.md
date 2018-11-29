# Some base components for new microservice based on symfony 4 framework
============================================================================

### Setup

##### 1. Install module using composer require

```
composer require backend-module/MicroBaseComponent
```
 
##### 2. Or add new require package to composer config file manually

```
"backend-module/MicroBaseComponent": "^0.5"
```

##### 3. Initialize event listeners in your service
* setup ApiVersionListener listener

```
Micro\BaseComponent\EventListener\ApiVersionListener:
 tags:
     - { name: kernel.event_listener, event: kernel.request}
```


 * setup JsonListener listener
 
```
Micro\BaseComponent\EventListener\JsonListener:
    tags:
        - { name: kernel.event_listener, event: kernel.request}

```

 * setup ExceptionListener listener
 
```
Micro\BaseComponent\EventListener\ExceptionListener:
    arguments:
      - '@kernel'
      - '@logger'
    tags:
        - { name: kernel.event_listener, event: kernel.exception}
```  

 * setup ApiResponseListener listener
 
```
Micro\BaseComponent\EventListener\ApiResponseListener:
    tags:
        - { name: kernel.event_listener, event: kernel.view}
```  
  
* setup ViewListener listener
  
 ```
Micro\BaseComponent\EventListener\ViewListener:
    tags:
        - { name: kernel.event_listener, event: kernel.view}
 ```  