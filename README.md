# Nuki v1.0

**Please be aware that the Nuki framework is still in BETA and NOT production ready. 
Documentation for usage is also a minimum at this time**

### Initial build package setup
composer require hnto/nuki

**It is advised to use the skeleton application that already holds the setup required
for the framework to run**
"composer create-project hnto/nuki-skeleton application_name"

### Init Nuki Framework
**The build package tool is available as an executable as "build/phing"**
When using the build tool you are given a list of usefull commands to setup your application.
- Execute "build/phing" to view the available commands
    - when executing "init" the build tool will install the required packages, setup the files, folders etc.

### Run framework
The Nuki framework works with a 'modules type of way', called **Units**. 
These Units contain the necessary folders, classes and such to run your application.
A service is required for a unit to be executed. 
In the service you can process user input, do templating, registering events, watchers (listeners) and firing them accordingly.

**Steps**
- Create a new unit by running the build command "create-unit" and follow the steps.
- Create a new service for a unit by running the build command "create-service" and follow the steps
- Create (optional) an event(s) for the service by running the build command "create-events" and follow the steps
- Create repositories for your application by running the build command "create-repository" and follow the steps
- Create (optional) providers for your repositories by running the build command "create-provider" and follow the steps
- Add your routes in the "routes/app.php" file

**Units structure**
- Unit -> Authentication
    - Service -> Login
      - Method: index
      
**For an example of how this looks like, go the skeleton app and view the folder "build/format/Units/Skeleton"**
