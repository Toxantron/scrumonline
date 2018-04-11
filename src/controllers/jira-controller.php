<?php

/*
 * Jira controller class to handle all Jira operations
 */
class JiraController extends ControllerBase
{
    public function getIssues()
    {
        $parameters = array_merge((array) $jiraConfiguration, $_POST);

        $jiraUrl = $parameters['base_url'] . '/rest/api/2/search?jql=project=' . $parameters['project'];
        if ($parameters['jql']) {
            $jiraUrl .= ' and ' . $parameters['jql'];
        }

        if (substr_count(strtolower($parameters['jql']), "order by") == 0 && substr_count(strtolower($parameters['jql']), "order%20by") == 0) {
            $jiraUrl .= ' order by priority';
        }

        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', $jiraUrl, [
            'auth' => [$parameters['username'], $parameters['password']]
        ]);
        $response = json_decode($res->getBody()->getContents(), true);
        return $response;
    }
}

return new JiraController($entityManager);
