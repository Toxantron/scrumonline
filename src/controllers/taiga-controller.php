<?php

/*
 * Taiga controller class to handle all Taiga operations
 */
class TaigaController extends ControllerBase
{
 
    public function auth()
    {
        global $taigaConfiguration;
        $parameters = array_merge((array) $taigaConfiguration, $_POST);

        $taigaUrl = $parameters['base_url'] . '/api/v1/auth';

        $client = new GuzzleHttp\Client();
        $res = $client->request('POST', $taigaUrl, [
            'json' => [
                'type' => $parameters['type'],
                'username' => $parameters['username'],
                'password' => $parameters['password']
            ]
        ]);
        $response = json_decode($res->getBody()->getContents(), true);
        return $response;
    }

    public function resolve()
    {
        global $taigaConfiguration;
        $parameters = array_merge((array) $taigaConfiguration, $_POST);

        $taigaUrl = $parameters['base_url'] . '/api/v1/resolver?';

        $query = '';

        if (isset($parameters['project'])) {
            $query .= '&project=' . $parameters['project'];
        }

        if (isset($parameters['us'])) {
            $query .= '&us=' . $parameters['us'];
        }

        if (isset($parameters['issue'])) {
            $query .= '&issue=' . $parameters['issue'];
        }

        if (isset($parameters['task'])) {
            $query .= '&task=' . $parameters['task'];
        }

        if (isset($parameters['milestone'])) {
            $query .= '&milestone=' . $parameters['milestone'];
        }

        if (isset($parameters['wikipage'])) {
            $query .= '&wikipage=' . $parameters['wikipage'];
        }

        if (isset($parameters['ref'])) {
            $query .= '&ref=' . $parameters['ref'];
        }

        $taigaUrl .= substr($query, 1);

        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', $taigaUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $parameters['auth_token']
            ]
        ]);
        $response = json_decode($res->getBody()->getContents(), true);
        return $response;
    }

    public function getStoryStatuses()
    {
        global $taigaConfiguration;
        $parameters = array_merge((array) $taigaConfiguration, $_POST);

        $taigaUrl = $parameters['base_url'] . '/api/v1/userstory-statuses?project=' . $parameters['project'];

        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', $taigaUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $parameters['auth_token']
            ]
        ]);
        $response = json_decode($res->getBody()->getContents(), true);
        return $response;
    }

    public function getStory()
    {
        global $taigaConfiguration;
        $parameters = array_merge((array) $taigaConfiguration, $_POST);

        $taigaUrl = $parameters['base_url'] . '/api/v1/userstories/' . $parameters['id'];

        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', $taigaUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $parameters['auth_token']
            ]
        ]);
        $response = json_decode($res->getBody()->getContents(), true);
        return $response;
    }

    public function getStories()
    {
        global $taigaConfiguration;
        $parameters = array_merge((array) $taigaConfiguration, $_POST);

        $taigaUrl = $parameters['base_url'] . '/api/v1/userstories?project=' . $parameters['project'];
        if (isset($parameters['milestone'])) {
            $taigaUrl .= '&milestone=' . $parameters['milestone'];
        }

        # Careful, Taiga's API has an extra underscore here
        if (isset($parameters['milestone_isnull'])) {
            $taigaUrl .= '&milestone__isnull=' . $parameters['milestone_isnull'];
        }

        if (isset($parameters['status'])) {
            $taigaUrl .= '&status=' . $parameters['status'];
        }

        if (isset($parameters['status_is_archived'])) {
            $taigaUrl .= '&status_is_archived=' . $parameters['status_is_archived'];
        }

        if (isset($parameters['tags'])) {
            $taigaUrl .= '&tags=' . $parameters['tags'];
        }

        if (isset($parameters['watchers'])) {
            $taigaUrl .= '&watchers=' . $parameters['watchers'];
        }

        if (isset($parameters['assigned_to'])) {
            $taigaUrl .= '&assigned_to=' . $parameters['assigned_to'];
        }

        if (isset($parameters['epic'])) {
            $taigaUrl .= '&epic=' . $parameters['epic'];
        }

        if (isset($parameters['role'])) {
            $taigaUrl .= '&role=' . $parameters['role'];
        }

        if (isset($parameters['status_is_closed'])) {
            $taigaUrl .= '&status_is_closed=' . $parameters['status_is_closed'];
        }

        if (isset($parameters['exclude_status'])) {
            $taigaUrl .= '&exclude_status=' . $parameters['exclude_status'];
        }

        if (isset($parameters['exclude_tags'])) {
            $taigaUrl .= '&exclude_tags=' . $parameters['exclude_tags'];
        }

        if (isset($parameters['exclude_assigned_to'])) {
            $taigaUrl .= '&exclude_assigned_to=' . $parameters['exclude_assigned_to'];
        }

        if (isset($parameters['exclude_role'])) {
            $taigaUrl .= '&exclude_role=' . $parameters['exclude_role'];
        }

        if (isset($parameters['exclude_epic'])) {
            $taigaUrl .= '&exclude_epic=' . $parameters['exclude_epic'];
        }

        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', $taigaUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $parameters['auth_token']
            ]
        ]);
        $response = json_decode($res->getBody()->getContents(), true);
        return $response;
    }
}

return new TaigaController($entityManager);
