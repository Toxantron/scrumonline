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

        $jiraUrl .= ' order by priority';

        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', $jiraUrl, [
            'auth' => [$parameters['username'], $parameters['password']]
        ]);
        $response = json_decode($res->getBody()->getContents(), true);
        return $response;
    }
}

return new JiraController($entityManager);
