<?php

    require 'AzureARMRESTAPI.php';

        require 'vendor/autoload.php';

    print("Load the environment variables\n");

    // Load environment variables

    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
    $a_tenant_id = getenv('TENANT_ID');
    $a_client_id = getenv('CLIENT_ID');
    $a_client_secret = getenv('CLIENT_SECRET');
    $a_subscription_id = getenv('SUBSCRIPTION_ID');
    $a_resource_group_name = getenv('RESOURCE_GROUP_NAME');
    $azure_datacenter_location = "east us";

    $access_token = get_auth_token( $a_tenant_id, $a_client_id, $a_client_secret);
    $result = create_resource_group($a_subscription_id,$a_resource_group_name,$access_token,$azure_datacenter_location);

?>