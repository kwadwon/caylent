# README #

This README would normally document whatever steps are necessary to get your application up and running.

### What is this repository for? ###

* This repo is to help Caylent quickly get connect to the [Azure Resource Management API](https://docs.microsoft.com/en-us/rest/api/resources/)
* 1.0


### How do I get set up? ###

#### API
The [Azure Resource Management API's](https://docs.microsoft.com/en-us/rest/api/resources/) [Deployment operations](https://docs.microsoft.com/en-us/rest/api/resources/deploymentoperations) and [Deployments](https://docs.microsoft.com/en-us/rest/api/resources/deployments) are all wrapped in the [AzureARMRESTAPI.php](AzureARMRESTAPI.php) file.  This is a flat implementation right over the REST API rather some object oriented wrapper.  If you look at the code you'll see that each API function has a mirror function in the wrapper.  For example, Get Deployment API maps to get_deployment.  There is also a Get in the Deployment Operations so we call that one get_deployment_operation.  

To use the API and get the auth piece done you need to following:
* Create their directory
* Define an app in that directory
* Define an access key for the app
* Go to the subscription and give the app access

How do you do this?  Read [Use portal to create Active Directory application and service principal that can access resources](https://docs.microsoft.com/en-us/azure/azure-resource-manager/resource-group-create-service-principal-portal)

To use this API you need a couple pieces of information:
* Users Tenant
* Users Subscription
* Users Key

These components can be seen during the AD App creation step.  That said, sample can be found in the [.env file](https://bitbucket.org/caylent/microsoft-ascend-plus/src/723d41b043404f92c3cfa60b57e316132e8e85eb/.env?at=master)

How do I see how this is used?
Take a look at [ARMTest.php](https://bitbucket.org/caylent/microsoft-ascend-plus/src/723d41b043404f92c3cfa60b57e316132e8e85eb/ARMTest.php?at=master), which is a "test app" we used to test the API