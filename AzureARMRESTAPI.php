<?php

    function make_request(array $request_array, $ret_code = null)
    {

        $curl  = curl_init();
        curl_setopt_array($curl, $request_array);

        $result = curl_exec($curl);
        $resp = json_decode($result);
        
        // List of Curl error numbers - https://curl.haxx.se/libcurl/c/libcurl-errors.html
        print("Curl Error number: "); 
        print(curl_errno($curl));
        print("\n");

        print("Http status code ");
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        print($http_status);
        print("\n");
        //print("Completed auth request with status of\n");

        //var_dump($resp);
        
        curl_close($curl);

        if ($ret_code)
            return $http_status;
        else 
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


        //var_dump($curl_opt_array);

        print("Make request for auth token\n");
        
        $resp = make_request($curl_opt_array);
        
        //print_r($resp);
        //var_dump($resp);

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
        
        //print_r($resp);

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

    function create_or_update_deployment($subscription_id, $resource_group_name, $deployment_name, $access_token, $data)
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
        
        //print_r($resp);

        return $resp;
    }

    //https://docs.microsoft.com/en-us/rest/api/resources/deploymentoperations
    function get_list_deployment_operations($subscription_id, $resource_group_name, $deployment_name, $access_token)
    {
        //https://management.azure.com/subscriptions/$a_subscription_id/resourcegroups/$resource_group_name/deployments/$deployment_name/operations?api-version=2016-09-01

      // Initialize curl and set options
        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://management.azure.com/subscriptions/$subscription_id/resourcegroups/$resource_group_name/deployments/$deployment_name/operations?api-version=2016-09-01",
            CURLOPT_HTTPHEADER => array(
                "Content-type: Application/json",
                "Authorization: Bearer $access_token")
        );

        $resp = make_request($curl_opt_array);
        
        print_r($resp);

        return $resp;


    }

    //https://docs.microsoft.com/en-us/rest/api/resources/deploymentoperations
    function get_deployment_operation($subscription_id, $resource_group_name, $deployment_name, $operation_id ,$access_token)
    {
        //GET /subscriptions/{subscriptionId}/resourcegroups/{resourceGroupName}/deployments/{deploymentName}/operations/{operationId}?api-version=2016-09-01

        // Initialize curl and set options
        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://management.azure.com/subscriptions/$subscription_id/resourcegroups/$resource_group_name/deployments/$deployment_name/operations/$operation_id?api-version=2016-09-01",
            CURLOPT_HTTPHEADER => array(
                "Content-type: Application/json",
                "Authorization: Bearer $access_token")
        );

        $resp = make_request($curl_opt_array);
        
        //print_r($resp);

        return $resp;

    }
  
    function cancel_deployment($subscription_id, $resource_group_name, $deployment_name, $access_token)
    {
        //POST /subscriptions/{subscriptionId}/resourcegroups/{resourceGroupName}/providers/Microsoft.Resources/deployments/{deploymentName}/cancel?api-version=2016-09-01

        // Initialize curl and set options
        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://management.azure.com/subscriptions/$subscription_id/resourcegroups/$resource_group_name/deployments/$deployment_name/cancel?api-version=2016-09-01",
            CURLOPT_HTTPHEADER => array(
                "Content-type: Application/json",
                "Authorization: Bearer $access_token")
        );

        $resp = make_request($curl_opt_array);
        
        //print_r($resp);

        return $resp;


    }

    function check_existence($subscription_id, $resource_group_name, $deployment_name, $access_token)
    {
        
        //HEAD /subscriptions/{subscriptionId}/resourcegroups/{resourceGroupName}/providers/Microsoft.Resources/deployments/{deploymentName}?api-version=2016-09-01

        // Initialize curl and set options
        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST => "HEAD",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_NOBODY => true,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://management.azure.com/subscriptions/$subscription_id/resourcegroups/$resource_group_name/deployments/$deployment_name?api-version=2016-09-01",
            CURLOPT_HTTPHEADER => array(
                "Content-type: Application/json",
                "Authorization: Bearer $access_token")
        );

        print("About to make request to make_request for check_existence\n");
        $resp = make_request($curl_opt_array,true);
        
        print("Response from check_existence\n");
        print_r($resp);
        print("after Response from check_existence\n");

        return $resp;

    }

    function delete_deployment($subscription_id, $resource_group_name, $deployment_name, $access_token)
    {
        //DELETE /subscriptions/{subscriptionId}/resourcegroups/{resourceGroupName}/providers/Microsoft.Resources/deployments/{deploymentName}?api-version=2016-09-01

        // Initialize curl and set options
        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://management.azure.com/subscriptions/{$subscription_id}/resourcegroups/$resource_group_name/providers/Microsoft.Resources/deployments/$deployment_name?api-version=2016-09-01",
            CURLOPT_HTTPHEADER => array(
                "Content-type: Application/json",
                "Authorization: Bearer $access_token")
        );

        $resp = make_request($curl_opt_array,true);
        
        print("Response from delete_deployment\n");
        print_r($resp);
        print("after Response from delete_deployment\n");

        return $resp;

    }

    function export_template($subscription_id, $resource_group_name, $deployment_name, $access_token)
    {
        //POST /subscriptions/{subscriptionId}/resourcegroups/{resourceGroupName}/providers/Microsoft.Resources/deployments/{deploymentName}/exportTemplate?api-version=2016-09-01
        
        // Initialize curl and set options
        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://management.azure.com/subscriptions/$subscription_id/resourcegroups/$resource_group_name/providers/Microsoft.Resources/deployments/$deployment_name/exportTemplate?api-version=2016-09-01",
            CURLOPT_HTTPHEADER => array(
                "Content-type: Application/json",
                "Authorization: Bearer $access_token")
        );

        $resp = make_request($curl_opt_array);
        
        print("Response from export_template\n");
        print_r($resp);
        print("after Response from export_template\n");

        return $resp;


    }

    function get_deployment($subscription_id, $resource_group_name, $deployment_name, $access_token)
    {
        //GET /subscriptions/{subscriptionId}/resourcegroups/{resourceGroupName}/providers/Microsoft.Resources/deployments/{deploymentName}?api-version=2016-09-01
        throw new Exception('Not implemented');
                
        // Initialize curl and set options
        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://management.azure.com/$subscription_id/resourcegroups/$resource_group_name/providers/Microsoft.Resources/deployments/$deployment_name?api-version=2016-09-01",
            CURLOPT_HTTPHEADER => array(
                "Content-type: Application/json",
                "Authorization: Bearer $access_token")
        );

        $resp = make_request($curl_opt_array);
        
        print("Response from get_deployment\n");
        print_r($resp);
        print("after Response from get_deployment\n");

        return $resp;


    }

    function list_deployments($subscription_id, $resource_group_name, $deployment_name, $access_token)
    {
        //GET /subscriptions/{subscriptionId}/resourcegroups/{resourceGroupName}/providers/Microsoft.Resources/deployments/?api-version=2016-09-01[&$filter&$top]

        // Initialize curl and set options
        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://management.azure.com/subscriptions/$subscription_id/resourcegroups/$resource_group_name/providers/Microsoft.Resources/deployments/?api-version=2016-09-01",
            CURLOPT_HTTPHEADER => array(
                "Content-type: Application/json",
                "Authorization: Bearer $access_token")
        );

        $resp = make_request($curl_opt_array);
        
        print("Response from list_deployments\n");
        print_r($resp);
        print("after Response from list_deployments\n");

        return $resp;

    }

    function validate_deployment($subscription_id, $resource_group_name, $deployment_name, $data)
    {
        //POST /subscriptions/{subscriptionId}/resourcegroups/{resourceGroupName}/providers/Microsoft.Resources/deployments/{deploymentName}/validate?api-version=2016-09-01

        // Initialize curl and set options
        $curl_opt_array = array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://management.azure.com/subscriptions/$subscription_id/resourcegroups/$resource_group_name/providers/Microsoft.Resources/deployments/$deployment_name/validate?api-version=2016-09-01",
            CURLOPT_HTTPHEADER => array(
                "Content-type: Application/json",
                "Authorization: Bearer $access_token"),
            CURLOPT_POSTFIELDS => $data
        );

        $resp = make_request($curl_opt_array);
        
        print_r($resp);

        return $resp;
    }

?>