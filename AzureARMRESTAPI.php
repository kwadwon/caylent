<?php

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

    $is_debug = true;

    $access_token = get_auth_token($a_tenant_id, $a_client_id, $a_client_secret);
    $result = create_resource_group($a_subscription_id,$a_resource_group_name,$access_token,$azure_datacenter_location);

    print("Access token\n");
    var_dump( $access_token);

    function make_request(array $request_array)
    {

        $curl  = curl_init();
        curl_setopt_array($curl, $request_array);

        $result = curl_exec($curl);
        $resp = json_decode($result);
        
        print(curl_error($curl));

        print("\n");

        print("Completed auth request with status of\n");

        var_dump($resp);
        
        curl_close($curl);

        return $resp;


    }

    function get_auth_token($tenant_id, $client_id, $client_secret)
    {
        $curl_opt_array = array(
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => "https://login.microsoftonline.com:443/$tenant_id/oauth2/token??api-version=1.0",
            CURLOPT_POSTFIELDS => array(
                "grant_type" => "client_credentials",
                "resource" => "https://management.core.windows.net/",
                "client_id" => "$client_id",
                "client_secret" => "$client_secret"
            ));


        var_dump($curl_opt_array);

        print("Make request for auth token\n");
        
        $resp = make_request($curl_opt_array);
        
        print_r($resp);
        var_dump($resp);

        $access_token = $resp->access_token;

        return $access_token;
    }      

    function create_resource_group($subscription_id,$resource_group_name,$access_token,$azure_location)
    {
        /* Create new resource group and deploy from ARM template */
        $data = array("location" => $azure_location);
        $data_json_location = json_encode($data);
        
        // Initialize curl and set options
        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://management.azure.com/subscriptions/$subscription_id/resourcegroups/$resource_group_name?api-version=2016-06-01",
            CURLOPT_HTTPHEADER => array(
                "Content-type: Application/json",
                "Authorization: Bearer $access_token"),
            CURLOPT_POSTFIELDS => $data_json_location
        );

        $resp = make_request($curl_opt_array);
        
        print_r($resp);

        return $resp;

    }

  // $deployment_name = "FirstCaylentDeployment3";
    // $template_uri = "https://raw.githubusercontent.com/Azure/azure-quickstart-templates/master/101-vm-simple-linux/azuredeploy.json";
    // $adminUsername = "KwadwoTest";
    // $adminPassword = "KwadwoTest123!!!";
    // $dnslabelPrefix = "caylentdeployment3";


    // /* Use arm template to create resources in created resource group */
    // $data = array(
    //     "properties" => array(
    //         "templateLink" => array(
    //             "uri" => $template_uri,
    //             "contentVersion" => "1.0.0.0"
    //             ),
    //         "mode" => "Incremental",
    //         "parameters" => array(
    //             "adminUsername" => array(
    //                 "value" => $adminUsername
    //             ),
    //             "adminPassword" => array(
    //                 "value" => $adminPassword
    //             ),
    //             "dnsLabelPrefix" => array(
    //                 "value" => $dnslabelPrefix
    //             )
    //         )
    //     )
    // );
    // $data_json = json_encode($data);

    function create_or_update_deployment($subscription_id, $resource_group_name, $deployment_name, $data)
    {
        $data_json = json_encode($data);
        // Initialize curl and set options
        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://management.azure.com/subscriptions/$subscription_id/resourcegroups/$resource_group_name/providers/microsoft.resources/deployments/$deployment_name?api-version=2016-09-01",
            CURLOPT_HTTPHEADER => array(
                "Content-type: Application/json",
                "Authorization: Bearer $access_token"),
            CURLOPT_POSTFIELDS => $data_json
        );

        $resp = make_request($curl_opt_array);
        
        print_r($resp);

        return $resp;
    }

    //https://docs.microsoft.com/en-us/rest/api/resources/deploymentoperations
    function get_list_deployment_operations()
    {
        //https://management.azure.com/subscriptions/$a_subscription_id/resourcegroups/$resource_group_name/deployments/$deployment_name/operations?api-version=2016-09-01

                // Initialize curl and set options
        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://management.azure.com/subscriptions/$a_subscription_id/resourcegroups/$resource_group_name/deployments/$deployment_name/operations?api-version=2016-09-01",
            CURLOPT_HTTPHEADER => array(
                "Content-type: Application/json",
                "Authorization: Bearer $access_token")
        );

        $resp = make_request($curl_opt_array);
        
        print_r($resp);

        return $resp;


    }

    //https://docs.microsoft.com/en-us/rest/api/resources/deploymentoperations
    function get_deployment_operation()
    {

    }
  

?>